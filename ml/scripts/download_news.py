"""Download a diverse long-form fake/real news dataset for augmentation.

Source: the "GonzaloA/fake_news" dataset (long-form articles). In that corpus
label 1 = REAL and label 0 = FAKE (verified: Reuters articles are label 1).

Adds more long-form article volume on top of the primary Kaggle corpus.

Output: ml/data/raw/augment_news.csv with columns text,label (REAL/FAKE).
"""

from __future__ import annotations

import csv
import io
import sys
import urllib.request
from pathlib import Path

RAW_DIR = Path(__file__).resolve().parent.parent / "data" / "raw"
SOURCE_URL = "https://huggingface.co/datasets/GonzaloA/fake_news/resolve/main/train.csv"
MAX_PER_CLASS = 6000

# GonzaloA label convention.
LABEL_MAP = {"1": "REAL", "0": "FAKE"}


def main() -> int:
    RAW_DIR.mkdir(parents=True, exist_ok=True)
    dest = RAW_DIR / "augment_news.csv"

    print(f"Downloading long-form news dataset from {SOURCE_URL} ...")
    req = urllib.request.Request(SOURCE_URL, headers={"User-Agent": "Mozilla/5.0"})
    try:
        raw = urllib.request.urlopen(req, timeout=90).read().decode("utf-8", "ignore")
    except Exception as exc:  # noqa: BLE001
        print(f"  Failed: {exc}", file=sys.stderr)
        return 1

    reader = csv.DictReader(io.StringIO(raw), delimiter=";")
    counts = {"FAKE": 0, "REAL": 0}
    rows: list[tuple[str, str]] = []

    for row in reader:
        text = (row.get("text") or "").strip()
        label = LABEL_MAP.get((row.get("label") or "").strip())

        if not text or label is None:
            continue

        if counts[label] >= MAX_PER_CLASS:
            continue

        rows.append((text, label))
        counts[label] += 1

    if not rows:
        print("  No usable rows parsed.", file=sys.stderr)
        return 1

    with open(dest, "w", encoding="utf-8", newline="") as f:
        writer = csv.writer(f)
        writer.writerow(["text", "label"])
        writer.writerows(rows)

    print(f"  Saved {len(rows)} rows to {dest} ({counts['REAL']} REAL, {counts['FAKE']} FAKE)")
    print("Done. Run: python ml/scripts/train.py")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
