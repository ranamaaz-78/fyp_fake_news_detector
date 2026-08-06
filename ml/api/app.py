"""Flask REST API for fake news prediction."""

from __future__ import annotations

import json
import os
import subprocess
import sys
import time
import uuid
from pathlib import Path

from flask import Flask, jsonify, request

ROOT = Path(__file__).resolve().parent.parent
CONFIG_PATH = ROOT / "config.json"

sys.path.insert(0, str(ROOT))

from src.training_status import (  # noqa: E402
    acquire_lock,
    is_locked,
    read_status,
    release_lock,
)

with open(CONFIG_PATH, encoding="utf-8") as f:
    CONFIG = json.load(f)

MIN_CHARS = CONFIG["min_input_chars"]
MAX_CHARS = CONFIG["max_input_chars"]
FACT_CHECK_CONFIG = CONFIG.get("fact_check", {})
OVERRIDE_THRESHOLD = float(FACT_CHECK_CONFIG.get("override_threshold", 85))
ALLOWED_HOSTS = os.environ.get("FNI_ALLOWED_HOSTS", "127.0.0.1,localhost").split(",")

DISCLAIMER = (
    "This tool checks writing style and a limited database of known facts. It cannot verify "
    "every real-world claim. Always confirm important news with a trusted source."
)

app = Flask(__name__)

# Register AI image detection blueprint.
from api.image_routes import image_bp  # noqa: E402
app.register_blueprint(image_bp)

_predictor = None
_last_reloaded_job_id: str | None = None
_training_process: subprocess.Popen | None = None


def get_predictor():
    global _predictor
    if _predictor is None:
        from src.predictor import FakeNewsPredictor

        _predictor = FakeNewsPredictor()
    return _predictor


def reload_predictor() -> None:
    global _predictor, _last_reloaded_job_id
    _predictor = None
    status = read_status()
    _last_reloaded_job_id = status.get("job_id")


def _client_allowed() -> bool:
    if os.environ.get("FNI_ALLOW_ALL", "").lower() in ("1", "true", "yes"):
        return True
    remote = request.remote_addr or ""
    forwarded = (request.headers.get("X-Forwarded-For") or "").split(",")[0].strip()
    host = forwarded or remote
    return host in ALLOWED_HOSTS or host.startswith("127.") or host == "::1"


def _maybe_reload_on_complete() -> None:
    global _last_reloaded_job_id
    status = read_status()
    if status.get("status") != "completed":
        return
    job_id = status.get("job_id")
    if job_id and job_id != _last_reloaded_job_id:
        reload_predictor()


def _confidence_level(confidence: float) -> str:
    if confidence >= 75:
        return "HIGH"
    if confidence >= 60:
        return "MEDIUM"
    return "LOW"


def _build_explanation(label, verdict_source, style_label, fact, analysis):
    """Plain-language write-up of how the verdict was reached."""
    bullets = []

    for item in fact.get("evidence", [])[:3]:
        bullets.append(item["statement"])

    for signal in analysis["signals"]:
        if signal["tone"] != "neutral":
            bullets.append(f"{signal['label']} — {signal['detail']}")
    for signal in analysis["signals"]:
        if signal["tone"] == "neutral":
            bullets.append(f"{signal['label']} — {signal['detail']}")

    if verdict_source == "fact_check" and label == "FAKE":
        headline = "This contradicts our records"
        plain = (
            "We checked the claim against known facts and found it does not match. "
            "This is a stronger signal than the writing style, so it decides the result."
        )
        if style_label == "REAL":
            plain += (
                " The text is written calmly, which is why the style check alone rated it as "
                "real — a false statement can still be well written."
            )
    elif verdict_source == "fact_check" and label == "UNCERTAIN":
        headline = "We could not decide"
        plain = (
            "The writing style looked suspicious, but the specific claim we could check matched "
            "our records. That disagreement means the result is not reliable either way."
        )
    elif verdict_source == "content_signal":
        reliability = analysis.get("reliability", {})
        if reliability.get("satire_markers"):
            headline = "This is written as fiction, not news"
            plain = (
                "The text describes itself as a made-up or humorous story. Satire is not "
                "reporting, and it causes real confusion once it is forwarded without that "
                "label, so we do not mark it as genuine news."
            )
        else:
            language = "Urdu" if reliability.get("language") == "urdu" else "Roman Urdu"
            headline = "We cannot reliably check this text"
            plain = (
                f"This is written in {language}, and our AI model was trained only on English "
                "news, so its style reading means very little here. Rather than give you a "
                "confident answer we cannot back up, we are marking this as unverified."
            )
    elif label == "FAKE":
        headline = "This looks like fake news"
        plain = (
            "The wording matches patterns we usually see in false or misleading stories. "
            "We could not check the facts directly, so this is based on how it is written."
        )
    elif label == "REAL":
        headline = "This reads like genuine reporting"
        plain = (
            "The wording matches the calm, factual style of established news outlets. "
            "We did not find anything in our fact database that contradicts it."
        )
    else:
        headline = "We are not sure about this one"
        plain = (
            "The text does not clearly match either genuine reporting or fake news, and we "
            "could not check the facts directly. Treat it as unverified."
        )

    if fact.get("degraded"):
        plain += " Some online fact sources were unreachable, so only offline checks ran."

    return {
        "headline": headline,
        "plain": plain,
        "bullets": bullets[:6],
        "disclaimer": DISCLAIMER,
    }


def _combine(prediction, analysis, fact):
    """Merge the style model with the fact layer.

    A verified contradiction overrides the model: no amount of polished writing
    makes a false statement true. The reverse is deliberately not symmetrical —
    confirming one claim says nothing about the rest of the text, so a support
    can only soften a weak FAKE, never promote anything to REAL.
    """
    style_label = prediction["label"]
    style_confidence = prediction["confidence"]

    label = style_label
    confidence = style_confidence
    verdict_source = "model"

    verdict = fact.get("verdict")
    if verdict == "CONTRADICTED" and fact.get("confidence", 0) >= OVERRIDE_THRESHOLD:
        label = "FAKE"
        confidence = fact["confidence"]
        verdict_source = "fact_check"
    elif verdict == "SUPPORTED" and style_label == "FAKE" and style_confidence < 75:
        label = "UNCERTAIN"
        confidence = style_confidence
        verdict_source = "fact_check"

    # With no fact-layer ruling, the model's own verdict is only worth reporting
    # when the model could actually read the text. Roman Urdu and Urdu are absent
    # from its training corpus, and a story that declares itself fiction is not
    # reporting whatever its prose looks like. In both cases a confident REAL is
    # false reassurance, so the honest answer is UNCERTAIN.
    reliability = analysis.get("reliability", {})
    if verdict_source == "model":
        if reliability.get("satire_markers"):
            if label != "FAKE":
                label = "UNCERTAIN"
                confidence = min(confidence, 55.0)
            verdict_source = "content_signal"
        elif not reliability.get("language_supported", True):
            label = "UNCERTAIN"
            confidence = min(confidence, 55.0)
            verdict_source = "content_signal"

    result = dict(prediction)
    result["label"] = label
    result["confidence"] = round(confidence, 2)
    result["confidence_level"] = "LOW" if label == "UNCERTAIN" else _confidence_level(confidence)
    result["style_label"] = style_label
    result["style_confidence"] = style_confidence
    result["verdict_source"] = verdict_source
    result["fact_check"] = fact
    result["signals"] = analysis["signals"]
    result["scores"] = {
        "style": analysis["style_score"],
        "source": analysis["source_score"],
        "model": round(style_confidence),
    }
    result["stats"] = analysis["stats"]
    result["reliability"] = reliability
    result["explanation"] = _build_explanation(label, verdict_source, style_label, fact, analysis)
    return result


@app.route("/health", methods=["GET"])
def health():
    try:
        get_predictor()
        model_loaded = True
    except Exception as exc:
        return jsonify({"status": "unhealthy", "error": str(exc)}), 503
    return jsonify({"status": "ok", "model_loaded": model_loaded})


@app.route("/predict", methods=["POST"])
def predict():
    if not _client_allowed():
        return jsonify({"error": "Forbidden", "message": "Internal API only."}), 403

    start = time.perf_counter()
    data = request.get_json(silent=True) or {}
    text = (data.get("text") or "").strip()

    if not text:
        return jsonify({"error": "Validation failed", "message": "Text is required."}), 422
    if len(text) < MIN_CHARS:
        return (
            jsonify(
                {
                    "error": "Validation failed",
                    "message": f"Text must be at least {MIN_CHARS} characters.",
                }
            ),
            422,
        )
    if len(text) > MAX_CHARS:
        return (
            jsonify(
                {
                    "error": "Validation failed",
                    "message": f"Text must not exceed {MAX_CHARS} characters.",
                }
            ),
            422,
        )

    try:
        prediction = get_predictor().predict(text)
    except FileNotFoundError:
        return (
            jsonify(
                {
                    "error": "Service unavailable",
                    "message": "Model not trained. Run training from the admin panel first.",
                }
            ),
            503,
        )
    except Exception as exc:
        return jsonify({"error": "Prediction failed", "message": str(exc)}), 500

    from src import explain, fact_verification

    analysis = explain.analyse(text)
    try:
        fact = fact_verification.verify(text, FACT_CHECK_CONFIG)
    except Exception:
        # The fact layer is an enhancement; a failure here must never cost the
        # user their prediction.
        fact = {
            "checked": False,
            "verdict": "NO_DATA",
            "confidence": 0.0,
            "claims": [],
            "evidence": [],
            "sources_checked": [],
            "degraded": True,
        }

    result = _combine(prediction, analysis, fact)
    elapsed_ms = round((time.perf_counter() - start) * 1000, 2)
    result["input_length"] = len(text)
    result["response_time_ms"] = elapsed_ms
    return jsonify(result)


@app.route("/train/start", methods=["POST"])
def train_start():
    global _training_process

    if not _client_allowed():
        return jsonify({"error": "Forbidden", "message": "Internal API only."}), 403

    if is_locked():
        return jsonify({"error": "Conflict", "message": "A training job is already running."}), 409

    data = request.get_json(silent=True) or {}
    job_id = (data.get("job_id") or str(uuid.uuid4())).strip()

    fake_path = ROOT / "data" / "raw" / "Fake.csv"
    true_path = ROOT / "data" / "raw" / "True.csv"
    if not fake_path.exists() or not true_path.exists():
        return jsonify(
            {
                "error": "Validation failed",
                "message": "Fake.csv and True.csv must exist in ml/data/raw/.",
            }
        ), 422

    if not acquire_lock(job_id):
        return jsonify({"error": "Conflict", "message": "A training job is already running."}), 409

    script = ROOT / "scripts" / "run_training_job.py"
    try:
        _training_process = subprocess.Popen(
            [sys.executable, str(script), "--job-id", job_id],
            cwd=str(ROOT),
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL,
        )
    except Exception as exc:
        release_lock()
        return jsonify({"error": "Training failed", "message": str(exc)}), 500

    return jsonify({"job_id": job_id, "status": "running"}), 202


@app.route("/train/status", methods=["GET"])
def train_status():
    if not _client_allowed():
        return jsonify({"error": "Forbidden", "message": "Internal API only."}), 403

    _maybe_reload_on_complete()
    return jsonify(read_status())


@app.route("/train/reload", methods=["POST"])
def train_reload():
    if not _client_allowed():
        return jsonify({"error": "Forbidden", "message": "Internal API only."}), 403

    try:
        reload_predictor()
        get_predictor()
        return jsonify({"status": "ok", "model_loaded": True})
    except Exception as exc:
        return jsonify({"status": "error", "message": str(exc)}), 503


@app.errorhandler(404)
def not_found(_):
    return jsonify({"error": "Not found"}), 404


@app.errorhandler(405)
def method_not_allowed(_):
    return jsonify({"error": "Method not allowed"}), 405


if __name__ == "__main__":
    port = int(os.environ.get("FNI_ML_PORT", 5000))
    app.run(host="127.0.0.1", port=port, debug=os.environ.get("FLASK_DEBUG") == "1")
