"""Benchmark Flask /predict response times."""

from __future__ import annotations

import statistics
import sys
import time
from pathlib import Path

import requests

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

BASE = "http://127.0.0.1:5000"
SAMPLE = (
    "Government officials announced new economic policy measures after extensive review "
    "by independent experts and public consultation sessions held nationwide this year."
)


def main() -> int:
    times = []
    for _ in range(10):
        start = time.perf_counter()
        r = requests.post(f"{BASE}/predict", json={"text": SAMPLE}, timeout=10)
        elapsed = (time.perf_counter() - start) * 1000
        times.append(elapsed)
        if r.status_code != 200:
            print(f"Error {r.status_code}: {r.text}")
            return 1

    print(f"Requests: {len(times)}")
    print(f"Mean: {statistics.mean(times):.2f} ms")
    print(f"Max: {max(times):.2f} ms")
    print(f"p95: {sorted(times)[int(len(times) * 0.95) - 1]:.2f} ms")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
