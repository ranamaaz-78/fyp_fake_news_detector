"""Writing-style signals behind a verdict.

These checks used to run in the browser (`performNLPAnalysis` in verifier.js),
which meant the scores shown to users could not be reproduced server-side and
the "fact-check cross-reference" bar was invented from a hardcoded topic list.
Everything measurable now happens here, so the API, the inline results panel and
the standalone result pages all describe the same analysis.

Each signal is a plain-language statement a non-technical reader can act on.
"""

from __future__ import annotations

import re
from typing import Any

NEGATIVE = "negative"
POSITIVE = "positive"
NEUTRAL = "neutral"

SENSATIONAL_WORDS = (
    "shocking", "shocked", "secret", "secrets", "miracle", "exposed", "conspiracy",
    "unbelievable", "insane", "scandal", "hoax", "cabal", "illuminati", "propaganda",
    "bombshell", "you won't believe", "they don't want you to know", "wake up",
    "mainstream media", "cover-up", "coverup", "banned", "censored", "leaked",
)

EMOTIONAL_WORDS = (
    "hate", "furious", "evil", "disgusting", "filthy", "terror", "panic", "chaos",
    "outrage", "betrayal", "traitor", "traitors", "liars", "destroy", "destroyed",
)

TRUSTED_PUBLISHERS = (
    "reuters.com", "apnews.com", "bbc.com", "bbc.co.uk", "nytimes.com",
    "washingtonpost.com", "theguardian.com", "bloomberg.com", "npr.org", "wsj.com",
    "ft.com", "aljazeera.com", "dawn.com", "geo.tv", "arynews.tv", "thenews.com.pk",
    "tribune.com.pk", "app.com.pk", "radio.gov.pk", "wikipedia.org",
)

KNOWN_RUMOR_SITES = (
    "infowars.com", "naturalnews.com", "worldnewsdailyreport.com",
    "yournewswire.com", "newspunch.com", "beforeitsnews.com",
)

SUSPICIOUS_TLDS = (".info", ".xyz", ".biz", ".su", ".click", ".online", ".today", ".ru", ".tk")

ATTRIBUTION_MARKERS = (
    "according to", "said in a statement", "told reporters", "spokesperson",
    "confirmed that", "reported by", "in a press conference", "official statement",
)

# Text that announces itself as invented. A story can be perfectly well written
# and still be fiction, so this is checked independently of the style score.
SATIRE_MARKERS = (
    "satire", "satirical", "parody", "spoof", "fictional", "fiction",
    "hypothetical", "imaginary", "made-up", "made up story", "not a real story",
    "for entertainment purposes", "humour piece", "humor piece", "the onion",
    # Roman Urdu
    "mazahiya", "mazaqiya", "mazaq", "tanzia", "tanz o mazah", "farzi",
    "man gharat", "mangharat", "afsana", "fictional kahani", "jhooti kahani",
)

# Common Roman Urdu function words. Deliberately excludes tokens that are also
# English words ("is", "us", "the", "par") so English text cannot trip the check.
ROMAN_URDU_MARKERS = (
    "ka", "ke", "ki", "ko", "se", "ne", "aur", "hai", "hain", "tha", "thi", "thay",
    "nahi", "nahin", "kya", "kiya", "gaya", "gayi", "gayin", "diya", "diye", "raha",
    "rahi", "rahe", "mein", "mutabiq", "unhon", "unhein", "apne", "apni", "kaha",
    "bataya", "hua", "hui", "hue", "liye", "sath", "saath", "bhi", "jab", "phir",
    "pehle", "baad", "zyada", "wala", "wali", "har", "kuch", "yeh", "woh", "achanak",
    "bana", "banaya", "karwaya", "dono", "taur", "waqt", "logon", "khabar",
)

_URDU_SCRIPT_RE = re.compile(r"[\u0600-\u06ff]")
_WORD_RE = re.compile(r"[a-z]+")

_URL_RE = re.compile(r"\bhttps?://\S+", re.IGNORECASE)
_DOMAIN_RE = re.compile(
    r"\b((?:[a-z0-9-]+\.)+(?:com|org|net|info|news|xyz|biz|gov|edu|co|uk|pk|tv|io|me))\b",
    re.IGNORECASE,
)


def _find_terms(text_lower: str, vocabulary: tuple[str, ...]) -> list[str]:
    found = []
    for term in vocabulary:
        pattern = r"\b" + re.escape(term).replace(r"\ ", r"\s+") + r"\b"
        if re.search(pattern, text_lower):
            found.append(term)
    return found


def _extract_domains(text: str) -> list[str]:
    domains = []
    for url in _URL_RE.findall(text):
        match = _DOMAIN_RE.search(url)
        if match:
            domains.append(match.group(1).lower())
    domains.extend(m.lower() for m in _DOMAIN_RE.findall(text))
    return sorted(set(domains))


def _signal(signal_id: str, label: str, tone: str, detail: str) -> dict[str, str]:
    return {"id": signal_id, "label": label, "tone": tone, "detail": detail}


def detect_language(text: str) -> str:
    """Classify input as "urdu", "roman_urdu" or "english".

    The classifier was trained on an English news corpus, so anything else is
    outside what it can actually read. Detection uses distinct function words
    rather than a ratio, because a Roman Urdu sentence can still contain many
    English nouns ("cricket superstar", "cabinet meeting").
    """
    if not text:
        return "english"
    if len(_URDU_SCRIPT_RE.findall(text)) >= 5:
        return "urdu"

    tokens = set(_WORD_RE.findall(text.lower()))
    if len(tokens & set(ROMAN_URDU_MARKERS)) >= 4:
        return "roman_urdu"
    return "english"


def analyse(text: str) -> dict[str, Any]:
    """Return pattern signals plus style and source scores (both 0-100)."""
    text = text or ""
    lowered = text.lower()
    words = [w for w in re.split(r"\s+", text) if w]
    signals: list[dict[str, str]] = []

    style_score = 100.0

    shouty = [w for w in words if len(w) > 3 and w.isupper() and any(c.isalpha() for c in w)]
    caps_ratio = len(shouty) / len(words) if words else 0.0
    if caps_ratio > 0.15:
        style_score -= min(30.0, caps_ratio * 120)
        signals.append(
            _signal(
                "caps",
                "Lots of CAPITAL LETTERS",
                NEGATIVE,
                f"{len(shouty)} of {len(words)} words are in full capitals. "
                "Real news outlets rarely shout in the middle of a story.",
            )
        )

    exclamations = text.count("!")
    if exclamations > 2:
        style_score -= min(25.0, exclamations * 5)
        signals.append(
            _signal(
                "exclamation",
                "Too many exclamation marks",
                NEGATIVE,
                f"Found {exclamations} exclamation marks. Fake and clickbait stories use "
                "them to create urgency.",
            )
        )

    sensational = _find_terms(lowered, SENSATIONAL_WORDS)
    if sensational:
        style_score -= min(40.0, len(sensational) * 12)
        signals.append(
            _signal(
                "sensational",
                "Clickbait wording found",
                NEGATIVE,
                "Words like " + ", ".join(f'"{w}"' for w in sensational[:4])
                + " are common in misleading headlines.",
            )
        )

    emotional = _find_terms(lowered, EMOTIONAL_WORDS)
    if emotional:
        style_score -= min(25.0, len(emotional) * 8)
        signals.append(
            _signal(
                "emotional",
                "Emotionally charged language",
                NEGATIVE,
                "Words like " + ", ".join(f'"{w}"' for w in emotional[:4])
                + " push a reaction instead of reporting facts.",
            )
        )

    question_marks = text.count("?")
    if len(text) < 150 and question_marks > 0:
        style_score -= 10
        signals.append(
            _signal(
                "question_headline",
                "Headline asks a question",
                NEGATIVE,
                "Short headlines phrased as questions often hint at a claim without proving it.",
            )
        )

    satire = _find_terms(lowered, SATIRE_MARKERS)
    if satire:
        style_score -= 35
        signals.append(
            _signal(
                "satire",
                "Text says it is fiction or satire",
                NEGATIVE,
                "The text itself uses " + ", ".join(f'"{w}"' for w in satire[:3])
                + ". Satire and made-up stories are written to entertain, not to report "
                "real events, and they spread as real news once the label is dropped.",
            )
        )

    language = detect_language(text)
    if language != "english":
        label = "Urdu" if language == "urdu" else "Roman Urdu"
        signals.append(
            _signal(
                "language_unsupported",
                f"Written in {label}",
                NEUTRAL,
                f"Our AI model was trained on English news only, so it cannot judge "
                f"{label} writing style. Treat its confidence score as unreliable here — "
                "the fact check below is the part you should rely on.",
            )
        )

    style_score = max(15, round(style_score))

    domains = _extract_domains(text)
    trusted = [d for d in domains if any(t in d or d in t for t in TRUSTED_PUBLISHERS)]
    rumor = [d for d in domains if any(r in d for r in KNOWN_RUMOR_SITES)]
    suspicious = [d for d in domains if d.endswith(SUSPICIOUS_TLDS)]

    if rumor:
        source_score = 15
        signals.append(
            _signal(
                "source_rumor",
                "Known unreliable website",
                NEGATIVE,
                ", ".join(rumor) + " has a track record of publishing false stories.",
            )
        )
    elif trusted:
        source_score = 95
        signals.append(
            _signal(
                "source_trusted",
                "Links to a known news outlet",
                POSITIVE,
                "Mentions " + ", ".join(trusted[:3]) + ", which is an established publisher.",
            )
        )
    elif suspicious:
        source_score = 35
        signals.append(
            _signal(
                "source_suspicious",
                "Unusual website address",
                NEGATIVE,
                ", ".join(suspicious[:3]) + " uses a domain type often chosen by fake news sites.",
            )
        )
    elif domains:
        source_score = 60
        signals.append(
            _signal(
                "source_unknown",
                "Unrecognised website",
                NEUTRAL,
                "We could not match " + ", ".join(domains[:3]) + " to a known publisher.",
            )
        )
    else:
        source_score = 55
        signals.append(
            _signal(
                "source_missing",
                "No source link included",
                NEUTRAL,
                "There is no website link to check. Genuine reports usually say where the "
                "information came from.",
            )
        )

    attribution = _find_terms(lowered, ATTRIBUTION_MARKERS)
    if attribution:
        source_score = min(100, source_score + 10)
        signals.append(
            _signal(
                "attribution",
                "Quotes a named source",
                POSITIVE,
                'Phrases like "' + attribution[0] + '" suggest the claim is attributed to someone.',
            )
        )

    if len(text) < 200:
        signals.append(
            _signal(
                "short_input",
                "Very short text",
                NEUTRAL,
                "Short claims give the AI little to work with. Paste the full article for a "
                "more reliable style check.",
            )
        )

    # Claiming the wording "matches normal news reporting" would be dishonest when
    # the model cannot read the language the text is written in.
    if language == "english" and not any(s["tone"] == NEGATIVE for s in signals):
        signals.append(
            _signal(
                "neutral_tone",
                "Calm, professional wording",
                POSITIVE,
                "The writing style matches normal news reporting. Note that a false claim can "
                "still be written calmly.",
            )
        )

    return {
        "signals": signals,
        "style_score": style_score,
        "source_score": int(source_score),
        "reliability": {
            "language": language,
            "language_supported": language == "english",
            "satire_markers": satire,
        },
        "stats": {
            "word_count": len(words),
            "character_count": len(text),
            "caps_ratio": round(caps_ratio, 3),
            "exclamation_count": exclamations,
            "domains": domains[:5],
        },
    }
