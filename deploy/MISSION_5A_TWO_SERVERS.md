# Mission 5A — CareConnect on two servers (App server + DB server)

Full step-by-step guide with screenshots list and video script: *Cloud Computing Lab Notes → Mission 5A → Path 2 (F0–F15)*.
This file is the short version that lives next to the code.

```
Fedora laptop                Server 01  mission5a-app (SGP1)              Server 02  mission5a-db (SGP1)
Browser ── :80   ──────▶     Apache ▶ frontend ──┐
Browser ── :8080 ──────▶     Apache ▶ backend  ──┤
                                                  ▼
                             Apache 127.0.0.1:8081 ▶ api ── DB_HOST ─────▶  MySQL 8 :3306 (careconnect_db)
DBeaver ── :3306 (home IP only) ─────────────────────────────────────────▶  MySQL
```

| | App server | DB server |
|---|---|---|
| Installed | Apache2, PHP 8.4, Composer, this repo | MySQL 8 only |
| ufw | OpenSSH, 80, 8080 | OpenSSH, 3306 from the APP IP and your HOME IP |
| Key setting | `api/.env` → `DB_HOST=<DB server IP>` | `'careconnect'@'<APP server IP>'` user |

## DB server (as root)

```bash
apt update && apt upgrade -y && apt install -y mysql-server micro
sed -i 's/^bind-address.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf
systemctl restart mysql && ss -tlnp | grep 3306          # 0.0.0.0:3306

APP=1.2.3.4; MYHOME=5.6.7.8; DBPASS='YourPass#2026'      # your real values
mysql -e "CREATE DATABASE careconnect_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER 'careconnect'@'$APP' IDENTIFIED BY '$DBPASS';"
mysql -e "GRANT ALL PRIVILEGES ON careconnect_db.* TO 'careconnect'@'$APP' WITH GRANT OPTION;"
mysql -e "CREATE USER 'careconnect'@'$MYHOME' IDENTIFIED WITH mysql_native_password BY '$DBPASS';"
mysql -e "GRANT ALL PRIVILEGES ON careconnect_db.* TO 'careconnect'@'$MYHOME' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"
ufw allow OpenSSH
ufw allow from $APP to any port 3306 proto tcp
ufw allow from $MYHOME to any port 3306 proto tcp
ufw --force enable
```

## App server (as root)

Same as Mission 4b (`MISSION_4B_DEPLOY.md`) **without** `mysql-server`: PHP 8.4 from packages.sury.org,
Apache, Composer, the `deploy` user, clone, the composer/.env loop. Then fill the `.env` files with the
helper (no manual editing, no placeholders):

```bash
DB=5.6.7.8 MYAPP=1.2.3.4 DBPASS='YourPass#2026' ADMINPASS='AdminPass#2026' \
  bash /var/www/careconnect/deploy/scripts/set-env.sh

cd /var/www/careconnect/api
sudo -u deploy -H php artisan migrate --seed --force     # tables are created ON the DB server
sudo -u deploy -H php artisan db:show                    # host = DB server IP
```

Then permissions, the three Apache sites, `ufw allow 80/tcp` and `ufw allow 8080/tcp`, and
`bash deploy/scripts/check.sh` (it now also tests that the DB server's port 3306 is reachable and
skips the local MySQL checks when MySQL isn't installed on the app server).

## Prove it

* DB server, while clicking the website: `mysql -e "SHOW PROCESSLIST;"` → connections from the APP IP.
* DBeaver → host = DB server IP, port 3306, database `careconnect_db`, user `careconnect`
  (Driver properties: `allowPublicKeyRetrieval=true`, `useSSL=false`).
* Book an appointment, change it in the admin panel, see the same row change in DBeaver.

## Common errors

| Error | Fix |
|---|---|
| `SQLSTATE[HY000] [1130] Host 'x.x.x.x' is not allowed` | DB server: `CREATE USER` + `GRANT` for exactly that IP |
| `SQLSTATE[HY000] [2002] Connection timed out` | DB server: `ufw status` (3306 from APP IP), `bind-address = 0.0.0.0` |
| `[1045] Access denied` | `DBPASS` differs from the DB user's password → rerun `set-env.sh`, then `php artisan optimize:clear` |
| `.env` change ignored | `sudo -u www-data php artisan optimize:clear` in that app |
