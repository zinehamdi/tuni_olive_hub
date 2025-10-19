<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\ExportShipment;
use App\Models\ExportOffer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class Prompt4Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed minimal roles
        User::factory()->create(['id'=>1,'role'=>'admin']);
    }

    public function test_export_shipments_and_documents_flow(): void
    {
        $exporter = User::factory()->create(['role'=>'exporter']);
        $buyer = User::factory()->create(['role'=>'consumer']);
        $offer = ExportOffer::factory()->create(['seller_id'=>$exporter->id]);
        $contract = Contract::create([
            'export_offer_id' => $offer->id,
            'buyer_id' => $buyer->id,
            'payment_term' => 'lc',
            'escrow' => false,
            // Use a valid enum status defined in migration
            'status' => 'signed',
        ]);

        // Create shipment
        $resp = $this->actingAs($exporter)->postJson('/api/v1/export/shipments', [
            'contract_id' => $contract->id,
            'incoterm' => 'fob',
            'port_from' => 'Rades',
            'port_to' => 'Dubai',
        ]);
        $resp->assertCreated();
        $shipment = ExportShipment::first();

        // Attach COA → auto docs_pending
        $a = $this->actingAs($exporter)->postJson("/api/v1/export/shipments/{$shipment->id}/documents", [
            'type' => 'coa', 'url' => 'https://files/coa.pdf'
        ])->assertCreated();
        $shipment->refresh();
        $this->assertSame('docs_pending', $shipment->status);

        // Attach BL and verify both → ready
        $this->actingAs($exporter)->postJson("/api/v1/export/shipments/{$shipment->id}/documents", [
            'type' => 'bl', 'url' => 'https://files/bl.pdf'
        ])->assertCreated();
        $docs = $shipment->documents;
        $this->actingAs($exporter)->patchJson("/api/v1/export/shipments/{$shipment->id}/documents/{$docs[0]->id}/verify")->assertOk();
        $this->actingAs($exporter)->patchJson("/api/v1/export/shipments/{$shipment->id}/documents/{$docs[1]->id}/verify")->assertOk();
        $shipment->refresh();
        $this->assertSame('ready', $shipment->status);

        // Transitions
        foreach (['shipped','arrived','cleared','closed'] as $to) {
            $this->actingAs($exporter)->patchJson("/api/v1/export/shipments/{$shipment->id}/transition", ['to'=>$to])->assertOk();
        }

        // Unauthorized transition as random user
        $rand = User::factory()->create(['role'=>'consumer']);
        $this->actingAs($rand)->patchJson("/api/v1/export/shipments/{$shipment->id}/transition", ['to'=>'closed'])->assertStatus(403);
    }

    public function test_invoices_and_payouts_with_webhooks(): void
    {
        $seller = User::factory()->create(['role'=>'farmer']);
        $buyer = User::factory()->create(['role'=>'consumer']);
        $order = Order::factory()->create(['seller_id'=>$seller->id,'buyer_id'=>$buyer->id,'status'=>'delivered']);

        // Issue invoice by seller
        $issue = $this->actingAs($seller)->postJson('/api/v1/invoices/issue', [
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'currency' => 'TND',
            'subtotal' => 100,
            'tax' => 19,
            'total' => 119,
        ])->assertCreated();
        $id = $issue['data']['invoice']['id'];
        $this->assertNotEmpty($issue['data']['invoice']['pdf_url'] ?? '');

        // Show invoice as buyer and seller
        $this->actingAs($buyer)->getJson("/api/v1/invoices/{$id}")->assertOk();
        $this->actingAs($seller)->getJson("/api/v1/invoices/{$id}")->assertOk();
        // Random user forbidden
        $rand = User::factory()->create();
        $this->actingAs($rand)->getJson("/api/v1/invoices/{$id}")->assertStatus(403);

        // Request payout by payee (seller)
        $p = $this->actingAs($seller)->postJson('/api/v1/payouts/request', [
            'payee_id' => $seller->id,
            'invoice_id' => $id,
            'amount' => 119,
            'currency' => 'TND',
            'provider' => 'bank',
        ])->assertCreated();
        $payoutId = $p['data']['payout']['id'];

        // Webhook valid HMAC → processing
        config(['services.payouts.webhook_secret' => 'secret']);
    $arr = ['payout_id'=>$payoutId,'event_id'=>Str::uuid()->toString(),'status'=>'processing'];
    $payload = json_encode($arr);
    $sig = hash_hmac('sha256', $payload, 'secret');
    $this->withHeaders(['X-Payout-Signature'=>$sig])->postJson('/api/v1/payouts/webhook', $arr)->assertOk();

        // Invalid HMAC
    $this->withHeaders(['X-Payout-Signature'=>'bad'])->postJson('/api/v1/payouts/webhook', $arr)->assertStatus(401);

        // Duplicate event id → idempotent no-op 200
    $this->withHeaders(['X-Payout-Signature'=>$sig])->postJson('/api/v1/payouts/webhook', $arr)->assertOk();
    }

    public function test_public_storefront_and_seo(): void
    {
        // Seed some products
        $seller1 = User::factory()->create();
        Product::factory()->create(['seller_id'=>$seller1->id,'is_premium'=>true,'export_ready'=>true]);
        Product::factory()->create(['seller_id'=>$seller1->id,'is_premium'=>false,'export_ready'=>false]);

        // Landing
        $landing = $this->get('/public/landing.json')->assertOk();
        $this->assertTrue(isset($landing['data']['featured']));
        $this->assertLessThanOrEqual(8, count($landing['data']['featured']));

        // Sitemap
        $sm = $this->get('/public/sitemap.xml');
        $sm->assertOk();
        $sm->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $this->assertStringContainsString('/products/', $sm->getContent());

        // RSS
        $rss = $this->get('/public/feed.rss');
        $rss->assertOk();
        $rss->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $this->assertStringContainsString('<item>', $rss->getContent());
    }

    public function test_ci_guards_smoke(): void
    {
        $routes = new Process(['php','scripts/guard_routes.php'], base_path());
        $routes->run();
        $this->assertSame(0, $routes->getExitCode(), $routes->getErrorOutput());

        // Start a tiny PHP built-in server for payload guard
        $server = new Process(['php','artisan','serve','--host=127.0.0.1','--port=8081'], base_path());
        $server->start();
        usleep(800000);
        $payload = new Process(['php','scripts/guard_payloads.php'], base_path(), ['APP_URL'=>'http://127.0.0.1:8081']);
        $payload->run();
        $server->stop(1);
        $this->assertSame(0, $payload->getExitCode(), $payload->getErrorOutput());
    }
}
