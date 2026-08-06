"""Regenerate ml/data/knowledge_base.json from Wikidata.

Every fact the fact-verification layer serves offline is sourced here, so the
knowledge base stays auditable: each entry carries the Wikidata item it came
from and the date it was refreshed.

    python scripts/refresh_knowledge_base.py
    python scripts/refresh_knowledge_base.py --dry-run

Entries marked "manual": true in the existing file are preserved untouched.
"""

from __future__ import annotations

import argparse
import json
import sys
import time
from datetime import date
from pathlib import Path

import requests

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

# Holder names carry diacritics that the default Windows console codepage cannot
# encode, which would otherwise abort the refresh partway through.
for _stream in (sys.stdout, sys.stderr):
    try:
        _stream.reconfigure(encoding="utf-8", errors="replace")
    except (AttributeError, ValueError):
        pass

from src.fact_verification import ROLE_WIKIDATA_PROPERTY  # noqa: E402

KB_PATH = ROOT / "data" / "knowledge_base.json"
API = "https://www.wikidata.org/w/api.php"
HEADERS = {"User-Agent": "VeriFactAI/1.0 (FYP knowledge base refresh)"}

# (canonical role, place label) pairs resolved through the place's Wikidata item.
OFFICE_TARGETS: list[tuple[str, str, list[str]]] = [
    ("prime minister", "Pakistan", ["Islamic Republic of Pakistan"]),
    ("president", "Pakistan", ["Islamic Republic of Pakistan"]),
    ("prime minister", "India", ["Republic of India", "Bharat"]),
    ("president", "India", ["Republic of India"]),
    ("president", "United States", ["USA", "US", "United States of America", "America"]),
    ("prime minister", "United Kingdom", ["UK", "Britain", "Great Britain"]),
    ("monarch", "United Kingdom", ["UK", "Britain", "Great Britain"]),
    ("prime minister", "Canada", []),
    ("prime minister", "Australia", []),
    ("prime minister", "Japan", []),
    ("prime minister", "Israel", []),
    ("prime minister", "Bangladesh", []),
    ("president", "Bangladesh", []),
    ("president", "France", []),
    ("president", "Russia", ["Russian Federation"]),
    ("president", "China", ["People's Republic of China"]),
    ("president", "Turkey", ["Turkiye", "Türkiye"]),
    ("president", "Afghanistan", []),
    ("president", "Sri Lanka", []),
    ("president", "Indonesia", []),
    ("president", "Egypt", []),
    ("president", "South Africa", []),
    ("president", "Brazil", []),
    ("monarch", "Saudi Arabia", []),
    ("chief minister", "Punjab, Pakistan", ["Punjab"]),
    ("chief minister", "Sindh", []),
]

# People whose occupations are checked so viral "X is now the PM" claims fail fast.
PERSON_TARGETS: list[str] = [
    "Babar Azam",
    "Shaheen Afridi",
    "Virat Kohli",
    "Cristiano Ronaldo",
    "Lionel Messi",
    "Shah Rukh Khan",
    "Elon Musk",
    "Bill Gates",
    "Malala Yousafzai",
    "Wasim Akram",
    "Shoaib Akhtar",
    "Fawad Khan",
    "Atif Aslam",
]

# Occupations that make a head-of-state / head-of-government claim plausible.
POLITICAL_OCCUPATIONS = {
    "politician",
    "statesperson",
    "diplomat",
    "head of state",
    "head of government",
    "monarch",
    "military officer",
}

# Roles ruled out for anyone with no political occupation on record.
POLITICAL_ROLES = ["prime minister", "president", "monarch", "chief minister"]


# Wikidata throttles anonymous clients aggressively; pace requests rather than
# hammering it and getting the whole refresh rejected partway through.
REQUEST_INTERVAL = 0.5
_last_request_at = 0.0


def api_get(params: dict) -> dict:
    global _last_request_at

    for attempt in range(5):
        wait = REQUEST_INTERVAL - (time.monotonic() - _last_request_at)
        if wait > 0:
            time.sleep(wait)

        try:
            response = requests.get(
                API, params={**params, "format": "json"}, timeout=20, headers=HEADERS
            )
            _last_request_at = time.monotonic()
            if response.status_code == 429:
                retry_after = float(response.headers.get("Retry-After") or 0)
                time.sleep(max(retry_after, 5.0 * (attempt + 1)))
                continue
            response.raise_for_status()
            return response.json()
        except requests.RequestException:
            _last_request_at = time.monotonic()
            if attempt == 4:
                raise
            time.sleep(3.0 * (attempt + 1))
    return {}


def search_entity(name: str) -> str | None:
    data = api_get(
        {"action": "wbsearchentities", "search": name, "language": "en", "type": "item", "limit": 1}
    )
    results = data.get("search") or []
    return results[0]["id"] if results else None


def current_claim_ids(entity_id: str, prop: str) -> list[str]:
    data = api_get({"action": "wbgetclaims", "entity": entity_id, "property": prop})
    statements = (data.get("claims") or {}).get(prop) or []
    preferred = [s for s in statements if s.get("rank") == "preferred"]
    ongoing = [s for s in statements if "P582" not in (s.get("qualifiers") or {})]
    chosen = preferred or ongoing or statements

    ids = []
    for statement in chosen:
        try:
            ids.append(statement["mainsnak"]["datavalue"]["value"]["id"])
        except (KeyError, TypeError):
            continue
    return ids


def entities_details(entity_ids: list[str]) -> dict[str, tuple[str | None, list[str]]]:
    """Batch label/alias lookup — wbgetentities accepts up to 50 ids per call."""
    out: dict[str, tuple[str | None, list[str]]] = {}
    for start in range(0, len(entity_ids), 50):
        batch = entity_ids[start : start + 50]
        data = api_get(
            {
                "action": "wbgetentities",
                "ids": "|".join(batch),
                "props": "labels|aliases",
                "languages": "en",
            }
        )
        for entity_id, entity in (data.get("entities") or {}).items():
            label = entity.get("labels", {}).get("en", {}).get("value")
            aliases = [a["value"] for a in entity.get("aliases", {}).get("en", [])]
            out[entity_id] = (label, aliases)
    return out


def entity_details(entity_id: str) -> tuple[str | None, list[str]]:
    return entities_details([entity_id]).get(entity_id, (None, []))


def occupations(entity_id: str) -> list[str]:
    occupation_ids = current_claim_ids(entity_id, "P106")
    if not occupation_ids:
        return []
    details = entities_details(occupation_ids)
    return [details[i][0] for i in occupation_ids if details.get(i, (None, []))[0]]


def build_office_holders(today: str) -> list[dict]:
    entries = []
    for role, place, place_aliases in OFFICE_TARGETS:
        prop = ROLE_WIKIDATA_PROPERTY.get(role)
        if not prop:
            print(f"  skip  {role} of {place}: no Wikidata property mapped")
            continue

        try:
            place_id = search_entity(place)
            if not place_id:
                print(f"  skip  {role} of {place}: place not found")
                continue

            holder_ids = current_claim_ids(place_id, prop)
            if not holder_ids:
                print(f"  skip  {role} of {place}: no current {prop} statement")
                continue

            holder, aliases = entity_details(holder_ids[0])
        except requests.RequestException as exc:
            print(f"  fail  {role} of {place}: {exc}")
            continue

        if not holder:
            print(f"  skip  {role} of {place}: holder has no English label")
            continue

        entries.append(
            {
                "role": role,
                "place": place,
                "place_aliases": place_aliases,
                "place_wikidata_id": place_id,
                "holder": holder,
                "holder_aliases": aliases[:8],
                "source": f"https://www.wikidata.org/wiki/{place_id}#{prop}",
                "updated_at": today,
            }
        )
        print(f"  ok    {role} of {place}: {holder}")
    return entries


def build_person_roles(today: str) -> list[dict]:
    entries = []
    for name in PERSON_TARGETS:
        try:
            person_id = search_entity(name)
            if not person_id:
                print(f"  skip  {name}: not found")
                continue

            label, aliases = entity_details(person_id)
            jobs = occupations(person_id)
        except requests.RequestException as exc:
            print(f"  fail  {name}: {exc}")
            continue

        if not jobs:
            print(f"  skip  {name}: no occupation on record")
            continue

        is_political = any(job.lower() in POLITICAL_OCCUPATIONS for job in jobs)
        not_roles = [] if is_political else list(POLITICAL_ROLES)

        entries.append(
            {
                "person": label or name,
                "aliases": aliases[:8],
                "wikidata_id": person_id,
                "known_roles": jobs[:6],
                "not_roles": not_roles,
                "source": f"https://www.wikidata.org/wiki/{person_id}",
                "updated_at": today,
            }
        )
        print(f"  ok    {label or name}: {', '.join(jobs[:3])}")
    return entries


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--dry-run", action="store_true", help="print results without writing")
    args = parser.parse_args()

    today = date.today().isoformat()

    preserved_offices: list[dict] = []
    preserved_people: list[dict] = []
    if KB_PATH.exists():
        try:
            with open(KB_PATH, encoding="utf-8") as f:
                existing = json.load(f)
            preserved_offices = [e for e in existing.get("office_holders", []) if e.get("manual")]
            preserved_people = [e for e in existing.get("person_roles", []) if e.get("manual")]
        except (json.JSONDecodeError, OSError):
            pass

    print("Office holders:")
    office_holders = build_office_holders(today)
    print("\nPeople:")
    person_roles = build_person_roles(today)

    payload = {
        "updated_at": today,
        "generated_by": "scripts/refresh_knowledge_base.py",
        "notes": (
            "Facts are sourced from Wikidata. Re-run the script to refresh. "
            "Add hand-curated entries with \"manual\": true to keep them across refreshes."
        ),
        "office_holders": preserved_offices + office_holders,
        "person_roles": preserved_people + person_roles,
    }

    print(
        f"\n{len(payload['office_holders'])} office holders, "
        f"{len(payload['person_roles'])} people"
    )

    if args.dry_run:
        print("Dry run — not written.")
        return 0

    KB_PATH.parent.mkdir(parents=True, exist_ok=True)
    with open(KB_PATH, "w", encoding="utf-8") as f:
        json.dump(payload, f, indent=2, ensure_ascii=False)
    print(f"Wrote {KB_PATH}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
