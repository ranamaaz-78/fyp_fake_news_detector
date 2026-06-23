"""Core model training logic with optional progress reporting."""

from __future__ import annotations

import json
from collections.abc import Callable
from pathlib import Path
from typing import Any

import joblib
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, f1_score
from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.svm import LinearSVC

from src.preprocess import ensure_nltk_data, tokenize_and_stem

ROOT = Path(__file__).resolve().parent.parent
RAW_DIR = ROOT / "data" / "raw"
MODELS_DIR = ROOT / "models"
REPORTS_DIR = ROOT / "reports"

ProgressCallback = Callable[[str, int], None]

MODEL_PROGRESS = {
    "logistic_regression": (35, "Training Logistic Regression..."),
    "naive_bayes": (55, "Training Naive Bayes..."),
    "svm": (75, "Training SVM..."),
}


def load_config() -> dict:
    with open(ROOT / "config.json", encoding="utf-8") as f:
        return json.load(f)


def load_dataset(max_per_class: int | None = 8000) -> pd.DataFrame:
    fake_path = RAW_DIR / "Fake.csv"
    true_path = RAW_DIR / "True.csv"

    if fake_path.exists() and true_path.exists():
        fake = pd.read_csv(fake_path, nrows=max_per_class)
        true = pd.read_csv(true_path, nrows=max_per_class)
        fake["label"] = "FAKE"
        true["label"] = "REAL"
        text_col = "text" if "text" in fake.columns else fake.columns[-1]
        df = pd.concat(
            [
                fake[[text_col, "label"]].rename(columns={text_col: "text"}),
                true[[text_col, "label"]].rename(columns={text_col: "text"}),
            ],
            ignore_index=True,
        )
        return df.dropna(subset=["text"])

    sample_path = ROOT / "data" / "sample_news.csv"
    if sample_path.exists():
        return pd.read_csv(sample_path)

    raise FileNotFoundError(
        "Fake.csv and True.csv are required in ml/data/raw/. Upload both files from the admin panel."
    )


def build_models() -> dict[str, Pipeline]:
    return {
        "logistic_regression": Pipeline(
            [
                ("tfidf", TfidfVectorizer(max_features=5000, ngram_range=(1, 2))),
                (
                    "clf",
                    LogisticRegression(max_iter=1000, class_weight="balanced", random_state=42),
                ),
            ]
        ),
        "naive_bayes": Pipeline(
            [
                ("tfidf", TfidfVectorizer(max_features=5000, ngram_range=(1, 2))),
                ("clf", MultinomialNB()),
            ]
        ),
        "svm": Pipeline(
            [
                ("tfidf", TfidfVectorizer(max_features=5000, ngram_range=(1, 2))),
                (
                    "clf",
                    LinearSVC(class_weight="balanced", random_state=42, max_iter=3000),
                ),
            ]
        ),
    }


def _report(stage: str, progress: int, callback: ProgressCallback | None) -> None:
    if callback:
        callback(stage, progress)


def run_training(progress_callback: ProgressCallback | None = None) -> dict[str, Any]:
    ensure_nltk_data()
    config = load_config()
    MODELS_DIR.mkdir(parents=True, exist_ok=True)
    REPORTS_DIR.mkdir(parents=True, exist_ok=True)

    _report("Validating dataset files...", 5, progress_callback)

    fake_path = RAW_DIR / "Fake.csv"
    true_path = RAW_DIR / "True.csv"
    if not fake_path.exists() or not true_path.exists():
        raise FileNotFoundError("Fake.csv and True.csv must exist in ml/data/raw/.")

    _report("Loading dataset...", 15, progress_callback)
    df = load_dataset()

    _report("Preprocessing text...", 25, progress_callback)
    df["processed"] = df["text"].astype(str).map(tokenize_and_stem)
    df = df[df["processed"].str.len() > 0]

    X_train, X_test, y_train, y_test = train_test_split(
        df["processed"],
        df["label"],
        test_size=config["test_size"],
        random_state=config["random_state"],
        stratify=df["label"],
    )

    results = []
    best_name = None
    best_f1 = -1.0
    best_pipeline = None

    for name, pipeline in build_models().items():
        progress, stage = MODEL_PROGRESS[name]
        _report(stage, progress, progress_callback)
        pipeline.fit(X_train, y_train)
        y_pred = pipeline.predict(X_test)
        acc = accuracy_score(y_test, y_pred)
        f1 = f1_score(y_test, y_pred, average="weighted")
        report = classification_report(y_test, y_pred)

        results.append({"model": name, "accuracy": acc, "f1_weighted": f1})
        if f1 > best_f1:
            best_f1 = f1
            best_name = name
            best_pipeline = pipeline

        _ = report

    assert best_pipeline is not None and best_name is not None

    _report("Saving model and reports...", 95, progress_callback)

    model_path = MODELS_DIR / "model.pkl"
    meta_path = MODELS_DIR / "model_meta.json"

    joblib.dump(best_pipeline, model_path)
    best_accuracy = next(r["accuracy"] for r in results if r["model"] == best_name)
    meta = {
        "model_name": best_name,
        "accuracy": best_accuracy,
        "f1_weighted": best_f1,
        "uncertain_threshold": config["uncertain_threshold"],
        "labels": ["REAL", "FAKE"],
    }
    with open(meta_path, "w", encoding="utf-8") as f:
        json.dump(meta, f, indent=2)

    with open(REPORTS_DIR / "evaluation.json", "w", encoding="utf-8") as f:
        json.dump({"results": results, "best_model": best_name}, f, indent=2)

    with open(REPORTS_DIR / "confusion_matrix.txt", "w", encoding="utf-8") as f:
        y_pred = best_pipeline.predict(X_test)
        cm = confusion_matrix(y_test, y_pred, labels=["REAL", "FAKE"])
        f.write(f"Best model: {best_name}\n")
        f.write(str(cm))

    _report("Training completed", 100, progress_callback)

    return {
        "best_model": best_name,
        "accuracy": round(float(best_accuracy), 4),
        "f1_weighted": round(float(best_f1), 4),
        "rows_trained": len(df),
        "results": results,
    }
