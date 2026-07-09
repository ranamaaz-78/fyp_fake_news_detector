"""Download a satire dataset and build an augmentation file for training.

Source: the "OnionOrNot" dataset (satirical Onion headlines vs. real
"NotTheOnion" headlines). Satire is treated as FAKE, genuine news as REAL.

Adding satirical / off-domain examples helps the classifier recognise satire
and unusual-but-real stories that the US-political Kaggle corpus does not cover.

Output: ml/data/raw/augment.csv with columns text,label (REAL/FAKE).
"""

from __future__ import annotations

import csv
import io
import sys
import urllib.request
from pathlib import Path

RAW_DIR = Path(__file__).resolve().parent.parent / "data" / "raw"
SOURCE_URL = "https://raw.githubusercontent.com/lukefeilberg/onion/master/OnionOrNot.csv"
MAX_PER_CLASS = 6000


def main() -> int:
    RAW_DIR.mkdir(parents=True, exist_ok=True)
    dest = RAW_DIR / "augment.csv"

    print(f"Downloading satire dataset from {SOURCE_URL} ...")
    req = urllib.request.Request(SOURCE_URL, headers={"User-Agent": "Mozilla/5.0"})
    try:
        raw = urllib.request.urlopen(req, timeout=60).read().decode("utf-8", "ignore")
    except Exception as exc:  # noqa: BLE001
        print(f"  Failed: {exc}", file=sys.stderr)
        return 1

    reader = csv.DictReader(io.StringIO(raw))
    counts = {"FAKE": 0, "REAL": 0}
    rows: list[tuple[str, str]] = []

    for row in reader:
        text = (row.get("text") or "").strip()
        label_raw = (row.get("label") or "").strip()

        if not text:
            continue

        # OnionOrNot: 1 = satire (Onion) -> FAKE, 0 = genuine news -> REAL
        if label_raw == "1":
            label = "FAKE"
        elif label_raw == "0":
            label = "REAL"
        else:
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
