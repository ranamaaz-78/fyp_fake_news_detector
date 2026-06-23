"""Generate minimal sample CSV when full Kaggle download is unavailable."""

from __future__ import annotations

from pathlib import Path

import pandas as pd

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / "data" / "sample_news.csv"

REAL_SAMPLES = [
    "The central bank announced a quarter point interest rate adjustment following inflation data released this morning by the statistics office.",
    "Researchers at the national university published peer reviewed findings on renewable energy storage in the journal of applied science.",
    "Local officials confirmed road repairs will begin next month after the city council approved funding for infrastructure improvements.",
    "Health authorities reported vaccination coverage increased across rural districts according to the latest public health survey.",
    "The weather service issued a standard advisory for coastal regions expecting moderate rainfall through the weekend.",
] * 200

FAKE_SAMPLES = [
    "Shocking secret cure doctors hide from you will destroy the medical industry overnight according to anonymous insiders online.",
    "Celebrity secretly controls world governments through hidden messages in every television broadcast says viral social post.",
    "Scientists confirm drinking only lemon water reverses aging completely and big pharma is suppressing the miracle discovery.",
    "Breaking exclusive leaked document proves every election was staged by a single unknown group controlling all media outlets.",
    "Instant miracle pill melts body fat in hours without diet or exercise and authorities are banning this simple trick.",
] * 200


def main() -> None:
    df = pd.DataFrame(
        [{"text": t, "label": "REAL"} for t in REAL_SAMPLES]
        + [{"text": t, "label": "FAKE"} for t in FAKE_SAMPLES]
    )
    OUT.parent.mkdir(parents=True, exist_ok=True)
    df.to_csv(OUT, index=False)
    print(f"Wrote {len(df)} rows to {OUT}")


if __name__ == "__main__":
    main()
