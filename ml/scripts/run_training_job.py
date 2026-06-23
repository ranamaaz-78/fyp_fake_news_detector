"""Background training worker invoked by Flask /train/start."""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from src.training_status import TrainingStatusManager, release_lock  # noqa: E402
from src.trainer import run_training  # noqa: E402


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--job-id", required=True)
    args = parser.parse_args()

    manager = TrainingStatusManager(args.job_id)

    try:
        manager.mark_running()

        def progress_callback(stage: str, progress: int) -> None:
            manager.update(stage, progress)

        metrics = run_training(progress_callback=progress_callback)
        manager.mark_completed(metrics)
        return 0
    except Exception as exc:
        manager.mark_failed(str(exc))
        return 1
    finally:
        release_lock()


if __name__ == "__main__":
    raise SystemExit(main())
