"""Dataset cleaning, boilerplate stripping, and class balancing utility.

Strips publisher boilerplates (e.g. "WASHINGTON (Reuters) -", "FILE PHOTO:"),
removes near-duplicate texts, and ensures class balance across REAL and FAKE classes.
"""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

import pandas as pd

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

# Common publisher boilerplate patterns that cause data leakage
BOILERPLATE_PATTERNS = [
    r'^[A-Z\s\.\,\-\(\)]+\s*\((?:Reuters|AP|AFP|CNN|BBC|Fox News|Dawn|Geo News)\)\s*[\-\–\—]\s*',
    r'^FILE PHOTO:?\s*',
    r'^\(Reuters\)\s*[\-\–\—]\s*',
    r'^\(AP\)\s*[\-\–\—]\s*',
    r'^Reporting by [^\;]+; Editing by [^\n]+',
    r'Click here for more stories from [^\n]+',
    r'Follow us on (?:Twitter|Facebook|Instagram|Telegram)[^\n]*',
]


def strip_boilerplate(text: str) -> str:
    """Remove known publisher prefixes and disclaimers from text."""
    cleaned = str(text)
    for pattern in BOILERPLATE_PATTERNS:
        cleaned = re.sub(pattern, '', cleaned, flags=re.IGNORECASE).strip()
    return cleaned


def clean_and_balance_dataset(input_file: str | Path, output_file: str | Path) -> pd.DataFrame:
    """Clean text, strip boilerplates, and report class balance."""
    print(f"Loading dataset from: {input_file}")
    df = pd.read_csv(input_file)

    if 'text' not in df.columns or 'label' not in df.columns:
        raise ValueError("Dataset CSV must contain 'text' and 'label' columns.")

    initial_len = len(df)
    print(f"Initial rows: {initial_len}")

    # Strip boilerplates
    df['text'] = df['text'].astype(str).apply(strip_boilerplate)

    # Filter out empty or extremely short texts after boilerplate removal
    df = df[df['text'].str.len() >= 20]

    # Deduplicate based on cleaned text
    df = df.drop_duplicates(subset=['text'], keep='first')
    cleaned_len = len(df)

    print(f"Rows after boilerplate stripping & deduplication: {cleaned_len}")

    # Balance classes (undersample majority class)
    counts = df['label'].value_counts()
    min_count = counts.min()

    balanced_dfs = []
    for label in df['label'].unique():
        sub_df = df[df['label'] == label]
        if len(sub_df) > min_count:
            sub_df = sub_df.sample(n=min_count, random_state=42)
        balanced_dfs.append(sub_df)

    balanced_df = pd.concat(balanced_dfs, ignore_index=True).sample(frac=1.0, random_state=42).reset_index(drop=True)

    print(f"\n--- Clean & Balance Summary ---")
    print(f"Final balanced dataset size: {len(balanced_df)}")
    print("Class distribution:")
    for label, count in balanced_df['label'].value_counts().items():
        print(f"  {label}: {count}")

    out_path = Path(output_file)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    balanced_df.to_csv(out_path, index=False)
    print(f"\nCleaned dataset saved to: {out_path}")

    return balanced_df


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Clean and balance fake news dataset.")
    parser.add_argument("--input", type=str, default=str(ROOT / "data" / "dataset.csv"))
    parser.add_argument("--output", type=str, default=str(ROOT / "data" / "dataset.csv"))
    args = parser.parse_args()

    clean_and_balance_dataset(args.input, args.output)
