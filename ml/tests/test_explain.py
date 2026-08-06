"""Tests for the writing-style signal analyser."""

from __future__ import annotations

import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from src import explain  # noqa: E402


def signal_ids(text: str) -> set[str]:
    return {s["id"] for s in explain.analyse(text)["signals"]}


def test_detects_shouting_capitals():
    text = "BREAKING SHOCKING NEWS TODAY EVERYONE MUST READ THIS URGENT REPORT NOW"
    assert "caps" in signal_ids(text)


def test_ignores_normal_capitalisation():
    text = (
        "The State Bank of Pakistan said on Tuesday that inflation had eased for a third "
        "consecutive month, according to figures published by the statistics bureau."
    )
    assert "caps" not in signal_ids(text)


def test_detects_exclamation_spam():
    assert "exclamation" in signal_ids("This is amazing! Truly amazing! Unbelievable! Wow!")


def test_two_exclamations_are_not_flagged():
    assert "exclamation" not in signal_ids("What a result! The team played well today!")


def test_detects_sensational_vocabulary():
    text = "The shocking secret they exposed is a conspiracy the mainstream media hid from you."
    ids = signal_ids(text)
    assert "sensational" in ids


def test_detects_emotional_vocabulary():
    text = (
        "These disgusting traitors spread chaos and panic across the country while officials "
        "watched, and the outrage continues to grow among ordinary citizens everywhere."
    )
    assert "emotional" in signal_ids(text)


def test_recognises_trusted_publisher_link():
    text = "Full report available at https://www.reuters.com/world/example-article published today."
    result = explain.analyse(text)
    assert "source_trusted" in {s["id"] for s in result["signals"]}
    assert result["source_score"] >= 90


def test_flags_known_rumor_site():
    text = "Read the full story at https://infowars.com/story about the alleged incident today."
    result = explain.analyse(text)
    assert "source_rumor" in {s["id"] for s in result["signals"]}
    assert result["source_score"] <= 20


def test_flags_missing_source():
    assert "source_missing" in signal_ids("A senior official confirmed the policy change today.")


def test_detects_attribution_phrase():
    text = "According to the finance ministry, the budget deficit narrowed during the last quarter."
    assert "attribution" in signal_ids(text)


def test_short_input_is_flagged():
    assert "short_input" in signal_ids("Babar Azam is Pakistan's Prime Minister")


def test_clean_text_gets_positive_signal():
    text = (
        "The finance ministry said on Monday that revenue collection had met its quarterly "
        "target, according to a statement issued after the cabinet meeting in Islamabad. "
        "Officials added that the figures would be reviewed by the auditor general before "
        "publication later this year."
    )
    result = explain.analyse(text)
    assert "neutral_tone" in {s["id"] for s in result["signals"]}
    assert result["style_score"] > 85


def test_scores_stay_in_range():
    for text in ["", "SHOCKING!!! SECRET EXPOSED HOAX CONSPIRACY!!!", "A calm factual sentence."]:
        result = explain.analyse(text)
        assert 0 <= result["style_score"] <= 100
        assert 0 <= result["source_score"] <= 100


def test_empty_text_is_handled():
    result = explain.analyse("")
    assert result["signals"]
    assert result["stats"]["word_count"] == 0


ROMAN_URDU = (
    "Islamabad: Ek fictional aur mazahiya kahani ke mutabiq, cricket superstar Babar Azam ko "
    "achanak Pakistan ka Wazir-e-Azam bana diya gaya. Cabinet meeting se pehle unhon ne toss "
    "karwaya aur har ministry ko batting order ke mutabiq zimmedariyan de di gayin"
)


def test_detects_roman_urdu():
    assert explain.detect_language(ROMAN_URDU) == "roman_urdu"
    assert "language_unsupported" in signal_ids(ROMAN_URDU)


def test_detects_urdu_script():
    assert explain.detect_language("\u0628\u0627\u0628\u0631 \u0627\u0639\u0638\u0645 \u067e\u0627\u06a9\u0633\u062a\u0627\u0646") == "urdu"


def test_english_news_is_not_flagged_as_urdu():
    text = (
        "The State Bank of Pakistan said on Tuesday that inflation had eased for a third "
        "consecutive month, according to figures published by the statistics bureau in Karachi."
    )
    assert explain.detect_language(text) == "english"
    assert "language_unsupported" not in signal_ids(text)


def test_detects_declared_fiction():
    assert "satire" in signal_ids(ROMAN_URDU)
    assert explain.analyse(ROMAN_URDU)["reliability"]["satire_markers"]
    assert "satire" in signal_ids("This is a satirical column about talking cats.")


def test_unreadable_language_never_earns_a_praising_signal():
    """Calling Roman Urdu "calm, professional wording" would be a claim we cannot make."""
    assert "neutral_tone" not in signal_ids(ROMAN_URDU)


def test_every_signal_has_a_readable_shape():
    result = explain.analyse("SHOCKING!!! The secret was exposed!!! Read more at example.xyz")
    for signal in result["signals"]:
        assert set(signal) == {"id", "label", "tone", "detail"}
        assert signal["tone"] in ("negative", "positive", "neutral")
        assert signal["label"] and signal["detail"]
