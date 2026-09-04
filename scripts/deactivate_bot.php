<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\BotSetting::set('bot_enabled', '0', 'حالة تشغيل البوت العامة (معطل مؤقتاً)');

echo "=== BOT AUTO-REPLY HAS BEEN DEACTIVATED (bot_enabled = 0) ===\n";
