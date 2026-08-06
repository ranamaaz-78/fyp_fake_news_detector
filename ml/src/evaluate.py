"""Standalone evaluation script for VeriFact AI ML models.

Runs Stratified K-Fold cross-validation on the trained model pipeline or dataset,
computing precision, recall, F1-score, and ROC-AUC metrics across folds.
"""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

import joblib
import numpy as np
import pandas as pd
from sklearn.metrics import (
    accuracy_score,
    classification_report,
    confusion_matrix,
    f1_score,
    precision_score,
    recall_score,
    roc_auc_score,
)
from sklearn.model_selection import StratifiedKFold

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))


def evaluate_model(model_path: str | Path, dataset_path: str | Path, n_splits: int = 5) -> dict:
    """Evaluate a trained model pipeline on a dataset using Stratified K-Fold CV."""
    print(f"Loading pipeline from: {model_path}")
    pipeline = joblib.load(model_path)

    print(f"Loading dataset from: {dataset_path}")
    df = pd.read_csv(dataset_path)

    if 'text' not in df.columns or 'label' not in df.columns:
        raise ValueError("Dataset CSV must contain 'text' and 'label' columns.")

    df = df.dropna(subset=['text', 'label'])
    X = df['text'].astype(str).values
    y = df['label'].astype(str).values

    # Map labels to 0 (REAL) and 1 (FAKE)
    y_binary = np.array([1 if label.upper() == 'FAKE' else 0 for label in y])

    skf = StratifiedKFold(n_splits=n_splits, shuffle=True, random_state=42)

    accuracies, precisions, recalls, f1s, aucs = [], [], [], [], []
    conf_matrices = []

    print(f"\nRunning {n_splits}-Fold Cross Validation...")
    for fold, (train_idx, test_idx) in enumerate(skf.split(X, y_binary), 1):
        X_train, X_test = X[train_idx], X[test_idx]
        y_train, y_test = y_binary[train_idx], y_binary[test_idx]

        # Fit fold pipeline
        pipeline.fit(X_train, y_train)

        y_pred = pipeline.predict(X_test)
        y_proba = pipeline.predict_proba(X_test)[:, 1] if hasattr(pipeline, "predict_proba") else y_pred

        acc = accuracy_score(y_test, y_pred)
        prec = precision_score(y_test, y_pred, zero_division=0)
        rec = recall_score(y_test, y_pred, zero_division=0)
        f1 = f1_score(y_test, y_pred, zero_division=0)
        auc = roc_auc_score(y_test, y_proba)

        accuracies.append(acc)
        precisions.append(prec)
        recalls.append(rec)
        f1s.append(f1)
        aucs.append(auc)

        cm = confusion_matrix(y_test, y_pred).tolist()
        conf_matrices.append(cm)

        print(f"Fold {fold}: Acc={acc:.4f}, Prec={prec:.4f}, Rec={rec:.4f}, F1={f1:.4f}, AUC={auc:.4f}")

    results = {
        "n_splits": n_splits,
        "metrics_mean": {
            "accuracy": round(float(np.mean(accuracies)), 4),
            "precision": round(float(np.mean(precisions)), 4),
            "recall": round(float(np.mean(recalls)), 4),
            "f1_score": round(float(np.mean(f1s)), 4),
            "roc_auc": round(float(np.mean(aucs)), 4),
        },
        "metrics_std": {
            "accuracy": round(float(np.std(accuracies)), 4),
            "precision": round(float(np.std(precisions)), 4),
            "recall": round(float(np.std(recalls)), 4),
            "f1_score": round(float(np.std(f1s)), 4),
            "roc_auc": round(float(np.std(aucs)), 4),
        },
        "per_fold": [
            {
                "fold": i + 1,
                "accuracy": round(accuracies[i], 4),
                "precision": round(precisions[i], 4),
                "recall": round(recalls[i], 4),
                "f1_score": round(f1s[i], 4),
                "roc_auc": round(aucs[i], 4),
            }
            for i in range(n_splits)
        ]
    }

    print("\n--- Summary Evaluation ---")
    print(json.dumps(results["metrics_mean"], indent=2))

    return results


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Evaluate VeriFact AI ML model.")
    parser.add_argument("--model", type=str, default=str(ROOT / "models" / "best_model.joblib"))
    parser.add_argument("--dataset", type=str, default=str(ROOT / "data" / "dataset.csv"))
    parser.add_argument("--folds", type=int, default=5)
    args = parser.parse_args()

    evaluate_model(args.model, args.dataset, args.folds)
