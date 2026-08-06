"""Fact-verification layer.

The TF-IDF classifier only sees token frequencies, so a calmly written false
statement ("X is the Prime Minister of Y") looks exactly like real reporting to
it. This module extracts structured role claims from the input and checks them
against a local knowledge base, Wikidata, and the Google Fact Check Tools API,
producing a verdict that the API layer can use to override the style model.

No verification source is allowed to raise into the request path: every network
call is bounded by a deadline and falls back to the offline knowledge base.
"""

from __future__ import annotations

import json
import os
import re
import threading
import time
from difflib import SequenceMatcher
from pathlib import Path
from typing import Any

ROOT = Path(__file__).resolve().parent.parent
DEFAULT_KB_PATH = ROOT / "data" / "knowledge_base.json"
DEFAULT_CACHE_PATH = ROOT / "data" / "cache" / "fact_cache.json"

CONTRADICTED = "CONTRADICTED"
SUPPORTED = "SUPPORTED"
NO_DATA = "NO_DATA"

WIKIDATA_API = "https://www.wikidata.org/w/api.php"
FACT_CHECK_API = "https://factchecktools.googleapis.com/v1alpha1/claims:search"

# Canonical role name -> the Wikidata property that holds it on a place entity.
# P6 = head of government, P35 = head of state.
ROLE_WIKIDATA_PROPERTY = {
    "prime minister": "P6",
    "chief minister": "P6",
    "mayor": "P6",
    "president": "P35",
    "monarch": "P35",
}

# Surface form -> canonical role. Keys are written with single spaces; hyphens and
# repeated whitespace are normalised away before lookup, so "Wazir-e-Azam",
# "wazir e azam" and "Wazir--e--Azam" all resolve to the same role.
ROLE_ALIASES = {
    "prime minister": "prime minister",
    "premier": "prime minister",
    "pm": "prime minister",
    "vice president": "vice president",
    "deputy prime minister": "deputy prime minister",
    "president": "president",
    "chief minister": "chief minister",
    "chief justice": "chief justice",
    "chief of army staff": "army chief",
    "army chief": "army chief",
    "governor": "governor",
    "mayor": "mayor",
    "king": "monarch",
    "queen": "monarch",
    "monarch": "monarch",
    "pope": "pope",
    "secretary general": "secretary general",
    "foreign minister": "foreign minister",
    "finance minister": "finance minister",
    "defence minister": "defence minister",
    "defense minister": "defence minister",
    "interior minister": "interior minister",
    "captain": "captain",
    "chief executive officer": "ceo",
    "ceo": "ceo",
    "chairman": "chairman",
    # Roman Urdu. Viral claims in Pakistan are usually written this way rather
    # than in English, so the same offices need their local names.
    "wazir e azam": "prime minister",
    "wazir i azam": "prime minister",
    "wazeer e azam": "prime minister",
    "vazir e azam": "prime minister",
    "sadar": "president",
    "sadr": "president",
    "sadar e mumlikat": "president",
    "sadar mumlikat": "president",
    "wazir e ala": "chief minister",
    "wazir e aala": "chief minister",
    "wazeer e ala": "chief minister",
    "wazir e khazana": "finance minister",
    "wazir e kharja": "foreign minister",
    "wazir e kharija": "foreign minister",
    "wazir e difa": "defence minister",
    "wazir e dakhla": "interior minister",
    "sipah salar": "army chief",
    "chief justice sahib": "chief justice",
    "kaptan": "captain",
    "gavarnar": "governor",
}

# Google Fact Check publishers phrase their verdicts freely; these substrings are
# the common ways of saying "this claim is false" / "this claim is true".
FALSE_RATINGS = (
    "false",
    "pants on fire",
    "fake",
    "incorrect",
    "misleading",
    "no evidence",
    "unsupported",
    "unfounded",
    "altered",
    "hoax",
    "scam",
    "debunked",
    "not true",
    "distorts",
)
TRUE_RATINGS = ("true", "correct", "accurate", "verified", "confirmed", "legitimate")


def _role_pattern() -> str:
    forms = sorted(ROLE_ALIASES, key=len, reverse=True)
    # Spaces inside an alias may surface as hyphens ("Wazir-e-Azam"), so both
    # separators are accepted wherever the alias has a space.
    return "|".join(
        re.escape(f).replace(r"\ ", r"[\s\-\u2013]+") for f in forms
    )


# Entity detection relies on capitalisation, so the patterns are compiled
# case-sensitively and only the fixed vocabulary fragments (roles, verbs,
# articles) opt into case-insensitivity via inline (?i:...) groups. Compiling
# the whole pattern with re.IGNORECASE would let [A-Z] match lowercase words and
# swallow the rest of the sentence into the subject.
_ROLE = rf"(?i:{_role_pattern()})"
# A capitalised token run: "Babar Azam", "United States", "Shehbaz Sharif".
_ENTITY = r"[A-Z][\w.'\u2019-]*(?:\s+(?:of\s+|the\s+)?[A-Z][\w.'\u2019-]*)*"
_ARTICLE = r"(?i:the\s+|a\s+)?"
_QUALIFIER = r"(?i:current\s+|new\s+|newly\s+elected\s+|acting\s+|former\s+|ex-)?"
_IS = r"(?i:is\s+now|is|was|became|has\s+become|has\s+been\s+elected|was\s+elected)"

# Roman Urdu fragments. Only lowercase filler is skipped, so a capitalised word
# can never be swallowed as filler when it should have been captured as a place.
_UR_OF = r"(?i:ka|ke|ki)"
_UR_QUALIFIER = r"(?i:naya\s+|naye\s+|nai\s+|maujuda\s+|maujooda\s+|sabiq\s+|nau\s+muntakhab\s+)?"
_UR_FILLER = r"(?:[a-z]+\s+){0,3}?"
_UR_IS = (
    r"(?i:ban\s+ga(?:ye|ya|yi|yin)|ban\s+chuke|banaye\s+ga(?:ye|ya)|bane|"
    r"muqarrar|muntakhab|hain|hai|thay|thi)"
)

CLAIM_PATTERNS: tuple[tuple[str, str], ...] = (
    # "Babar Azam is Pakistan's Prime Minister"
    (
        "subject_possessive_role",
        rf"(?P<subject>{_ENTITY})\s+{_IS}\s+{_ARTICLE}"
        rf"(?P<place>{_ENTITY})['\u2019]s\s+{_QUALIFIER}(?P<role>{_ROLE})\b",
    ),
    # "Babar Azam is the Prime Minister of Pakistan"
    (
        "subject_role_of_place",
        rf"(?P<subject>{_ENTITY})\s+{_IS}\s+{_ARTICLE}{_QUALIFIER}(?P<role>{_ROLE})\s+"
        rf"(?i:of)\s+{_ARTICLE}(?P<place>{_ENTITY})",
    ),
    # "The Prime Minister of Pakistan is Babar Azam"
    (
        "role_of_place_is_subject",
        rf"\b{_ARTICLE}{_QUALIFIER}(?P<role>{_ROLE})\s+(?i:of)\s+{_ARTICLE}"
        rf"(?P<place>{_ENTITY})\s+{_IS}\s+{_ARTICLE}(?P<subject>{_ENTITY})",
    ),
    # "Pakistan's Prime Minister Babar Azam" / "Pakistan's Prime Minister is Babar Azam"
    (
        "possessive_role_subject",
        rf"(?P<place>{_ENTITY})['\u2019]s\s+{_QUALIFIER}(?P<role>{_ROLE})\s+"
        rf"(?:{_IS}\s+)?{_ARTICLE}(?P<subject>{_ENTITY})",
    ),
    # "Babar Azam, the Prime Minister of Pakistan, said ..."
    (
        "appositive",
        rf"(?P<subject>{_ENTITY}),\s+{_ARTICLE}{_QUALIFIER}(?P<role>{_ROLE})\s+(?i:of)\s+"
        rf"{_ARTICLE}(?P<place>{_ENTITY}),",
    ),
    # --- Roman Urdu ---------------------------------------------------------
    # Urdu puts the verb last, so the English patterns above never fire on the
    # phrasing most viral claims in Pakistan actually use.
    #
    # "Babar Azam ko achanak Pakistan ka Wazir-e-Azam bana diya gaya"
    (
        "ur_subject_ko_place_role",
        rf"(?P<subject>{_ENTITY})\s+(?i:ko)\s+{_UR_FILLER}"
        rf"(?P<place>{_ENTITY})\s+{_UR_OF}\s+{_UR_QUALIFIER}(?P<role>{_ROLE})\b",
    ),
    # "Babar Azam Pakistan ke naye Wazir-e-Azam ban gaye / hain"
    (
        "ur_subject_place_role_verb",
        rf"(?P<subject>{_ENTITY})\s+{_UR_FILLER}(?P<place>{_ENTITY})\s+{_UR_OF}\s+"
        rf"{_UR_QUALIFIER}(?P<role>{_ROLE})\s+{_UR_IS}",
    ),
    # "Pakistan ke Wazir-e-Azam Babar Azam ne kaha"
    (
        "ur_place_role_subject",
        rf"(?P<place>{_ENTITY})\s+{_UR_OF}\s+{_UR_QUALIFIER}(?P<role>{_ROLE})\s+"
        rf"(?P<subject>{_ENTITY})",
    ),
)

_COMPILED_PATTERNS = [(name, re.compile(pattern)) for name, pattern in CLAIM_PATTERNS]

# Stripped from a captured place/subject so "the United States" == "United States".
_LEADING_ARTICLES = ("the ", "a ", "an ")

# Sentence boundary: terminal punctuation preceded by a lowercase letter, digit or
# closing quote. Requiring a non-uppercase character before the period keeps
# initialisms such as "U.S." intact.
_SENTENCE_SPLIT = re.compile(r"(?<=[a-z0-9)\"'\u2019])[.!?]+[\s\u00a0]+(?=[\"'\u201c(]?[A-Z])")


def normalize_name(value: str) -> str:
    """Lowercase, drop punctuation and articles, collapse whitespace."""
    if not value:
        return ""
    text = value.strip().lower()
    for article in _LEADING_ARTICLES:
        if text.startswith(article):
            text = text[len(article) :]
    text = re.sub(r"[^\w\s]", " ", text)
    return re.sub(r"\s+", " ", text).strip()


def names_match(a: str, b: str) -> bool:
    """Compare two person/place names tolerantly.

    Transliterated names vary in spelling across outlets (Shehbaz / Shahbaz), so
    an exact match is too strict, but a low similarity floor would let unrelated
    people match. 0.86 separates spelling variants from different people.
    """
    left, right = normalize_name(a), normalize_name(b)
    if not left or not right:
        return False
    if left == right:
        return True
    return SequenceMatcher(None, left, right).ratio() >= 0.86


def _describe_roles(roles: list[str], limit: int = 3) -> str:
    """Turn ["cricketer", "athlete"] into "a cricketer and athlete"."""
    cleaned = [r.strip() for r in roles[:limit] if r and r.strip()]
    if not cleaned:
        return "known for something else entirely"

    article = "an" if cleaned[0][:1].lower() in "aeiou" else "a"
    if len(cleaned) == 1:
        return f"{article} {cleaned[0]}"
    return f"{article} {', '.join(cleaned[:-1])} and {cleaned[-1]}"


def canonical_role(surface: str) -> str | None:
    key = re.sub(r"[\s\-\u2013]+", " ", surface.strip().lower())
    return ROLE_ALIASES.get(key)


def split_sentences(text: str) -> list[str]:
    return [part.strip() for part in _SENTENCE_SPLIT.split(text or "") if part.strip()]


def extract_claims(text: str, limit: int = 5) -> list[dict[str, str]]:
    """Pull structured (subject, role, place) triples out of free text.

    Matching runs per sentence so a capitalised entity cannot absorb the start of
    the following sentence ("Pakistan. Indeed" as a place name).
    """
    if not text:
        return []

    claims: list[dict[str, str]] = []
    seen: set[tuple[str, str, str]] = set()

    for sentence in split_sentences(text):
        for pattern_name, compiled in _COMPILED_PATTERNS:
            for match in compiled.finditer(sentence):
                groups = match.groupdict()
                role = canonical_role(groups.get("role") or "")
                subject = (groups.get("subject") or "").strip(" ,.;:")
                place = (groups.get("place") or "").strip(" ,.;:")

                if not role or not subject or not place:
                    continue
                # A role word captured as the subject means the regex latched onto
                # the wrong span, e.g. "President" in "President of Pakistan is ...".
                if canonical_role(subject) or canonical_role(place):
                    continue

                key = (normalize_name(subject), role, normalize_name(place))
                if key in seen:
                    continue
                seen.add(key)

                claims.append(
                    {
                        "subject": subject,
                        "role": role,
                        "place": place,
                        "pattern": pattern_name,
                        "raw": match.group(0).strip(),
                    }
                )
                if len(claims) >= limit:
                    return claims

    return claims


class FileCache:
    """Small JSON-backed TTL cache so repeated checks skip the network."""

    def __init__(self, path: Path | None = None, ttl_days: int = 7, max_entries: int = 500):
        self.path = Path(path or DEFAULT_CACHE_PATH)
        self.ttl_seconds = ttl_days * 86400
        self.max_entries = max_entries
        self._lock = threading.Lock()
        self._data: dict[str, dict[str, Any]] | None = None

    def _load(self) -> dict[str, dict[str, Any]]:
        if self._data is not None:
            return self._data
        try:
            with open(self.path, encoding="utf-8") as f:
                loaded = json.load(f)
            self._data = loaded if isinstance(loaded, dict) else {}
        except (FileNotFoundError, json.JSONDecodeError, OSError):
            self._data = {}
        return self._data

    def get(self, key: str) -> Any | None:
        with self._lock:
            entry = self._load().get(key)
            if not entry:
                return None
            if entry.get("expires_at", 0) < time.time():
                self._data.pop(key, None)
                return None
            return entry.get("value")

    def set(self, key: str, value: Any) -> None:
        with self._lock:
            data = self._load()
            data[key] = {"value": value, "expires_at": time.time() + self.ttl_seconds}
            if len(data) > self.max_entries:
                for stale in sorted(data, key=lambda k: data[k].get("expires_at", 0))[
                    : len(data) - self.max_entries
                ]:
                    data.pop(stale, None)
            self._flush()

    def _flush(self) -> None:
        try:
            self.path.parent.mkdir(parents=True, exist_ok=True)
            with open(self.path, "w", encoding="utf-8") as f:
                json.dump(self._data, f)
        except OSError:
            pass  # A cache that cannot persist is still usable in memory.


class LocalKnowledgeBase:
    """Offline structured facts. Always available, never times out."""

    def __init__(self, path: Path | None = None):
        self.path = Path(path or DEFAULT_KB_PATH)
        self.office_holders: list[dict[str, Any]] = []
        self.person_roles: list[dict[str, Any]] = []
        self.updated_at: str | None = None
        self._load()

    def _load(self) -> None:
        try:
            with open(self.path, encoding="utf-8") as f:
                data = json.load(f)
        except (FileNotFoundError, json.JSONDecodeError, OSError):
            return
        self.office_holders = data.get("office_holders", []) or []
        self.person_roles = data.get("person_roles", []) or []
        self.updated_at = data.get("updated_at")

    def find_office_holder(self, role: str, place: str) -> dict[str, Any] | None:
        for entry in self.office_holders:
            if entry.get("role") != role:
                continue
            candidates = [entry.get("place", "")] + list(entry.get("place_aliases", []))
            if any(names_match(place, candidate) for candidate in candidates):
                return entry
        return None

    def find_person(self, name: str) -> dict[str, Any] | None:
        for entry in self.person_roles:
            candidates = [entry.get("person", "")] + list(entry.get("aliases", []))
            if any(names_match(name, candidate) for candidate in candidates):
                return entry
        return None

    def check(self, claim: dict[str, str]) -> dict[str, Any] | None:
        """Return an evidence dict, or None when this claim is not covered."""
        subject, role, place = claim["subject"], claim["role"], claim["place"]

        person = self.find_person(subject)
        if person and any(normalize_name(r) == normalize_name(role) for r in person.get("not_roles", [])):
            return {
                "source": "local_kb",
                "verdict": CONTRADICTED,
                "confidence": 95.0,
                "statement": (
                    f"{person.get('person', subject)} is "
                    f"{_describe_roles(person.get('known_roles', []))}, "
                    f"not the {role} of {place}."
                ),
                "url": person.get("source"),
                "rating": "False",
            }

        entry = self.find_office_holder(role, place)
        if not entry:
            return None

        holder = entry.get("holder", "")
        candidates = [holder] + list(entry.get("holder_aliases", []))
        if any(names_match(subject, candidate) for candidate in candidates):
            return {
                "source": "local_kb",
                "verdict": SUPPORTED,
                "confidence": 90.0,
                "statement": f"Our records list {holder} as the {role} of {entry.get('place', place)}.",
                "url": entry.get("source"),
                "rating": "True",
            }

        return {
            "source": "local_kb",
            "verdict": CONTRADICTED,
            "confidence": 96.0,
            "statement": (
                f"The {role} of {entry.get('place', place)} is {holder}, not {subject}."
            ),
            "url": entry.get("source"),
            "rating": "False",
        }


class WikidataClient:
    """Live office-holder lookup. Used only where the local KB has no entry."""

    def __init__(self, cache: FileCache, timeout: float = 2.5):
        self.cache = cache
        self.timeout = timeout
        self.user_agent = os.environ.get(
            "FNI_USER_AGENT", "VeriFactAI/1.0 (FYP fact verification layer)"
        )

    def _get(self, params: dict[str, str], deadline: float) -> dict[str, Any] | None:
        if time.monotonic() >= deadline:
            return None
        import requests

        remaining = min(self.timeout, deadline - time.monotonic())
        response = requests.get(
            WIKIDATA_API,
            params={**params, "format": "json"},
            timeout=remaining,
            headers={"User-Agent": self.user_agent},
        )
        response.raise_for_status()
        return response.json()

    def _search_entity(self, name: str, deadline: float) -> str | None:
        cache_key = f"wd:search:{normalize_name(name)}"
        cached = self.cache.get(cache_key)
        if cached is not None:
            return cached or None

        data = self._get(
            {"action": "wbsearchentities", "search": name, "language": "en", "type": "item", "limit": "1"},
            deadline,
        )
        if not data:
            return None
        results = data.get("search") or []
        entity_id = results[0].get("id") if results else None
        self.cache.set(cache_key, entity_id or "")
        return entity_id

    def _current_claim_value(self, entity_id: str, prop: str, deadline: float) -> str | None:
        cache_key = f"wd:claim:{entity_id}:{prop}"
        cached = self.cache.get(cache_key)
        if cached is not None:
            return cached or None

        data = self._get({"action": "wbgetclaims", "entity": entity_id, "property": prop}, deadline)
        if not data:
            return None

        statements = (data.get("claims") or {}).get(prop) or []
        preferred = [s for s in statements if s.get("rank") == "preferred"]
        # Wikidata keeps every past office holder; without a preferred rank, the
        # current one is the statement that has no end-date (P582) qualifier.
        ongoing = [s for s in statements if "P582" not in (s.get("qualifiers") or {})]
        chosen = (preferred or ongoing or statements)[:1]
        if not chosen:
            self.cache.set(cache_key, "")
            return None

        try:
            holder_id = chosen[0]["mainsnak"]["datavalue"]["value"]["id"]
        except (KeyError, TypeError):
            self.cache.set(cache_key, "")
            return None

        self.cache.set(cache_key, holder_id)
        return holder_id

    def _label(self, entity_id: str, deadline: float) -> str | None:
        cache_key = f"wd:label:{entity_id}"
        cached = self.cache.get(cache_key)
        if cached is not None:
            return cached or None

        data = self._get(
            {"action": "wbgetentities", "ids": entity_id, "props": "labels", "languages": "en"},
            deadline,
        )
        if not data:
            return None
        label = (
            (data.get("entities") or {})
            .get(entity_id, {})
            .get("labels", {})
            .get("en", {})
            .get("value")
        )
        self.cache.set(cache_key, label or "")
        return label

    def check(self, claim: dict[str, str], deadline: float) -> dict[str, Any] | None:
        prop = ROLE_WIKIDATA_PROPERTY.get(claim["role"])
        if not prop:
            return None

        place_id = self._search_entity(claim["place"], deadline)
        if not place_id:
            return None
        holder_id = self._current_claim_value(place_id, prop, deadline)
        if not holder_id:
            return None
        holder = self._label(holder_id, deadline)
        if not holder:
            return None

        url = f"https://www.wikidata.org/wiki/{place_id}"
        if names_match(claim["subject"], holder):
            return {
                "source": "wikidata",
                "verdict": SUPPORTED,
                "confidence": 88.0,
                "statement": f"Wikidata lists {holder} as the {claim['role']} of {claim['place']}.",
                "url": url,
                "rating": "True",
            }
        return {
            "source": "wikidata",
            "verdict": CONTRADICTED,
            "confidence": 92.0,
            "statement": (
                f"Wikidata lists {holder} as the {claim['role']} of {claim['place']}, "
                f"not {claim['subject']}."
            ),
            "url": url,
            "rating": "False",
        }


class FactCheckClient:
    """Google Fact Check Tools API — published fact-checks by PolitiFact, AFP, etc."""

    def __init__(self, cache: FileCache, api_key: str | None = None, timeout: float = 2.5):
        self.cache = cache
        self.api_key = api_key or os.environ.get("FNI_FACTCHECK_API_KEY", "")
        self.timeout = timeout

    @property
    def enabled(self) -> bool:
        return bool(self.api_key)

    @staticmethod
    def _map_rating(rating: str) -> str:
        lowered = (rating or "").strip().lower()
        if not lowered:
            return NO_DATA
        if any(token in lowered for token in FALSE_RATINGS):
            return CONTRADICTED
        if any(token in lowered for token in TRUE_RATINGS):
            return SUPPORTED
        return NO_DATA

    def search(self, query: str, deadline: float) -> list[dict[str, Any]]:
        if not self.enabled or not query or time.monotonic() >= deadline:
            return []

        cache_key = f"gfc:{normalize_name(query)[:180]}"
        cached = self.cache.get(cache_key)
        if cached is not None:
            return cached

        import requests

        remaining = min(self.timeout, deadline - time.monotonic())
        response = requests.get(
            FACT_CHECK_API,
            params={"query": query[:300], "key": self.api_key, "languageCode": "en", "pageSize": 5},
            timeout=remaining,
        )
        response.raise_for_status()
        payload = response.json()

        evidence: list[dict[str, Any]] = []
        for claim in (payload.get("claims") or [])[:3]:
            for review in (claim.get("claimReview") or [])[:1]:
                verdict = self._map_rating(review.get("textualRating", ""))
                if verdict == NO_DATA:
                    continue
                publisher = (review.get("publisher") or {}).get("name") or "a fact-checking organisation"
                evidence.append(
                    {
                        "source": "google_factcheck",
                        "verdict": verdict,
                        "confidence": 88.0 if verdict == CONTRADICTED else 80.0,
                        "statement": (
                            f"{publisher} reviewed a similar claim "
                            f"(\"{(claim.get('text') or '').strip()[:160]}\") "
                            f"and rated it: {review.get('textualRating')}."
                        ),
                        "url": review.get("url"),
                        "rating": review.get("textualRating"),
                    }
                )

        self.cache.set(cache_key, evidence)
        return evidence


class FactVerifier:
    def __init__(self, config: dict[str, Any] | None = None):
        cfg = config or {}
        self.enabled = _env_flag("FNI_FACT_CHECK_ENABLED", cfg.get("enabled", True))
        self.timeout_seconds = float(cfg.get("timeout_seconds", 4.0))
        self.source_timeout = float(cfg.get("source_timeout_seconds", 2.5))
        sources = cfg.get("sources") or ["local_kb", "wikidata", "google_factcheck"]
        self.sources = set(sources)

        self.kb = LocalKnowledgeBase(cfg.get("knowledge_base_path"))
        self.cache = FileCache(
            cfg.get("cache_path"),
            ttl_days=int(cfg.get("cache_ttl_days", 7)),
        )
        self.wikidata = WikidataClient(self.cache, timeout=self.source_timeout)
        self.fact_check = FactCheckClient(self.cache, timeout=self.source_timeout)

    def verify(self, text: str) -> dict[str, Any]:
        result: dict[str, Any] = {
            "checked": False,
            "verdict": NO_DATA,
            "confidence": 0.0,
            "claims": [],
            "evidence": [],
            "sources_checked": [],
            "degraded": False,
            "knowledge_base_updated_at": self.kb.updated_at,
        }
        if not self.enabled:
            return result

        deadline = time.monotonic() + self.timeout_seconds
        claims = extract_claims(text)
        result["checked"] = True
        result["claims"] = [
            {"subject": c["subject"], "role": c["role"], "place": c["place"], "raw": c["raw"]}
            for c in claims
        ]

        evidence: list[dict[str, Any]] = []
        sources_checked: list[str] = []
        degraded = False

        if claims and "local_kb" in self.sources:
            sources_checked.append("local_kb")
            for claim in claims:
                found = self.kb.check(claim)
                if found:
                    evidence.append({**found, "claim": claim["raw"]})

        uncovered = [
            c for c in claims if not any(e.get("claim") == c["raw"] for e in evidence)
        ]
        if uncovered and "wikidata" in self.sources:
            sources_checked.append("wikidata")
            for claim in uncovered:
                try:
                    found = self.wikidata.check(claim, deadline)
                except Exception:
                    degraded = True
                    break
                if found:
                    evidence.append({**found, "claim": claim["raw"]})

        if "google_factcheck" in self.sources and self.fact_check.enabled:
            sources_checked.append("google_factcheck")
            query = claims[0]["raw"] if claims else text.strip()[:200]
            try:
                evidence.extend(self.fact_check.search(query, deadline))
            except Exception:
                degraded = True

        result["evidence"] = evidence
        result["sources_checked"] = sources_checked
        result["degraded"] = degraded

        contradictions = [e for e in evidence if e["verdict"] == CONTRADICTED]
        supports = [e for e in evidence if e["verdict"] == SUPPORTED]

        # A contradiction outranks a support: one verified-false claim is enough
        # to discredit the text, whereas one verified-true claim proves nothing
        # about the rest of it.
        if contradictions:
            best = max(contradictions, key=lambda e: e["confidence"])
            result["verdict"] = CONTRADICTED
            result["confidence"] = best["confidence"]
        elif supports:
            best = max(supports, key=lambda e: e["confidence"])
            result["verdict"] = SUPPORTED
            result["confidence"] = best["confidence"]

        return result


def _env_flag(name: str, default: bool) -> bool:
    raw = os.environ.get(name)
    if raw is None:
        return bool(default)
    return raw.strip().lower() in ("1", "true", "yes", "on")


_verifier: FactVerifier | None = None
_verifier_lock = threading.Lock()


def get_verifier(config: dict[str, Any] | None = None) -> FactVerifier:
    global _verifier
    with _verifier_lock:
        if _verifier is None:
            _verifier = FactVerifier(config)
        return _verifier


def reset_verifier() -> None:
    """Drop the cached singleton so a reloaded knowledge base takes effect."""
    global _verifier
    with _verifier_lock:
        _verifier = None


def verify(text: str, config: dict[str, Any] | None = None) -> dict[str, Any]:
    return get_verifier(config).verify(text)
