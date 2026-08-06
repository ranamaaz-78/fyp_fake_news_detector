"""Flask blueprint for AI-generated image analysis."""

from __future__ import annotations

import os
import sys
import tempfile
from pathlib import Path

from flask import Blueprint, jsonify, request

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

image_bp = Blueprint("image", __name__)

MAX_FILE_SIZE = 10 * 1024 * 1024  # 10 MB
ALLOWED_EXTENSIONS = {"png", "jpg", "jpeg", "webp", "bmp", "tiff", "gif"}


def _allowed_file(filename: str) -> bool:
    return "." in filename and filename.rsplit(".", 1)[1].lower() in ALLOWED_EXTENSIONS


@image_bp.route("/analyze-image", methods=["POST"])
def analyze_image():
    """Accept an uploaded image and return AI-detection results.

    Expects a multipart/form-data POST with a field named ``image``.
    """
    if "image" not in request.files:
        return jsonify({"error": "Validation failed", "message": "No image file provided."}), 422

    file = request.files["image"]
    if not file or not file.filename:
        return jsonify({"error": "Validation failed", "message": "Empty file."}), 422

    if not _allowed_file(file.filename):
        return (
            jsonify(
                {
                    "error": "Validation failed",
                    "message": f"Unsupported file type. Allowed: {', '.join(sorted(ALLOWED_EXTENSIONS))}",
                }
            ),
            422,
        )

    image_bytes = file.read()
    if len(image_bytes) > MAX_FILE_SIZE:
        return (
            jsonify(
                {
                    "error": "Validation failed",
                    "message": f"File too large. Maximum size is {MAX_FILE_SIZE // (1024 * 1024)} MB.",
                }
            ),
            422,
        )

    try:
        from src.image_detector import analyse_image

        result = analyse_image(image_bytes=image_bytes)
    except Exception as exc:
        return jsonify({"error": "Analysis failed", "message": str(exc)}), 500

    return jsonify(result)
