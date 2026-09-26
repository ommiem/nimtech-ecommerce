# Deployment

Production deploys from the `main` branch after the Nimtech hosting target and
deployment credentials have been configured.

Build assets are committed to Git for this project. Always run the local build before committing frontend or Blade/CSS-related changes so `public/build` is included in the same deploy commit.

Local release prep:

```powershell
.\scripts\prepare-production-commit.ps1
git commit -m "Your deploy message"
git push origin main
```

Example server deploy (replace the placeholders with the Nimtech hosting
details):

```bash
ssh <nimtech-host>
cd <nimtech-document-root>
git pull --ff-only origin main
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
```

Verify:

```bash
git log -1 --oneline
php artisan migrate:status
```
