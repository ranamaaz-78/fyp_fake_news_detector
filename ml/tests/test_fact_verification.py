"""Tests for the fact-verification layer."""

from __future__ import annotations

import json
import sys
from pathlib import Path

import pytest

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from src.fact_verification import (  # noqa: E402
    CONTRADICTED,
    NO_DATA,
    SUPPORTED,
    FactCheckClient,
    FactVerifier,
    FileCache,
    LocalKnowledgeBase,
    canonical_role,
    extract_claims,
    names_match,
    normalize_name,
)


# --- claim extraction -------------------------------------------------------


@pytest.mark.parametrize(
    "text,subject,role,place",
    [
        ("Babar Azam is Pakistan's Prime Minister", "Babar Azam", "prime minister", "Pakistan"),
        (
            "Babar Azam is the Prime Minister of Pakistan",
            "Babar Azam",
            "prime minister",
            "Pakistan",
        ),
        (
            "The Prime Minister of Pakistan is Babar Azam",
            "Babar Azam",
            "prime minister",
            "Pakistan",
        ),
        (
            "Donald Trump is the President of the United States",
            "Donald Trump",
            "president",
            "United States",
        ),
        (
            "Narendra Modi, the Prime Minister of India, met officials.",
            "Narendra Modi",
            "prime minister",
            "India",
        ),
    ],
)
def test_extract_claims_shapes(text, subject, role, place):
    claims = extract_claims(text)
    assert claims, f"no claim extracted from {text!r}"
    assert claims[0]["subject"] == subject
    assert claims[0]["role"] == role
    assert claims[0]["place"] == place


def test_extract_claims_stops_at_sentence_content():
    """Regression: a case-insensitive pattern swallowed the rest of the sentence."""
    claims = extract_claims("Pakistan's Prime Minister Shehbaz Sharif addressed the nation today.")
    assert claims
    assert claims[0]["subject"] == "Shehbaz Sharif"
    assert claims[0]["place"] == "Pakistan"


@pytest.mark.parametrize(
    "text,subject,role,place",
    [
        # The phrasing the user actually reported: Urdu word order, hyphenated role.
        (
            "cricket superstar Babar Azam ko achanak Pakistan ka Wazir-e-Azam bana diya gaya",
            "Babar Azam",
            "prime minister",
            "Pakistan",
        ),
        (
            "Babar Azam ko Pakistan ka Wazir e Azam bana diya gaya hai",
            "Babar Azam",
            "prime minister",
            "Pakistan",
        ),
        (
            "Babar Azam Pakistan ke naye Wazir-e-Azam ban gaye hain",
            "Babar Azam",
            "prime minister",
            "Pakistan",
        ),
        (
            "Pakistan ke Wazir-e-Azam Babar Azam ne qaum se khitab kiya",
            "Babar Azam",
            "prime minister",
            "Pakistan",
        ),
        (
            "Babar Azam ko Punjab ka Wazir-e-Ala muqarrar kar diya gaya",
            "Babar Azam",
            "chief minister",
            "Punjab",
        ),
    ],
)
def test_extract_claims_roman_urdu(text, subject, role, place):
    """Urdu puts the verb last, so English patterns never fire on these."""
    claims = extract_claims(text)
    assert claims, f"no claim extracted from {text!r}"
    assert claims[0]["subject"] == subject
    assert claims[0]["role"] == role
    assert claims[0]["place"] == place


@pytest.mark.parametrize(
    "surface", ["Wazir-e-Azam", "wazir e azam", "WAZIR-E-AZAM", "Wazir  e  Azam"]
)
def test_canonical_role_normalises_hyphens_and_spacing(surface):
    assert canonical_role(surface) == "prime minister"


def test_extract_claims_ignores_plain_text():
    text = "Officials met to discuss the economy and inflation targets for the coming year."
    assert extract_claims(text) == []


def test_extract_claims_deduplicates():
    text = (
        "Babar Azam is the Prime Minister of Pakistan. "
        "Indeed, Babar Azam is the Prime Minister of Pakistan."
    )
    assert len(extract_claims(text)) == 1


# --- name comparison --------------------------------------------------------


def test_normalize_name_drops_articles_and_punctuation():
    assert normalize_name("the United States") == "united states"
    assert normalize_name("Charles III.") == "charles iii"


def test_names_match_tolerates_transliteration():
    assert names_match("Shehbaz Sharif", "Shahbaz Sharif")
    assert names_match("the United States", "United States")


def test_names_match_rejects_different_people():
    assert not names_match("Babar Azam", "Shehbaz Sharif")
    assert not names_match("Narendra Modi", "Donald Trump")


# --- local knowledge base ---------------------------------------------------


@pytest.fixture
def kb(tmp_path):
    payload = {
        "updated_at": "2026-01-01",
        "office_holders": [
            {
                "role": "prime minister",
                "place": "Pakistan",
                "place_aliases": [],
                "holder": "Shehbaz Sharif",
                "holder_aliases": ["Shahbaz Sharif"],
                "source": "https://www.wikidata.org/wiki/Q843",
            }
        ],
        "person_roles": [
            {
                "person": "Babar Azam",
                "aliases": [],
                "known_roles": ["cricketer"],
                "not_roles": ["prime minister", "president"],
                "source": "https://www.wikidata.org/wiki/Q23767766",
            }
        ],
    }
    path = tmp_path / "kb.json"
    path.write_text(json.dumps(payload), encoding="utf-8")
    return LocalKnowledgeBase(path)


def test_kb_contradicts_wrong_office_holder(kb):
    claim = extract_claims("Babar Azam is Pakistan's Prime Minister")[0]
    evidence = kb.check(claim)
    assert evidence["verdict"] == CONTRADICTED
    assert evidence["source"] == "local_kb"
    assert evidence["url"]


def test_kb_supports_correct_office_holder(kb):
    claim = extract_claims("Shehbaz Sharif is the Prime Minister of Pakistan")[0]
    evidence = kb.check(claim)
    assert evidence["verdict"] == SUPPORTED


def test_kb_accepts_holder_alias(kb):
    claim = extract_claims("Shahbaz Sharif is the Prime Minister of Pakistan")[0]
    assert kb.check(claim)["verdict"] == SUPPORTED


def test_kb_returns_none_for_uncovered_claim(kb):
    """An uncovered pair must yield no evidence rather than a guess."""
    claim = extract_claims("Jane Doe is the Prime Minister of Atlantis")[0]
    assert kb.check(claim) is None


def test_kb_missing_file_is_not_fatal(tmp_path):
    empty = LocalKnowledgeBase(tmp_path / "does-not-exist.json")
    assert empty.office_holders == []


# --- verifier orchestration -------------------------------------------------


def _verifier(tmp_path, kb_path, sources=("local_kb",)):
    return FactVerifier(
        {
            "enabled": True,
            "knowledge_base_path": kb_path,
            "cache_path": tmp_path / "cache.json",
            "sources": list(sources),
        }
    )


def test_verify_flags_the_babar_azam_case_offline(tmp_path, kb):
    verifier = _verifier(tmp_path, kb.path)
    result = verifier.verify("Babar Azam is Pakistan's Prime Minister and will address the nation.")

    assert result["verdict"] == CONTRADICTED
    assert result["confidence"] >= 85
    assert result["evidence"]
    assert result["sources_checked"] == ["local_kb"]
    assert result["degraded"] is False


def test_verify_returns_no_data_without_claims(tmp_path, kb):
    verifier = _verifier(tmp_path, kb.path)
    result = verifier.verify("The central bank held interest rates steady this quarter.")

    assert result["checked"] is True
    assert result["verdict"] == NO_DATA
    assert result["claims"] == []


def test_verify_degrades_when_network_source_fails(tmp_path, kb, monkeypatch):
    verifier = _verifier(tmp_path, kb.path, sources=("local_kb", "wikidata"))

    def boom(*_args, **_kwargs):
        raise RuntimeError("network down")

    monkeypatch.setattr(verifier.wikidata, "check", boom)

    result = verifier.verify("Jane Doe is the Prime Minister of Atlantis.")
    assert result["degraded"] is True
    assert result["verdict"] == NO_DATA


def test_verify_disabled_returns_unchecked(tmp_path, kb):
    verifier = FactVerifier(
        {"enabled": False, "knowledge_base_path": kb.path, "cache_path": tmp_path / "c.json"}
    )
    result = verifier.verify("Babar Azam is Pakistan's Prime Minister")
    assert result["checked"] is False
    assert result["verdict"] == NO_DATA


def test_local_kb_result_skips_network_lookup(tmp_path, kb, monkeypatch):
    """A claim the offline KB already answered must not hit Wikidata."""
    verifier = _verifier(tmp_path, kb.path, sources=("local_kb", "wikidata"))
    calls = []
    monkeypatch.setattr(verifier.wikidata, "check", lambda *a, **k: calls.append(1))

    verifier.verify("Babar Azam is Pakistan's Prime Minister")
    assert calls == []


# --- cache ------------------------------------------------------------------


def test_cache_round_trip(tmp_path):
    cache = FileCache(tmp_path / "cache.json", ttl_days=7)
    cache.set("key", {"a": 1})
    assert cache.get("key") == {"a": 1}

    reopened = FileCache(tmp_path / "cache.json", ttl_days=7)
    assert reopened.get("key") == {"a": 1}


def test_cache_expires(tmp_path):
    cache = FileCache(tmp_path / "cache.json", ttl_days=0)
    cache.set("key", "value")
    assert cache.get("key") is None


def test_cache_survives_corrupt_file(tmp_path):
    path = tmp_path / "cache.json"
    path.write_text("{not json", encoding="utf-8")
    cache = FileCache(path)
    assert cache.get("anything") is None
    cache.set("key", 1)
    assert cache.get("key") == 1


def test_cache_evicts_beyond_max_entries(tmp_path):
    cache = FileCache(tmp_path / "cache.json", ttl_days=7, max_entries=5)
    for i in range(12):
        cache.set(f"k{i}", i)
    assert len(cache._load()) <= 5


# --- fact check rating mapping ----------------------------------------------


@pytest.mark.parametrize(
    "rating,expected",
    [
        ("False", CONTRADICTED),
        ("Pants on Fire", CONTRADICTED),
        ("Mostly false", CONTRADICTED),
        ("Misleading", CONTRADICTED),
        ("True", SUPPORTED),
        ("Accurate", SUPPORTED),
        ("", NO_DATA),
        ("Needs context", NO_DATA),
    ],
)
def test_rating_mapping(rating, expected):
    assert FactCheckClient._map_rating(rating) == expected


def test_fact_check_disabled_without_api_key(tmp_path):
    client = FactCheckClient(FileCache(tmp_path / "c.json"), api_key="")
    assert client.enabled is False
    assert client.search("anything", deadline=0) == []
