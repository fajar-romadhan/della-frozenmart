# AGENTS.md

## Project
Della Frozenmart is a Laravel stock management web application.
The project is already hosted, so all changes must be safe, minimal, and easy to rollback.

## Tech Stack
- Laravel
- PHP
- Blade
- MySQL
- Composer
- Node.js
- Vite or frontend assets if used by the project

## Main Features
- Product management
- Stock management
- Purchase invoice
- Dashboard
- User access
- Database migration
- Hosting deployment

## Rules for AI Agent
1. Inspect existing files before editing.
2. Do not rewrite the whole project.
3. Do not touch `.env` unless explicitly requested.
4. Do not run destructive database commands.
5. Prefer new migrations over editing old migrations.
6. Keep UI consistent with the existing design.
7. Explain every changed file.
8. Run verification commands when possible.

## Safe Commands
- php artisan route:list
- php artisan migrate:status
- php artisan test
- php artisan config:clear
- php artisan cache:clear
- php artisan view:clear
- composer install
- npm.cmd install
- npm.cmd run build

## Forbidden Commands Without Approval
- php artisan migrate:fresh
- php artisan db:wipe
- composer update
- deleting storage files
- changing production `.env`
- force push

## Deployment & Live Environment Info
- **Live URL**: http://dellafrozenmart.my.id/
- **Remote Repo**: https://github.com/fajar-romadhan/della-frozenmart.git
- **Deployment Method**: SSH Terminal on cPanel hosting server.
  - Commands to pull changes on hosting terminal:
    ```bash
    cd public_html
    git stash
    git pull origin main
    git stash pop
    ```
- **Laravel Cache Clearing**: Clean cache on hosting by opening:
  `http://dellafrozenmart.my.id/clean.php?key=DellaFrozenMart2026_SecureKey`
- **Security Access Token**: `DellaFrozenMart2026_SecureKey` (defined as `DEMO_RESET_KEY` in `.env`). Append `?key=DellaFrozenMart2026_SecureKey` to access utility files (`clean.php`, `reset_demo.php`, `diagnose.php`, `check_count.php`).

## Last Session Status (15 July 2026)
* **Product Name Alignment**: Resolved product name mismatches between database/seeder and Excel Gabungan columns via migration `2026_07_15_000000_align_product_names_with_excel.php` (e.g. `PRD-0019` renamed to `Chicken Nugget Stick 500g`).
* **Code Simplification**: Removed manual/hardcoded mapping overrides (`$aliases` / `$aliasMapping`) in `reset_demo.php`, `check_db_excel.php`, `ImportPenjualanController.php`, and `InventoryAnalysisController.php` since names now match exactly.
* **Safe Bootstrapping**: Refactored Laravel bootstrap requires in `reset_demo.php` and `check_db_excel.php` to prevent `Call to a member function make() on bool` when loaded sequentially.
* **Import Verification**: 
  - Sales data (Excel): 3491 rows imported cleanly (PAS 100% sync, 0 differences across all 31 products).
  - Incoming goods (Word): 132 rows imported cleanly with balanced locations.
  - Active stock: 10 products have stock loaded from Word; 21 products have 0 stock (expected).
  - Outgoing goods: 0 records (clean slate for manual transaction tests).
* **Deployment**: Changes pushed to `origin/main`, pulled on live hosting server, migrated, and verified successfully.

