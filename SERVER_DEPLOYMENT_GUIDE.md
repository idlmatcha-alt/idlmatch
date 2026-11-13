# 🚀 IDL Match — Server Deployment Guide (Go-Live)

Follow these steps **in order** on your production server.

---

## 1. Pull Latest Code

```bash
cd /path/to/your/project
git pull origin main
```

> If this is a fresh server, clone instead:
> ```bash
> git clone https://github.com/magzelsoft/magzel_idlmatch.git
> cd magzel_idlmatch
> ```

---

## 2. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

---

## 3. Configure Environment

Copy the `.env` file if not already present:

```bash
cp .env.example .env   # Only if .env doesn't exist yet
```

**Edit `.env` and set these critical values:**

```env
APP_NAME="Ideal Marriage Center"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://idlmatch.com
APP_TIMEZONE=Asia/Karachi

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magzel_idlmatch
DB_USERNAME=magzel_magzel_idlmatch
DB_PASSWORD=Magzel@123456@

QUEUE_CONNECTION=database        # ← IMPORTANT: must be "database", not "sync"

MAIL_MAILER=smtp
MAIL_HOST=alerts.magzel.com
MAIL_PORT=465
MAIL_USERNAME=new@alerts.magzel.com
MAIL_PASSWORD=Magzel@123456
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="new@alerts.magzel.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Then generate the app key (only on first setup):

```bash
php artisan key:generate
```

---

## 4. Run Database Migrations

```bash
php artisan migrate --force
```

---

## 5. Clean Database for Go-Live (Remove Test/Old Data)

```bash
php artisan db:clean-for-launch
```

This will:
- ✅ Keep admin users, plans, locations, blog posts
- ❌ Delete all non-admin users, profiles, messages, interests, etc.

Type `yes` when prompted.

---

## 6. Seed Fresh Admin & Data

```bash
php artisan db:seed
```

This creates:
- **Admin login:** `admin@idlmatch.com` / `12341234`
- Countries and Pakistan's states/cities

---

## 7. Create Jobs Table (if not exists)

Since we use database queue, make sure the jobs table exists:

```bash
php artisan queue:table       # Creates migration if needed
php artisan migrate --force   # Run it
```

---

## 8. Storage Link

```bash
php artisan storage:link
```

This creates `public/storage` → `storage/app/public` symlink for photo uploads.

---

## 9. Cache Configuration (Performance)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> ⚠️ Run these **after** every code update or `.env` change.

---

## 10. Set Up Queue Worker (CRITICAL for Emails)

The queue worker processes profile suggestion emails. Without it, **no match emails will be sent**.

### Option A: Supervisor (Recommended — Auto-restarts)

Install Supervisor:
```bash
sudo apt-get install supervisor
```

Create config file:
```bash
sudo nano /etc/supervisor/conf.d/idlmatch-worker.conf
```

Paste this:
```ini
[program:idlmatch-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work database --queue=emails,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

> ⚠️ Replace `/path/to/your/project` with your actual project path.

Then start it:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start idlmatch-worker:*
```

### Option B: Simple (If no Supervisor available)

Run in a screen/tmux session:
```bash
screen -S worker
php artisan queue:work database --queue=emails,default --sleep=3 --tries=3
# Press Ctrl+A then D to detach
```

---

## 11. Set Up Cron Job (CRITICAL for Daily Emails)

The Laravel scheduler sends daily profile suggestions at 9:00 AM PKT.

```bash
crontab -e
```

Add this line:
```
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

> ⚠️ Replace `/path/to/your/project` with your actual project path.

---

## 12. Set Folder Permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 13. Frontend Assets (No npm needed on server)

Make sure these files exist on the server:
- `public/static/app.css`
- `public/static/app.js`

If they don't exist, build locally and upload:
```bash
# On your LOCAL machine:
npm install
npm run build
# Then copy public/static/app.css and app.js to the server
```

---

## ✅ Post-Deployment Checklist

| Item | Command to Verify |
|------|------------------|
| Site loads | Visit `https://idlmatch.com` |
| Admin panel works | Visit `https://idlmatch.com/admin` and login with `admin@idlmatch.com` |
| Queue worker running | `sudo supervisorctl status idlmatch-worker:*` |
| Cron job active | `crontab -l` (should show schedule:run) |
| Test email sending | Register a new user → should receive 5 profile suggestions |
| Check queued jobs | `php artisan tinker` then `DB::table('jobs')->count()` |
| Check failed jobs | `php artisan queue:failed` |

---

## 🔄 Future Updates

After any code change:
```bash
cd /path/to/your/project
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart idlmatch-worker:*
```

---

## 📧 How Profile Match Emails Work

| Trigger | When | Count | What |
|---------|------|-------|------|
| New registration | Instantly (staggered over 8 min) | 5 profiles | Best-match opposite-gender profiles |
| Daily scheduler | Every day at 9 AM PKT | 5-10 profiles | Random opposite-gender profiles |

Both require the **queue worker** and **cron job** to be running.
