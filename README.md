# Nimtech Ecommerce

Independent Laravel 12 ecommerce application for [nimtech.co.ke](https://nimtech.co.ke).

## Included modules

- Administrator dashboard and user management
- Products, brands, categories, stock and multiple product images
- Orders, checkout, promotions and M-Pesa metadata
- Editable pages, homepage slides, settings and theme controls
- WhatsApp lead tracking, campaigns and reports
- SEO metadata, canonical URLs, sitemaps and product redirects
- Dedicated Nimtech storefront theme

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL/MariaDB for production (SQLite is supported for local development)
- Node.js and pnpm/npm for frontend builds

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
pnpm install
pnpm run build
php artisan serve
```

Never commit `.env`, production credentials, customer data, database dumps or uploaded private documents.

## Deployment

Deployment details are documented in `DEPLOYMENT.md`. Do not point the live Nimtech domain at this application until it has been tested on a staging address and the existing Zivo URLs have a migration/redirect plan.
