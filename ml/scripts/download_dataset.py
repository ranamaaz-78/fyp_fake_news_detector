"""Download Kaggle Fake and Real News dataset into ml/data/raw/."""

from __future__ import annotations

import sys
import urllib.request
from pathlib import Path

RAW_DIR = Path(__file__).resolve().parent.parent / "data" / "raw"

# Public mirrors (subset-friendly; full dataset via Kaggle preferred)
SOURCES = {
    "Fake.csv": "https://raw.githubusercontent.com/tyanakiev/NewsAnalyze/main/Fake.csv",
    "True.csv": "https://raw.githubusercontent.com/tyanakiev/NewsAnalyze/main/True.csv",
}


def download_file(name: str, url: str, dest: Path) -> None:
    print(f"Downloading {name} ...")
    dest.parent.mkdir(parents=True, exist_ok=True)
    try:
        urllib.request.urlretrieve(url, dest)
        print(f"  Saved to {dest} ({dest.stat().st_size // 1024} KB)")
    except Exception as exc:
        print(f"  Failed: {exc}", file=sys.stderr)
        raise


def main() -> int:
    RAW_DIR.mkdir(parents=True, exist_ok=True)
    for name, url in SOURCES.items():
        dest = RAW_DIR / name
        if dest.exists() and dest.stat().st_size > 1000:
            print(f"Skip {name} (already exists)")
            continue
        download_file(name, url, dest)
    print("Done. Run: python ml/scripts/train.py")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
