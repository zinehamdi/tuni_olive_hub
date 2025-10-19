<?php

namespace Tests\Feature;

use App\Events\OrderPaid;
use App\Events\TripDelivered;
use App\Events\TripStarted;
use App\Models\Address;
use App\Models\CarrierOffer;
use App\Models\Listing;
use App\Models\Load;
use App\Models\Order;
use App\Models\Product;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class Prompt1E2eTest extends TestCase
{
    use RefreshDatabase;

    protected function seedUsers(): array
    {
        $buyer = User::factory()->create(['role' => 'consumer']);
        $seller = User::factory()->create(['role' => 'farmer']);
        $carrier = User::factory()->create(['role' => 'trader_carrier']);
        $bAddr = Address::factory()->create(['user_id' => $buyer->id]);
        $sAddr = Address::factory()->create(['user_id' => $seller->id]);
        return compact('buyer','seller','carrier','bAddr','sAddr');
    }

    public function test_happy_path_cod_end_to_end(): void
    {
        Event::fake([TripStarted::class, TripDelivered::class, OrderPaid::class]);
        extract($this->seedUsers());
    $product = Product::factory()->create(['seller_id' => $seller->id, 'type' => 'oil', 'stock' => 100]);
    $listing = Listing::factory()->create(['product_id' => $product->id, 'seller_id' => $seller->id, 'min_order' => 5]);

        // Buyer creates order
        $this->actingAs($buyer);
        $res = $this->postJson('/api/v1/orders', [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'listing_id' => $listing->id,
            'qty' => 10,
            'unit' => 'l',
            'price_unit' => 8,
            'payment_method' => 'cod',
        ])->assertCreated();
        $orderId = $res->json('data.id');

        // Seller confirms and ready
        $this->actingAs($seller);
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'confirm'])->assertOk();
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'ready'])->assertOk();

    // Seller ships first time -> auto-load created, but not matched yet so 422
    $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'ship'])->assertStatus(422);
    // Create offer in-range, accept, then ship ok
        $load = Load::first();
        $this->actingAs($carrier);
    $this->postJson("/api/v1/loads/{$load->id}/offers", ['offer_price' => $load->price_floor ?? 8, 'eta_minutes' => 60])->assertCreated();
        $this->actingAs($seller);
        $offer = CarrierOffer::first();
        $this->patchJson("/api/v1/offers/{$offer->id}/accept")->assertOk();
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'ship'])->assertOk();

        // Carrier creates trip, starts
        $this->actingAs($carrier);
        $res = $this->postJson('/api/v1/trips', ['carrier_id' => $carrier->id, 'load_ids' => [$load->id]])->assertCreated();
        $tripId = $res->json('data.id');
        $start = $this->postJson("/api/v1/trips/{$tripId}/start")->assertOk();
        $pinMasked = $start->json('data.pin_token');
        $this->assertNotNull($pinMasked);

        // Submit POD wrong pin -> unverified
        $this->postJson("/api/v1/trips/{$tripId}/pod", [
            'dropoff_photos' => ['ph1'], 'signed_name' => 'X', 'signed_pin' => '000000'
        ])->assertCreated();
        // Complete should 422
        $this->postJson("/api/v1/trips/{$tripId}/complete", ['distance_km' => 12])->assertStatus(422);

        // We cannot get plain PIN from API; emulate reading from DB for test purpose
        /** @var Trip $trip */
        $trip = Trip::find($tripId);
        $pin = '123456';
        $trip->pin_hash = Trip::hashPin($pin);
        $trip->save();
        $this->postJson("/api/v1/trips/{$tripId}/pod", [
            'dropoff_photos' => ['ph1'], 'signed_name' => 'X', 'signed_pin' => $pin
        ])->assertCreated();
        $this->postJson("/api/v1/trips/{$tripId}/complete", ['distance_km' => 12])->assertOk();

        // Order delivered and COD collected
        $this->actingAs($buyer);
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'deliver'])->assertOk();
        $ord = Order::find($orderId);
        $this->assertEquals('delivered', $ord->status);
        $this->assertEquals('collected', $ord->payment_status);
        Event::assertDispatched(OrderPaid::class);
    }

    public function test_prevent_delivery_without_verified_pod(): void
    {
        extract($this->seedUsers());
    $product = Product::factory()->create(['seller_id' => $seller->id, 'type' => 'oil', 'stock' => 100]);
    $listing = Listing::factory()->create(['product_id' => $product->id, 'seller_id' => $seller->id, 'min_order' => 5]);
        $this->actingAs($buyer);
        $orderId = $this->postJson('/api/v1/orders', [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'listing_id' => $listing->id,
            'qty' => 10,
            'unit' => 'l',
            'price_unit' => 8,
            'payment_method' => 'cod',
        ])->json('data.id');
        $this->actingAs($seller);
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'confirm']);
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'ready']);
        // Auto-create load on attempted ship
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'ship'])->assertStatus(422);
        $load = Load::first();
        // Create trip but do not POD
        $carrier = User::factory()->create(['role' => 'trader_carrier']);
        $this->actingAs($carrier);
        // Need to match load first to allow start
        $load->carrier_id = $carrier->id; $load->status = Load::ST_MATCHED; $load->save();
        $tripId = $this->postJson('/api/v1/trips', ['carrier_id' => $carrier->id, 'load_ids' => [$load->id]])->json('data.id');
        $this->postJson("/api/v1/trips/{$tripId}/start");
        // Try deliver order -> blocked
        $this->actingAs($seller);
        $this->patchJson("/api/v1/orders/{$orderId}/transition", ['next' => 'deliver'])->assertStatus(422);
    }

    public function test_offer_range_validation(): void
    {
        extract($this->seedUsers());
        $this->actingAs($seller);
        $load = Load::factory()->create(['owner_id' => $seller->id, 'price_floor' => 7, 'price_ceiling' => 9]);
        $this->actingAs($carrier);
        $this->postJson("/api/v1/loads/{$load->id}/offers", ['offer_price' => 5, 'eta_minutes' => 60])->assertStatus(422);
        $this->postJson("/api/v1/loads/{$load->id}/offers", ['offer_price' => 8, 'eta_minutes' => 60])->assertCreated();
    }

    public function test_webhook_idempotent_and_status_updates(): void
    {
        $order = Order::factory()->create(['payment_method' => 'd17']);
        $secret = 's3cr3t';
        config()->set('services.d17.webhook_secret', $secret);
        $payload = ['order_id' => $order->id, 'event_id' => 'evt_1', 'status' => 'authorized', 'amount' => 100, 'currency' => 'TND'];
        $sig = hash_hmac('sha256', json_encode($payload), $secret);
        $this->withHeader('X-Signature', $sig)->postJson('/api/v1/payments/d17/webhook', $payload)->assertOk();
        $order->refresh();
        $this->assertEquals('authorized', $order->payment_status);

        // Capture
        $payload['status'] = 'captured';
        $sig2 = hash_hmac('sha256', json_encode($payload), $secret);
        $this->withHeader('X-Signature', $sig2)->postJson('/api/v1/payments/d17/webhook', $payload)->assertOk();
        $order->refresh();
        $this->assertEquals('captured', $order->payment_status);
        // Repeat event -> idempotent no change
        $this->withHeader('X-Signature', $sig2)->postJson('/api/v1/payments/d17/webhook', $payload)->assertOk();
    }

    public function test_mill_overview_meta_and_zeros(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->getJson('/api/v1/mill/overview')->assertOk()->assertJsonPath('data.pressed_today', 0)->assertJsonPath('data.meta.interval.0', 'today');
    }
}
