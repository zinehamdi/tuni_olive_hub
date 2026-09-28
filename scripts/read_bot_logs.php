<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = App\Models\BotMessageLog::latest('id')->take(5)->get();
foreach($logs as $l) {
    echo "====================================\n";
    echo "LOG ID: " . $l->id . " | Date: " . $l->created_at . " | Channel: " . $l->channel . "\n";
    echo "Ext ID: " . $l->external_id . "\n";
    echo "User: " . $l->user_message . "\n";
    echo "Bot: " . $l->bot_reply . "\n";
}
