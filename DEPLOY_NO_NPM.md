# Deploy Without npm (Shared Hosting)

This project now supports running without `npm` on the server.

## How it works

- Layouts load assets through `resources/views/components/frontend-assets.blade.php`.
- If `public/build/manifest.json` exists, it uses Vite build output.
- If it does not exist, it falls back to:
  - `public/static/app.css`
  - `public/static/app.js`

## For your hosting

1. Upload/pull code normally.
2. Ensure these files are present:
   - `public/static/app.css`
   - `public/static/app.js`
3. Run only PHP/Laravel commands on hosting:
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`

No `npm install` or `npm run build` is required on hosting.

## When frontend changes in future

Build assets on local machine (or CI), then refresh static files:

1. `npm run build`
2. Copy latest built files to:
   - `public/static/app.css`
   - `public/static/app.js`
3. Deploy code to hosting.
