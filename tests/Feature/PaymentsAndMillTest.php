<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsAndMillTest extends TestCase
{
    use RefreshDatabase;

    public function test_mill_overview_requires_auth(): void
    {
        $this->getJson('/api/v1/mill/overview')->assertStatus(401);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $order = Order::factory()->create();
        $payload = json_encode(['order_id' => $order->id, 'status' => 'authorized']);
        $this->withHeader('X-Signature', 'bad')->postJson('/api/v1/payments/flouci/webhook', json_decode($payload, true))
            ->assertStatus(401);
    }
}
