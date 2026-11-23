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

line "Apache sites enabled (should be careconnect + careconnect-admin)"
ls /etc/apache2/sites-enabled/
apache2ctl configtest 2>&1 | tail -1

line "Ports listening (80 Apache, 8080 Apache, 3306 MySQL)"
ss -tlnp | grep -E ':(80|8080|3306) ' || echo "nothing on 80/8080/3306?!"

for app in frontend backend; do
    line "$app: Laravel + database"
    cd "$APP/$app" || continue
    php artisan --version
    grep -E '^DB_(CONNECTION|DATABASE|USERNAME)' .env
    php artisan db:show 2>&1 | head -12
done

line "HTTP answers from inside the server"
printf 'website  http://localhost/            -> '; curl -s -o /dev/null -w '%{http_code}\n' http://localhost/
printf 'admin    http://localhost:8080/admin/login -> '; curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/admin/login
echo
echo "200 = OK. Anything else: tail -n 50 $APP/frontend/storage/logs/laravel.log"
