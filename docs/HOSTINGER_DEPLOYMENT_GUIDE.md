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
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   ```

6. **Clear Server RAM (OPcache/LiteSpeed)**:
   ```bash
   killall -9 lsphp
   ```

7. **Cron & Queue Management**:
   Refer to [`CRON_AND_QUEUE_GUIDE.md`](./CRON_AND_QUEUE_GUIDE.md) for background worker and email queue operations.

> [!WARNING]
> Do **NOT** use `~/laravel_app/` for deployments, as it is outdated. The active live site runs entirely from `domains/zintoop.com/public_html`.
