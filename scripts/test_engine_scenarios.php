<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$engine = app(App\Services\Bot\EzzitouniSocialEngine::class);

echo "=== TEST 1: General Greeting ===\n";
$res1 = $engine->generateResponse("عسلامة، يعطيك الصحة", "facebook_comment", "test_1_" . time(), "كريم");
echo "Reply 1:\n" . $res1['reply'] . "\n\n";

echo "=== TEST 2: Price Question ===\n";
$res2 = $engine->generateResponse("سلام، بقداش سوم الزيت والزيتون توة في صفاقس وسيدي بوزيد؟", "facebook_comment", "test_2_" . time(), "صالح");
echo "Reply 2:\n" . $res2['reply'] . "\n\n";

echo "=== TEST 3: Trademark / INNORPI Question ===\n";
$res3 = $engine->generateResponse("سلام، نحب نعمل مارك ديبوزي للزيت متاعي، قداش تتكلف؟", "facebook_comment", "test_3_" . time(), "منير");
echo "Reply 3:\n" . $res3['reply'] . "\n\n";
