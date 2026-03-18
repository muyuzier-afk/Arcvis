# Arcvis

A lightweight PHP 8 + MySQL retro monochrome CMS/blog/work showcase with:

- public blog and work portfolio pages
- admin dashboard for posts, works, and settings
- i18n (`en`, `zh-CN`)
- web installer that bootstraps the database and first admin account
- black/white terminal typewriter visual theme

## Requirements

- PHP 8.0+
- PDO MySQL extension
- MySQL 8+

## Run locally

```bash
php -S 127.0.0.1:8000 -t public
```


- `http://127.0.0.1:8000/install.php` for first-time install
- `http://127.0.0.1:8000/index.php` for the public site
- `http://127.0.0.1:8000/admin/login.php` for admin login

## Default structure

- `public/` public pages and assets
- `admin/` admin pages
- `app/` core classes
- `data/schema.sql` database schema and seed structure
- `storage/` generated config and runtime files