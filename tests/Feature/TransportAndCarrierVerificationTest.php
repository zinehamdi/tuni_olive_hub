<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Listing;
use App\Models\Load;
use App\Models\Order;
use App\Models\Product;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransportAndCarrierVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');
    }

    public function test_carrier_can_accept_and_deny_transport_mission(): void
    {
        $seller = User::factory()->create(['role' => 'farmer', 'name' => 'Fellah Sfax']);
        $buyer = User::factory()->create(['role' => 'normal', 'name' => 'Chari Tunis']);
        $carrier = User::factory()->create(['role' => 'carrier', 'name' => 'Naqel Sousse']);

        $pickup = Address::create([
            'user_id' => $seller->id,
            'governorate' => 'Sfax',
            'delegation' => 'Thyna',
            'lat' => 34.7406,
            'lng' => 10.7603,
            'label' => 'Farm Pickup'
        ]);

        $dropoff = Address::create([
            'user_id' => $buyer->id,
            'governorate' => 'Tunis',
            'delegation' => 'Centre',
            'lat' => 36.8065,
            'lng' => 10.1815,
            'label' => 'Warehouse'
        ]);

        $product = Product::create([
            'seller_id' => $seller->id,
            'variety' => 'Chemlali',
            'quality' => 'extra_virgin',
            'type' => 'oil',
            'price' => 15.0,
        ]);

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'status' => 'active',
        ]);

        $order = Order::create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'listing_id' => $listing->id,
            'status' => 'confirmed',
            'qty' => 500,
            'unit' => 'l',
            'price_unit' => 15.0,
            'total' => 7500.0,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $load = Load::create([
            'owner_id' => $seller->id,
            'order_id' => $order->id,
            'carrier_id' => $carrier->id,
            'pickup_addr_id' => $pickup->id,
            'dropoff_addr_id' => $dropoff->id,
            'kind' => 'oil',
            'qty' => 500,
            'unit' => 'l',
            'status' => Load::ST_MATCHED,
            'meta' => [
                'distance_km' => 270,
                'estimated_cost' => 350.0,
            ]
        ]);

        // 1. Carrier accepts mission
        $resAccept = $this->actingAs($carrier)->postJson(route('transport.loads.accept', [
            'locale' => 'ar',
            'load' => $load->id
        ]));

        $resAccept->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('loads', [
            'id' => $load->id,
            'status' => Load::ST_IN_TRANSIT,
            'carrier_id' => $carrier->id,
        ]);

        // 2. Carrier denies mission (on a second load)
        $load2 = Load::create([
            'owner_id' => $seller->id,
            'order_id' => $order->id,
            'carrier_id' => $carrier->id,
            'pickup_addr_id' => $pickup->id,
            'dropoff_addr_id' => $dropoff->id,
            'kind' => 'oil',
            'qty' => 100,
            'unit' => 'l',
            'status' => Load::ST_MATCHED,
        ]);

        $resDeny = $this->actingAs($carrier)->postJson(route('transport.loads.deny', [
            'locale' => 'ar',
            'load' => $load2->id
        ]));

        $resDeny->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('loads', [
            'id' => $load2->id,
            'carrier_id' => null,
            'status' => Load::ST_NEW,
        ]);
    }

    public function test_public_live_shipment_tracking_page_and_live_api(): void
    {
        $carrier = User::factory()->create(['role' => 'carrier', 'name' => 'Transporteur Rapide', 'is_verified' => true]);
        $srCode = 'SR-TEST1234';

        $trip = Trip::create([
            'carrier_id' => $carrier->id,
            'sr_code' => $srCode,
            'load_ids' => [],
            'start_at' => now(),
            'current_lat' => 35.8245,
            'current_lng' => 10.6346,
            'distance_km' => 140,
        ]);

        // 1. Public tracking page (no login required)
        $resPage = $this->get(route('trip.publicTrack', [
            'locale' => 'ar',
            'token' => $srCode
        ]));

        $resPage->assertOk()
            ->assertSee($srCode)
            ->assertSee('Transporteur Rapide');

        // 2. Live GPS JSON endpoint
        $resLive = $this->getJson(route('trip.publicTrack.live', [
            'token' => $srCode
        ]));

        $resLive->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'in_transit',
                'current_lat' => 35.8245,
                'current_lng' => 10.6346,
            ]);
    }

    public function test_carrier_document_upload_and_admin_approval(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin ZinToop']);
        $carrier = User::factory()->create(['role' => 'carrier', 'name' => 'Karim Chauffeur', 'is_verified' => false]);

        $carteGrise = UploadedFile::fake()->image('carte_grise.jpg', 600, 400);
        $permis = UploadedFile::fake()->image('permis.jpg', 600, 400);
        $cin = UploadedFile::fake()->image('cin.jpg', 600, 400);

        // 1. Carrier uploads verification documents
        $resUpload = $this->actingAs($carrier)->post(route('carrier.verification.upload', ['locale' => 'ar']), [
            'carte_grise' => $carteGrise,
            'permis' => $permis,
            'cin' => $cin,
            'vehicle_plate' => '214 تونس 9876',
            'notes' => 'شاحنة مجهزة بصهريج إينوكس غذائي 3000 لتر',
        ]);

        $resUpload->assertRedirect();

        $carrier->refresh();
        $this->assertNotNull($carrier->meta_data['carrier_verification']);
        $this->assertEquals('pending', $carrier->meta_data['carrier_verification']['status']);
        $this->assertFalse($carrier->is_verified);

        // 2. Admin reviews carrier verifications list
        $resAdminList = $this->actingAs($admin)->get(route('admin.carrier_verifications', ['locale' => 'ar']));
        $resAdminList->assertOk()
            ->assertSee('Karim Chauffeur')
            ->assertSee('214 تونس 9876');

        // 3. Admin approves verification
        $resApprove = $this->actingAs($admin)->post(route('admin.carrier_verifications.approve', [
            'locale' => 'ar',
            'user' => $carrier->id
        ]));

        $resApprove->assertRedirect();

        $carrier->refresh();
        $this->assertTrue($carrier->is_verified);
        $this->assertEquals('approved', $carrier->meta_data['carrier_verification']['status']);
    }
}
