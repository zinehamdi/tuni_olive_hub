#!/bin/bash
cd /home/u346640129/domains/zintoop.com/public_html
echo '=== 1. GIT RESET & PULL ==='
git reset --hard HEAD
git pull origin main

echo '=== 2. UPDATE BOT SYSTEM PROMPT IN PROD DB ==='
php scripts/update_bot_prompt_db.php

echo '=== 3. CLEAR LARAVEL CACHES ==='
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear

echo '=== 4. REFRESH OPCACHE / LSPHP ==='
killall -9 lsphp 2>/dev/null || true

echo '=== 5. TEST EZZITOUNI SOCIAL ENGINE ==='
php -r '
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$engine = app(App\Services\Bot\EzzitouniSocialEngine::class);
$res = $engine->generateResponse("سلام، نحب نسجل مارك ديبوزي لزيت الزيتون متاعي، شكوني الادارة والوثائق المطلوبة والاسعار؟", "facebook_comment", "test_comment_innorpi_2", "سامي");
echo "\n>>> LIVE BOT REPLY:\n" . $res["reply"] . "\n";
'
