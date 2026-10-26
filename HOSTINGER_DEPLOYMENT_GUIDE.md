# Hostinger Deployment Guide

This document outlines the exact sequence of commands to execute when deploying updates to the production server.

## Context
- **PHP Version**: 8.2.30 (LiteSpeed Web Server)
- **Application Root**: `/home/u346640129/domains/zintoop.com/public_html`
- **SSH Port**: 65002

## Deployment Steps

1. **Access the Server via SSH**:
   ```bash
   ssh -p 65002 u346640129@147.93.54.167
   # (Password: Zine2026$)
   ```

2. **Navigate to the Correct Directory**:
   ```bash
   cd domains/zintoop.com/public_html
   ```

3. **Reset and Pull Latest Code**:
   ```bash
   git reset --hard HEAD
   git pull origin main
   ```

4. **Build Assets**:
   ```bash
   npm install
   npm run build
   ```

5. **Run Migrations & Clear Laravel Caches**:
   ```bash
   php artisan migrate --force
   php artisan route:clear
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   ```
   > **Note**: `route:clear` is critical when new routes are added or modified. Never skip it.

6. **Clear Server RAM (OPcache/LiteSpeed)**:
   ```bash
   killall -9 lsphp
   ```

7. **Cron & Queue Management**:
   Refer to [`CRON_AND_QUEUE_GUIDE.md`](./CRON_AND_QUEUE_GUIDE.md) for background worker and email queue operations.

> [!WARNING]
> Do **NOT** use `~/laravel_app/` for deployments, as it is outdated. The active live site runs entirely from `domains/zintoop.com/public_html`.

---

## ⚡ One-Line Full Deployment (Copy-Paste Ready)

For routine updates after `git push origin main` locally:

```bash
ssh -p 65002 u346640129@147.93.54.167 'cd domains/zintoop.com/public_html && git reset --hard HEAD && git pull origin main && npm install --silent && npm run build && php artisan migrate --force && php artisan route:clear && php artisan view:clear && php artisan config:clear && php artisan cache:clear && killall -9 lsphp && echo "✅ Deployed successfully"'
```

> [!NOTE]
> If you need to add `OBFUSCATOR_SALT` or any new env variable, do it **before** running the above command via `nano .env` on the server.

*Last updated: September 7, 2026*
