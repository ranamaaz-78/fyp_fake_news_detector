# VeriFact AI — Complete Project Reference (For Documentation Generation)

> **Purpose of this file:** This is a comprehensive fact-sheet of every technical and academic detail about the VeriFact AI project. Copy-paste this entire file (or relevant sections) into any AI tool and ask it to write documentation, reports, or presentations topic-by-topic.

---

## 1. PROJECT IDENTITY

- **Project Title:** VeriFact AI — Deep Learning-Powered Fact-Checking & Fake News Detection
- **Alternate Title:** FNI — Fake News Identification
- **University:** University of Central Punjab (UCP), Gujranwala Campus, Pakistan
- **Faculty:** Faculty of Information Technology & Computer Science
- **Degree:** Bachelor of Science in Computer Science (BSCS)
- **Group ID:** G1F22FYPCS016
- **Project Advisor / Supervisor:** Prof. Muzammil Sadiq

### Team Members
| Reg # | Name | Role |
|---|---|---|
| G1F22UBSCS168 | Maaz Naveed | Team Member |
| G1F22UBSCS066 | Jazil Mehmood | Team Member |
| G1F22UBSCS174 | Muhammad Abrar | Team Member |

---

## 2. PROBLEM STATEMENT

Misinformation, clickbait, and coordinated disinformation campaigns ("fake news") on social media and digital journalism platforms have become a global threat. They can sway elections, impact stock markets, and trigger social unrest. Manual fact-checking by human experts is highly accurate but does not scale — it cannot keep up with the millions of claims generated every hour. There is a need for an automated, real-time, AI-powered system that can classify news articles and claims as REAL, FAKE, or UNCERTAIN with measurable confidence.

---

## 3. PROJECT SUMMARY / ABSTRACT

VeriFact AI is a web-based AI system that classifies digital news text as **REAL**, **FAKE**, or **UNCERTAIN** with probabilistic confidence scores. It uses a **dual-server microservices architecture**: a **Laravel 12** web portal + a **Python/Flask ML API** built on Scikit-learn. Users can input raw text, paste article URLs (auto-extracted), or upload images (OCR-processed). The ML engine uses TF-IDF vectorization and trains three algorithms — Logistic Regression, Naive Bayes, and SVM (LinearSVC) — automatically deploying the best model by F1-score. The system achieved **99.78% accuracy** on the Kaggle Fake & Real News dataset (16,000 rows trained, SVM best model).

---

## 4. COMPLETE TECHNOLOGY STACK

### 4.1 Web Backend (Laravel)
- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Authentication:** Laravel Breeze (with email verification)
- **Database ORM:** Eloquent
- **Template Engine:** Blade
- **Package Manager:** Composer
- **Key packages:** laravel/framework ^12.0, laravel/breeze ^2.4, laravel/tinker ^2.10.1

### 4.2 Frontend
- **CSS Framework:** Tailwind CSS 3.1, Bootstrap 5 (vendor)
- **JS Reactivity:** Alpine.js 3.4
- **Build Tool:** Vite 7.0
- **Animations:** GSAP, ScrollTrigger, WOW.js, Swiper.js
- **Icons:** FontAwesome 6, Material Symbols
- **Fonts:** Inter (Google Fonts), custom theme font
- **Package Manager:** NPM

### 4.3 Machine Learning API
- **Framework:** Flask 3.0+
- **Language:** Python 3.11 (3.10+ required)
- **ML Library:** Scikit-learn 1.4+
- **NLP Library:** NLTK 3.8+ (Porter Stemmer, English stopwords, Punkt tokenizer)
- **Data Processing:** Pandas 2.0+, NumPy 1.26+
- **Model Serialization:** joblib 1.3+
- **Testing:** pytest 8.0+
- **HTTP Client:** requests 2.31+

### 4.4 Database
- **Development/Local:** SQLite (file: `database/database.sqlite`)
- **Production:** MySQL 8.0 (database name: `fni_db`)
- **Session/Cache/Queue:** Database-driven (configurable)

### 4.5 External APIs
- **OCR Space API** (https://api.ocr.space/parse/image) — for extracting text from uploaded images
  - Free tier API key: `helloworld`
  - Language: `eng`

---

## 5. ARCHITECTURE OVERVIEW

### Dual-Server Microservices Architecture:
```
USER BROWSER (Tailwind CSS + Alpine.js + Bootstrap + AJAX)
        ↓ HTTP Request
LARAVEL WEB SERVER (Port 8000)
  - Controllers: NewsCheckController, HistoryController, ProfileController
  - Admin Controllers: DashboardController, TrainingController
  - Services: FakeNewsApiService, ArticleExtractionService, MlTrainingService, AuditLogger
  - Auth: Laravel Breeze (login, register, password reset, email verify)
  - ORM: Eloquent → SQLite/MySQL
        ↓ REST API (JSON) via HTTP
FLASK ML API SERVER (Port 5000)
  - Endpoints: /health, /predict, /train/start, /train/status, /train/reload
  - Engine: FakeNewsPredictor class → loads model.pkl
  - Pipeline: TfidfVectorizer → LinearSVC / LogisticRegression / MultinomialNB
  - Training: Subprocess spawns run_training_job.py
```

### Design Patterns Used:
- **MVC Pattern** — Laravel controllers + Blade views + Eloquent models
- **Service Layer** — Business logic isolated in `app/Services/`
- **Dependency Injection** — Laravel container auto-injects services into controllers
- **Pipeline Pattern** — Scikit-learn `Pipeline` bundles TF-IDF + classifier
- **Repository Pattern** — Eloquent models abstract DB queries

---

## 6. COMPLETE FILE/FOLDER STRUCTURE

```
fyp_fake_news_detector/
├── app/
│   ├── Http/Controllers/
│   │   ├── NewsCheckController.php      # Main claim check (text/URL/image)
│   │   ├── HistoryController.php        # User prediction history
│   │   ├── ProfileController.php        # User profile CRUD
│   │   └── Admin/
│   │       ├── DashboardController.php  # Admin overview, users, audit logs, datasets
│   │       └── TrainingController.php   # Upload datasets, trigger training, check status
│   ├── Models/
│   │   ├── User.php                     # Auth user (role: user/admin)
│   │   ├── Prediction.php              # Prediction log (result, confidence, model)
│   │   ├── Dataset.php                 # Uploaded CSV dataset record
│   │   ├── TrainingJob.php             # ML training job tracking
│   │   └── AuditLog.php               # Admin audit trail
│   └── Services/
│       ├── FakeNewsApiService.php      # HTTP client to Flask /predict
│       ├── ArticleExtractionService.php # URL scraping & text extraction
│       ├── MlTrainingService.php       # Training job orchestration
│       └── AuditLogger.php            # Audit logging helper
├── ml/
│   ├── api/
│   │   └── app.py                      # Flask REST API (5 endpoints)
│   ├── src/
│   │   ├── predictor.py                # FakeNewsPredictor class
│   │   ├── preprocess.py               # clean_text(), tokenize_and_stem()
│   │   ├── trainer.py                  # run_training() — trains 3 models, saves best
│   │   └── training_status.py          # Lock file management for training jobs
│   ├── scripts/
│   │   ├── train.py                    # CLI training entry point
│   │   └── run_training_job.py         # Background job script (spawned by Flask)
│   ├── models/
│   │   ├── model.pkl                   # Serialized best model (joblib)
│   │   ├── model_meta.json             # Best model metadata (name, accuracy, F1)
│   │   └── training_status.json        # Current training job status
│   ├── data/raw/                       # Fake.csv & True.csv (Kaggle dataset)
│   ├── reports/                        # evaluation.json, confusion_matrix.txt
│   ├── config.json                     # ML configuration
│   └── requirements.txt               # Python dependencies
├── resources/views/
│   ├── layouts/
│   │   ├── frontend.blade.php          # Main public layout (with nav, footer, JS)
│   │   ├── fni.blade.php               # Authenticated user layout
│   │   └── navigation.blade.php        # Auth navigation bar
│   ├── news/
│   │   ├── home.blade.php              # Landing page with verifier widget
│   │   ├── features.blade.php          # Features page
│   │   ├── how-it-works.blade.php      # How It Works page
│   │   ├── integrations.blade.php      # Integrations page
│   │   ├── faq.blade.php               # FAQ page
│   │   └── contact.blade.php           # Contact page
│   ├── admin/                          # Admin dashboard views
│   ├── history/                        # User history views
│   └── profile/                        # Profile management views
├── database/
│   ├── migrations/                     # Laravel migration files
│   ├── seeders/                        # Database seeders (demo users)
│   └── database.sqlite                 # SQLite database file
├── public/assets/                      # Static CSS, JS, images, fonts
├── routes/web.php                      # All Laravel web routes
├── .env                                # Environment configuration
├── composer.json                       # PHP dependencies
├── package.json                        # Node.js dependencies
└── php_local/php.exe                   # Bundled PHP 8.2 binary
```

---

## 7. DATABASE SCHEMA (ALL TABLES)

### Table: users
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | Auto-increment |
| name | string | |
| username | string | NULLABLE |
| email | string | UNIQUE |
| password | string | Hashed via bcrypt |
| role | string | 'user' or 'admin' |
| is_active | boolean | DEFAULT true |
| email_verified_at | timestamp | NULLABLE |
| remember_token | string | |
| created_at / updated_at | timestamps | |

### Table: predictions
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | Auto-increment |
| user_id | bigint (FK → users) | NULLABLE (guest predictions) |
| input_text | text | The claim text analyzed |
| result | string | 'REAL', 'FAKE', or 'UNCERTAIN' |
| confidence | decimal(5,2) | 0.00 – 100.00 |
| confidence_level | string | 'HIGH', 'MEDIUM', or 'LOW' |
| model_used | string | e.g. 'svm', 'logistic_regression' |
| created_at / updated_at | timestamps | |

### Table: datasets
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| uploaded_by | bigint (FK → users) | Admin who uploaded |
| filename | string | Stored filename |
| original_name | string | User's original filename |
| row_count | integer | NULLABLE |
| status | string | 'processed' or 'failed' |
| notes | text | NULLABLE |
| created_at / updated_at | timestamps | |

### Table: training_jobs
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| started_by | bigint (FK → users) | Admin who started |
| ml_job_id | string | UUID |
| status | string | 'queued', 'running', 'completed', 'failed' |
| progress | integer | 0 – 100 |
| stage | string | e.g. 'Training SVM...' |
| metrics | JSON/text | accuracy, F1, per-model results |
| error_message | text | NULLABLE |
| fake_csv_path / true_csv_path | string | Paths to dataset files |
| started_at / finished_at | timestamps | |
| created_at / updated_at | timestamps | |

### Table: audit_logs
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| user_id | bigint (FK → users) | |
| action | string | e.g. 'login', 'training_started' |
| target_type | string | e.g. 'Dataset', 'TrainingJob' |
| target_id | bigint | |
| metadata | JSON | Extra context |
| ip_address | string | |
| created_at / updated_at | timestamps | |

### Relationships:
- users 1 ──→ ∞ predictions
- users 1 ──→ ∞ datasets
- users 1 ──→ ∞ training_jobs
- users 1 ──→ ∞ audit_logs

---

## 8. ALL API ENDPOINTS

### 8.1 Laravel Web Routes
| Method | URL | Controller | Auth | Description |
|---|---|---|---|---|
| GET | `/` | NewsCheckController@home | No | Landing page with verifier widget |
| POST | `/check` | NewsCheckController@check | No | Submit claim (text/URL/image) |
| GET | `/features` | View | No | Features page |
| GET | `/how-it-works` | View | No | How It Works page |
| GET | `/integrations` | View | No | Integrations page |
| GET | `/faq` | View | No | FAQ page |
| GET | `/contact` | View | No | Contact page |
| GET | `/dashboard` | Redirect | Auth | Redirects admin→admin, user→history |
| GET | `/history` | HistoryController@index | Auth | User's prediction history |
| DELETE | `/history/{id}` | HistoryController@destroy | Auth | Delete a prediction |
| GET | `/history/{id}/recheck` | HistoryController@recheck | Auth | Re-verify a past prediction |
| GET | `/profile` | ProfileController@edit | Auth | Edit profile page |
| PATCH | `/profile` | ProfileController@update | Auth | Update profile |
| DELETE | `/profile` | ProfileController@destroy | Auth | Delete account |
| GET | `/admin/` | AdminController@overview | Admin | Admin dashboard |
| GET | `/admin/users` | AdminController@users | Admin | User management |
| PATCH | `/admin/users/{id}/toggle` | AdminController@toggleUser | Admin | Enable/disable user |
| GET | `/admin/predictions` | AdminController@predictions | Admin | All predictions |
| GET | `/admin/audit` | AdminController@auditLogs | Admin | Audit log viewer |
| GET | `/admin/datasets` | AdminController@datasets | Admin | Dataset management |
| POST | `/admin/datasets` | AdminController@storeDataset | Admin | Upload dataset |
| POST | `/admin/datasets/{id}/train` | AdminController@trainDataset | Admin | Train from dataset |
| DELETE | `/admin/datasets/{id}` | AdminController@destroyDataset | Admin | Delete dataset |
| GET | `/admin/training` | TrainingController@index | Admin | Training console page |
| POST | `/admin/training/upload` | TrainingController@upload | Admin | Upload CSV files |
| POST | `/admin/training/start` | TrainingController@start | Admin | Start training job |
| GET | `/admin/training/status` | TrainingController@status | Admin | Poll training progress |

### 8.2 Flask ML API Endpoints (Port 5000)
| Method | URL | Description | Auth |
|---|---|---|---|
| GET | `/health` | Health check, model load status | IP-restricted |
| POST | `/predict` | Classify text → REAL/FAKE/UNCERTAIN | IP-restricted |
| POST | `/train/start` | Start background training job | IP-restricted |
| GET | `/train/status` | Get training job progress | IP-restricted |
| POST | `/train/reload` | Force reload model.pkl | IP-restricted |

### Flask /predict Request & Response:
**Request:** `POST /predict` with JSON body `{"text": "article text here..."}`
**Response:**
```json
{
  "label": "FAKE",
  "confidence": 94.32,
  "confidence_level": "HIGH",
  "model": "svm",
  "probabilities": {"REAL": 5.68, "FAKE": 94.32},
  "input_length": 523,
  "response_time_ms": 12.45
}
```

---

## 9. ML PIPELINE — DETAILED TECHNICAL BREAKDOWN

### 9.1 Dataset
- **Source:** Kaggle Fake and Real News Dataset (Clément Bisaillon)
- **URL:** https://www.kaggle.com/datasets/clmentbisaillon/fake-and-real-news-dataset
- **Files:** `Fake.csv` (~23,502 articles), `True.csv` (~21,417 articles)
- **Max rows per class:** 8,000 (configurable)
- **Total trained on:** 16,000 rows
- **Split:** 80% train / 20% test (stratified)

### 9.2 Text Preprocessing Pipeline
1. **clean_text():** Remove HTML tags → Remove URLs → Lowercase → Remove non-alpha characters → Normalize whitespace
2. **tokenize_and_stem():** Split into words → Remove English stopwords (NLTK) → Apply Porter Stemmer → Rejoin into string

### 9.3 Feature Extraction
- **Method:** TF-IDF Vectorization (TfidfVectorizer)
- **Max features:** 5,000
- **N-gram range:** (1, 2) — unigrams and bigrams

### 9.4 Models Trained (All Three Every Time)
| Model | Scikit-learn Class | Key Parameters |
|---|---|---|
| Logistic Regression | `LogisticRegression` | max_iter=1000, class_weight='balanced', random_state=42 |
| Naive Bayes | `MultinomialNB` | Default parameters |
| SVM | `LinearSVC` | class_weight='balanced', random_state=42, max_iter=3000 |

### 9.5 Model Selection
- All three models trained on same train split
- Evaluated on test split using **weighted F1-score**
- Best model by F1-score is saved as `model.pkl` via joblib
- Metadata saved in `model_meta.json`

### 9.6 Actual Training Results (from training_status.json)
| Model | Accuracy | F1 (weighted) |
|---|---|---|
| Logistic Regression | 99.31% | 0.9931 |
| Naive Bayes | 95.94% | 0.9594 |
| **SVM (Best)** | **99.78%** | **0.9978** |

- **Best Model:** SVM (LinearSVC)
- **Rows Trained:** 16,000
- **Training Duration:** ~2 min 45 sec

### 9.7 Prediction Logic
- Confidence threshold for UNCERTAIN: 0.55 (55%)
- If max probability < 0.55 → label = UNCERTAIN
- If REAL probability ≥ FAKE probability → label = REAL
- Else → label = FAKE
- Confidence levels: HIGH (≥75%), MEDIUM (60-74%), LOW (<60%)

### 9.8 ML Configuration (ml/config.json)
```json
{
  "dataset": "kaggle_fake_real_news",
  "uncertain_threshold": 0.55,
  "min_input_chars": 20,
  "max_input_chars": 10000,
  "max_tfidf_features": 5000,
  "test_size": 0.2,
  "random_state": 42,
  "models": ["logistic_regression", "naive_bayes", "svm"]
}
```

---

## 10. THREE INPUT MODES FOR CLAIM VERIFICATION

### Mode 1: Raw Text Input
- User types or pastes article text (20 – 10,000 characters)
- Text sent directly to Flask `/predict`

### Mode 2: URL Input
- User pastes a URL (e.g., `https://example.com/news-article`)
- Laravel's `ArticleExtractionService` fetches the page via HTTP
- Extracts main text content (strips HTML, scripts, navigation)
- Extracted text sent to Flask `/predict`

### Mode 3: Image Input (OCR)
- User uploads a screenshot/image of a news article (JPG/PNG, max 4MB)
- Laravel sends the image to **OCR Space API** (`https://api.ocr.space/parse/image`)
- Extracted text returned and sent to Flask `/predict`
- Minimum extracted text length required: 20 characters

---

## 11. USER ROLES & PERMISSIONS

### Guest (Unauthenticated)
- Can access: Home page, Features, How It Works, Integrations, FAQ, Contact
- Can run: Text check, URL check, Image check
- Predictions shown but **not saved** to history

### Registered User
- Everything Guest can do PLUS:
- Predictions **saved** to database with user_id
- Can view `/history` — chronological list of past checks
- Can delete individual history items
- Can re-check past predictions
- Can edit profile, change password, delete account

### Administrator (role = 'admin')
- Everything Registered User can do PLUS:
- Access `/admin/` dashboard with:
  - Overview stats (total users, predictions, datasets)
  - User management (view all users, toggle active/inactive)
  - All predictions viewer
  - Audit log viewer
  - Dataset management (upload, delete, view status)
  - Training console (upload CSVs, start training, monitor progress)

### Demo Accounts (from database seeder)
| Email | Password | Role |
|---|---|---|
| admin@fni.test | password | admin |
| test@example.com | password | user |

---

## 12. TEST RESULTS — COMPLETE BREAKDOWN

### 12.1 Laravel PHPUnit Tests — 51 PASSED (117 Assertions)
| Test Suite | Tests | Details |
|---|---|---|
| ExampleTest (Unit) | 1 | Basic truth assertion |
| AdminAccessTest | 3 | Guest blocked, regular user blocked, admin can access |
| AdminDatasetTest | 6 | Upload valid/invalid datasets, train from dataset, delete |
| AdminTrainingTest | 6 | Access control, upload validation, start training, status polling |
| AuthenticationTest | 4 | Login screen, authenticate, invalid password, logout |
| EmailVerificationTest | 3 | Render screen, verify email, invalid hash |
| PasswordConfirmationTest | 3 | Render screen, confirm valid, reject invalid |
| PasswordResetTest | 4 | Render screen, request link, render reset, reset password |
| PasswordUpdateTest | 2 | Update password, reject wrong current password |
| RegistrationTest | 2 | Render screen, register new user |
| ExampleTest (Feature) | 1 | Home page returns 200 |
| HistoryTest | 2 | View own history, delete own history |
| NewsCheckTest | 7 | Home loads, min-length validation, mocked ML check, logged-in save, guest save, URL check, OCR check, multiple-input rejection |
| ProfileTest | 5 | Display profile, update info, email verification unchanged, delete account, wrong password rejection |

### 12.2 Python Pytest Tests — 9 PASSED
| Test | Description |
|---|---|
| test_health | GET /health returns ok |
| test_predict_validation_empty | Empty text returns 422 |
| test_predict_validation_short | Short text returns 422 |
| test_predict_success_shape | Valid text returns correct JSON shape |
| test_predictor_unit | Predictor class returns expected label |
| test_predictor_svm_label_direction | SVM correctly separates REAL/FAKE |
| test_train_status_idle | Training status returns idle when not training |
| test_train_start_requires_dataset | Training without CSVs returns 422 |
| test_train_start_conflict_when_locked | Concurrent training returns 409 |

### 12.3 Summary
- **Total tests:** 60
- **Passed:** 60
- **Failed:** 0
- **Success rate:** 100%
- **Laravel duration:** 72.94 seconds
- **ML duration:** 36.99 seconds

---

## 13. FRONTEND PAGES LIST

| Page | URL | Layout | Body Class |
|---|---|---|---|
| Home (Landing) | `/` | frontend.blade.php | home-six |
| Features | `/features` | frontend.blade.php | innerpage |
| How It Works | `/how-it-works` | frontend.blade.php | innerpage |
| Integrations | `/integrations` | frontend.blade.php | innerpage |
| FAQ | `/faq` | frontend.blade.php | innerpage |
| Contact | `/contact` | frontend.blade.php | innerpage |
| Login | `/login` | auth-fni.blade.php | — |
| Register | `/register` | auth-fni.blade.php | — |
| User History | `/history` | fni.blade.php | — |
| User Profile | `/profile` | fni.blade.php | — |
| Admin Overview | `/admin/` | fni.blade.php | — |
| Admin Users | `/admin/users` | fni.blade.php | — |
| Admin Predictions | `/admin/predictions` | fni.blade.php | — |
| Admin Audit Logs | `/admin/audit` | fni.blade.php | — |
| Admin Datasets | `/admin/datasets` | fni.blade.php | — |
| Admin Training | `/admin/training` | fni.blade.php | — |

---

## 14. KEY CONFIGURATION VALUES

### Laravel .env
```
APP_NAME=FNI
APP_URL=http://localhost
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
FNI_ML_API_URL=http://127.0.0.1:5000
FNI_MIN_INPUT_CHARS=20
FNI_MAX_INPUT_CHARS=10000
FNI_ML_TIMEOUT=10
FNI_ML_TRAIN_TIMEOUT=30
FNI_URL_FETCH_TIMEOUT=10
FNI_URL_MAX_BYTES=524288
```

### PHP Local Config (php_local/php.ini)
```
upload_max_filesize = 128M
post_max_size = 256M
max_execution_time = 600
memory_limit = 512M
Extensions: curl, fileinfo, gd, mbstring, openssl, pdo_sqlite, sqlite3, zip
```

---

## 15. HOW TO RUN THE PROJECT

### Prerequisites:
- PHP 8.2+ (bundled in `php_local/` directory)
- Python 3.10+ with pip
- Node.js + NPM
- Internet connection (for OCR & URL extraction)

### Step 1: Laravel Setup
```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
```

### Step 2: ML Setup
```powershell
cd ml
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
python scripts/train.py
```

### Step 3: Run Both Servers
```powershell
# Terminal 1 — Flask ML API
cd ml
.venv\Scripts\activate
python api/app.py
# Runs on http://127.0.0.1:5000

# Terminal 2 — Laravel Web App
.\php_local\php.exe -S 127.0.0.1:8000 -t public vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
# Open http://127.0.0.1:8000
```

---

## 16. KEYWORDS & TOPICS FOR DOCUMENTATION

Use these keywords when prompting any AI for specific documentation sections:

- Fake news detection, misinformation, disinformation, fact-checking
- TF-IDF vectorization, text classification, NLP, natural language processing
- Support Vector Machine (SVM), Logistic Regression, Naive Bayes, LinearSVC
- Porter Stemmer, stopword removal, tokenization, text preprocessing
- Laravel 12, PHP 8.2, Blade templates, Eloquent ORM, Laravel Breeze
- Flask REST API, Python microservice, Scikit-learn pipeline
- OCR (Optical Character Recognition), OCR Space API, image text extraction
- URL scraping, article extraction, web content parsing
- SQLite, MySQL, database migrations, Eloquent relationships
- MVC pattern, service layer, dependency injection, pipeline pattern
- PHPUnit testing, pytest, automated testing, CI/CD
- Dual-server architecture, microservices, REST API communication
- Admin dashboard, model retraining, dataset management, audit logging
- Tailwind CSS, Alpine.js, Bootstrap 5, responsive design
- Kaggle Fake and Real News dataset, Clément Bisaillon
- Confusion matrix, F1-score, accuracy, precision, recall
- Background training job, subprocess, asynchronous processing
