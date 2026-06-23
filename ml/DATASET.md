# Dataset Decision — Kaggle Fake and Real News

**Decision:** Use the **Kaggle Fake and Real News Dataset** (Clément Bisaillon).

**Why not LIAR?**
- LIAR uses PolitiFact statement labels (true, mostly-true, half-true, etc.) — not a clean Real/Fake binary.
- Kaggle dataset maps directly to our `REAL` / `FAKE` labels and article-length text matches the SRS input form.

**Files**
- `Fake.csv` — ~23,502 fake articles
- `True.csv` — ~21,417 real articles

**Source:** https://www.kaggle.com/datasets/clmentbisaillon/fake-and-real-news-dataset

**Download (manual or script):**
```bash
python ml/scripts/download_dataset.py
```

Place files in `ml/data/raw/` or run the training script which loads a bundled sample if raw files are missing.

**Split:** 80% train / 20% test (stratified). TF-IDF fitted on train only.
