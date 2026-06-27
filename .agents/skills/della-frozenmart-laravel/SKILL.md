---
name: della-frozenmart-laravel
description: Use when working on the Della Frozenmart Laravel stock management web app, including product stock, purchase invoices, CRUD, database, Blade UI, hosting-safe fixes, and documentation.
---

# Della Frozenmart Laravel Stock Web Skill

## Project Context
This project is a Laravel web application for product stock management.
The project is already hosted.
The agent must prioritize safe, minimal, and traceable changes.

## Main Rules
1. Read routes, controllers, models, migrations, views, and config before editing.
2. Make a short plan before changing files.
3. Keep changes small and focused.
4. Do not edit `.env`, hosting credentials, `vendor`, or production data without explicit approval.
5. Do not run destructive database commands.
6. For database changes, create a new migration instead of editing old migrations.
7. For UI changes, keep the existing Blade structure consistent and responsive.
8. For stock features, protect data integrity between products, purchases, sales, and stock movement.
9. After editing, explain changed files and verification steps.

## Safe Commands
- php artisan route:list
- php artisan migrate:status
- php artisan test
- php artisan config:clear
- php artisan cache:clear
- php artisan view:clear

## Forbidden Without Approval
- php artisan migrate:fresh
- php artisan db:wipe
- composer update
- deleting storage files
- changing production `.env`
- modifying hosting credentials
- force pushing git history

## Output Format
When finishing a task, report:
1. Files changed.
2. Reason for each change.
3. Test or verification result.
4. Deployment note if needed.
