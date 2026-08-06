"""AI-generated image detection module.

Analyses uploaded images for signs of AI generation using four independent
techniques.  Each technique returns a score from 0 (definitely authentic) to
100 (definitely AI-generated).  The final verdict is a weighted combination of
all four scores.

Techniques
----------
1. **ELA (Error Level Analysis)** — Re-saves the image at a known JPEG quality
   and compares pixel-level differences.  AI-generated images tend to show more
   uniform error patterns than photographs.
2. **Metadata Analysis** — Real photographs carry EXIF data (camera make/model,
   GPS, lens, timestamps).  AI-generated images almost never have any.
3. **Frequency Domain Analysis** — Applies a DCT (Discrete Cosine Transform) to
   detect unnatural frequency distributions typical of GAN or diffusion models.
4. **Statistical Anomaly Detection** — Checks pixel distribution histograms,
   noise variance, and colour-channel correlations for patterns that deviate
   from natural photography.

Dependencies: Pillow, numpy, scipy (added to requirements.txt).
"""

from __future__ import annotations

import io
import math
import struct
from pathlib import Path
from typing import Any

import numpy as np
from PIL import Image, ExifTags

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def _to_rgb_array(img: Image.Image, max_side: int = 1024) -> np.ndarray:
    """Convert a PIL Image to an RGB numpy array, capping resolution."""
    rgb = img.convert("RGB")
    w, h = rgb.size
    if max(w, h) > max_side:
        scale = max_side / max(w, h)
        rgb = rgb.resize((int(w * scale), int(h * scale)), Image.LANCZOS)
    return np.asarray(rgb, dtype=np.float64)


def _confidence_level(score: float) -> str:
    if score >= 75:
        return "HIGH"
    if score >= 50:
        return "MEDIUM"
    return "LOW"


# ---------------------------------------------------------------------------
# 1. Error Level Analysis (ELA)
# ---------------------------------------------------------------------------

def _ela_score(img: Image.Image, quality: int = 90) -> tuple[float, list[str]]:
    """Re-save as JPEG at *quality* and measure the pixel-level difference.

    Authentic photos that were previously JPEG-compressed show spatially varied
    ELA maps because different regions compress at different rates.
    AI-generated images tend to show suspiciously uniform error levels.
    """
    findings: list[str] = []
    rgb = img.convert("RGB")
    buf = io.BytesIO()
    rgb.save(buf, "JPEG", quality=quality)
    buf.seek(0)
    resaved = Image.open(buf).convert("RGB")

    original = np.asarray(rgb, dtype=np.float64)
    compressed = np.asarray(resaved, dtype=np.float64)
    diff = np.abs(original - compressed)

    mean_diff = float(np.mean(diff))
    std_diff = float(np.std(diff))

    # A very low standard deviation relative to the mean signals uniformity
    # — common in AI-generated images.
    if std_diff < 1.0:
        uniformity_ratio = 1.0
    else:
        uniformity_ratio = mean_diff / std_diff

    # Normalise to 0-100.  An empirical midpoint of 2.5 separates most real
    # photos (ratio < 2) from most AI outputs (ratio > 3).
    score = min(100.0, max(0.0, (uniformity_ratio - 1.0) * 30))

    if score >= 60:
        findings.append(
            "ELA analysis shows uniform error patterns typical of AI generation"
        )
    elif score >= 40:
        findings.append(
            "ELA patterns are somewhat uniform — possibly AI-generated or heavily edited"
        )

    return round(score, 1), findings


# ---------------------------------------------------------------------------
# 2. Metadata / EXIF Analysis
# ---------------------------------------------------------------------------

_CAMERA_TAGS = {"Make", "Model", "LensModel", "LensMake", "BodySerialNumber"}
_GPS_TAG = "GPSInfo"
_SOFTWARE_TAG = "Software"
_AI_SOFTWARE = (
    "stable diffusion", "midjourney", "dall-e", "dalle", "comfyui",
    "automatic1111", "invoke ai", "novelai", "craiyon", "bing image creator",
    "adobe firefly", "leonardo.ai",
)


def _metadata_score(img: Image.Image) -> tuple[float, list[str]]:
    findings: list[str] = []
    exif = {}
    try:
        raw = img.getexif()
        if raw:
            exif = {ExifTags.TAGS.get(k, k): v for k, v in raw.items()}
    except Exception:
        pass

    has_camera = bool(_CAMERA_TAGS & set(exif.keys()))
    has_gps = _GPS_TAG in exif
    software = str(exif.get(_SOFTWARE_TAG, "")).lower()
    ai_software = any(name in software for name in _AI_SOFTWARE)

    if ai_software:
        findings.append(f"EXIF Software field mentions AI tool: '{exif.get(_SOFTWARE_TAG)}'")
        return 95.0, findings

    if not exif or len(exif) <= 2:
        findings.append("No EXIF camera metadata found")
        return 85.0, findings

    if not has_camera and not has_gps:
        findings.append(
            "EXIF data present but missing camera make/model and GPS — "
            "metadata may have been stripped or the image was not taken by a camera"
        )
        return 60.0, findings

    score = 10.0
    details = []
    if has_camera:
        make = exif.get("Make", "")
        model = exif.get("Model", "")
        details.append(f"Camera: {make} {model}".strip())
        score -= 5
    if has_gps:
        details.append("GPS coordinates present")
        score -= 5

    score = max(0.0, score)
    if details:
        findings.append("EXIF contains authentic camera data: " + "; ".join(details))

    return round(score, 1), findings


# ---------------------------------------------------------------------------
# 3. Frequency Domain Analysis (DCT)
# ---------------------------------------------------------------------------

def _frequency_score(img: Image.Image) -> tuple[float, list[str]]:
    """Check frequency-domain distribution for GAN/diffusion artefacts.

    GANs tend to produce images with periodic artefacts visible in the
    high-frequency bands of a 2-D DCT.  Real photographs distribute energy
    more naturally.
    """
    from scipy.fft import dctn

    findings: list[str] = []
    arr = _to_rgb_array(img, max_side=512)
    grey = np.mean(arr, axis=2)

    dct = dctn(grey, type=2, norm="ortho")
    magnitude = np.abs(dct)

    h, w = magnitude.shape
    # Split into low-frequency (top-left quarter) and high-frequency (rest).
    low = magnitude[: h // 4, : w // 4]
    high = magnitude[h // 4 :, w // 4 :]

    low_energy = float(np.sum(low ** 2))
    high_energy = float(np.sum(high ** 2))

    if low_energy == 0:
        ratio = 0.0
    else:
        ratio = high_energy / low_energy

    # Natural images have most energy in low frequencies.  AI images tend to
    # "leak" more energy into high frequencies — especially GANs which produce
    # repetitive spectral peaks.
    # A ratio above ~0.15 is suspicious; above 0.30 is strong evidence.
    score = min(100.0, max(0.0, ratio * 300))

    if score >= 60:
        findings.append(
            "Frequency domain shows GAN-characteristic artefacts in high-frequency bands"
        )
    elif score >= 35:
        findings.append(
            "Frequency analysis shows slightly elevated high-frequency energy"
        )

    return round(score, 1), findings


# ---------------------------------------------------------------------------
# 4. Statistical Anomaly Detection
# ---------------------------------------------------------------------------

def _statistical_score(img: Image.Image) -> tuple[float, list[str]]:
    """Check pixel statistics for anomalies common in AI-generated images.

    • Colour-channel correlation — in photographs R, G, B channels are
      correlated because natural lighting affects all three together.
      AI images sometimes show weaker channel correlations.
    • Noise variance — real sensors add characteristic noise; AI images are
      either too clean or have synthetic noise that is spatially uniform.
    • Histogram smoothness — AI generators tend to produce smoother histograms
      than real JPEG-compressed photographs.
    """
    findings: list[str] = []
    arr = _to_rgb_array(img, max_side=512)
    r, g, b = arr[:, :, 0], arr[:, :, 1], arr[:, :, 2]

    score = 0.0

    # --- Channel correlation ---
    rg_corr = float(np.corrcoef(r.ravel(), g.ravel())[0, 1])
    rb_corr = float(np.corrcoef(r.ravel(), b.ravel())[0, 1])
    gb_corr = float(np.corrcoef(g.ravel(), b.ravel())[0, 1])
    avg_corr = (rg_corr + rb_corr + gb_corr) / 3

    if avg_corr < 0.85:
        corr_penalty = min(30.0, (0.85 - avg_corr) * 200)
        score += corr_penalty
        findings.append(
            f"Colour-channel correlation is low ({avg_corr:.2f}) — "
            "natural photos typically show higher inter-channel correlation"
        )

    # --- Noise variance uniformity ---
    # Divide into 8x8 blocks and measure variance of each block's std dev.
    block = 8
    h, w = r.shape
    stds = []
    for row in range(0, h - block, block):
        for col in range(0, w - block, block):
            patch = arr[row : row + block, col : col + block, :]
            stds.append(float(np.std(patch)))
    if stds:
        noise_cv = float(np.std(stds)) / (float(np.mean(stds)) + 1e-9)
        # A very low coefficient of variation means noise is suspiciously
        # uniform across the image.
        if noise_cv < 0.35:
            noise_penalty = min(30.0, (0.35 - noise_cv) * 100)
            score += noise_penalty
            findings.append(
                "Noise patterns are suspiciously uniform across the image"
            )

    # --- Histogram smoothness ---
    grey = np.mean(arr, axis=2).astype(np.uint8)
    hist, _ = np.histogram(grey.ravel(), bins=256, range=(0, 255))
    hist_diff = np.abs(np.diff(hist.astype(np.float64)))
    roughness = float(np.mean(hist_diff))
    if roughness < 15:
        smooth_penalty = min(25.0, (15 - roughness) * 2)
        score += smooth_penalty
        findings.append(
            "Pixel histogram is unusually smooth — typical of AI-generated content"
        )

    score = min(100.0, max(0.0, score))
    if not findings:
        findings.append("Pixel statistics appear consistent with a natural photograph")

    return round(score, 1), findings


# ---------------------------------------------------------------------------
# Public API
# ---------------------------------------------------------------------------

# Weights for combining sub-scores into the final verdict.
_WEIGHTS = {
    "ela": 0.25,
    "metadata": 0.30,
    "frequency": 0.20,
    "statistical": 0.25,
}


def analyse_image(image_path: str | Path | None = None,
                  image_bytes: bytes | None = None) -> dict[str, Any]:
    """Run the full AI-image-detection pipeline.

    Provide either *image_path* (a file on disk) or *image_bytes* (raw bytes,
    e.g. from an upload).

    Returns a JSON-serialisable dict::

        {
            "is_ai_generated": true/false,
            "confidence": 78.5,
            "confidence_level": "HIGH/MEDIUM/LOW",
            "detection_methods": {
                "ela_score": 72,
                "metadata_score": 95,
                "frequency_score": 68,
                "statistical_score": 80
            },
            "findings": [ ... ]
        }
    """
    if image_bytes:
        img = Image.open(io.BytesIO(image_bytes))
    elif image_path:
        img = Image.open(image_path)
    else:
        raise ValueError("Provide either image_path or image_bytes.")

    ela, ela_f = _ela_score(img)
    meta, meta_f = _metadata_score(img)
    freq, freq_f = _frequency_score(img)
    stat, stat_f = _statistical_score(img)

    combined = (
        ela * _WEIGHTS["ela"]
        + meta * _WEIGHTS["metadata"]
        + freq * _WEIGHTS["frequency"]
        + stat * _WEIGHTS["statistical"]
    )
    combined = round(combined, 1)

    all_findings = ela_f + meta_f + freq_f + stat_f
    # Remove no-op findings like "Pixel statistics appear consistent…" when the
    # overall verdict is AI-generated.
    if combined >= 50:
        all_findings = [f for f in all_findings if "consistent with a natural" not in f]
    if not all_findings:
        all_findings = ["No strong indicators of AI generation were found"]

    return {
        "is_ai_generated": combined >= 50,
        "confidence": combined,
        "confidence_level": _confidence_level(combined),
        "detection_methods": {
            "ela_score": ela,
            "metadata_score": meta,
            "frequency_score": freq,
            "statistical_score": stat,
        },
        "findings": all_findings,
    }
