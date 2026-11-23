# CareConnect

Hospital appointment platform: patients find hospitals and book appointments, hospitals manage
their bookings, and platform admins manage everything. Three Laravel 13 apps:

| Folder | App | Default local URL | Database |
|---|---|---|---|
| [`api/`](api/) | REST API (Sanctum tokens) | http://127.0.0.1:8081/api/v1 | **MySQL (only this app)** |
| [`frontend/`](frontend/) | Website: patients + hospital admins | http://127.0.0.1:8000 | none, calls the API |
| [`backend/`](backend/) | Admin panel: platform admins | http://127.0.0.1:8080 | none, calls the API |

```
browser ──▶ frontend ──┐
                       ├──HTTP + Bearer token──▶ api ──▶ MySQL
browser ──▶ backend  ──┘
```

## Run it locally

Requirements: PHP 8.3+ (8.4 recommended), Composer 2, MySQL 8.

```bash
# 1. API (creates the tables and demo data)
cd api
composer install
cp .env.example .env            # set DB_DATABASE / DB_USERNAME / DB_PASSWORD
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --port=8081

# 2. Website (new terminal)
cd frontend
composer install
cp .env.example .env && php artisan key:generate
php artisan serve --port=8000

# 3. Admin panel (new terminal)
cd backend
composer install
cp .env.example .env && php artisan key:generate
php artisan serve --port=8080
```

Demo logins: admin panel `admin@careconnect.com` / `admin123` (from `ADMIN_EMAIL` / `ADMIN_PASSWORD`).
Seeded patients and hospital admins use the password `11111111`.

## Tests

```bash
cd api && php artisan test        # security rules, bookings, uploads (SQLite in memory)
cd frontend && php artisan test   # API integration (API answers are faked)
cd backend && php artisan test
```

## Deploy to a DigitalOcean Droplet (Cloud Computing Mission 4b)

Follow **[deploy/MISSION_4B_DEPLOY.md](deploy/MISSION_4B_DEPLOY.md)**. The `deploy/` folder also holds the Apache virtual hosts, the production `.env` templates and a final-check script.
