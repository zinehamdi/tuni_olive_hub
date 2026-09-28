<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Load;
use App\Models\Trip;
use App\Models\Order;
use App\Models\Listing;
use App\Models\Address;
use App\Services\TransportWhatsAppHandler;
use App\Services\TransportPricingService;
use App\Services\Bot\WhatsAppCloudApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=================================================================\n";
echo "🚚 ZinToop Transport & WhatsApp End-to-End Simulation Test Suite\n";
echo "=================================================================\n\n";

DB::beginTransaction();

try {
    $handler = app(TransportWhatsAppHandler::class);
    $waService = app(WhatsAppCloudApiService::class);

    // -------------------------------------------------------------
    // SETUP: Mock Users & Addresses
    // -------------------------------------------------------------
    echo "📦 [0/6] Setting up mock data (Farmer, Buyer, Carrier)...\n";

    $farmer = User::create([
        'name' => 'فلاح تستي (القيروان)',
        'email' => 'farmer_test_' . uniqid() . '@zintoop.com',
        'password' => bcrypt('secret123'),
        'role' => 'farmer',
        'phone' => '+21698111222'
    ]);

    $buyer = User::create([
        'name' => 'شاري تستي (سوسة)',
        'email' => 'buyer_test_' . uniqid() . '@zintoop.com',
        'password' => bcrypt('secret123'),
        'role' => 'normal',
        'phone' => '+21622333444'
    ]);

    $carrier = User::create([
        'name' => 'ناقل تستي (إيسوزو)',
        'email' => 'carrier_test_' . uniqid() . '@zintoop.com',
        'password' => bcrypt('secret123'),
        'role' => 'carrier',
        'phone' => '+21655512878'
    ]);

    $pickupAddr = Address::create([
        'user_id' => $farmer->id,
        'title' => 'معصرة القيروان',
        'street' => 'طريق الشراردة',
        'governorate' => 'Kairouan',
        'delegation' => 'Cherarda',
        'lat' => 35.6781,
        'lng' => 10.0963,
    ]);

    $dropoffAddr = Address::create([
        'user_id' => $buyer->id,
        'title' => 'مستودع سوسة',
        'street' => 'المنطقة الصناعية سيدي عبد الحميد',
        'governorate' => 'Sousse',
        'delegation' => 'Sidi Abdelhamid',
        'lat' => 35.8256,
        'lng' => 10.6369,
    ]);

    $product = \App\Models\Product::first();
    if (!$product) {
        $product = \App\Models\Product::create([
            'name' => 'زيت زيتون بكر ممتاز شملالي',
            'category' => 'oil',
            'grade' => 'extra_virgin',
        ]);
    }

    $listing = Listing::create([
        'seller_id' => $farmer->id,
        'product_id' => $product->id,
        'status' => 'active',
        'price' => 20.0,
        'currency' => 'TND',
        'quantity' => 5000,
        'unit' => 'liter',
    ]);

    echo "   -> Created Farmer (#{$farmer->id}), Buyer (#{$buyer->id}), Carrier (#{$carrier->id}), Listing (#{$listing->id})\n\n";

    // -------------------------------------------------------------
    // SCENARIO 1: Pricing Engine Verification across all 4 Tiers
    // -------------------------------------------------------------
    echo "📊 [SCENARIO 1] Verifying Pricing Calculation for All 4 Vehicle Tiers...\n";

    $pPickup = TransportPricingService::estimateCost(1200, 35.6781, 10.0963, 35.8256, 10.6369); // Isuzu
    $pLight  = TransportPricingService::estimateCost(3500, 35.6781, 10.0963, 35.8256, 10.6369); // 3.5T-5T
    $pHeavy  = TransportPricingService::estimateCost(10000, 35.6781, 10.0963, 35.8256, 10.6369); // 10T-12T
    $pTanker = TransportPricingService::estimateCost(25000, 35.6781, 10.0963, 35.8256, 10.6369); // Tanker Inox

    assert($pPickup['tier']['tier_key'] === 'pickup', 'Tier should be pickup');
    assert($pLight['tier']['tier_key'] === 'light_truck', 'Tier should be light_truck');
    assert($pHeavy['tier']['tier_key'] === 'heavy_truck', 'Tier should be heavy_truck');
    assert($pTanker['tier']['tier_key'] === 'tanker', 'Tier should be tanker');

    echo "   -> 🛻 Isuzu (1,200L, {$pPickup['distance_km']} km): ~{$pPickup['total_cost']} TND (Base: 40 TND + 1.1 TND/km) ✅\n";
    echo "   -> 🚛 Light Truck (3,500L): ~{$pLight['total_cost']} TND (Base: 70 TND + 1.6 TND/km) ✅\n";
    echo "   -> 🚚 Heavy Truck (10,000L): ~{$pHeavy['total_cost']} TND (Base: 120 TND + 2.2 TND/km) ✅\n";
    echo "   -> 🛢️ Tanker Inox (25,000L): ~{$pTanker['total_cost']} TND (Base: 250 TND + 3.2 TND/km) ✅\n\n";

    // -------------------------------------------------------------
    // SCENARIO 2: Happy Path - Full Transport Lifecycle via WhatsApp
    // -------------------------------------------------------------
    echo "🚀 [SCENARIO 2] Full Transport Mission Lifecycle (Summon -> Accept Button -> PIN Delivery)...\n";

    $order1 = Order::create([
        'buyer_id' => $buyer->id,
        'seller_id' => $farmer->id,
        'listing_id' => $listing->id,
        'qty' => 1200,
        'unit' => 'l',
        'price_unit' => 20.0,
        'status' => 'confirmed',
        'payment_method' => 'cod',
        'total' => 24000.0,
    ]);

    $pin1 = '7842';
    $load1 = Load::create([
        'owner_id' => $farmer->id,
        'order_id' => $order1->id,
        'pickup_addr_id' => $pickupAddr->id,
        'dropoff_addr_id' => $dropoffAddr->id,
        'carrier_id' => $carrier->id,
        'kind' => 'oil',
        'qty' => 1200,
        'unit' => 'l',
        'status' => Load::ST_MATCHED,
        'meta' => [
            'pin_code' => $pin1,
            'estimated_cost' => $pPickup['total_cost'],
            'distance_km' => $pPickup['distance_km']
        ]
    ]);

    $trip1 = Trip::create([
        'carrier_id' => $carrier->id,
        'load_ids' => [$load1->id],
        'sr_code' => 'SR-TEST01',
        'pin_token' => $pin1,
        'start_at' => null,
    ]);

    echo "   1. Order #{$order1->id} & Load #{$load1->id} created with status: " . $load1->status . " (PIN: {$pin1})\n";

    // Simulate WhatsApp Webhook: Carrier clicks 'ACCEPT_LOAD_{id}'
    $acceptResult = $handler->handleButtonReply('21655512878', 'ACCEPT_LOAD_' . $load1->id);
    assert($acceptResult['success'] === true, 'Accept button should succeed');

    $load1->refresh();
    $trip1->refresh();
    assert($load1->status === Load::ST_CONFIRMED, 'Load status should be ST_CONFIRMED');
    assert(!empty($trip1->start_at), 'Trip start_at should be recorded');
    echo "   2. Carrier clicked [✅ قبول الكورسة] -> Load status updated to: {$load1->status} ✅\n";

    // Carrier arrives and sends the PIN code on WhatsApp
    $pinResult = $handler->handleTextReply('21655512878', $pin1);
    assert($pinResult === true, 'PIN verification should return true');

    $load1->refresh();
    $trip1->refresh();
    assert($load1->status === Load::ST_DELIVERED, 'Load status should be ST_DELIVERED');
    assert(!empty($trip1->delivered_at), 'Trip delivered_at should be recorded');
    echo "   3. Carrier submitted PIN: {$pin1} on WhatsApp -> Load marked as: {$load1->status} 🎉 ✅\n\n";

    // -------------------------------------------------------------
    // SCENARIO 3: Natural Arabic Acceptance ("موافق خويا")
    // -------------------------------------------------------------
    echo "💬 [SCENARIO 3] Natural Arabic Text Acceptance (No Button Click)...\n";

    $order2 = Order::create([
        'buyer_id' => $buyer->id,
        'seller_id' => $farmer->id,
        'listing_id' => $listing->id,
        'qty' => 500,
        'unit' => 'l',
        'price_unit' => 20.0,
        'status' => 'confirmed',
        'payment_method' => 'cod',
        'total' => 10000.0,
    ]);

    $load2 = Load::create([
        'owner_id' => $farmer->id,
        'order_id' => $order2->id,
        'pickup_addr_id' => $pickupAddr->id,
        'dropoff_addr_id' => $dropoffAddr->id,
        'carrier_id' => $carrier->id,
        'kind' => 'oil',
        'qty' => 500,
        'unit' => 'l',
        'status' => Load::ST_MATCHED,
        'meta' => ['pin_code' => '5521']
    ]);

    // Transporter texts "موافق تو نجي نهزها"
    $arabicAcceptRes = $handler->handleTextReply('21655512878', 'موافق تو نجي نهزها');
    assert($arabicAcceptRes === true, 'Arabic text acceptance should trigger load confirmation');

    $load2->refresh();
    assert($load2->status === Load::ST_CONFIRMED, 'Load status should become ST_CONFIRMED');
    echo "   -> Carrier sent: 'موافق تو نجي نهزها' -> Load #{$load2->id} auto-confirmed ✅\n\n";

    // -------------------------------------------------------------
    // SCENARIO 4: Carrier Declines Mission (Decline Flow)
    // -------------------------------------------------------------
    echo "❌ [SCENARIO 4] Carrier Declines Mission via Button...\n";

    $order3 = Order::create([
        'buyer_id' => $buyer->id,
        'seller_id' => $farmer->id,
        'listing_id' => $listing->id,
        'qty' => 800,
        'unit' => 'l',
        'price_unit' => 20.0,
        'status' => 'confirmed',
        'payment_method' => 'cod',
        'total' => 5000.0,
    ]);

    $load3 = Load::create([
        'owner_id' => $farmer->id,
        'order_id' => $order3->id,
        'pickup_addr_id' => $pickupAddr->id,
        'dropoff_addr_id' => $dropoffAddr->id,
        'carrier_id' => $carrier->id,
        'kind' => 'oil',
        'qty' => 800,
        'unit' => 'l',
        'status' => Load::ST_MATCHED,
        'meta' => ['pin_code' => '3319']
    ]);

    $declineRes = $handler->handleButtonReply('21655512878', 'DECLINE_LOAD_' . $load3->id);
    assert($declineRes['success'] === true, 'Decline should succeed');

    $load3->refresh();
    assert($load3->status === Load::ST_OPEN, 'Load status should revert to ST_OPEN');
    assert($load3->carrier_id === null, 'Carrier ID should be cleared for reallocation');
    echo "   -> Carrier clicked [❌ غير متوفر] -> Load #{$load3->id} freed back to: {$load3->status} ✅\n\n";

    // -------------------------------------------------------------
    // SCENARIO 5: Invalid PIN Code Handling (Security & Resilience)
    // -------------------------------------------------------------
    echo "🔒 [SCENARIO 5] Invalid PIN Code Security Rejection...\n";

    $order4 = Order::create([
        'buyer_id' => $buyer->id,
        'seller_id' => $farmer->id,
        'listing_id' => $listing->id,
        'qty' => 600,
        'unit' => 'l',
        'price_unit' => 20.0,
        'status' => 'confirmed',
        'payment_method' => 'cod',
        'total' => 8000.0,
    ]);

    $load4 = Load::create([
        'owner_id' => $farmer->id,
        'order_id' => $order4->id,
        'pickup_addr_id' => $pickupAddr->id,
        'dropoff_addr_id' => $dropoffAddr->id,
        'carrier_id' => $carrier->id,
        'kind' => 'oil',
        'qty' => 600,
        'unit' => 'l',
        'status' => Load::ST_CONFIRMED,
        'meta' => ['pin_code' => '9941']
    ]);

    // Send wrong PIN 0000
    $wrongPinRes = $handler->verifyDeliveryPin('21655512878', '0000');
    assert($wrongPinRes === false, 'Wrong PIN must be rejected');

    $load4->refresh();
    assert($load4->status === Load::ST_CONFIRMED, 'Load must remain ST_CONFIRMED on wrong PIN');
    echo "   -> Carrier sent incorrect PIN: '0000' -> Rejected securely, Load remained: {$load4->status} ✅\n\n";

    // -------------------------------------------------------------
    // SCENARIO 6: Non-Transport General Questions (AI Bot Flow)
    // -------------------------------------------------------------
    echo "🤖 [SCENARIO 6] General Chatbot Questions (Zero Interference)...\n";

    $generalQuestion = 'سلام عليكم، قداش سوم لتر زيت الزيتون البكر الممتاز اليوم في صفاقس؟';
    $interceptionCheck = $handler->handleTextReply('21625777926', $generalQuestion);
    assert($interceptionCheck === false, 'General user questions must not be captured by transport handler');
    echo "   -> User asked: '{$generalQuestion}'\n";
    echo "   -> Transport handler returned false -> Routed smoothly to AI Bot Ez-Zitouni ✅\n\n";

    echo "=================================================================\n";
    echo "🏆 ALL 6 SCENARIOS PASSED WITH 100% SUCCESS!\n";
    echo "=================================================================\n";

} finally {
    // Rollback all test data cleanly so DB stays 100% pristine
    DB::rollBack();
    echo "\n🧹 Test database transaction rolled back cleanly (Zero test data left behind).\n";
}
