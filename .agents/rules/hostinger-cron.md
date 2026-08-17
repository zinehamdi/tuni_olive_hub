# Hostinger Cron Job Configuration (zintoop.com)

If the user asks about or needs to set up the email queue cron job on Hostinger, the **ONLY** correct configuration is:

## The Issue
Hostinger's default `php` binary in the Custom Cron environment points to an old, broken PHP version (like 7.x) which crashes Laravel 11. Using the "PHP" radio button in hPanel also causes syntax issues when combined with `cd`.

## The Solution
The user MUST use the **Custom** radio button in Hostinger Cron Jobs and use the absolute path to the PHP 8.3 binary (`/opt/alt/php83/usr/bin/php`).

### Exact Command for Hostinger hPanel:
```bash
cd /home/u346640129/domains/zintoop.com/public_html && /opt/alt/php83/usr/bin/php artisan queue:work --stop-when-empty
```

### Setup Instructions for User:
1. Go to Hostinger Cron Jobs.
2. Select the **Custom** radio button (NOT the PHP one).
3. Paste the exact command above.
4. Set the schedule manually to **Every minute (* * * * *)** by selecting `(*)` for all 5 dropdown boxes.
5. Ignore the generic "you may lose all data" warning (it's just a standard Hostinger alert for editing crons).
6. Save.

## Bulk Email Consideration
This project has a custom bulk email system (`php artisan mail:send-bulk`) that uses `sleep(1)` to throttle emails and avoid SMTP rate limits. Therefore, `QUEUE_CONNECTION` must remain `database`. Do **NOT** change it to `sync`, as it will block the UI for normal users.
