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
- `POST /predict` — `{ "text": "..." }` → verdict, confidence, style signals, fact-check evidence
- `POST /train/start` — `{ "job_id": "..." }` — background training (requires `Fake.csv` + `True.csv` in `data/raw/`)
- `GET /train/status` — training progress JSON
- `POST /train/reload` — reload model after training completes

## Fact-verification layer

The TF-IDF model classifies **writing style**, not facts. A calmly written false
statement ("Babar Azam is Pakistan's Prime Minister") reads exactly like real
reporting to it, so [`src/fact_verification.py`](src/fact_verification.py) adds a
second opinion that can override the model.

It extracts role claims — *(subject, role, place)* — and checks them against:

1. **`data/knowledge_base.json`** — offline, always available, no network needed
2. **Wikidata** — only for claims the local file does not cover
3. **Google Fact Check Tools API** — published fact-checks, only when an API key is set

A verified contradiction overrides the model and the result is labelled FAKE. The
reverse is deliberately not symmetrical: confirming one claim says nothing about
the rest of the text, so a confirmation can only soften a weak FAKE — it never
promotes anything to REAL. Claims with no coverage leave the model verdict alone
rather than guessing.

Every external call is bounded by a deadline and wrapped so a network failure
degrades to the offline knowledge base instead of costing the user a prediction.

### Roman Urdu

Viral claims in Pakistan are usually written in Roman Urdu, which puts the verb
last ("Babar Azam **ko** Pakistan **ka** Wazir-e-Azam bana diya gaya") and names
offices locally. The English patterns match none of that, so `CLAIM_PATTERNS`
carries a Roman Urdu set alongside them and `ROLE_ALIASES` includes local office
names. Hyphens and spaces are interchangeable, so `Wazir-e-Azam`, `Wazir e Azam`
and `wazir-e-azam` all resolve to `prime minister`.

### When the model cannot read the input

Two cases make the classifier's own verdict meaningless, and in both the honest
answer is UNCERTAIN (`verdict_source: "content_signal"`) rather than a confident
REAL:

- **Unsupported language.** The model was trained on English news only, so its
  score on Roman Urdu or Urdu script carries no information.
- **Declared fiction.** Text that calls itself satire, a parody or a *mazahiya
  kahani* is not reporting, however well written it is.

A verified contradiction still outranks both — "we know this is false" is a
stronger answer than "we cannot tell". These checks live in
[`src/explain.py`](src/explain.py) and surface as `reliability` in the response.

### Environment variables

| Variable | Default | Purpose |
|---|---|---|
| `FNI_FACT_CHECK_ENABLED` | `true` | Set to `0` to disable the layer entirely |
| `FNI_FACTCHECK_API_KEY` | *(unset)* | Google Fact Check Tools API key; the source is skipped without it |
| `FNI_USER_AGENT` | `VeriFactAI/1.0` | Sent to Wikidata, which requires a descriptive agent |

Tuning lives under `fact_check` in [`config.json`](config.json): `override_threshold`,
`timeout_seconds`, `cache_ttl_days` and the enabled `sources`.

### Refreshing the knowledge base

Office holders change, so the file is generated from Wikidata rather than written
by hand. Each entry records the Wikidata item it came from and the date it was
refreshed.

```bash
python scripts/refresh_knowledge_base.py            # ~10 minutes; Wikidata is throttled
python scripts/refresh_knowledge_base.py --dry-run  # preview without writing
```

Add targets to `OFFICE_TARGETS` / `PERSON_TARGETS` in that script. Hand-written
entries marked `"manual": true` are preserved across refreshes.

### Checking it works

```bash
python scripts/verify_samples.py --offline   # curated true/false claims, no network
```

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

## Roadmap — model strengthening (not yet implemented)

The classifier is still TF-IDF + Logistic Regression trained on English news
articles. The fact-verification layer covers the worst failure mode, but the
model itself has known limits: it has never seen Pakistani names, local politics,
Roman Urdu, or short social-media claims, so on anything outside long-form
English news it is effectively judging formatting.

Planned next steps, in order of cost:

1. **Improved classic ML** — widen the n-gram range, add character n-grams
   (robust to misspellings and Roman Urdu), add stylometric features (sentence
   length, punctuation ratio), and swap Logistic Regression for a gradient
   boosted model. Cheapest to retrain.
2. **Dataset expansion** — merge LIAR (short political statements, much closer in
   shape to the claims users actually paste), FakeNewsNet and ISOT alongside the
   current Kaggle set; deduplicate near-identical articles and strip publisher
   boilerplate so the model cannot cheat by memorising source formatting.
3. **Fine-tuned transformer** — DistilBERT, or XLM-RoBERTa if Urdu and Roman Urdu
   matter. Strongest results, but needs a GPU to be practical.

Evaluation should move off raw accuracy to precision, recall, F1 and ROC-AUC with
k-fold cross-validation, since the current 90.2% figure comes from a single
stratified split on one dataset.
