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
ALLOWED_HOSTS = os.environ.get("FNI_ALLOWED_HOSTS", "127.0.0.1,localhost").split(",")

DEFAULT_TESSERACT_CMD = r"C:\Program Files\Tesseract-OCR\tesseract.exe"
TESSERACT_CMD = os.environ.get("FNI_TESSERACT_CMD", DEFAULT_TESSERACT_CMD)
MAX_IMAGE_BYTES = int(os.environ.get("FNI_IMAGE_MAX_BYTES", 8 * 1024 * 1024))

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = MAX_IMAGE_BYTES
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
        result = get_predictor().predict(text)
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

    elapsed_ms = round((time.perf_counter() - start) * 1000, 2)
    result["input_length"] = len(text)
    result["response_time_ms"] = elapsed_ms
    return jsonify(result)


@app.route("/ocr", methods=["POST"])
def ocr():
    if not _client_allowed():
        return jsonify({"error": "Forbidden", "message": "Internal API only."}), 403

    start = time.perf_counter()
    file = request.files.get("image")

    if file is None or not file.filename:
        return jsonify({"error": "Validation failed", "message": "An image file is required."}), 422

    try:
        from PIL import Image, ImageOps, UnidentifiedImageError
        import pytesseract
    except ImportError:
        return (
            jsonify(
                {
                    "error": "Service unavailable",
                    "message": "OCR libraries are not installed. Run: pip install pytesseract Pillow.",
                }
            ),
            503,
        )

    if TESSERACT_CMD:
        pytesseract.pytesseract.tesseract_cmd = TESSERACT_CMD

    try:
        image = Image.open(file.stream)
        image = ImageOps.exif_transpose(image)
    except UnidentifiedImageError:
        return jsonify({"error": "Validation failed", "message": "That file is not a readable image."}), 422
    except Exception as exc:
        return jsonify({"error": "OCR failed", "message": str(exc)}), 500

    try:
        text = pytesseract.image_to_string(image)
    except pytesseract.TesseractNotFoundError:
        return (
            jsonify(
                {
                    "error": "Service unavailable",
                    "message": "Tesseract OCR engine not found. Install it and set FNI_TESSERACT_CMD.",
                }
            ),
            503,
        )
    except Exception as exc:
        return jsonify({"error": "OCR failed", "message": str(exc)}), 500

    text = " ".join(text.split()).strip()

    if len(text) < MIN_CHARS:
        return (
            jsonify(
                {
                    "error": "Validation failed",
                    "message": "Could not read enough text from the image. Try a clearer screenshot.",
                }
            ),
            422,
        )

    if len(text) > MAX_CHARS:
        text = text[:MAX_CHARS]

    elapsed_ms = round((time.perf_counter() - start) * 1000, 2)
    return jsonify({"text": text, "char_count": len(text), "response_time_ms": elapsed_ms})


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
