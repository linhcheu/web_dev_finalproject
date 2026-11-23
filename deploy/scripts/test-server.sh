#!/usr/bin/env bash
# CareConnect quick TEST server - all three apps with `php artisan serve`.
# Use it to try the project on your Droplet's IPv4 before doing the full
# Apache setup (deploy/MISSION_4B_DEPLOY.md). Not for production.
#
#   Website      http://YOUR_IPV4:8000
#   Admin panel  http://YOUR_IPV4:8080
#   API          127.0.0.1:8081 (private, only the two apps call it)
#
# Needs: PHP 8.3+, Composer, MySQL with an empty database + user (guide section 5).
# Usage (from the repository root):
#   DB_USERNAME=linhcheu DB_PASSWORD='StrongPass#2026' bash deploy/scripts/test-server.sh
# Optional: SERVER_IP=1.2.3.4 (auto-detected), DB_DATABASE (default careconnect_db),
#           ADMIN_PASSWORD (default admin123), FRESH=1 to wipe and re-seed the data.
# Stop everything with Ctrl+C.

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

: "${DB_USERNAME:?Set DB_USERNAME (your MySQL user)}"
: "${DB_PASSWORD:?Set DB_PASSWORD (your MySQL password)}"
DB_DATABASE="${DB_DATABASE:-careconnect_db}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin123}"
SERVER_IP="${SERVER_IP:-$(curl -4 -s --max-time 5 ifconfig.me || hostname -I | awk '{print $1}')}"
export COMPOSER_ALLOW_SUPERUSER=1

say() { printf '\n\033[1;36m==> %s\033[0m\n' "$1"; }

# set_env FILE KEY VALUE  - replace or append KEY=VALUE in a .env file
set_env() {
    local file=$1 key=$2 value=$3
    if grep -q "^${key}=" "$file"; then
        KEY="$key" VALUE="$value" perl -pi -e 's/^\Q$ENV{KEY}\E=.*/$ENV{KEY}=$ENV{VALUE}/' "$file"
    else
        printf '%s=%s\n' "$key" "$value" >> "$file"
    fi
}

for app in api frontend backend; do
    say "$app: installing PHP packages"
    (cd "$app" && composer install --no-interaction --no-progress -q)
    [ -f "$app/.env" ] || cp "$app/.env.example" "$app/.env"
done

say "Configuring .env files for http://$SERVER_IP"
set_env api/.env DB_DATABASE "$DB_DATABASE"
set_env api/.env DB_USERNAME "$DB_USERNAME"
set_env api/.env DB_PASSWORD "\"$DB_PASSWORD\""
set_env api/.env ADMIN_PASSWORD "\"$ADMIN_PASSWORD\""
set_env api/.env APP_URL "http://127.0.0.1:8081"
set_env frontend/.env APP_URL "http://$SERVER_IP:8000"
set_env frontend/.env API_URL "http://127.0.0.1:8081"
set_env frontend/.env API_MEDIA_URL "http://$SERVER_IP:8000/storage"
set_env backend/.env APP_URL "http://$SERVER_IP:8080"
set_env backend/.env API_URL "http://127.0.0.1:8081"
set_env backend/.env API_MEDIA_URL "http://$SERVER_IP:8080/storage"
for app in api frontend backend; do
    grep -q '^APP_KEY=base64' "$app/.env" || (cd "$app" && php artisan key:generate -q)
    (cd "$app" && php artisan optimize:clear -q)
done

say "Database: tables + demo data"
if [ "${FRESH:-0}" = "1" ]; then
    (cd api && php artisan migrate:fresh --seed --force)
else
    (cd api && php artisan migrate --force)
    (cd api && php artisan tinker --execute='exit(\App\Models\Admin::count() ? 0 : 1);' >/dev/null 2>&1) \
        || (cd api && php artisan db:seed --force)
fi

# Uploaded pictures live in the API; both web apps serve them at /storage.
mkdir -p api/storage/app/public
ln -sfn "$ROOT/api/storage/app/public" frontend/public/storage
ln -sfn "$ROOT/api/storage/app/public" backend/public/storage

if command -v ufw >/dev/null && ufw status 2>/dev/null | grep -q "Status: active"; then
    say "Opening ports 8000 and 8080 in ufw"
    ufw allow 8000/tcp >/dev/null
    ufw allow 8080/tcp >/dev/null
fi

PIDS=()
cleanup() { say "Stopping"; kill "${PIDS[@]}" 2>/dev/null || true; }
trap cleanup EXIT INT TERM

(cd api      && PHP_CLI_SERVER_WORKERS=4 php artisan serve --host=127.0.0.1 --port=8081 >/dev/null 2>&1) & PIDS+=($!)
(cd frontend && PHP_CLI_SERVER_WORKERS=4 php artisan serve --host=0.0.0.0   --port=8000 >/dev/null 2>&1) & PIDS+=($!)
(cd backend  && PHP_CLI_SERVER_WORKERS=4 php artisan serve --host=0.0.0.0   --port=8080 >/dev/null 2>&1) & PIDS+=($!)

for _ in $(seq 1 30); do
    curl -s -o /dev/null http://127.0.0.1:8081/up && curl -s -o /dev/null http://127.0.0.1:8000/up && curl -s -o /dev/null http://127.0.0.1:8080/up && break
    sleep 1
done

say "CareConnect is running"
printf '  Website      http://%s:8000\n' "$SERVER_IP"
printf '  Admin panel  http://%s:8080   (admin@careconnect.com / your ADMIN_PASSWORD)\n' "$SERVER_IP"
printf '  Demo patient / hospital logins use the password 11111111\n'
printf '\nPress Ctrl+C to stop.\n'
wait
