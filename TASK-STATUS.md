# IDL MATCH — TASK STATUS

**Say "check task status" and I read this file first. Do not re-investigate the project from scratch.**
Last updated: 2026-11-05 (session 8 — LIVE DEPLOYED)

> **LIVE and healthy.** `https://idlmatch.com` serves the current build: 32/32 pages 200,
> 0 broken internal links, CSS/JS/logo 200, member photos 200, `sitemap.xml` 200 (49 URLs),
> `robots.txt` 200 with the `Sitemap:` line. `/artisan`, `/composer.json`, `/.env`,
> `*.backup-*`, `*.zip|log|sql|md` = 403; `/storage/logs/laravel.log` = 404.

---

## 1. HOW TO RESUME (read this first, every time)

| Item | Value |
|---|---|
| Project folder | `D:\xampp\htdocs\idlmatchqo` |
| Local site URL | `https://idlmatchqo.test` (served by Herd, not XAMPP Apache) |
| Live site URL | `https://idlmatch.com` |
| Local DB | MariaDB `idlmatch`, user `root`, no password, host `127.0.0.1:3306` |
| Start local DB | `D:\xampp\mysql\bin\mysqld.exe --defaults-file=D:\xampp\mysql\bin\my.ini` (or XAMPP Control Panel → MySQL → Start) |
| Admin login (local) | `admin@idlmatch.com` / `12341234` (from `database/seeders/DatabaseSeeder.php`) |
| PHP binary | `php` (8.4, also `D:\xampp\php\php.exe`) |
| Audit tool | `php _local/_audit.php https://idlmatch.com` — checks 32 pages, links, images, SEO, sitemap |
| Deploy archive | `D:\xampp\htdocs\idlmatch-deploy-20261105-0605.zip` (4.37 MB, 441 files, built 2026-11-05 11:05) — note it sits one level ABOVE the project folder |
| Link checker | `php _local/_linkcheck.php` (needs `php artisan serve` on 127.0.0.1:8000) |

**Rule:** if any local page shows `SQLSTATE[HY000] [2002] No connection could be made`, MySQL is not running. Start it, then re-test. It stops whenever the PC reboots or the terminal that launched it closes.

---

## 2. TASK BOARD

Legend: DONE = finished and verified · NEXT = ready to do, waiting on user · BLOCKED = needs user action outside this project

### Done and verified

| # | Task | Verified how | Date |
|---|---|---|---|
| A1 | Local MySQL was not running → every page 500. Started mysqld on 3306. | `/` went 500 → 200 | 2026-11-04 |
| A2 | Filament admin login page customisation (own view + "Forgot your password?" link). | `GET /admin/login` = 200, markup contains the link | 2026-11-04 |
| A3 | Admin dashboard rebuilt into two sections — "Member overview" (6 cards) and "Website overview" (7 cards), each card coloured and clickable into the filtered resource list. | Logged in as admin, `/admin` = 200, all 13 cards rendered with real numbers; `Pending approvals` link returns exactly 14 results | 2026-11-04 |
| A4 | Added `verification_status` SelectFilter to `ProfileResource` so the Pending/Verified cards can filter the list. | Filtered URL works | 2026-11-04 |
| A5 | Full site audit written and run against live + local. Results in section 3. | `_local/_audit.php` output captured | 2026-11-05 |

### Done — live deployment (2026-11-05, session 8)

| # | Task | How it was done | Status |
|---|---|---|---|
| B1 | Public assets reachable at the web root | `deploy-online.php` copied `idlmatch_app/public/*` → `public_html` (50 files, 0 errors) | DONE |
| B2 | Serve `/storage/*` (member photos) | `symlink()` is disabled on this host, so `public_html/storage-serve.php` + an `.htaccess` rewrite serve `storage/app/public` instead | DONE |
| B3 | Upload the current build | `idlmatch-online-full.zip` (44.6 MB, 11,672 files) → extracted over `/home/u599271327/idlmatch_app` | DONE |
| B4 | Clear caches + migrate | Installer ran `cache:clear`/`config:clear`/`view:clear`; `migrate --force` added `jobs` + `subscribers` | DONE |
| B5 | `APP_DEBUG=false`, `APP_ENV=production` | Written by the installer, `APP_URL=https://idlmatch.com` | DONE |
| B6 | Re-run the audit | `php _local/_audit.php https://idlmatch.com` → 32/32 OK, 0 broken links | DONE |

### Next — local-only tidy-ups

| # | Task | Detail | Status |
|---|---|---|---|
| C1 | Change `APP_URL` in `.env` from `http://127.0.0.1:8000` to `https://idlmatchqo.test` | Country-card images (`/storage/countries/*.jpg`) currently point at a dev server that is not running | NEXT — one line, ask before editing `.env` |
| C2 | Visual confirmation of the new admin dashboard | Screenshot from user has not arrived yet | BLOCKED — waiting for user screenshot |
| C3 | Decide whether the dashboard cards should match the reference more closely (icon tiles on the right, "View More" button text) | Reference images are in `admin/31.png`, `admin/2.png`, `admin/3.png` | NEXT |

---

## 3. AUDIT RESULT — 2026-11-05 (32 URLs each)

**Local `idlmatchqo.test` = COMPLETE.** 32/32 pages OK (4xx = 0). 61 internal links, 0 broken. `<title>` + meta description + canonical + `og:image` + JSON-LD present. `sitemap.xml` = 200 with 49 URLs. `robots.txt` includes the sitemap line. Google sign-in link present on `/register`. Auth pages (`/dashboard`, `/profile`, `/favorites`, `/threads`) redirect to `/login` correctly.

**Live `idlmatch.com` = NOT COMPLETE.** 9 of 32 URLs fail:

| Problem | Evidence |
|---|---|
| No stylesheet / no JS loads | `/static/app.css` 404 and `/static/app.js` 404, but `/public/static/app.css` 200 → document root is one level too high |
| Logo 404 on every page | `/images/logo.png` 404, `/public/images/logo.png` 200 |
| Member + couple photos 404 | `/storage/profiles/26/*.webp`, `/storage/couples/talha_hira.png`, `saad_rabia.png`, `zain_maham.png` |
| 7 pages missing | `/idltech`, `/affiliate`, `/hire-consultant`, `/our-services`, `/legal-advisor`, `/leadership`, `/grooming-consultant`, `/marriage-counseling` → 404 |
| No SEO at all | no meta description, no canonical, no og tags, no JSON-LD; `/sitemap.xml` 404; `robots.txt` has no sitemap line |
| Debug mode on | `/public/sitemap.xml` returns a 1.1 MB Laravel debug page exposing DB user/host |
| Still fine on live | 52 internal links, 0 broken; login/register/search/packages/blog/faqs/success-stories all 200; Google sign-in present |

---

## 4. FILES CHANGED IN THIS PROJECT (most recent first)

| File | What it is |
|---|---|
| `app/Filament/Widgets/SectionStats.php` | NEW — base widget that prints a section heading above stat cards |
| `resources/views/filament/widgets/section-stats.blade.php` | NEW — the heading + stats layout |
| `app/Filament/Widgets/AdminStats.php` | REWRITTEN — "Member overview" cards |
| `app/Filament/Widgets/PaymentStats.php` | REWRITTEN — "Website overview" cards |
| `app/Filament/Resources/ProfileResource.php` | EDITED — added Verification filter |
| `_local/_audit.php` | NEW — the site completeness checker |
| `_local/deploy/UPLOAD-STEPS.md` | NEW — upload runbook for idlmatch.com (idlmatch_app is the live app) |
| `_local/deploy/root.htaccess` | NEW — security block for `public_html` (.env, artisan, composer, logs, app dirs) |
| `_local/deploy/deploy-run.php` | NEW — one-time runner: syncs public assets, creates storage symlink, appends .htaccess rules, runs artisan |
| `resources/views/filament/auth/login.blade.php` + `app/Filament/Pages/Auth/Login.php` + `config/filament.php` | the admin login customisation (previous session) |
| `config/database.php` | PHP 8.5-safe `PDO::MYSQL_ATTR_SSL_CA` handling — verified valid, do not "fix" |

Filament version is **v2.17.0** — icon names must be heroicons v1 style (`heroicon-o-*`); `heroicon-m-*`, `user-plus`, `user-minus`, `banknotes`, `photo`, `rectangle-stack` do NOT exist.

---

## 5. SESSION LOG

- **2026-11-04** — found MySQL not running (cause of all local 500s), started it; confirmed admin login customisation works; rebuilt dashboard cards into the two reference sections; verified as logged-in admin.
- **2026-11-05** — audited live vs local; live deployment is behind (assets, storage symlink, 7 pages, SEO, debug mode); wrote `_local/_audit.php`; MySQL had died again and was restarted.
- **2026-11-05 (upload prep, corrected again)** — second FTP screenshot showed the account root: `domains/`, `idlmatch_app/`, dotfiles, no top-level `public_html`. So the web root is `/home/u599271327/domains/idlmatch.com/public_html` (File Manager opens the website folder, FTP opens the account home — that is why the views differed). `idlmatch_app` is the live app. Helper made location-aware: `$webRoot = __DIR__`, refuses to run unless the zip is extracted (`config/seo.php` present), it is outside `idlmatch_app/public`, and `index.php` exists. User asked whether to delete the hosting account — advised **no** (would lose DB, uploads, email, DNS; the issue is only deployment paths).
- **NEXT SESSION — start here:** user uploads per `_local/deploy/UPLOAD-STEPS.md`, then run `php _local/_audit.php https://idlmatch.com`.
- **2026-11-05 (session 4 — status re-check, no code changed)** — nothing has been uploaded yet. Live still returns `/static/app.css` 404 and `/images/logo.png` 404 (so the design is still missing for visitors), and `/composer.json`, `/artisan` and `/storage/logs/laravel.log` (1.6 MB) are still publicly downloadable. `/public/sitemap.xml` now 404 instead of the earlier 500 debug page. Confirmed the deploy kit is on disk: `D:\xampp\htdocs\idlmatch-deploy-20261105-0605.zip` (4.37 MB, built 11:05) plus `_local/deploy/UPLOAD-STEPS.md`, `root.htaccess`, `deploy-run.php`. B1 reworded: repointing the document root is not possible, the `.htaccess` bridge is the fix.
- **2026-11-05 (session 5 - uploads landed):** user uploaded the zip to `/idlmatch_app/` and to `public_html/`, then extracted the copy in `public_html`. File Manager renamed the colliding old folders (`app.9047`, `bootstrap.6544`, `config.9653`, `database.9053`, `lang.1628`, `public.7708`, `resources.7227`, `routes.7384`) and created `_snapshot_old`. This did NOT activate the new build because `public_html/index.php` still boots `idlmatch_app`. Nothing was damaged. Helper upgraded so it: finds the zip in either `idlmatch_app` or `public_html`, extracts it into `idlmatch_app` itself (with unsafe-path check), deletes the stray web-root zip, syncs `idlmatch_app/public/*` into `public_html`, replaces `public_html/storage` with a symlink, appends the security block (now also blocks `app.9047`-style folders and `_snapshot_old`), patches `.env` (APP_ENV/APP_DEBUG/APP_URL/LOG_LEVEL, backup first), clears caches and runs `migrate --force`. Syntax-checked, 12.1 KB.
- **2026-11-05 (session 6 - INCIDENT):** user deleted everything in the account root except the deploy zip; `idlmatch_app` (live app) is gone and `public_html` now contains only `idlmatch-deploy-20261105-0605.zip`. Live site returns **403** (`https://idlmatch.com/` 403, `/static/app.css` 404, `/storage/...` 404, `/robots.txt` still 200 from cache/CDN). The MySQL database is on a separate service and was NOT deleted. Files may be recoverable from File Manager **Trash bin** or hPanel **Files -> Backups**. Do not delete or upload anything until recovery is attempted. Local project still has `vendor/` (11,157 files, 71.3 MB) and `storage/` (691 files, 31.9 MB) so a full rebuild is possible; `.env` must be rebuilt from hPanel -> Databases credentials.
- **2026-11-05 (session 7 - files restored):** user used hPanel -> Files -> Backups -> "Files restored from backup" (version from 2026-11-01 05:06). Files are back (no more 403) but every app route now returns **500** with an empty body; `/static/app.css` = 200 (115,327 b) and `/robots.txt` = 200 (24 b, stale) so the web root is populated again. `/artisan`, `/composer.json`, `/sitemap.xml`, `/` all 500. Cause unknown - prepared a read-only diagnostic `_local/deploy/diag.php` (token protected) that reports app/web paths, `.env` keys, storage tree, last 40 log lines (secrets masked) and the real boot exception.
- **NEXT ACTION FOR THE USER:** upload `_local/deploy/diag.php` to `/home/u599271327/domains/idlmatch.com/public_html/diag.php`, open `https://idlmatch.com/diag.php?token=idl-9f3c7a1e5b2d6c4a`, send the output. Do not upload the deploy build yet.
- **2026-11-06 (session 9 - footer ad + credit + blog images LIVE):** FTP password recovered from the CuteFTP site-manager log (`u599271327`). Uploaded 7 files directly over FTP to `/idlmatch_app/` (`public-footer.blade.php`, `credit.blade.php`, `layouts/auth-full.blade.php`, `blog/show.blade.php`, `app/Support/Media.php`, `config/magzel_guard.php`, the blog-image backfill migration), backed up the previous versions to `_local/deploy/backup-20261106/`, then ran a one-shot `idl-migrate.php` in `public_html` (config/cache/view/route clear + `migrate --force`) and deleted it. Verified live: `/`, `/blog`, `/login`, `/register`, one blog article all 200; footer credit is "Developed by IDLTech" (no Magzel credit); AdSense slot `8390742781` present on every page; `/blog` now renders all 10 post thumbnails (13 posts, 10 with images). Follow-up (same session, user request): the pre-footer unit in `layouts/marketing|app|guest.blade.php` was switched from slot `8553482905` to the new slot `8390742781`, uploaded, views cleared, and verified live (`newSlot=2, oldSlot=0` on marketing/app/guest and auth-full-with-chrome pages; 1 on bare auth pages).
