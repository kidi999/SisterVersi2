# Copilot & AI Agent Instructions for SISTER

## Project Overview
- **SISTER** is a Laravel 12 (PHP 8.2) web-based academic information system for university management (students, faculty, courses, grades, schedules).
- Main app code is in `app/` (MVC: Controllers, Models), views in `resources/views/`, routes in `routes/web.php`.
- Database: MySQL, schema managed via Laravel migrations in `database/migrations/`.
- Frontend: Blade templates + Bootstrap 5.3.

## Key Workflows
- **Start local dev server:**
  ```bash
  php artisan serve
  # Or use a different port if 8000 is busy:
  php artisan serve --port=8001
  ```
- **Run migrations:**
  ```bash
  php artisan migrate
  ```
- **Install dependencies:**
  ```bash
  composer install
  ```
- **Sample data** is included by default (see `README_SISTER.md`).

## Project Structure & Conventions
- **Controllers:** `app/Http/Controllers/` (e.g., `FakultasController.php`, `MahasiswaController.php`).
- **Models:** `app/Models/` (e.g., `Fakultas.php`, `Mahasiswa.php`).
- **Views:** `resources/views/` (Blade, e.g., `dashboard.blade.php`, `fakultas/`).
- **Routes:** All main routes in `routes/web.php`.
- **Database:**
  - Migrations: `database/migrations/`
  - Seeders: `database/seeders/`
- **Config:** Environment variables in `.env` (DB, mail, etc.).
- **Public entry:** `public/index.php`.

## Patterns & Practices
- **RESTful CRUD** for main entities (Fakultas, Mahasiswa, Dosen, etc.)
- **Blade** for all HTML rendering; use layouts in `resources/views/layouts/`.
- **Bootstrap** for UI; use Bootstrap Icons.
- **No SPA/JS framework**: All logic is server-side PHP/Blade.
- **Use Eloquent ORM** for all DB access; avoid raw SQL unless necessary.
- **Follow Laravel naming conventions** for controllers, models, migrations.
- **Routes** are grouped by entity (see `web.php`).

## Troubleshooting
- If DB errors: check `.env` and run `php artisan migrate`.
- If port 8000 busy: use `php artisan serve --port=8001`.
- If dependencies fail: run `composer install`.

## Extending SISTER
- Add new modules by creating new Model, Controller, Blade view, and route entries.
- For new DB tables, add migration in `database/migrations/` and run `php artisan migrate`.
- See `README_SISTER.md` for more details and sample data.

---
For more, see `README_SISTER.md` and code comments in each module.
