<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$engine = app(App\Services\Bot\EzzitouniSocialEngine::class);

echo "=== SCENARIO 1: First Contact on Price Post ===\n";
$firstMsgPrice = $engine->getInitialGreeting("prix", "تحيين أسعار زيت الزيتون في أسواق تونس اليوم");
echo "Reply 1:\n" . $firstMsgPrice . "\n\n";

echo "=== SCENARIO 2: First Contact on Trademark / INNORPI Post ===\n";
$firstMsgInnorpi = $engine->getInitialGreeting("مهتم", "دليل تسجيل وحماية علامتك التجارية لزيت الزيتون لدى INNORPI");
echo "Reply 2:\n" . $firstMsgInnorpi . "\n\n";

echo "=== SCENARIO 3: First Contact from Farmer Selling Harvest ===\n";
$firstMsgFarmer = $engine->getInitialGreeting("عندي صابة زيتون وزيت للبيع", null);
echo "Reply 3:\n" . $firstMsgFarmer . "\n\n";

echo "=== SCENARIO 4: First Contact from Buyer / Exporter ===\n";
$firstMsgBuyer = $engine->getInitialGreeting("نحب نشري شحنة للتصدير", null);
echo "Reply 4:\n" . $firstMsgBuyer . "\n\n";

echo "=== SCENARIO 5: General Greeting ===\n";
$firstMsgGeneral = $engine->getInitialGreeting("سلام عليكم", null);
echo "Reply 5:\n" . $firstMsgGeneral . "\n\n";
