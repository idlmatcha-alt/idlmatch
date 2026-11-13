# IDL Mail Agent v2

Central PHP/MySQL shared-hosting agent for **idlmatch.com**, supporting **idlfx.com** and **idlmatch.com**.

## Included
- Dawn/Jang public-source collector with same-domain crawling.
- Email extraction from HTML and text-layer PDFs.
- Deduplication and source tracking.
- Contact states: pending, approved, blocked, unsubscribed.
- Separate brand campaigns and authenticated SMTP sender.
- Dry-run mode, sending cap, pacing, and send logs.
- Fixed CSRF/session handling in the admin panel.
- Protected storage directory.

## Sending policy
A public email address is not by itself proof of permission for marketing. Keep contacts pending until you have a lawful/appropriate basis to contact them, and honor unsubscribe/suppression requests. This application is not intended to be an unrestricted bulk-spam sender.

## cPanel install
1. Create a MySQL database/user.
2. Import `schema.sql` in phpMyAdmin.
3. Copy `config.example.php` to `config.php` and set DB/SMTP values.
4. Create an admin hash with:
   `php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"`
5. Upload this folder to `public_html/agent/` on idlmatch.com.
6. Open `https://idlmatch.com/agent/admin/`.
7. Add the desired Dawn/Jang collection URLs in the dashboard.
8. Configure a cPanel Cron Job calling `cron/run.php` every 15–30 minutes.

Example:
`*/15 * * * * /usr/local/bin/php /home/CPANELUSER/public_html/agent/cron/run.php >> /home/CPANELUSER/public_html/agent/storage/logs/cron.log 2>&1`

Keep `sending.dry_run=true` until collection, SMTP, and your recipient authorization are verified.

## DNS/email deliverability
Use a mailbox on the sending domain and configure SPF, DKIM, and DMARC. Monitor bounces and complaints.

## Jang PDFs
Text-layer PDFs are supported. Image-only scanned PDFs need OCR/document processing and are not automatically OCR'd on shared hosting.

## Deployment limitation
This bundle is ready to upload to cPanel/shared hosting, but I cannot directly log into a private hosting account from this chat unless an actual deployment connection to that account is provided. Do not send cPanel/FTP passwords in chat.
