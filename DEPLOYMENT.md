# FNI Deployment Guide (LAMP + Flask)

## Architecture

| Service | Port | Notes |
|---------|------|-------|
| Laravel (Apache/Nginx) | 80/443 | Public web app |
| Flask ML API | 5000 | **Internal only** — bind `127.0.0.1` |
| MySQL | 3306 | Users, predictions, audit logs |

## Local development (Windows / WAMP)

### 1. WAMP MySQL setup (one-time)

1. Start **WAMP** — MySQL service should be green.
2. Enable the PHP **`pdo_mysql`** extension (WAMP tray → PHP → PHP extensions).
3. Create the database in **phpMyAdmin** or MySQL CLI:

```sql
CREATE DATABASE fni_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Default WAMP credentials (adjust if you changed them):

| Setting | Value |
|---------|-------|
| Host | `127.0.0.1` |
| Port | `3306` |
| User | `root` |
| Password | *(empty, or your WAMP password)* |

MySQL CLI example (WAMP path may vary by version):

```powershell
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS fni_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2. Laravel `.env`

Copy `.env.example` to `.env` if needed, then set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fni_db
DB_USERNAME=root
DB_PASSWORD=

FNI_ML_API_URL=http://127.0.0.1:5000
```

For quick dev without WAMP, use SQLite instead (see commented block in `.env.example`).

### 3. Install and migrate

```powershell
cd d:\fyp_fake_news_detector
composer install
cp .env.example .env   # if .env does not exist
php artisan key:generate
php artisan config:clear
php artisan migrate:fresh --seed
php artisan db:show
npm install && npm run build
```

Verify seeded users:

```powershell
php artisan tinker --execute="echo App\Models\User::count();"
```

Expected output: `2` (admin + test user).

### 4. ML API

```powershell
cd ml
pip install -r requirements.txt
python scripts/train.py
python api/app.py
```

### 5. Run the app

```powershell
# Terminal 1 — Flask (from ml/)
python api/app.py

# Terminal 2 — Laravel (project root)
php artisan serve
```

Open http://127.0.0.1:8000

### 6. WAMP Apache (optional FYP demo)

Instead of `php artisan serve`, point Apache at the Laravel `public/` folder:

1. Enable **`mod_rewrite`** in WAMP.
2. Add a virtual host in `httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName fni.local
    DocumentRoot "d:/fyp_fake_news_detector/public"
    <Directory "d:/fyp_fake_news_detector/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Add `127.0.0.1 fni.local` to `C:\Windows\System32\drivers\etc\hosts`.
4. Restart Apache.

Flask still runs separately: `python ml/api/app.py`

### Common WAMP / MySQL errors

| Error | Fix |
|-------|-----|
| `SQLSTATE[1045] Access denied` | Check `DB_USERNAME` / `DB_PASSWORD` in `.env` |
| `Unknown database 'fni_db'` | Run the `CREATE DATABASE` SQL above |
| `could not find driver` | Enable `pdo_mysql` in WAMP PHP extensions |
| `1071 Specified key was too long` | Already handled via `Schema::defaultStringLength(191)` in `AppServiceProvider` |
| ML API connection failed | Start Flask on port 5000; confirm `FNI_ML_API_URL` |
| Training stuck / no progress | Ensure Flask API is running; only one training job at a time |
| `The POST data is too large` | Increase PHP limits (see below) — default WAMP is only 2M/8M |

### PHP upload limits (required for large CSV)

Kaggle `Fake.csv` / `True.csv` are large. WAMP default PHP limits are too small (`upload_max_filesize=2M`, `post_max_size=8M`).

**Option A — WAMP php.ini (recommended for Apache demo):**

1. WAMP tray → **PHP** → **php.ini**
2. Set:
   ```ini
   upload_max_filesize = 128M
   post_max_size = 256M
   max_execution_time = 600
   memory_limit = 512M
   ```
3. **Restart WAMP** (all services)

**Option B — `php artisan serve` with project php.ini:**

```powershell
cd d:\fyp_fake_news_detector
php -c php.ini artisan serve
```

The repo includes [`php.ini`](php.ini) and [`public/.user.ini`](public/.user.ini) with these values. After changing limits, restart PHP/Apache.

Verify:

```powershell
php -i | findstr /I "post_max_size upload_max_filesize"
```

### Admin model training (automated)

1. Login as admin → **Model Training** (`/admin/training`)
2. Upload Kaggle-style `Fake.csv` and `True.csv` (100+ rows each)
3. Click **Start Training** — runs in background (5–15 min)
4. Progress bar polls Flask `/train/status` automatically
5. On completion, new `ml/models/model.pkl` is loaded without restarting Flask manually

Requires migration: `php artisan migrate` (creates `training_jobs` table).

### Automated tests vs local DB

`php artisan test` uses **SQLite in-memory** (see `phpunit.xml`) — WAMP does not need to be running for tests.

- **Dev / demo:** MySQL (`fni_db` on WAMP)
- **CI / tests:** SQLite `:memory:`

## Production (Ubuntu LAMP)

### MySQL

```sql
CREATE DATABASE fni_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fni'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL ON fni_db.* TO 'fni'@'localhost';
```

`.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=fni_db
DB_USERNAME=fni
DB_PASSWORD=strong_password
APP_ENV=production
APP_DEBUG=false
```

### Flask systemd service

`/etc/systemd/system/fni-ml.service`:

```ini
[Unit]
Description=FNI Flask ML API
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/fni/ml
Environment=FNI_ML_PORT=5000
ExecStart=/var/www/fni/ml/.venv/bin/python api/app.py
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now fni-ml
```

### Laravel

- Point Apache/Nginx document root to `public/`
- Run `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`
- Ensure `storage/` and `bootstrap/cache/` are writable

### Security

- Do not expose port 5000 publicly
- Use HTTPS for Laravel
- Flask accepts requests from localhost only (see `FNI_ALLOW_ALL` in dev only)

## Default seeded accounts

| Email | Password | Role |
|-------|----------|------|
| admin@fni.test | password | admin |
| test@example.com | password | user |

Change passwords before production deployment.

## Verification

```bash
curl http://127.0.0.1:5000/health
php artisan test
pytest ml/tests/ -v
php artisan db:show
```
