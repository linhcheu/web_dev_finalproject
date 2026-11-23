# Mission 4b — Deploy CareConnect to a DigitalOcean Droplet

This guide follows **Mission 4b** in *Cloud Computing Lab Notes — Missions 3a–4b*, adapted to
**this** repository. Your laptop runs **Fedora + kitty**; the server runs **Ubuntu 24.04**.
Every step was tested on Ubuntu 24.04 with Apache and MySQL 8.0 before it was written down.

## What you are deploying

CareConnect is three Laravel 12 apps on **one** Droplet:

| App | Folder | URL | Talks to |
|---|---|---|---|
| Website (patients + hospital admins) | `frontend/` | `http://YOUR_SERVER_IP/` | the API |
| Admin panel (platform admins) | `backend/` | `http://YOUR_SERVER_IP:8080/` | the API |
| REST API | `api/` | `http://127.0.0.1:8081` (inside the server only) | **MySQL** |

```
Fedora laptop                         Ubuntu 24.04 Droplet (SGP1, 2 GB)
─────────────                         ────────────────────────────────────────────────────────
Browser ── http :80   ───────────▶    Apache ─▶ frontend/public ──┐
Browser ── http :8080 ───────────▶    Apache ─▶ backend/public  ──┤ HTTP + token
                                                                   ▼
                                      Apache 127.0.0.1:8081 ─▶ api/public ─▶ MySQL 8 careconnect_db
kitty   ── ssh :22 ──────────────▶    SSH
DBeaver ── :3306 (home IP only) ───────────────────────────────────────▶ MySQL
```

Explain these points in the video:

* **Only the API touches the database.** The website and admin panel have no database settings at all. They call the API and send a login token with each request.
* **The API is not public.** It listens on `127.0.0.1`, so only the two web apps on the same server can reach it. ufw doesn't need to open 8081.
* **Uploaded pictures** are stored by the API (`api/storage/app/public`). The two web apps serve them at `/storage/...`, and PHP is switched off in that folder.

## 0. Write your real values down FIRST (Lessons learned #7 and #8)

Do not type a CAPITAL placeholder into a command. Fill in this table on paper or in a notes app before you start:

| Placeholder | Your value | How to get it |
|---|---|---|
| `YOUR_SERVER_IP` | | DigitalOcean → Droplets → the **ipv4** next to `mission4b` |
| `YOUR_HOME_IP` | | On Fedora: `curl -4 ifconfig.me ; echo` |
| `YOUR_MYSQL_USER` | e.g. `linhcheu` | your name, lowercase, no spaces |
| `YOUR_MYSQL_PASSWORD` | | your own strong password, e.g. 8+ chars with `Aa1#` |
| `YOUR_ADMIN_PASSWORD` | | password for the admin panel login (`admin@careconnect.com`) |
| Database name | `careconnect_db` | (this guide uses this name) |

Rules from the notes: **one command at a time**, read the output before you run the next command, and keep **two kitty tabs** open (Ctrl+Shift+T).

---

## 1. Fedora laptop: one-time preparation

Run these in kitty on **Fedora**. Use `dnf` here, never `apt`.

```bash
sudo dnf install -y micro zip rsync           # editor + upload tools
ls -l ~/.ssh/                                 # do you already have id_ed25519.pub ?
ssh-keygen -t ed25519 -C "linhcheu@fedora"    # only if you don't have one (press Enter for the default path)
kitten clipboard < ~/.ssh/id_ed25519.pub      # copies the PUBLIC key
curl -4 ifconfig.me ; echo                    # = YOUR_HOME_IP, write it down
```

DBeaver, used to show the tables in the video:

```bash
flatpak install flathub io.dbeaver.DBeaverCommunity
```

---

## 2. Create the Droplet (Mission 4b Step 0)

In <https://cloud.digitalocean.com> click **Create → Droplets**:

1. Region **Singapore**, datacenter **SGP1**
2. Image **OS → Ubuntu → 24.04 (LTS) x64**
3. Size **Basic → Regular → 2 GB RAM** (about $12/month)
4. Authentication **SSH Key → Add SSH Key**: paste with Ctrl+V, name it `fedora-laptop`, and make sure it's **ticked**
5. Tick **Improved Metrics and monitoring** (free)
6. Hostname **`mission4b`** → **Create Droplet**
7. Copy the **ipv4** address. That's `YOUR_SERVER_IP`.

---

## 3. SSH in and do Part A3 (Mission 4b Step 0.2)

On Fedora:

```bash
kitten ssh root@YOUR_SERVER_IP        # type "yes" the first time
```

The prompt should now read `root@mission4b:~#`. Run everything from here on **on the server**, one line at a time:

```bash
apt update && apt upgrade -y          # purple screen? press Enter
lsb_release -a                        # must say Ubuntu 24.04
ufw status                            # Status: inactive (DigitalOcean ships it off)
ufw allow OpenSSH                     # ALWAYS before enabling, or you lock yourself out
ufw enable                            # type y
ufw status                            # Status: active, OpenSSH ALLOW
apt install -y micro
mkdir -p ~/.config/micro && echo '{ "clipboard": "terminal" }' > ~/.config/micro/settings.json
echo 'export SYSTEMD_PAGER=' >> ~/.bashrc && source ~/.bashrc   # no more "(END)" pager
```

If you picked a **1 GB** plan instead of 2 GB, also add swap (Part A4):

```bash
fallocate -l 2G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab
free -h                               # Swap: 2.0Gi
```

---

## 4. Mission 4a Steps 1–4: Apache, PHP 8.4, extensions, Composer

### Step 1: Apache2 (web server)

```bash
apt install -y apache2
systemctl enable --now apache2
systemctl status apache2 --no-pager   # active (running)
ufw allow 80/tcp
```

Open `http://YOUR_SERVER_IP/` in the browser. You should see the *Apache2 Ubuntu Default Page*.

### Step 2: PHP 8.4. Run these ONE AT A TIME (Lessons learned #2)

```bash
apt install -y curl gpg gnupg2 software-properties-common ca-certificates apt-transport-https lsb-release
add-apt-repository -y ppa:ondrej/php
apt update
apt-cache policy php8.4               # "Candidate:" must show 8.4.x
apt install -y php8.4 libapache2-mod-php8.4
php -v                                # PHP 8.4.x
apache2ctl -M | grep php              # php_module (shared)
```

### Step 3: PHP extensions

```bash
apt install -y php8.4-{cli,pdo,mysql,zip,gd,mbstring,curl,xml,bcmath,common}
apt install -y php8.4-intl php8.4-sqlite3
systemctl restart apache2
php -m | grep -E 'pdo_mysql|gd|mbstring|intl'
```

`gd` matters for this project because the profile and hospital picture uploads need it.

### Step 4: Composer (note the `$( )` in the HASH line)

```bash
apt install -y unzip curl git
cd /tmp
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
HASH=$(curl -sS https://composer.github.io/installer.sig)
php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('/tmp/composer-setup.php'); } echo PHP_EOL;"
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm /tmp/composer-setup.php
composer --version                    # Composer version 2.x
export COMPOSER_ALLOW_SUPERUSER=1     # stops the "running as root" question
```

Skip Mission 4a **Step 5** (create a new Laravel project) and **Step 12** (the hello controller). Your own project replaces them.

---

## 5. Mission 4a Steps 7–9: MySQL 8, database, remote access

### Step 7: Install MySQL

```bash
apt install -y mysql-server
systemctl enable --now mysql
mysql --version                       # Ver 8.0.x
mysql_secure_installation             # if it loops on "SET PASSWORD": Ctrl+C, run again, answer n to changing root pwd
```

### Step 8 + Mission 4b Step 4: database and users

```bash
mysql
```

At the `mysql>` prompt, replace the 3 placeholders with your **real** values. Every line ends with `;`:

```sql
CREATE DATABASE careconnect_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'YOUR_MYSQL_USER'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YOUR_MYSQL_PASSWORD';
GRANT ALL PRIVILEGES ON careconnect_db.* TO 'YOUR_MYSQL_USER'@'localhost' WITH GRANT OPTION;

CREATE USER 'YOUR_MYSQL_USER'@'YOUR_HOME_IP' IDENTIFIED BY 'YOUR_MYSQL_PASSWORD';
GRANT ALL PRIVILEGES ON careconnect_db.* TO 'YOUR_MYSQL_USER'@'YOUR_HOME_IP' WITH GRANT OPTION;

FLUSH PRIVILEGES;
SELECT user, host, plugin FROM mysql.user;   -- check: your name twice, with localhost and your REAL IP
EXIT;
```

If you see `yourname` or `YOUR_HOME_IP` in that list, run `DROP USER` on that account and create it again (Lessons learned #8).

### Step 9: remote access for DBeaver (your home IP only)

```bash
micro /etc/mysql/mysql.conf.d/mysqld.cnf
#   Ctrl+F  bind-address  ->  change 127.0.0.1 to 0.0.0.0  ->  Ctrl+S, Ctrl+Q
systemctl restart mysql
ss -tlnp | grep 3306                  # 0.0.0.0:3306
ufw allow from YOUR_HOME_IP to any port 3306 proto tcp
ufw status
```

---

## 6. Mission 4b Step 2: upload the project (Git, recommended)

```bash
cd /var/www
git clone https://github.com/linhcheu/web_dev_finalproject.git careconnect
cd /var/www/careconnect
ls                                    # api/  frontend/  backend/  deploy/  README.md
```

* If the repo is **private**, GitHub asks for a username and a **Personal Access Token** (GitHub → Settings → Developer settings → Tokens). Your normal password won't work.
* If these deploy files are still only on the branch `claude/project-setup-digitalocean-gn5uz1` and not merged into `main` yet, clone with
  `git clone -b claude/project-setup-digitalocean-gn5uz1 https://github.com/linhcheu/web_dev_finalproject.git careconnect`

<details><summary>Option B: zip + scp from Fedora instead of Git</summary>

On **Fedora**, in the folder that *contains* your project folder:

```bash
zip -r careconnect.zip web_dev_finalproject -x '*/vendor/*' '*/node_modules/*' '*/.git/*' '*.zip'
scp careconnect.zip root@YOUR_SERVER_IP:/var/www/
```

On the **server**:

```bash
cd /var/www && unzip careconnect.zip && mv web_dev_finalproject careconnect
```
</details>

---

## 7. Mission 4b Step 3: dependencies and .env (three apps)

Explain this in the video: `vendor/` is **not** uploaded because Composer rebuilds it from `composer.lock`. `--no-dev` skips test tools that a server doesn't need.

```bash
cd /var/www/careconnect/api      && composer install --no-dev --optimize-autoloader
cd /var/www/careconnect/frontend && composer install --no-dev --optimize-autoloader
cd /var/www/careconnect/backend  && composer install --no-dev --optimize-autoloader
```

Copy the production `.env` templates, then fill them in:

```bash
cd /var/www/careconnect
cp deploy/env/api.env.example      api/.env
cp deploy/env/frontend.env.example frontend/.env
cp deploy/env/backend.env.example  backend/.env

micro api/.env         # YOUR_MYSQL_USER, YOUR_MYSQL_PASSWORD, YOUR_ADMIN_PASSWORD
micro frontend/.env    # YOUR_SERVER_IP (2 places)
micro backend/.env     # YOUR_SERVER_IP (2 places)

grep -n YOUR_ api/.env frontend/.env backend/.env    # must print NOTHING

cd api      && php artisan key:generate && cd ..
cd frontend && php artisan key:generate && cd ..
cd backend  && php artisan key:generate && cd ..
```

Already set in the templates:

* **Different `SESSION_COOKIE` per app.** Without it, the two apps on one IP log each other out ("419 Page Expired").
* **`MAIL_MAILER=log`**, because DigitalOcean blocks SMTP.
* **`APP_DEBUG=false`**, so error pages never show code or passwords. While testing you may set `true`; turn it off again afterwards.

---

## 8. Mission 4b Step 4: create the tables and data (API only)

```bash
cd /var/www/careconnect/api
php artisan migrate --seed --force    # all tables + demo hospitals, users, appointments, feedback + the admin account
php artisan db:show                   # must say MySQL 8 and list 8 tables (Lessons learned #12)
```

`migrate` and `db:seed` are only ever run in `api/`. The other two apps have no database.

Demo logins created by the seeders:

| Where | E-mail | Password |
|---|---|---|
| Admin panel `:8080` | `admin@careconnect.com` (or `ADMIN_EMAIL`) | your `ADMIN_PASSWORD` |
| Website, hospital admin | `mysql -e "SELECT email FROM careconnect_db.users_table WHERE role='hospital_admin' LIMIT 3;"` | `11111111` |
| Website, patient | `mysql -e "SELECT email FROM careconnect_db.users_table WHERE role='user' LIMIT 3;"` | `11111111` |

You can also register new accounts on the website.

**Your own data instead (optional).** Export your local database to a `.sql` file (DBeaver: right-click → Tools → Dump database). Upload it with `scp careconnect.sql root@YOUR_SERVER_IP:/var/www/`, then run `mysql careconnect_db < /var/www/careconnect.sql` instead of `migrate --seed`. If you see a MariaDB collation error, run `sed -i 's/utf8mb4_uca1400_ai_ci/utf8mb4_unicode_ci/g' /var/www/careconnect.sql` and import again.

---

## 9. Mission 4b Step 5: storage link and permissions

Apache runs as **www-data**, and it must be able to write logs, sessions and uploaded pictures:

```bash
cd /var/www/careconnect/api && php artisan storage:link
cd /var/www/careconnect
chown -R www-data:www-data api/storage api/bootstrap/cache frontend/storage frontend/bootstrap/cache backend/storage backend/bootstrap/cache
chmod -R 775             api/storage api/bootstrap/cache frontend/storage frontend/bootstrap/cache backend/storage backend/bootstrap/cache
```

**Mission 4b Step 6 (Vite / npm) is not needed.** The website uses plain CSS from `frontend/public/css`. The admin panel's Tailwind CSS is already built into `backend/public/css/admin.css`.

---

## 10. Mission 4b Step 7: three Apache virtual hosts

```bash
cp /var/www/careconnect/deploy/apache/careconnect.conf       /etc/apache2/sites-available/
cp /var/www/careconnect/deploy/apache/careconnect-admin.conf /etc/apache2/sites-available/
cp /var/www/careconnect/deploy/apache/careconnect-api.conf   /etc/apache2/sites-available/
a2enmod rewrite
a2dissite 000-default.conf            # (and laravel.conf, if you reused a Mission 4a server)
a2ensite careconnect.conf careconnect-admin.conf careconnect-api.conf
apache2ctl configtest                 # Syntax OK  (an AH00558 "ServerName" warning is harmless)
systemctl restart apache2
ufw allow 8080/tcp                    # admin panel. NOT 8081: the API stays private
ufw status                            # 22, 80, 8080, 3306 (your IP only)
```

Explain these in the video:

* `DocumentRoot` ends in **/public**, so `.env` can never be downloaded.
* `AllowOverride All` lets Laravel's `.htaccess` rewrite URLs.
* `Listen 8080` opens the admin port.
* `Listen 127.0.0.1:8081` keeps the API private.
* `Alias /storage` serves the uploaded pictures.

---

## 11. Mission 4b Step 8: cache and test

```bash
cd /var/www/careconnect
for app in api frontend backend; do (cd $app && php artisan optimize); done
chown -R www-data:www-data */storage */bootstrap/cache

curl -s http://127.0.0.1:8081/api/v1/home | head -c 150; echo    # API answers with JSON
curl -I http://localhost/                                         # 200 OK
curl -I http://localhost:8080/admin/login                         # 200 OK
```

After **any** `.env` change, run `php artisan optimize:clear && php artisan optimize` in that app.

Then test in your browser:

1. Open `http://YOUR_SERVER_IP/` and **register** a patient.
2. **Book an appointment**.
3. **Upload a profile picture**. On the profile page, click **Edit** first.
4. In DBeaver, refresh `users_table` and `appointments`. The new rows are there.
5. Open `http://YOUR_SERVER_IP:8080/` and log in as the admin.
6. **Edit or delete** that appointment.
7. Refresh DBeaver again.

Hospital sign-ups (`/hospital-register`) start as **pending**. Approve one in the admin panel: **Hospitals → Edit → Approval status: Active**. It then appears on the website.

### Final check (run on camera)

```bash
bash /var/www/careconnect/deploy/scripts/check.sh
```

---

## 12. DBeaver on Fedora (Mission 4a Step 10)

**Database → New Database Connection → MySQL**:

* Host `YOUR_SERVER_IP`, port `3306`, database `careconnect_db`, your user and password
* **Driver properties** tab: `allowPublicKeyRetrieval = true`, `useSSL = false`
* **Test Connection** → Finish → `careconnect_db` → Tables

At school the IP is different. Use the **SSH** tab instead: tick *Use SSH Tunnel* with host `YOUR_SERVER_IP`, user `root`, and your key. Then on the Main tab use host `127.0.0.1`.

---

## 13. Updating the site later

Push your changes to GitHub from Fedora, then run on the server:

```bash
cd /var/www/careconnect && git pull
for app in api frontend backend; do (cd $app && composer install --no-dev --optimize-autoloader && php artisan optimize:clear); done
(cd api && php artisan migrate --force)
for app in api frontend backend; do (cd $app && php artisan optimize); done
chown -R www-data:www-data */storage */bootstrap/cache
```

---

## If it fails

First read the log of the app that failed: `tail -n 50 /var/www/careconnect/<app>/storage/logs/laravel.log`, or the Apache logs in `/var/log/apache2/careconnect*_error.log`.

| What you see | Why | Fix |
|---|---|---|
| Website or admin page: **503 "service is unavailable"** | The web app can't reach the API | `curl http://127.0.0.1:8081/api/v1/home`. If that fails: `a2ensite careconnect-api.conf && systemctl reload apache2`, and check `API_URL=http://127.0.0.1:8081` in `frontend/.env` / `backend/.env` |
| **502 "Something went wrong"** | The API itself returned an error | `tail -n 50 /var/www/careconnect/api/storage/logs/laravel.log` |
| API log: `SQLSTATE[HY000] [1045] Access denied` | Wrong DB user or password in `api/.env` | Fix it, keep the quotes around the password, then `php artisan optimize:clear` |
| API log: `Base table or view not found` | Migrations not run | `cd api && php artisan migrate --seed --force` |
| **419 Page Expired** | Same cookie name in both web apps, or `storage/` not writable | Different `SESSION_COOKIE` per app, redo the `chown` in section 9, `optimize:clear` |
| Pictures broken (404 on `/storage/...`) | `storage:link` missing, or wrong `API_MEDIA_URL` | `cd api && php artisan storage:link`; `API_MEDIA_URL=http://YOUR_SERVER_IP/storage` (`:8080/storage` in `backend/.env`) |
| Admin login: *Invalid admin credentials* | Wrong password, or the seeder didn't run | `cd api && php artisan db:seed --class=AdminSeeder --force` (uses `ADMIN_EMAIL` / `ADMIN_PASSWORD`) |
| "Too many attempts" at login | Brute-force protection: 5 tries per minute per e-mail | Wait one minute |
| Hospital I registered doesn't appear | New hospitals are *pending* | Approve it in the admin panel (Hospitals → Edit) |
| `:8080` times out | Port closed | `ufw allow 8080/tcp`; `ss -tlnp \| grep 8080` must show apache2 |
| `could not find driver` | `php8.4-mysql` missing | `apt install -y php8.4-mysql && systemctl restart apache2` |
| Composer `Killed` | Out of memory | Add swap (section 3) |
| `.env` change does nothing | Config is cached | `php artisan optimize:clear` in that app |

The general troubleshooting table and the *Lessons learned* in the lab notes still apply.

---

## Before you destroy the Droplet

- [ ] Website `:80` and admin panel `:8080` work, including login, booking, upload and admin edit/delete
- [ ] DBeaver connected; data change shown live
- [ ] Screenshots taken, video recorded (face + voice), PDF + video **submitted**
- [ ] **Grade received.** Only then: Droplet → *Destroy*, or *Snapshots → Take Live Snapshot* first if you want to keep it cheaply
