# FNI — Fake News Identification

UCP BSCS FYP: web app that classifies news as **REAL**, **FAKE**, or **UNCERTAIN** with confidence scores.

**Stack:** Laravel 12 · Flask ML API (Scikit-learn) · MySQL 8 (WAMP)

## Quick start (Windows / WAMP)

### Prerequisites

- PHP 8.2+, Composer, Node.js
- WAMP with MySQL running (`pdo_mysql` enabled)
- Python 3.10+ with pip

### 1. Database

Create `fni_db` in phpMyAdmin or MySQL CLI:

```sql
CREATE DATABASE fni_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Laravel

```powershell
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
```

`.env.example` defaults to MySQL (`fni_db`, user `root`, empty password). Adjust for your WAMP setup.

### 3. ML API

```powershell
cd ml
pip install -r requirements.txt
python scripts/train.py
python api/app.py
```

### 4. Run

```powershell
# Terminal 1 — ML API (from ml/)
python api/app.py

# Terminal 2 — Laravel (project root) — port 9000 with large CSV upload limits
php artisan serve --port=8000
# or: .\scripts\serve.ps1
```

Open http://127.0.0.1:8000

## Demo accounts

| Email | Password | Role |
|-------|----------|------|
| admin@fni.test | password | admin (includes Model Training at `/admin/training`) |
| test@example.com | password | user |

## Tests

```powershell
php artisan test          # Laravel — uses SQLite in-memory (no WAMP needed)
pytest ml/tests/ -v       # Flask ML API
```

## Documentation

- [DEPLOYMENT.md](DEPLOYMENT.md) — WAMP setup, Apache vhost, production LAMP
- [ml/README.md](ml/README.md) — dataset, training, API
- [ml/DATASET.md](ml/DATASET.md) — Kaggle Fake & Real News dataset

## Project structure

```
app/              Laravel controllers, models, services
ml/               Flask API, training scripts, model.pkl
resources/views/  Blade UI (FNI design system)
database/         Migrations and seeders
tests/            PHPUnit feature tests
```
