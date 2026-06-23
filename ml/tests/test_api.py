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
    assert data["label"] in ("REAL", "FAKE", "UNCERTAIN")
    assert "confidence" in data
    assert "model" in data
    assert "response_time_ms" in data


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
