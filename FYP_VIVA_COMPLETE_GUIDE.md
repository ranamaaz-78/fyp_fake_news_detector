# 🎓 VeriFact AI — Complete FYP Viva Guide & Detailed Documentation

> **Ye document tumhare FYP ke har ek chote se chote detail ko cover karta hai. Isko padh lo, toh viva mein koi bhi examiner ya supervisor sawal poochhe, tum aur tumhare dost confidently jawab de sakoge. Easy Urdu-English wording mein har feature, architecture, test cases aur ML concepts explained hain.**

---

## 📋 Table of Contents

1. [Project Identity (Kaun, Kya, Kahan)](#1--project-identity)
2. [Problem Statement (Masla Kya Hai)](#2--problem-statement)
3. [Project Summary & Abstract (Project Ka Khulasa)](#3--project-summary--abstract)
4. [System Architecture (System Kaise Kaam Karta Hai)](#4--system-architecture)
5. [Technology Stack (Konsi Technologies Use Ki Hain)](#5--technology-stack)
6. [Folder Structure (Files Kaise Organized Hain)](#6--folder-structure)
7. [Database Schema (Database Mein Kya Store Hota Hai)](#7--database-schema)
8. [ML Pipeline — Complete Detail (Machine Learning Kaise Kaam Karta Hai)](#8--ml-pipeline)
9. [Three Input Modes (3 Tareekon Se News Check Hoti Hai)](#9--three-input-modes)
10. [User Roles & Permissions (Kis Kisam Ke Users Hain)](#10--user-roles--permissions)
11. [All Features — Detailed Explanation (Har Feature Ka Detail)](#11--all-features)
12. [All API Endpoints (Saare API Routes)](#12--all-api-endpoints)
13. [Design Patterns Used (Konse Coding Patterns Use Kiye)](#13--design-patterns-used)
14. [Test Cases — Complete Detail (Testing Ka Poora Record)](#14--test-cases)
15. [Security Features (Security Kaise Handle Ki Hai)](#15--security-features)
16. [How to Run the Project (Project Kaise Chalana Hai)](#16--how-to-run-the-project)
17. [Viva Questions & Answers (Mumkin Sawalat Aur Jawab)](#17--viva-questions--answers)
18. [Future Scope (Aage Kya Kar Sakte Hain)](#18--future-scope)
19. [Quick Reference Card (Cheat Sheet)](#19--quick-reference-card)

---

## 1. 🆔 Project Identity

| Field | Detail |
|---|---|
| **Project Title** | VeriFact AI — Deep Learning-Powered Fact-Checking & Fake News Detection |
| **Alternate Name** | FNI — Fake News Identification |
| **University** | University of Central Punjab (UCP), Gujranwala Campus, Pakistan |
| **Faculty** | Faculty of Information Technology & Computer Science |
| **Degree** | Bachelor of Science in Computer Science (BSCS) |
| **Group ID** | G1F22FYPCS016 |
| **Supervisor** | Prof. Muzammil Sadiq |

### 👥 Team Members

| Reg # | Name | Role |
|---|---|---|
| G1F22UBSCS168 | Maaz Naveed | Team Member |
| G1F22UBSCS066 | Jazil Mehmood | Team Member |
| G1F22UBSCS174 | Muhammad Abrar | Team Member |

---

## 2. 🚨 Problem Statement

### Masla Kya Hai? (The Problem)

Aaj kal social media (Facebook, Twitter, WhatsApp) aur news websites par **fake news (jhooti khabren)** aur **misinformation** bohat tezi se phail rahi hain. Iske bohat se khatarnak asraat hotay hain:

- **Elections Manipulate Hoti Hain**: Jhooti khabron se public opinion change kiya jata hai.
- **Stock Market & Economy Impact Hoti Hai**: False rumors ki wajah se shares girte hain aur logon ka nuqsaan hota hai.
- **Social Unrest & Violence**: Ghalat fehmi phailane se riots aur fasaad hote hain.
- **Health Misinformation**: COVID-19 ke dauran dekha gaya ke ghalat ilaj aur fake claims se logon ki jaano ko khatra hua.

### Insaan Se Manual Fact-Checking Kyun Nahi Ho Sakti? (Why Automation?)

- **Volume Bohat High Hai**: Har minute lakhaun naye posts aur articles publish hote hain.
- **Manual Fact-Checking Slow Hai**: Ek human expert ko 1 article verify karne mein 15 se 30 minute lagte hain.
- **Scale Nahi Ho Sakta**: Unlimited fact-checkers hire nahi kiye ja sakte.
- **Delay Ka Nuqsaan**: Jab tak human fact-check kar ke bataye, tab tak fake news viral ho chuki hoti hai.

### Humara Solution (The Proposed System)

Humne **VeriFact AI** banaya hai jo ek **automated, real-time, AI-powered system** hai:
- **Milliseconds Mein Result**: Real-time mein check karta hai.
- **High Accuracy (99.78%)**: Advanced machine learning (SVM) use karta hai.
- **Three Input Modalities**: Raw text, Article URL, ya Screenshot/Image se analyze karta hai.
- **Transparent Output**: Single label (REAL, FAKE, UNCERTAIN) ke saath exact probabilistic confidence % batata hai.

---

## 3. 📝 Project Summary & Abstract

**VeriFact AI** ek dual-server web-based AI solution hai. Ye digital news claims aur full-length news content ko **REAL**, **FAKE**, ya **UNCERTAIN** mein classify karta hai. 

System ke 2 main components hain:
1. **Laravel 12 Web Portal**: User management, UI/UX, history logging, input extraction (URL scraping & OCR integration), aur admin management.
2. **Flask / Python ML Microservice**: NLP preprocessing pipeline (Text cleaning, Stopword removal, Porter Stemming), TF-IDF feature extraction, aur 3 ML models (Logistic Regression, Naive Bayes, LinearSVC).

Model automated retraining engine par chalta hai — teeno models ko train karta hai aur best weighted F1-score wale model ko production mein auto-deploy kar deta hai. Kaggle Fake and Real News dataset par system ne **99.78% accuracy** achieve ki hai.

---

## 4. 🏗️ System Architecture

### Dual-Server Microservices Architecture

Humne application ko **2 decoupled servers** mein divide kiya hai:

```
┌─────────────────────────────────────────────────────────────┐
│                   USER KA BROWSER                           │
│         (Tailwind CSS + Alpine.js + Bootstrap)              │
└──────────────────────┬──────────────────────────────────────┘
                       │ HTTP Request
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              SERVER 1: LARAVEL WEB APP                      │
│                    (Port 8000)                               │
│                                                             │
│  📁 Controllers:                                            │
│    • NewsCheckController — Main claim checking              │
│    • HistoryController — Prediction history & recheck       │
│    • ProfileController — User profile management            │
│    • Admin/DashboardController — Overview, Users, Audit     │
│    • Admin/TrainingController — ML Retraining console       │
│    • ContactController — Contact form submissions           │
│                                                             │
│  📁 Services:                                               │
│    • FakeNewsApiService — Communicates with Flask ML API    │
│    • ArticleExtractionService — Web scraping & HTML parse   │
│    • MlTrainingService — Orchestrates background jobs       │
│    • AuditLogger — Records admin actions                    │
│                                                             │
│  🔐 Auth: Laravel Breeze (Login, Register, Reset, Verify)   │
│  💾 Database: SQLite (local dev) / MySQL 8 (production)     │
└──────────────────────┬──────────────────────────────────────┘
                       │ REST API (JSON via HTTP)
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              SERVER 2: FLASK ML API                          │
│                    (Port 5000)                               │
│                                                             │
│  📡 Endpoints:                                              │
│    • GET  /health       — System health & model status      │
│    • POST /predict      — Preprocess & predict claim        │
│    • POST /train/start  — Launch background retraining      │
│    • GET  /train/status — Poll training progress            │
│    • POST /train/reload — Hot-reload updated model.pkl      │
│                                                             │
│  🧠 Engine:                                                 │
│    Text → Clean → Stem → TF-IDF Vectorizer → LinearSVC      │
│                                                             │
│  📦 Artifacts: model.pkl, model_meta.json, lock files       │
└─────────────────────────────────────────────────────────────┘
```

### Decoupled Microservices ke Faayde:

1. **Separation of Concerns**: Presentation/Web logic alag hai, Machine Learning computation alag hai.
2. **Independent Scaling**: ML service ko dedicated GPU/CPU server par scale kiya ja sakta hai bina Web server ko disturb kiye.
3. **Fault Tolerance**: Agar ML API temporary down bhi ho, web app crash nahi hoti (graceful error message dikhati hai).
4. **Tech-Stack Flexibility**: Laravel (PHP) best hai web framework ke liye, jabke Python best hai Data Science ke liye.

---

## 5. 💻 Technology Stack

### 5.1 Web Backend (Laravel 12)
- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Authentication**: Laravel Breeze (with Email Verification)
- **ORM**: Eloquent ORM
- **Templating**: Blade Engine
- **Dependencies**: `laravel/framework`, `laravel/breeze`, `laravel/tinker`

### 5.2 Frontend & UI
- **Styling**: Tailwind CSS 3.1, Bootstrap 5 (vendor)
- **Reactivity**: Alpine.js 3.4
- **Asset Bundler**: Vite 7.0
- **Animations**: GSAP, ScrollTrigger, WOW.js, Swiper.js
- **Typography & Icons**: FontAwesome 6, Google Fonts (Inter)

### 5.3 Machine Learning Microservice (Flask / Scikit-learn)
- **API Framework**: Flask 3.0+
- **Language**: Python 3.11 (3.10+ compatible)
- **ML Engine**: Scikit-learn 1.4+
- **NLP Toolkit**: NLTK 3.8+ (Porter Stemmer, English Stopwords, Punkt)
- **Data Manipulation**: Pandas 2.0+, NumPy 1.26+
- **Serialization**: joblib 1.3+
- **Testing**: pytest 8.0+

### 5.4 Database
- **Local / Dev**: SQLite (`database/database.sqlite`)
- **Production**: MySQL 8.0 (`fni_db`)

### 5.5 External Integrations
- **OCR Engine**: OCR Space REST API (`https://api.ocr.space/parse/image`)

---

## 6. 📁 Complete File & Folder Structure

```
fyp_fake_news_detector/
├── app/                              📂 Laravel Core Application
│   ├── Http/Controllers/
│   │   ├── NewsCheckController.php      → Claim verification router (text/url/image)
│   │   ├── HistoryController.php        → Logged-in user history & re-checking
│   │   ├── ProfileController.php        → Profile update & account deletion
│   │   ├── ContactController.php        → Contact form processor
│   │   └── Admin/
│   │       ├── DashboardController.php  → Overview, Users toggle, Audit logs, Datasets
│   │       └── TrainingController.php   → Dataset upload, retraining launcher, status polling
│   ├── Models/                          📂 Eloquent ORM Models
│   │   ├── User.php                     → Auth user (role: 'user' | 'admin')
│   │   ├── Prediction.php               → Stored verification logs
│   │   ├── Dataset.php                  → Uploaded CSV dataset meta
│   │   ├── TrainingJob.php              → Background retraining jobs
│   │   ├── AuditLog.php                 → Security & action trail
│   │   └── ContactMessage.php           → Submitted user inquiries
│   └── Services/                        📂 Business Services
│       ├── FakeNewsApiService.php        → HTTP client talking to Flask /predict
│       ├── ArticleExtractionService.php  → URL fetching, HTML DOM cleaning & scraping
│       ├── MlTrainingService.php         → Training job dispatcher
│       └── AuditLogger.php              → Helper for logging admin actions
├── ml/                               📂 Python Machine Learning API
│   ├── api/
│   │   └── app.py                       → Flask REST API server (5 endpoints)
│   ├── src/
│   │   ├── predictor.py                 → FakeNewsPredictor inference engine
│   │   ├── preprocess.py                → Text cleaning, stopword removal, stemming
│   │   ├── trainer.py                   → Automated training of LR, NB, SVM
│   │   └── training_status.py           → Status JSON & lock file manager
│   ├── scripts/
│   │   ├── train.py                     → CLI manual training script
│   │   └── run_training_job.py          → Asynchronous worker process
│   ├── models/
│   │   ├── model.pkl                    → Serialized best model pipeline
│   │   ├── model_meta.json              → Accuracy & model metadata
│   │   └── training_status.json         → Retraining status store
│   ├── data/raw/                        → Fake.csv & True.csv raw storage
│   ├── reports/                         → evaluation.json & confusion_matrix.txt
│   ├── tests/
│   │   └── test_api.py                  → 9 Pytest cases
│   ├── config.json                      → Hyperparameters & threshold config
│   └── requirements.txt                 → Pip requirements
├── resources/views/                  📂 Blade View Files
│   ├── layouts/                         → Frontend & Dashboard layouts
│   ├── news/                            → Public views & result pages (REAL, FAKE, UNCERTAIN)
│   ├── admin/                           → Admin panel views
│   ├── history/                         → Prediction history page
│   └── profile/                         → User profile management
├── database/                         📂 Database Migrations & Seeders
├── routes/                           📂 Application Routes
│   ├── web.php                          → Web portal routes
│   └── auth.php                         → Breeze authentication routes
├── tests/                            📂 PHPUnit Automated Test Suites
│   ├── Feature/                         → 7 Feature test suites (51 tests)
│   └── Unit/                            → Unit tests
├── public/                           📂 Public Web Assets & Service Worker
│   ├── service-worker.js                → Progressive Web App offline worker
│   └── manifest.json                    → Web App Manifest
└── FYP_VIVA_COMPLETE_GUIDE.md        → Complete Viva Documentation
```

---

## 7. 💾 Database Schema & Tables

### Table 1: `users`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | bigint | Primary Key, Auto-increment | Unique user ID |
| `name` | string | Required | Full name |
| `username` | string | Unique, Nullable | System username |
| `email` | string | Unique, Required | Email address |
| `password` | string | Required | Hashed via Bcrypt |
| `role` | enum | 'user', 'admin' (default: 'user') | Access control role |
| `is_active` | boolean | default: true | Account status |
| `email_verified_at`| timestamp | Nullable | Email verification timestamp |
| `created_at / updated_at` | timestamp | Nullable | Timestamps |

### Table 2: `predictions`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | bigint | Primary Key, Auto-increment | Prediction ID |
| `user_id` | bigint | FK → users.id, Nullable | Null for Guest checks |
| `input_text` | text | Required | News text analyzed |
| `result` | enum | 'REAL', 'FAKE', 'UNCERTAIN' | Classification result |
| `confidence` | decimal(5,2)| Required | Probability % (e.g. 94.32) |
| `confidence_level`| string | Nullable | 'HIGH', 'MEDIUM', 'LOW' |
| `model_used` | string | Required | Classifier identifier ('svm') |
| `created_at / updated_at` | timestamp | Nullable | Timestamps |

### Table 3: `datasets`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | bigint | Primary Key | Dataset ID |
| `uploaded_by` | bigint | FK → users.id | Admin user ID |
| `filename` | string | Required | Disk storage path |
| `original_name` | string | Required | Original upload name |
| `row_count` | integer | default: 0 | Number of dataset rows |
| `status` | enum | 'pending', 'processed', 'failed' | Validation status |
| `notes` | text | Nullable | Validation summary notes |

### Table 4: `training_jobs`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | bigint | Primary Key | Job ID |
| `started_by` | bigint | FK → users.id | Admin user ID |
| `ml_job_id` | string | UUID | Unique job tracking GUID |
| `status` | enum | 'queued', 'running', 'completed', 'failed' | Retraining state |
| `progress` | tinyint | 0 - 100 | Percentage progress |
| `stage` | string | Nullable | Progress stage message |
| `metrics` | json | Nullable | Evaluation metrics JSON |
| `error_message` | text | Nullable | Failure reason if failed |
| `fake_csv_path` | string | Required | Path to Fake CSV |
| `true_csv_path` | string | Required | Path to True CSV |

### Table 5: `audit_logs`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | bigint | Primary Key | Log ID |
| `user_id` | bigint | FK → users.id, Nullable | Admin user ID |
| `action` | string | Required | e.g. 'training.started' |
| `target_type` | string | Nullable | Entity model class |
| `target_id` | bigint | Nullable | Entity ID |
| `metadata` | json | Nullable | Context metadata |
| `ip_address` | string | Nullable | Client IP address |

### Table 6: `contact_messages`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | bigint | Primary Key | Message ID |
| `name` | string | Required | Sender name |
| `email` | string | Required | Sender email |
| `company` | string | Required | Company name |
| `job_title` | string | Required | Job designation |
| `company_size` | string | Required | Company headcount |
| `industry` | string | Nullable | Industry vertical |
| `message` | text | Required | Inquiry content |
| `attachment_path`| string | Nullable | Uploaded attachment path |

---

## 8. 🧠 ML Pipeline — Complete Breakdown

### 8.1 Dataset Description
- **Dataset Name**: Kaggle Fake & Real News Dataset (by Clément Bisaillon).
- **Files**: `Fake.csv` (~23,502 rows) and `True.csv` (~21,417 rows).
- **Trained Rows**: 16,000 rows (8,000 REAL + 8,000 FAKE balanced).
- **Split Ratio**: 80% Training Data (12,800) / 20% Testing Data (3,200) with **Stratified** sampling.

### 8.2 NLP Preprocessing Pipeline

Every input text undergoes two rigorous transformations in `ml/src/preprocess.py`:

#### 1. `clean_text(text)`
- **HTML Removal**: Strips markup tags (`<p>`, `<div>`).
- **URL Stripping**: Removes web links (`http://...`, `https://...`).
- **Lowercase Normalization**: Converts all characters to lower-case.
- **Publisher Watermark Removal**: Strips agency names at article beginnings (e.g. `WASHINGTON (Reuters) -`, `LONDON (Reuters) -`, `BBC -`).
  > **Why Publisher Removal is Critical**: Kaggle's `True.csv` articles almost all contain "Reuters" in the first sentence. If uncleaned, any machine learning model will simply learn that "Reuters = REAL", causing **Data Leakage**. Removing publisher names forces the model to learn actual semantic content.
- **Symbol & Number Removal**: Strips punctuation and digits, leaving alpha characters.
- **Whitespace Collapsing**: Trims extra spacing.

#### 2. `tokenize_and_stem(text)`
- **Tokenization**: Splits cleaned text string into individual word tokens.
- **Stopword Filtering**: Removes standard NLTK English stopwords (`is`, `the`, `and`, `at`, `which`).
- **Porter Stemming**: Applies `nltk.stem.PorterStemmer()` to map words to root forms (`running` → `run`, `policies` → `polici`, `announced` → `announc`).

### 8.3 Feature Extraction (TF-IDF Vectorization)
The system fits `sklearn.feature_extraction.text.TfidfVectorizer`:
- `max_features`: 10,000 top n-grams.
- `ngram_range`: `(1, 2)` — extracts both single words (unigrams) and 2-word phrases (bigrams).
- `min_df`: 3 (word must appear in at least 3 documents).
- `max_df`: 0.9 (words appearing in >90% documents are dropped).

### 8.4 Trained Classification Algorithms

The training module (`ml/src/trainer.py`) builds and fits three distinct Scikit-learn Pipelines:

1. **Logistic Regression (`LogisticRegression`)**:
   - `max_iter`: 1000, `class_weight`: 'balanced', `random_state`: 42.
   - Calculates calibrated log-odds boundary between classes.
2. **Multinomial Naive Bayes (`MultinomialNB`)**:
   - Probabilistic baseline model based on Bayes Theorem.
3. **Linear Support Vector Machine (`LinearSVC`)**:
   - `class_weight`: 'balanced', `random_state`: 42, `max_iter`: 3000.
   - Finds optimal maximum-margin hyperplane separating REAL and FAKE embeddings.

### 8.5 Model Evaluation & Selection

All 3 models are evaluated against the unseen 20% test split. The model with the **highest weighted F1-Score** is automatically selected and saved as `model.pkl`.

#### Benchmark Results (Actual Trained Metrics):
| Algorithm | Accuracy | Weighted F1-Score | Status |
|---|---|---|---|
| Logistic Regression | 99.31% | 0.9931 | Evaluated |
| Multinomial Naive Bayes | 95.94% | 0.9594 | Evaluated |
| **Linear Support Vector Machine (LinearSVC)** | **99.78%** | **0.9978** | 🏆 **BEST (Selected & Serialized)** |

### 8.6 Decision Logic & Uncertainty Threshold

In `ml/src/predictor.py`:
- Probabilities for SVM are generated using **Platt Scaling** (converting `decision_function` margin distance to calibrated probabilities via sigmoid transform).
- `uncertain_threshold` = `0.55` (55%).
- **Rule Engine**:
  - If $\max(P(\text{REAL}), P(\text{FAKE})) < 0.55 \implies \text{Result} = \mathbf{UNCERTAIN}$
  - Else If $P(\text{REAL}) \ge P(\text{FAKE}) \implies \text{Result} = \mathbf{REAL}$
  - Else $\implies \text{Result} = \mathbf{FAKE}$

---

## 9. 📥 Three Input Modes Explained

```
                  ┌──────────────────────────────┐
                  │      News Verification       │
                  └──────────────┬───────────────┘
                                 │
         ┌───────────────────────┼───────────────────────┐
         ▼                       ▼                       ▼
   ┌───────────┐           ┌───────────┐           ┌───────────┐
   │ 1. TEXT   │           │  2. URL   │           │ 3. IMAGE  │
   └─────┬─────┘           └─────┬─────┘           └─────┬─────┘
         │                       │                       │
   Direct Body           Scrape & Clean           OCR Space API
   (20-10,000)           DOM HTML Content         Image Text Extract
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 ▼
                     REST POST /predict (Flask)
```

### Mode 1: Direct Raw Text Input
- User pastes article text (20 to 10,000 characters).
- Controller directly forwards text to Flask ML `/predict`.

### Mode 2: Article URL Extraction
- User submits external news link (e.g. `https://news-site.com/article-1`).
- `ArticleExtractionService.php` validates URL against **SSRF Attacks** (blocks private IPs, `localhost`, non-HTTP schemes).
- Fetches HTML using Guzzle HTTP client (10s timeout, max 512KB).
- Parses HTML via DOMDocument: strips `<script>`, `<style>`, `<nav>`, `<footer>`, `<header>`, `<aside>`.
- Extracts core text from `<article>`, `<main>`, or combined `<p>` paragraphs.

### Mode 3: Image OCR (Optical Character Recognition)
- User uploads screenshot or image snippet of news article (JPG/PNG, max 4MB).
- Laravel forwards image to **OCR Space API** (`https://api.ocr.space/parse/image`).
- Extracted raw text is validated (min 20 chars) and sent to ML engine.

---

## 10. 👥 User Roles & Permissions Matrix

| Capability / Route | Guest (Public) | Registered User | Administrator (`role=admin`) |
|---|:---:|:---:|:---:|
| View Landing, Features, FAQ, Contact | ✅ | ✅ | ✅ |
| Execute Claims Check (Text/URL/Image) | ✅ | ✅ | ✅ |
| Save Check to Database History | ❌ (saved as guest) | ✅ (tied to user ID) | ✅ (tied to user ID) |
| View Personal History (`/history`) | ❌ | ✅ | ✅ |
| Delete Personal History Entry | ❌ | ✅ | ✅ |
| Re-check Past Prediction | ❌ | ✅ | ✅ |
| Profile & Password Management | ❌ | ✅ | ✅ |
| Access Admin Dashboard (`/admin`) | ❌ (Redirect to login) | ❌ (403 Forbidden) | ✅ |
| Toggle User Account Status | ❌ | ❌ | ✅ |
| View System Audit Logs | ❌ | ❌ | ✅ |
| Manage & Retrain ML Datasets | ❌ | ❌ | ✅ |

---

## 11. ⭐ All Features — Detailed Explanation

1. **Multimodal Claims Checking**: Instant classification across raw text, web links, and images.
2. **Explainable ML Metrics**: Displays percentage confidence scores, trust level badges (High $\ge 75\%$, Medium $60-74\%$, Low $<60\%$), and execution time in milliseconds.
3. **Personalized History Panel**: Chronological table showing past checks, search previews, date, model used, and instant deletion or re-checking capabilities.
4. **Admin Overview Dashboard**: Visual stats overview (Total checks, Real count, Fake count, Uncertain count, User metrics) and recent activity feed.
5. **User Management Console**: Enables admins to search users, review prediction counts, and toggle accounts active/inactive.
6. **Dataset Retraining Hub (`/admin/training`)**: Allows uploading custom labeled `Fake.csv` and `True.csv` datasets, enforcing minimum row counts (100 rows).
7. **Asynchronous Background Training**: Spawns non-blocking sub-processes for training routines, keeping the web application responsive.
8. **Live AJAX Progress Polling**: Admin UI polls progress every 2 seconds, displaying dynamic progress bars ($0\% \to 100\%$) and active stage messages.
9. **Audit Trail System**: Automated logging of security events (logins, account status toggles, dataset uploads, training jobs) with IP tracking.
10. **Offline PWA Support**: Service worker (`service-worker.js`) pre-caches core static assets and renders a sleek custom `/offline` page when internet connection drops.

---

## 12. 🔌 All API Endpoints Reference

### 12.1 Laravel Web Portal Endpoints

#### Public Routes
- `GET /` — Landing page with verifier widget.
- `POST /check` — Claim verification handler.
- `GET /features`, `/how-it-works`, `/faq`, `/contact`, `/offline` — Information pages.
- `POST /contact` — Contact form submission.

#### Authenticated User Routes (`middleware: auth, verified`)
- `GET /dashboard` — Dashboard router (Admin $\to$ Admin Overview, User $\to$ History).
- `GET /history` — View user's prediction history.
- `DELETE /history/{prediction}` — Delete history item.
- `GET /history/{prediction}/recheck` — Re-evaluate past text.
- `GET /profile`, `PATCH /profile`, `DELETE /profile` — Profile management.

#### Admin Routes (`middleware: auth, verified, admin`)
- `GET /admin/` — Overview metrics.
- `GET /admin/users` — User list & search.
- `PATCH /admin/users/{user}/toggle` — Enable/disable account.
- `GET /admin/predictions` — System-wide prediction browser.
- `GET /admin/audit` — Security audit trail viewer.
- `GET /admin/datasets`, `POST /admin/datasets`, `DELETE /admin/datasets/{id}` — Dataset management.
- `POST /admin/datasets/{id}/train` — Trigger training from dataset.
- `GET /admin/training` — Retraining console.
- `POST /admin/training/upload` — Upload `Fake.csv` & `True.csv`.
- `POST /admin/training/start` — Launch training background task.
- `GET /admin/training/status` — Status polling endpoint.
- `GET /admin/contacts`, `DELETE /admin/contacts/{id}` — Manage contact submissions.

### 12.2 Flask ML Microservice Endpoints (Port 5000)
- `GET /health` — Returns `{"status": "ok", "model_loaded": true}`.
- `POST /predict` — Accepts `{"text": "..."}`, returns label, confidence %, probabilities, response time.
- `POST /train/start` — Starts background training job subprocess.
- `GET /train/status` — Returns current training progress JSON.
- `POST /train/reload` — Hot-reloads `model.pkl` into memory.

---

## 13. 📐 Software Design Patterns Applied

1. **MVC (Model-View-Controller)**: Strictly decouples Eloquent Models, Blade Views, and Controllers.
2. **Service Layer Pattern**: Business logic isolated into dedicated Service classes (`ArticleExtractionService`, `FakeNewsApiService`, `MlTrainingService`, `AuditLogger`).
3. **Dependency Injection (DI)**: Laravel Container auto-injects services directly into controller constructors.
4. **Pipeline Pattern**: Scikit-learn `Pipeline` objects bundle vectorizer and model into a single atomic execution unit.
5. **Repository Pattern (via Eloquent)**: Abstract database interaction layers away from SQL queries.

---

## 14. 🧪 Automated Test Cases — 60 Tests (100% Pass Rate)

### 14.1 Laravel PHPUnit Test Suites — 51 PASSED (117 Assertions)

1. **`AdminAccessTest` (3 tests)**:
   - `test_guest_cannot_access_admin`: Redirects unauthenticated guest to login.
   - `test_regular_user_cannot_access_admin`: Returns 403 Forbidden for non-admin user.
   - `test_admin_can_access_overview`: Returns 200 OK and renders overview for admin.
2. **`AdminDatasetTest` (6 tests)**:
   - Validates CSV dataset upload, rejects invalid filetypes, requires files, handles dataset deletion, and validates dataset training triggers.
3. **`AdminTrainingTest` (6 tests)**:
   - Tests access control, filename enforcement (`Fake.csv`, `True.csv`), missing file checks, and status polling responses.
4. **`AuthenticationTest` (4 tests)**:
   - Login view rendering, authenticating valid credentials, rejecting invalid passwords, and user logout.
5. **`EmailVerificationTest` (3 tests)**:
   - Verification screen rendering, verifying email via link, rejecting invalid hash.
6. **`PasswordConfirmationTest` (3 tests)**:
   - Confirm password screen render, valid confirmation, invalid password rejection.
7. **`PasswordResetTest` (4 tests)**:
   - Reset link screen render, requesting link, reset screen render, token password reset.
8. **`PasswordUpdateTest` (2 tests)**:
   - Password updating with correct current password, rejection with incorrect password.
9. **`RegistrationTest` (2 tests)**:
   - Registration screen rendering, registering new user.
10. **`HistoryTest` (2 tests)**:
    - User viewing personal history, user deleting personal history item.
11. **`NewsCheckTest` (7 tests)**:
    - Home page rendering, min-length validation error, mocked ML API prediction verification, logged-in prediction database save, guest prediction database save, URL extraction & prediction, rejecting combined input methods.
12. **`ProfileTest` (5 tests)**:
    - Display profile, update info, email verification state preservation, account deletion with valid password, account deletion rejection with invalid password.
13. **`ExampleTest` (2 tests)**:
    - Basic unit & feature sanity checks.

### 14.2 Python Pytest Suites — 9 PASSED

1. `test_health`: Verifies GET `/health` endpoint status.
2. `test_predict_validation_empty`: Asserts 422 code on empty payload.
3. `test_predict_validation_short`: Asserts 422 code on text $< 20$ chars.
4. `test_predict_success_shape`: Validates full response payload schema.
5. `test_predictor_unit`: Direct unit test of `FakeNewsPredictor` class.
6. `test_predictor_svm_label_direction`: Regression test ensuring LinearSVC decision function correctly maps positive/negative margins to REAL/FAKE classes.
7. `test_train_status_idle`: Checks initial idle training status JSON.
8. `test_train_start_requires_dataset`: Asserts 422 code when datasets missing.
9. `test_train_start_conflict_when_locked`: Asserts 409 Conflict when concurrent training requested.

---

## 15. 🔒 Security Features Implemented

1. **Bcrypt Password Hashing**: Passwords are cryptographically hashed using standard Bcrypt.
2. **SSRF (Server-Side Request Forgery) Mitigation**: `ArticleExtractionService` inspects resolved DNS IPs and blocks requests targeting private IP ranges (`127.0.0.0/8`, `10.0.0.0/8`, `192.168.0.0/16`, `172.16.0.0/12`) and `localhost`.
3. **CSRF Protection**: All POST/PATCH/DELETE forms contain `@csrf` token validation.
4. **Restricted ML API Access**: Flask API enforces host checking (`127.0.0.1` internal communication only).
5. **Concurrency Mutex Locking**: Training process uses file locks (`.training.lock`) to prevent race conditions during model retraining.
6. **Input Sanitization**: HTML tag stripping and character length limits prevent XSS and buffer overflow exploits.

---

## 16. 🚀 Step-by-Step Execution Guide

### Step 1: Web Application Setup (Laravel)
```powershell
# Install PHP packages
composer install

# Environment configuration
copy .env.example .env
php artisan key:generate

# Run database migrations and seed default credentials
php artisan migrate:fresh --seed

# Build frontend assets
npm install
npm run build
```

### Step 2: ML Service Setup (Python)
```powershell
cd ml
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
python scripts/train.py
```

### Step 3: Launch Both Servers

**Terminal 1 (Flask ML API):**
```powershell
cd ml
.venv\Scripts\activate
python api/app.py
# Runs on http://127.0.0.1:5000
```

**Terminal 2 (Laravel Portal):**
```powershell
.\php_local\php.exe -S 127.0.0.1:8000 -t public vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
# Open http://127.0.0.1:8000 in browser
```

---

## 17. ❓ Expected Viva Questions & Answers

### Q1: What is the main objective of your FYP?
**Answer:** The objective of VeriFact AI is to provide an automated, real-time fact-checking system that classifies digital news text into REAL, FAKE, or UNCERTAIN classes with probabilistic confidence scores, reducing the latency and scalability bottlenecks of manual fact-checking.

### Q2: Why did you choose Support Vector Machine (LinearSVC) over Deep Learning or Naive Bayes?
**Answer:** We evaluated Logistic Regression, Naive Bayes, and LinearSVC on the Kaggle dataset. LinearSVC achieved the highest weighted F1-Score of **99.78%** due to its ability to construct an optimal high-dimensional separating hyperplane for sparse TF-IDF text vectors, outperforming Naive Bayes (95.94%) with minimal computational overhead compared to heavy deep learning models.

### Q3: Why did you strip publisher names (like "Reuters") during text preprocessing?
**Answer:** In the Kaggle dataset, almost all true news articles originate from Reuters. If publisher names are left in, models suffer from **Data Leakage** — learning to classify based solely on the word "Reuters" rather than the underlying news content. Removing publisher markers ensures true semantic learning.

### Q4: How does your system handle URLs and Images?
**Answer:** For URLs, our `ArticleExtractionService` fetches HTML, strips navigation/scripts, extracts main article DOM content, and checks against SSRF vulnerabilities. For Images, we integrate the external OCR Space API to perform Optical Character Recognition, extracting text before sending it to our ML pipeline.

### Q5: What is the purpose of the UNCERTAIN class and how is it triggered?
**Answer:** To avoid false confidence on ambiguous input, if the maximum predicted class probability is below our tuned confidence threshold (55%), the system categorizes the claim as **UNCERTAIN**, informing the user that the claim requires further verification.

---

## 18. 🔮 Future Scope

1. **Transformer Models**: Upgrade ML microservice to integrate pre-trained BERT/RoBERTa transformers for enhanced contextual nuance.
2. **Multilingual Support**: Extend NLP pipeline to support Urdu and regional languages.
3. **Browser Extension**: Develop Chrome/Firefox extensions for one-click verification while browsing.
4. **Multi-Modal Verification**: Incorporate deepfake image and audio analysis alongside text classification.

---

## 19. 📊 Quick Reference Card

```
╔══════════════════════════════════════════════════════════════╗
║                    VERIFACT AI — CHEAT SHEET                 ║
╠══════════════════════════════════════════════════════════════╣
║ Frameworks  : Laravel 12 (PHP 8.2) + Flask (Python 3.11)    ║
║ ML Engine   : Scikit-learn LinearSVC (SVM)                  ║
║ Accuracy    : 99.78% F1-Score (16,000 Kaggle articles)       ║
║ Inputs      : Direct Text, URL Scraping, Image OCR           ║
║ Features    : TF-IDF (10,000 features, Unigram + Bigram)     ║
║ Security    : SSRF Protection, Bcrypt, CSRF, Audit Logs      ║
║ Tests       : 60 Automated Tests (51 PHPUnit + 9 Pytest)     ║
║ Group ID    : G1F22FYPCS016 · Supervisor: Prof. Muzammil Sadiq ║
╚══════════════════════════════════════════════════════════════╝
```
