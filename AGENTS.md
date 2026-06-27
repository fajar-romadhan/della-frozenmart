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
