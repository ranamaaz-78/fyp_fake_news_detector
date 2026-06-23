"""Train LR, Naive Bayes, and SVM; export best model by F1-score."""

from __future__ import annotations

import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from src.trainer import run_training  # noqa: E402


def train() -> None:
    def progress_callback(stage: str, progress: int) -> None:
        print(f"[{progress:3d}%] {stage}")

    metrics = run_training(progress_callback=progress_callback)
    print(f"\nBest model: {metrics['best_model']} (F1={metrics['f1_weighted']:.4f})")
    print(f"Saved: {ROOT / 'models' / 'model.pkl'}")


if __name__ == "__main__":
    train()
