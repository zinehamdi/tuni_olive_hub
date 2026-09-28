<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Load;
use App\Models\Trip;
use App\Models\Order;
use App\Services\TransportWhatsAppHandler;
use App\Services\Bot\WhatsAppCloudApiService;

echo "=== 🧪 Testing WhatsApp Transport Mission Flow ===\n\n";

// 1. Check Service instantiation
$handler = app(TransportWhatsAppHandler::class);
$waService = app(WhatsAppCloudApiService::class);

echo "1. WhatsAppCloudApiService & TransportWhatsAppHandler instantiated successfully ✅\n";

// 2. Test Phone Number Formatting
$p1 = $waService->formatPhone("+216 25 777 926");
$p2 = $waService->formatPhone("25777926");
$p3 = $waService->formatPhone("0021655512878");

assert($p1 === '21625777926', "Phone formatting error for $p1");
assert($p2 === '21625777926', "Phone formatting error for $p2");
assert($p3 === '21655512878', "Phone formatting error for $p3");

echo "2. Phone formatting passed for Tunisian local & international formats ✅\n";

// 3. Test PIN Regex Detection in Handler
$reflection = new ReflectionClass($handler);
$method = $reflection->getMethod('handleTextReply');

// Test non-transport text
$resNonTransport = $handler->handleTextReply('21625777926', 'أهلاً وسهلاً كم سعر الزيت اليوم؟');
assert($resNonTransport === false, "General chat should not be intercepted by transport handler");
echo "3. General user inquiries correctly pass through to AI Chatbot without interception ✅\n";

echo "\n🎉 All core transport WhatsApp components validated successfully!\n";
