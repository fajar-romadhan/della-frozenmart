---
name: hosting-safe-deploy
description: Use when preparing deployment or hosting-safe changes for a Laravel project that is already live.
---

# Hosting Safe Deploy Skill

## Use This Skill When
Use this skill before changing files that may affect a hosted Laravel application.

## Instructions
1. Check whether the change affects routes, database, assets, storage, cache, or environment config.
2. Do not assume the hosting supports all local commands.
3. Prepare a safe deployment checklist.
4. Separate local development commands from hosting commands.
5. Warn before any change that can break production.
6. Prefer reversible changes.
7. Do not expose secrets, database credentials, cPanel credentials, or `.env` values.

## Laravel Hosting Checklist
Before deployment:
- Check composer.json.
- Check migration impact.
- Check route changes.
- Clear Laravel cache after deployment if needed.
- Verify login, dashboard, product CRUD, stock update, and invoice export.

## Forbidden Without Approval
- Running destructive migration.
- Replacing production database.
- Removing uploaded files.
- Changing app key.
- Sharing secrets in chat.
