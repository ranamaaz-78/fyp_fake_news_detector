"""Read/write training job status for admin UI polling."""

from __future__ import annotations

import json
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

ROOT = Path(__file__).resolve().parent.parent
MODELS_DIR = ROOT / "models"
STATUS_PATH = MODELS_DIR / "training_status.json"
LOCK_PATH = MODELS_DIR / ".training.lock"


def _utc_now() -> str:
    return datetime.now(timezone.utc).isoformat()


def read_status() -> dict[str, Any]:
    if not STATUS_PATH.exists():
        return {"status": "idle", "progress": 0, "stage": "Idle", "job_id": None}

    with open(STATUS_PATH, encoding="utf-8") as f:
        return json.load(f)


def write_status(payload: dict[str, Any]) -> None:
    MODELS_DIR.mkdir(parents=True, exist_ok=True)
    with open(STATUS_PATH, "w", encoding="utf-8") as f:
        json.dump(payload, f, indent=2)


class TrainingStatusManager:
    def __init__(self, job_id: str) -> None:
        self.job_id = job_id

    def mark_running(self) -> None:
        write_status(
            {
                "job_id": self.job_id,
                "status": "running",
                "progress": 0,
                "stage": "Starting training...",
                "started_at": _utc_now(),
                "finished_at": None,
                "metrics": None,
                "error": None,
            }
        )

    def update(self, stage: str, progress: int) -> None:
        current = read_status()
        current.update(
            {
                "job_id": self.job_id,
                "status": "running",
                "stage": stage,
                "progress": max(0, min(100, progress)),
            }
        )
        write_status(current)

    def mark_completed(self, metrics: dict[str, Any]) -> None:
        write_status(
            {
                "job_id": self.job_id,
                "status": "completed",
                "progress": 100,
                "stage": "Training completed",
                "started_at": read_status().get("started_at"),
                "finished_at": _utc_now(),
                "metrics": metrics,
                "error": None,
            }
        )

    def mark_failed(self, error: str) -> None:
        current = read_status()
        write_status(
            {
                "job_id": self.job_id,
                "status": "failed",
                "progress": current.get("progress", 0),
                "stage": "Training failed",
                "started_at": current.get("started_at"),
                "finished_at": _utc_now(),
                "metrics": None,
                "error": error,
            }
        )


def acquire_lock(job_id: str) -> bool:
    MODELS_DIR.mkdir(parents=True, exist_ok=True)
    if LOCK_PATH.exists():
        return False
    LOCK_PATH.write_text(job_id, encoding="utf-8")
    return True


def release_lock() -> None:
    if LOCK_PATH.exists():
        LOCK_PATH.unlink()


def is_locked() -> bool:
    return LOCK_PATH.exists()
