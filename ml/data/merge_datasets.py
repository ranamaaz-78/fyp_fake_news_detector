"""Dataset consolidation and merging utility.

Merges multiple public datasets (e.g. Kaggle, ISOT, LIAR, local Pakistani news)
into a unified schema with 'text' and 'label' columns, removing duplicates and
reporting class balances.
"""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

import pandas as pd

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))


def normalize_label(val: str | int) -> str:
    """Normalize arbitrary label values into standard 'REAL' or 'FAKE'."""
    val_str = str(val).strip().upper()
    if val_str in ('1', 'FAKE', 'FALSE', 'BARELY-TRUE', 'PANTS-FIRE', 'UNTRUE', '0.0'):
        return 'FAKE'
    if val_str in ('0', 'REAL', 'TRUE', 'MOSTLY-TRUE', 'HALF-TRUE', '1.0'):
        return 'REAL'
    return 'FAKE' if 'FAKE' in val_str or 'FALSE' in val_str else 'REAL'


def merge_csv_files(input_files: list[str | Path], output_file: str | Path) -> pd.DataFrame:
    """Read multiple CSV files, extract text and label, clean and merge them."""
    dfs = []

    for path_str in input_files:
        path = Path(path_str)
        if not path.exists():
            print(f"Warning: File {path} does not exist. Skipping.")
            continue

        print(f"Reading dataset: {path.name}")
        try:
            df = pd.read_csv(path)
        except Exception as e:
            print(f"Failed to read {path}: {e}")
            continue

        # Column mapping heuristic
        cols = {c.lower().strip(): c for c in df.columns}
        text_col = cols.get('text') or cols.get('statement') or cols.get('title') or cols.get('content')
        label_col = cols.get('label') or cols.get('target') or cols.get('class') or cols.get('verdict')

        if not text_col or not label_col:
            print(f"Skipping {path.name}: Could not find text/label columns.")
            continue

        subset = pd.DataFrame()
        subset['text'] = df[text_col].astype(str).str.strip()
        subset['label'] = df[label_col].apply(normalize_label)

        dfs.append(subset)

    if not dfs:
        raise ValueError("No valid datasets were loaded.")

    combined = pd.concat(dfs, ignore_index=True)

    # Clean empty/short rows
    combined = combined.dropna(subset=['text'])
    combined = combined[combined['text'].str.len() >= 20]

    # Deduplicate
    initial_len = len(combined)
    combined = combined.drop_duplicates(subset=['text'], keep='first')
    dedup_len = len(combined)

    print(f"\n--- Merge Summary ---")
    print(f"Total rows collected: {initial_len}")
    print(f"Duplicates removed: {initial_len - dedup_len}")
    print(f"Final dataset size: {dedup_len}")

    counts = combined['label'].value_counts()
    print("\nClass distribution:")
    for label, count in counts.items():
        pct = (count / dedup_len) * 100
        print(f"  {label}: {count} ({pct:.1f}%)")

    out_path = Path(output_file)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    combined.to_csv(out_path, index=False)
    print(f"\nMerged dataset saved to: {out_path}")

    return combined


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Merge multiple fake news datasets.")
    parser.add_argument("--inputs", nargs="+", help="Paths to input CSV files")
    parser.add_argument("--output", type=str, default=str(ROOT / "data" / "dataset.csv"))
    args = parser.parse_args()

    if args.inputs:
        merge_csv_files(args.inputs, args.output)
    else:
        default_inputs = [
            str(ROOT / "data" / "dataset.csv"),
            str(ROOT / "data" / "raw" / "pakistani_news.csv"),
            str(ROOT / "data" / "raw" / "liar_dataset.csv"),
        ]
        merge_csv_files(default_inputs, args.output)
