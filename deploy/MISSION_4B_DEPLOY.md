# Mission 4b — Deploy CareConnect to a DigitalOcean Droplet

This guide follows **Mission 4b** in *Cloud Computing Lab Notes — Missions 3a–4b*,
adapted to **this** repository. Your laptop is **Fedora + kitty**. The server is **Ubuntu 24.04**.

**Result:**

| URL | What it is | Folder in this repo |
|---|---|---|
| `http://YOUR_SERVER_IP/` | CareConnect website (patients + hospital admins) | `frontend/` |
| `http://YOUR_SERVER_IP:8080/` | CareConnect admin panel | `backend/` |

Both apps are **Laravel 12**, so they work on PHP 8.4 (see the Mission 4b version table). They share **one MySQL database**.
`frontend/` owns the migration that creates all the tables.
The `api/` folder is a separate JSON API. The website doesn't need it, so it is not deployed here. See the end of this guide if you want it.

```
Fedora laptop                      Ubuntu 24.04 Droplet (SGP1, 2 GB)
─────────────                      ─────────────────────────────────────────────
Browser ── http :80 ──────────▶    Apache ─▶ /var/www/careconnect/frontend/public ─┐
Browser ── http :8080 ────────▶    Apache ─▶ /var/www/careconnect/backend/public  ─┤
kitty   ── ssh :22 ───────────▶    SSH                                             ├─▶ MySQL 8  careconnect_db
DBeaver ── :3306 (home IP only) ─────────────────────────────────────────────────────┘
```

---

## 0. Write your real values down FIRST (Lessons learned #7 and #8)

Do not type a CAPITAL placeholder into a command. Fill in this table on paper or in a notes app before you start:

| Placeholder | Your value | How to get it |
|---|---|---|
| `YOUR_SERVER_IP` | | DigitalOcean → Droplets → the **ipv4** next to `mission4b` |
| `YOUR_HOME_IP` | | On Fedora: `curl -4 ifconfig.me ; echo` |
| `YOUR_MYSQL_USER` | e.g. `linhcheu` | your name, lowercase, no spaces |
| `YOUR_MYSQL_PASSWORD` | | your own strong password, e.g. 8+ chars with `Aa1#` |
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
ls                                    # frontend/  backend/  api/  deploy/ ...
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

## 7. Mission 4b Step 3: dependencies and .env (both apps)

Explain this in the video: `vendor/` is **not** uploaded because it's large, and Composer rebuilds it from `composer.lock`.

### Website (`frontend/`)

```bash
cd /var/www/careconnect/frontend
pwd                                   # /var/www/careconnect/frontend  (artisan only works here)
composer install --optimize-autoloader
cp ../deploy/env/frontend.env.example .env
micro .env                            # replace YOUR_SERVER_IP, YOUR_MYSQL_USER, YOUR_MYSQL_PASSWORD
php artisan key:generate
```

### Admin panel (`backend/`)

```bash
cd /var/www/careconnect/backend
composer install --optimize-autoloader
cp ../deploy/env/backend.env.example .env
micro .env                            # same DB values, APP_URL=http://YOUR_SERVER_IP:8080
php artisan key:generate
```

Check that you replaced every placeholder in both files. These two commands should print **nothing**:

```bash
grep -n YOUR_ /var/www/careconnect/frontend/.env
grep -n YOUR_ /var/www/careconnect/backend/.env
```

The `.env` files set things this project needs: `SESSION_DRIVER=file`, **different** `SESSION_COOKIE` names per app (otherwise the two apps on the same IP log each other out → *419 Page Expired*), `CACHE_STORE=file` and `QUEUE_CONNECTION=sync` (the project has no cache or jobs tables), and `MAIL_MAILER=log` (DigitalOcean blocks SMTP).

---

## 8. Mission 4b Step 4: create the tables and data

**Option A (recommended): migrations + seeders.** These create all tables plus demo hospitals, users, appointments and feedback.

```bash
cd /var/www/careconnect/frontend
php artisan config:clear
php artisan migrate --seed --force    # creates users_table, admins_table, hospitals, subscriptions, appointments, feedback, ...
php artisan db:show                   # must say MySQL 8 and list the tables (Lessons learned #12)

cd /var/www/careconnect/backend
php artisan config:clear
php artisan db:seed --force           # creates the platform admin account
```

The **only** table creator is `frontend`. Don't run `migrate` in `backend/` because it has no migrations of its own.

**Option B: import your own data from your laptop.** On Fedora, export with DBeaver (right-click DB → Tools → Dump database) or with `mysqldump -u root -p your_local_db > careconnect.sql`. Then upload and import it:

```bash
# Fedora
scp careconnect.sql root@YOUR_SERVER_IP:/var/www/
# server
mysql careconnect_db < /var/www/careconnect.sql
mysql -e "USE careconnect_db; SHOW TABLES;"
```

If you see `Unknown collation 'utf8mb4_uca1400_ai_ci'`, the dump came from MariaDB. Fix it with `sed -i 's/utf8mb4_uca1400_ai_ci/utf8mb4_unicode_ci/g' /var/www/careconnect.sql` and import again.

---

## 9. Mission 4b Step 5: permissions and shared uploads

Apache runs as **www-data**, so it must be able to write `storage/` (logs, sessions, uploaded pictures) and `bootstrap/cache`.

Both apps show the **same** pictures. A hospital logo uploaded on the website must also appear in the admin panel, and the other way round. To do that, make the admin panel use the website's upload folder:

```bash
cd /var/www/careconnect
rm -rf backend/storage/app/public
ln -s /var/www/careconnect/frontend/storage/app/public backend/storage/app/public

cd /var/www/careconnect/frontend && php artisan storage:link
cd /var/www/careconnect/backend  && php artisan storage:link

cd /var/www/careconnect
chown -R www-data:www-data frontend/storage frontend/bootstrap/cache backend/storage backend/bootstrap/cache
chmod -R 775 frontend/storage frontend/bootstrap/cache backend/storage backend/bootstrap/cache
```

Allow uploads up to 20 MB (the default is only 2 MB):

```bash
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 20M/; s/^post_max_size = .*/post_max_size = 25M/' /etc/php/8.4/apache2/php.ini
grep -E '^(upload_max_filesize|post_max_size)' /etc/php/8.4/apache2/php.ini
```

**Mission 4b Step 6 (Vite) is not needed.** The pages use plain CSS and JS from `public/`, not `@vite`, so `npm` is not required.

---

## 10. Mission 4b Step 7: Apache virtual hosts

The two vhost files are ready in `deploy/apache/`:

```bash
cp /var/www/careconnect/deploy/apache/careconnect.conf       /etc/apache2/sites-available/
cp /var/www/careconnect/deploy/apache/careconnect-admin.conf /etc/apache2/sites-available/
a2enmod rewrite
a2dissite 000-default.conf            # (and laravel.conf, if you reused a Mission 4a server)
a2ensite careconnect.conf
a2ensite careconnect-admin.conf
apache2ctl configtest                 # Syntax OK  (an AH00558 "ServerName" warning above it is harmless)
systemctl restart apache2
ufw allow 8080/tcp                    # admin panel port
ufw status                            # 22, 80, 8080, 3306 (your IP)
ls /etc/apache2/sites-enabled/        # only careconnect.conf + careconnect-admin.conf
```

Explain the key lines in the video. `DocumentRoot` ends in **/public**, so `.env` can never be downloaded. `AllowOverride All` lets Laravel's `.htaccess` rewrite URLs. `Listen 8080` makes Apache open the second port.

---

## 11. Mission 4b Step 8: cache and test

```bash
cd /var/www/careconnect/frontend && php artisan optimize:clear && php artisan optimize
cd /var/www/careconnect/backend  && php artisan optimize:clear && php artisan optimize
curl -I http://localhost/             # HTTP/1.1 200 OK
curl -I http://localhost:8080/admin/login
```

**Remember:** after **any** `.env` edit, run `php artisan optimize:clear` in that app. Otherwise your change is ignored.

Now open these in your browser:

| Test | URL | Login |
|---|---|---|
| Website home, hospitals list | `http://YOUR_SERVER_IP/` | none |
| Patient | `http://YOUR_SERVER_IP/login` | register a new account, or a seeded user (below) |
| Hospital admin | `http://YOUR_SERVER_IP/login` | seeded hospital admin (below) |
| Platform admin panel | `http://YOUR_SERVER_IP:8080/` | `admin@careconnect.com` / `admin123` |

To list the seeded accounts, use DBeaver or run:
`mysql -e "SELECT email, role FROM careconnect_db.users_table;"`
The password is set in `frontend/database/seeders/*Seeder.php`.

The demo for the video goes like this:

1. **Register** a patient on the website.
2. **Book an appointment**.
3. **Upload a profile picture**. On the profile page, click **Edit** first, because the fields stay locked until you do.
4. In DBeaver, refresh `users_table` and `appointments`. The new rows are there.
5. In the admin panel (`:8080`), **edit or delete** that appointment or user.
6. Refresh DBeaver again.

When everything works, set `APP_DEBUG=false` in **both** `.env` files, then run `php artisan optimize:clear && php artisan optimize` in both folders.

### Final check (run on camera)

```bash
bash /var/www/careconnect/deploy/scripts/check.sh
```

The script prints the Ubuntu, Apache, PHP, Composer and MySQL versions and `ufw status`. It also prints both apps' `db:show` and HTTP status codes. It only reads; it changes nothing.

---

## 12. DBeaver on Fedora (Mission 4a Step 10)

**Database → New Database Connection → MySQL**:

* Host `YOUR_SERVER_IP`, port `3306`, database `careconnect_db`, your user and password
* **Driver properties** tab: `allowPublicKeyRetrieval = true`, `useSSL = false`
* **Test Connection** → Finish → `careconnect_db` → Tables

At school the IP is different, so DBeaver can't connect directly. Use the **SSH** tab: tick *Use SSH Tunnel* with host `YOUR_SERVER_IP`, user `root`, and your key. Then on the Main tab use host `127.0.0.1`.

---

## 13. Updating the site later

Push your changes to GitHub from Fedora, then run on the server:

```bash
cd /var/www/careconnect && git pull
cd frontend && composer install --optimize-autoloader && php artisan migrate --force && php artisan optimize:clear && php artisan optimize
cd ../backend && composer install --optimize-autoloader && php artisan optimize:clear && php artisan optimize
```

---

## If it fails: errors specific to this project

| What you see | Why | Fix |
|---|---|---|
| **419 Page Expired** when logging in | Both apps use the same cookie name, or `storage/` isn't writable | Different `SESSION_COOKIE` in each `.env` (already set in the templates), redo the `chown` lines in section 9, then `php artisan optimize:clear` |
| `Table 'careconnect_db.cache' doesn't exist` | `.env` still says `CACHE_STORE=database` | `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, then `php artisan optimize:clear` |
| `Table 'careconnect_db.sessions' doesn't exist` | `SESSION_DRIVER=database` without the migration | `SESSION_DRIVER=file`, then `optimize:clear` |
| Admin panel: `Base table ... admins_table doesn't exist` | Migrations not run yet | `cd frontend && php artisan migrate --seed --force` |
| Admin login says invalid credentials | Backend seeder not run | `cd backend && php artisan db:seed --force` |
| Picture uploaded but shows broken (404 `/storage/...`) | `storage:link` missing, or wrong `APP_URL` | Section 9 again; `APP_URL` = `http://YOUR_SERVER_IP` (`:8080` for the admin) |
| Picture visible in the website but not in the admin panel | The shared-uploads symlink was skipped | The `rm -rf` + `ln -s` lines in section 9, then `storage:link` in `backend` |
| `:8080` times out | Port not open | `ufw allow 8080/tcp`; `ss -tlnp \| grep 8080` must show apache2 |
| `:8080` shows the website instead of the admin panel | `careconnect-admin.conf` not enabled | `a2ensite careconnect-admin.conf && systemctl reload apache2` |
| `SQLSTATE[HY000] [1045] Access denied` | Wrong password in `.env` or MySQL | Check the quotes in `DB_PASSWORD="..."`, then `php artisan config:clear` |
| `could not find driver` | `php8.4-mysql` missing | `apt install -y php8.4-mysql && systemctl restart apache2` |
| Composer `Killed` | Out of memory | Add swap (section 3) |
| Admin panel `/sub`: `View [subcription] not found` | `backend/resources/views/subcription.blade.php` (and `subscription_edit`) were never committed to the repo. This was tested: every other admin page works | Copy those view files from your laptop into `backend/resources/views/` and push. The page isn't in the admin menu, so you can skip it for the demo |
| Any 500 error | It's in the log | `tail -n 50 /var/www/careconnect/frontend/storage/logs/laravel.log` (or `backend/...`) and `tail -n 50 /var/log/apache2/careconnect_error.log` |

The general troubleshooting table and the *Lessons learned* in the lab notes still apply.

---

## Before you destroy the Droplet

- [ ] Website on `:80` and admin panel on `:8080` both work, including login, CRUD and upload
- [ ] DBeaver connected; data change shown live
- [ ] Screenshots taken, video recorded (face + voice), PDF + video **submitted**
- [ ] **Grade received.** Only then: Droplet → *Destroy*, or *Snapshots → Take Live Snapshot* first if you want to keep it cheaply

---

## Optional: also deploy the JSON API (`api/`)

Not needed for the website. If your teacher wants it, use port **8081**:

1. Copy `careconnect-admin.conf` to `careconnect-api.conf` and change `8080` to `8081` and `backend` to `api`.
2. Copy `backend.env.example` to `api/.env` and set `APP_URL=...:8081` and `SESSION_COOKIE=careconnect_api_session`.
3. Run `composer install` and `key:generate`.
4. Run `php artisan migrate --force` in `api/`. This adds Sanctum's `personal_access_tokens` table.
5. Run `chown` on `api/storage` and `api/bootstrap/cache`.
6. Run `a2ensite careconnect-api.conf`, then `ufw allow 8081/tcp`.
