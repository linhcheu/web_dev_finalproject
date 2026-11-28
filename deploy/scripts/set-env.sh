#!/usr/bin/env bash
# CareConnect - fill the three .env files with your REAL values (no placeholders).
# Mission 4b (one server) or Mission 5A (app server + DB server).
#
# Run on the APP server as root, after the composer/.env loop (guide step F9):
#   DB=5.6.7.8 MYAPP=1.2.3.4 DBPASS='YourPass#2026' ADMINPASS='AdminPass#2026' \
#     bash /var/www/careconnect/deploy/scripts/set-env.sh
#
#   DB         IP of the MySQL server (Mission 4b: 127.0.0.1, Mission 5A: the DB server's IP)
#   MYAPP      public IPv4 of THIS (app) server
#   DBPASS     the MySQL password you gave 'careconnect' on the DB server
#   ADMINPASS  the admin-panel login password (admin@careconnect.com)
# Passwords: letters, numbers and # only (no | & " ' $ \ ).

set -euo pipefail

: "${DB:?Set DB (database server IP)}"
: "${MYAPP:?Set MYAPP (public IPv4 of this server)}"
: "${DBPASS:?Set DBPASS (MySQL password)}"
: "${ADMINPASS:?Set ADMINPASS (admin login password)}"

if printf '%s%s' "$DBPASS" "$ADMINPASS" | grep -q '[|&"$\\'"'"']'; then
    echo "Passwords may only use letters, numbers and # (no | & quotes \$ or backslash)." >&2
    exit 1
fi

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

for f in api/.env frontend/.env backend/.env; do
    [ -f "$f" ] || { echo "$f is missing - run the composer/.env loop (F9) first." >&2; exit 1; }
done

setenv() {   # setenv FILE KEY VALUE
    if grep -q "^$2=" "$1"; then
        sed -i "s|^$2=.*|$2=$3|" "$1"
    else
        printf '%s=%s\n' "$2" "$3" >> "$1"
    fi
    printf '%-16s %s\n' "$1" "$(grep "^$2=" "$1")"
}

setenv api/.env      DB_HOST        "$DB"
setenv api/.env      DB_PORT        3306
setenv api/.env      DB_DATABASE    careconnect_db
setenv api/.env      DB_USERNAME    careconnect
setenv api/.env      DB_PASSWORD    "\"$DBPASS\""
setenv api/.env      ADMIN_PASSWORD "\"$ADMINPASS\""
setenv frontend/.env APP_URL        "http://$MYAPP"
setenv frontend/.env API_MEDIA_URL  "http://$MYAPP/storage"
setenv backend/.env  APP_URL        "http://$MYAPP:8080"
setenv backend/.env  API_MEDIA_URL  "http://$MYAPP:8080/storage"

echo
echo "Done. Next: test the DB connection -> cd api && sudo -u deploy -H php artisan db:show"
echo "After any later .env change: sudo -u www-data php artisan optimize:clear (in that app)"
