#!/usr/bin/env bash
# CareConnect - "Final check" for the Mission 4b video.
# Run on the server as root:   bash /var/www/careconnect/deploy/scripts/check.sh
# It only READS things - it changes nothing.

APP=/var/www/careconnect

line() { printf '\n==== %s ====\n' "$1"; }

line "Ubuntu";            lsb_release -d 2>/dev/null
line "Apache";            apache2 -v | head -1
line "PHP";               php -v | head -1
line "Composer";          composer --version 2>/dev/null
line "MySQL";             mysql --version
line "Firewall (ufw)";    ufw status

line "Apache sites (careconnect, careconnect-admin, careconnect-api)"
ls /etc/apache2/sites-enabled/
apache2ctl configtest 2>&1 | tail -1

line "Ports: 80 + 8080 public, 8081 API on 127.0.0.1 only, 3306 MySQL"
ss -tlnp | grep -E ':(80|8080|8081|3306) ' || echo "nothing listening on 80/8080/8081/3306?!"

line "API: Laravel + MySQL (the only app with a database)"
cd "$APP/api" && php artisan --version && grep -E '^DB_(CONNECTION|DATABASE|USERNAME)' .env
php artisan db:show 2>&1 | head -12

for app in frontend backend; do
    line "$app: Laravel + where it finds the API"
    cd "$APP/$app" || continue
    php artisan --version
    grep -E '^API_(URL|MEDIA_URL)' .env
done

line "HTTP answers from inside the server (200 = OK)"
printf 'API      http://127.0.0.1:8081/api/v1/home -> '; curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8081/api/v1/home
printf 'website  http://localhost/                 -> '; curl -s -o /dev/null -w '%{http_code}\n' http://localhost/
printf 'admin    http://localhost:8080/admin/login -> '; curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/admin/login
echo
echo "Anything not 200: tail -n 50 $APP/<api|frontend|backend>/storage/logs/laravel.log"
