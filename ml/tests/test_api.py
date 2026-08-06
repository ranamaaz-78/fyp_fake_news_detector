"""Tests for FNI ML API."""

from __future__ import annotations

import json
import sys
from pathlib import Path

import pytest

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from api.app import app  # noqa: E402


@pytest.fixture
def client():
    app.config["TESTING"] = True
    with app.test_client() as c:
        yield c


def test_health(client):
    r = client.get("/health")
    assert r.status_code in (200, 503)


def test_predict_validation_empty(client):
    r = client.post("/predict", json={"text": ""})
    assert r.status_code == 422


def test_predict_validation_short(client):
    r = client.post("/predict", json={"text": "too short"})
    assert r.status_code == 422


def test_predict_success_shape(client):
    text = (
        "Government officials announced new policy measures after extensive review "
        "by independent experts and public consultation sessions nationwide."
    )
    r = client.post("/predict", json={"text": text})
    if r.status_code == 503:
        pytest.skip("Model not trained")
    assert r.status_code == 200
    data = r.get_json()
    # Legacy keys the Laravel side already depends on.
    assert data["label"] in ("REAL", "FAKE", "UNCERTAIN")
    assert "confidence" in data
    assert "model" in data
    assert "response_time_ms" in data
    assert "probabilities" in data
    assert data["confidence_level"] in ("HIGH", "MEDIUM", "LOW")


def test_predict_includes_signals_and_fact_check(client):
    text = (
        "Government officials announced new policy measures after extensive review "
        "by independent experts and public consultation sessions nationwide."
    )
    r = client.post("/predict", json={"text": text})
    if r.status_code == 503:
        pytest.skip("Model not trained")
    data = r.get_json()

    assert data["verdict_source"] in ("model", "fact_check")
    assert data["style_label"] in ("REAL", "FAKE", "UNCERTAIN")
    assert isinstance(data["signals"], list) and data["signals"]
    for signal in data["signals"]:
        assert set(signal) == {"id", "label", "tone", "detail"}

    assert set(data["scores"]) == {"style", "source", "model"}
    assert data["fact_check"]["verdict"] in ("CONTRADICTED", "SUPPORTED", "NO_DATA")

    explanation = data["explanation"]
    assert explanation["headline"] and explanation["plain"]
    assert explanation["disclaimer"]
    assert isinstance(explanation["bullets"], list)


def test_predict_fact_layer_overrides_style_model(client):
    """The whole point of the layer: a calmly written false claim must be FAKE."""
    r = client.post("/predict", json={"text": "Babar Azam is Pakistan's Prime Minister."})
    if r.status_code == 503:
        pytest.skip("Model not trained")
    data = r.get_json()

    if data["fact_check"]["verdict"] != "CONTRADICTED":
        pytest.skip("Knowledge base does not cover this claim; run refresh_knowledge_base.py")

    assert data["label"] == "FAKE"
    assert data["verdict_source"] == "fact_check"
    assert data["fact_check"]["evidence"]


def test_predict_survives_fact_layer_failure(client, monkeypatch):
    from src import fact_verification

    def boom(*_args, **_kwargs):
        raise RuntimeError("fact layer exploded")

    monkeypatch.setattr(fact_verification, "verify", boom)

    text = (
        "Government officials announced new policy measures after extensive review "
        "by independent experts and public consultation sessions nationwide."
    )
    r = client.post("/predict", json={"text": text})
    if r.status_code == 503:
        pytest.skip("Model not trained")

    assert r.status_code == 200
    data = r.get_json()
    assert data["label"] in ("REAL", "FAKE", "UNCERTAIN")
    assert data["verdict_source"] == "model"
    assert data["fact_check"]["degraded"] is True


def test_combine_keeps_model_verdict_without_fact_data():
    from api.app import _combine

    prediction = {
        "label": "REAL",
        "confidence": 82.0,
        "confidence_level": "HIGH",
        "model": "logistic_regression",
        "probabilities": {"REAL": 82.0, "FAKE": 18.0},
    }
    analysis = {"signals": [], "style_score": 90, "source_score": 60, "stats": {}}
    fact = {"verdict": "NO_DATA", "confidence": 0.0, "evidence": [], "claims": []}

    result = _combine(prediction, analysis, fact)
    assert result["label"] == "REAL"
    assert result["verdict_source"] == "model"


def test_combine_softens_weak_fake_when_claim_is_supported():
    from api.app import _combine

    prediction = {
        "label": "FAKE",
        "confidence": 61.0,
        "confidence_level": "MEDIUM",
        "model": "logistic_regression",
        "probabilities": {"REAL": 39.0, "FAKE": 61.0},
    }
    analysis = {"signals": [], "style_score": 70, "source_score": 60, "stats": {}}
    fact = {"verdict": "SUPPORTED", "confidence": 90.0, "evidence": [], "claims": []}

    result = _combine(prediction, analysis, fact)
    assert result["label"] == "UNCERTAIN"
    assert result["verdict_source"] == "fact_check"


def test_combine_never_promotes_to_real_on_support():
    """Confirming one claim must not vouch for the whole text."""
    from api.app import _combine

    prediction = {
        "label": "UNCERTAIN",
        "confidence": 52.0,
        "confidence_level": "LOW",
        "model": "logistic_regression",
        "probabilities": {"REAL": 52.0, "FAKE": 48.0},
    }
    analysis = {"signals": [], "style_score": 80, "source_score": 60, "stats": {}}
    fact = {"verdict": "SUPPORTED", "confidence": 95.0, "evidence": [], "claims": []}

    result = _combine(prediction, analysis, fact)
    assert result["label"] == "UNCERTAIN"


def _prediction(label, confidence):
    other = round(100 - confidence, 2)
    return {
        "label": label,
        "confidence": confidence,
        "confidence_level": "HIGH" if confidence >= 75 else "MEDIUM",
        "model": "logistic_regression",
        "probabilities": {"REAL": confidence, "FAKE": other},
    }


def _analysis(**reliability):
    base = {"language": "english", "language_supported": True, "satire_markers": []}
    base.update(reliability)
    return {
        "signals": [],
        "style_score": 90,
        "source_score": 60,
        "stats": {},
        "reliability": base,
    }


NO_FACT = {"verdict": "NO_DATA", "confidence": 0.0, "evidence": [], "claims": []}


def test_combine_downgrades_real_when_language_is_unsupported():
    """The model has no Roman Urdu training data, so a confident REAL is not honest."""
    from api.app import _combine

    result = _combine(
        _prediction("REAL", 82.0),
        _analysis(language="roman_urdu", language_supported=False),
        NO_FACT,
    )
    assert result["label"] == "UNCERTAIN"
    assert result["verdict_source"] == "content_signal"
    assert "Roman Urdu" in result["explanation"]["plain"]


def test_combine_downgrades_real_when_text_declares_itself_fiction():
    from api.app import _combine

    result = _combine(_prediction("REAL", 88.0), _analysis(satire_markers=["fictional"]), NO_FACT)
    assert result["label"] == "UNCERTAIN"
    assert result["verdict_source"] == "content_signal"
    assert result["explanation"]["headline"] == "This is written as fiction, not news"


def test_fact_contradiction_still_wins_over_language_downgrade():
    """A verified contradiction is a stronger answer than "we cannot tell"."""
    from api.app import _combine

    fact = {"verdict": "CONTRADICTED", "confidence": 95.0, "evidence": [], "claims": []}
    result = _combine(
        _prediction("REAL", 68.0),
        _analysis(language="roman_urdu", language_supported=False, satire_markers=["mazahiya"]),
        fact,
    )
    assert result["label"] == "FAKE"
    assert result["verdict_source"] == "fact_check"


def test_english_text_is_unaffected_by_the_downgrade_rules():
    from api.app import _combine

    result = _combine(_prediction("REAL", 82.0), _analysis(), NO_FACT)
    assert result["label"] == "REAL"
    assert result["verdict_source"] == "model"


def test_roman_urdu_satire_end_to_end():
    """The exact text a user reported as wrongly rated credible."""
    payload = {
        "text": (
            "Islamabad: Ek fictional aur mazahiya kahani ke mutabiq, cricket superstar Babar "
            "Azam ko achanak Pakistan ka Wazir-e-Azam bana diya gaya. Cabinet meeting se pehle "
            "unhon ne toss karwaya aur har ministry ko batting order ke mutabiq zimmedariyan "
            "de di gayin"
        )
    }
    if not (ROOT / "models" / "model.pkl").exists():
        pytest.skip("Model not trained")

    with app.test_client() as client:
        data = client.post("/predict", json=payload).get_json()

    assert data["label"] != "REAL"
    assert data["reliability"]["language"] == "roman_urdu"
    assert data["reliability"]["satire_markers"]
    assert data["fact_check"]["verdict"] == "CONTRADICTED"


def test_predictor_unit():
    model_path = ROOT / "models" / "model.pkl"
    if not model_path.exists():
        pytest.skip("Model not trained")
    from src.predictor import FakeNewsPredictor

    p = FakeNewsPredictor()
    result = p.predict(
        "Official report confirms economic growth aligned with forecasts from national statistics bureau."
    )
    assert result["label"] in ("REAL", "FAKE", "UNCERTAIN")
    assert 0 <= result["confidence"] <= 100


def test_predictor_svm_label_direction(tmp_path):
    """Regression guard: LinearSVC decision_function must map to the correct class.

    A previous bug hard-coded the positive score to FAKE, flipping REAL/FAKE.
    """
    import joblib
    from sklearn.pipeline import Pipeline
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.svm import LinearSVC

    from src.predictor import FakeNewsPredictor

    real_texts = [f"official government economy policy report number {i}" for i in range(20)]
    fake_texts = [f"shocking alien hoax conspiracy secret lizard number {i}" for i in range(20)]
    X = real_texts + fake_texts
    y = ["REAL"] * len(real_texts) + ["FAKE"] * len(fake_texts)

    pipeline = Pipeline(
        [
            ("tfidf", TfidfVectorizer()),
            ("clf", LinearSVC()),
        ]
    )
    pipeline.fit(X, y)

    model_path = tmp_path / "model.pkl"
    meta_path = tmp_path / "meta.json"
    joblib.dump(pipeline, model_path)
    meta_path.write_text(
        json.dumps({"model_name": "svm", "uncertain_threshold": 0.5, "labels": ["REAL", "FAKE"]}),
        encoding="utf-8",
    )

    predictor = FakeNewsPredictor(model_path=model_path, meta_path=meta_path)

    real_result = predictor.predict("official government economy policy report annual")
    fake_result = predictor.predict("shocking alien hoax conspiracy secret lizard leaked")

    assert real_result["probabilities"]["REAL"] > real_result["probabilities"]["FAKE"]
    assert fake_result["probabilities"]["FAKE"] > fake_result["probabilities"]["REAL"]


def test_train_status_idle(client):
    r = client.get("/train/status")
    assert r.status_code == 200
    data = r.get_json()
    assert "status" in data
    assert "progress" in data


def test_train_start_requires_dataset(client, monkeypatch):
    monkeypatch.setattr("api.app.acquire_lock", lambda job_id: True)
    monkeypatch.setattr("api.app.release_lock", lambda: None)

    fake = ROOT / "data" / "raw" / "Fake.csv"
    true = ROOT / "data" / "raw" / "True.csv"
    backup_fake = fake.read_bytes() if fake.exists() else None
    backup_true = true.read_bytes() if true.exists() else None

    if fake.exists():
        fake.unlink()
    if true.exists():
        true.unlink()

    try:
        r = client.post("/train/start", json={"job_id": "test-job"})
        assert r.status_code == 422
    finally:
        if backup_fake is not None:
            fake.write_bytes(backup_fake)
        if backup_true is not None:
            true.write_bytes(backup_true)


def test_train_start_conflict_when_locked(client, monkeypatch):
    monkeypatch.setattr("api.app.is_locked", lambda: True)
    r = client.post("/train/start", json={"job_id": "test-job"})
    assert r.status_code == 409
