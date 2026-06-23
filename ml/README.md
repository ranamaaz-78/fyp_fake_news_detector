# FNI Machine Learning Module

Python ML backend for the Fake News Identification system.

## Setup

```bash
cd ml
pip install -r requirements.txt
python scripts/download_dataset.py   # optional — Kaggle Fake/True CSV
python scripts/train.py              # trains LR, NB, SVM; saves best model
```

## Run API

```bash
python api/app.py
# or: set FNI_ML_PORT=5000 && python api/app.py
```

Endpoints:
- `GET /health` — service health
- `POST /predict` — `{ "text": "..." }` → label, confidence, model
- `POST /train/start` — `{ "job_id": "..." }` — background training (requires `Fake.csv` + `True.csv` in `data/raw/`)
- `GET /train/status` — training progress JSON
- `POST /train/reload` — reload model after training completes

## Admin-triggered training

From Laravel admin: **Model Training** (`/admin/training`)

1. Upload Kaggle-style `Fake.csv` and `True.csv`
2. Click **Start Training** — Flask runs `scripts/run_training_job.py` in background
3. Progress is polled via `/admin/training/status` → Flask `/train/status`
4. Best model saved to `models/model.pkl` automatically

CLI training still works: `python scripts/train.py`

## Configuration

See [`config.json`](config.json):
- `uncertain_threshold`: 0.60 (max probability below this → UNCERTAIN)
- `min_input_chars`: 20

## Tests

```bash
pytest tests/ -v
python scripts/benchmark.py   # requires API running
```

## Dataset

Decision documented in [`DATASET.md`](DATASET.md) — **Kaggle Fake and Real News**.
