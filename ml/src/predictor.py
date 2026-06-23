"""Load trained model and produce REAL / FAKE / UNCERTAIN predictions."""

from __future__ import annotations

import json
from pathlib import Path

import joblib
import numpy as np

from src.preprocess import tokenize_and_stem

ROOT = Path(__file__).resolve().parent.parent
DEFAULT_MODEL = ROOT / "models" / "model.pkl"
DEFAULT_META = ROOT / "models" / "model_meta.json"


class FakeNewsPredictor:
    def __init__(self, model_path: Path | None = None, meta_path: Path | None = None):
        self.model_path = model_path or DEFAULT_MODEL
        self.meta_path = meta_path or DEFAULT_META
        self.pipeline = joblib.load(self.model_path)
        with open(self.meta_path, encoding="utf-8") as f:
            self.meta = json.load(f)
        self.threshold = float(self.meta.get("uncertain_threshold", 0.60))

    def predict(self, text: str) -> dict:
        processed = tokenize_and_stem(text)
        if not processed:
            return {
                "label": "UNCERTAIN",
                "confidence": 0.0,
                "confidence_level": "LOW",
                "model": self.meta.get("model_name", "unknown"),
                "probabilities": {"REAL": 0.0, "FAKE": 0.0},
            }

        clf = self.pipeline.named_steps["clf"]
        proba = self._get_probabilities(processed)
        real_p = float(proba.get("REAL", 0.5))
        fake_p = float(proba.get("FAKE", 0.5))
        max_p = max(real_p, fake_p)

        if max_p < self.threshold:
            label = "UNCERTAIN"
            confidence = max_p * 100
        elif real_p >= fake_p:
            label = "REAL"
            confidence = real_p * 100
        else:
            label = "FAKE"
            confidence = fake_p * 100

        return {
            "label": label,
            "confidence": round(confidence, 2),
            "confidence_level": self._confidence_level(confidence, label),
            "model": self.meta.get("model_name", "unknown"),
            "probabilities": {"REAL": round(real_p * 100, 2), "FAKE": round(fake_p * 100, 2)},
        }

    def _get_probabilities(self, processed: str) -> dict[str, float]:
        clf = self.pipeline.named_steps["clf"]
        if hasattr(clf, "predict_proba"):
            probs = clf.predict_proba(self.pipeline.named_steps["tfidf"].transform([processed]))[0]
            classes = list(clf.classes_)
            return {str(c): float(p) for c, p in zip(classes, probs)}

        if hasattr(clf, "decision_function"):
            scores = clf.decision_function(
                self.pipeline.named_steps["tfidf"].transform([processed])
            )
            if scores.ndim == 0:
                scores = np.array([scores])
            if len(scores) == 1:
                # binary LinearSVC
                fake_score = float(scores[0])
                fake_p = 1 / (1 + np.exp(-fake_score))
                return {"FAKE": fake_p, "REAL": 1 - fake_p}
            exp = np.exp(scores - np.max(scores))
            probs = exp / exp.sum()
            classes = list(clf.classes_)
            return {str(c): float(p) for c, p in zip(classes, probs)}

        pred = clf.predict(self.pipeline.named_steps["tfidf"].transform([processed]))[0]
        return {str(pred): 1.0, "REAL" if pred == "FAKE" else "FAKE": 0.0}

    @staticmethod
    def _confidence_level(confidence: float, label: str) -> str:
        if label == "UNCERTAIN":
            return "LOW"
        if confidence >= 75:
            return "HIGH"
        if confidence >= 60:
            return "MEDIUM"
        return "LOW"
