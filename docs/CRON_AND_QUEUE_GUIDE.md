# Cron & Queue Management Guide - دليل إدارة المهام المجدولة وقائمة الانتظار

This document details the configuration, monitoring, and troubleshooting of background queues and cron jobs on the production server (Hostinger).

---

## 1. Production Cron Job Setup

In **Hostinger hPanel → Advanced → Cron Jobs**:

- **Type**: Custom Cron Job
- **Schedule**: Every minute (`* * * * *`)
- **Command**:
  ```bash
  cd /home/u346640129/domains/zintoop.com/public_html && php artisan queue:work --stop-when-empty >> storage/logs/queue.log 2>&1
  ```

---

## 2. Queue Operations & Maintenance via SSH

To inspect or manually manage the email queue on the live server:

### Connect to Server:
```bash
ssh -p 65002 u346640129@147.93.54.167
cd domains/zintoop.com/public_html
```

### Common Commands:

| Task | Command |
|---|---|
| **Process Queue Manually** | `php artisan queue:work --stop-when-empty --tries=3` |
| **Check Remaining Jobs Count** | `php artisan tinker --execute='echo "Jobs: " . DB::table("jobs")->count() . "\n";'` |
| **View Failed Jobs List** | `php artisan queue:failed` |
| **Retry All Failed Jobs** | `php artisan queue:retry all` |
| **Clear/Flush All Failed Jobs** | `php artisan queue:flush` |
| **Monitor Live Queue Log** | `tail -f storage/logs/queue.log` |

---

## 3. Troubleshooting Known Issues

### Issue: `ModelNotFoundException` during email sending
- **Cause**: An email was queued for a model (e.g. `User`) that was deleted from the database before the email could be processed.
- **Solution**: Running the queue worker with `--tries=3` ensures that missing models are caught and moved to `failed_jobs` after 3 failed attempts, preventing the queue worker from getting stuck.
- **Cleanup**: Delete stale failed jobs with `php artisan queue:flush`.
