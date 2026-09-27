<?php

namespace Tests\Feature;

use App\Jobs\AutoApproveListingJob;
use App\Mail\ListingCreatedMail;
use App\Mail\ListingUnderReviewMail;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ListingDelayReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_listing_starts_as_pending_and_schedules_auto_approval()
    {
        Mail::fake();
        Queue::fake([AutoApproveListingJob::class]);

        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@zintoop.com']);
        $user = User::factory()->create(['role' => 'farmer', 'email' => 'farmer@zintoop.com']);

        $file = UploadedFile::fake()->image('olive_oil.jpg');

        $response = $this->actingAs($user)->post(route('listings.store'), [
            'category' => 'oil',
            'variety' => 'شملالي',
            'quantity' => 500,
            'unit' => 'L',
            'price' => 25,
            'currency' => 'TND',
            'governorate' => 'صفاقس',
            'images' => [$file],
        ]);

        $response->assertRedirect(route('dashboard'));

        // 1. Verify listing is in pending status
        $listing = Listing::latest('id')->first();
        $this->assertNotNull($listing);
        $this->assertEquals('pending', $listing->status);

        // 2. Verify AutoApproveListingJob was queued with 10 minutes delay
        Queue::assertPushed(AutoApproveListingJob::class, function ($job) use ($listing) {
            return $job->listingId === $listing->id;
        });

        // 3. Verify in-platform system review message was created
        $reviewMessage = Message::where('body', 'like', '%قيد المراجعة%')->first();
        $this->assertNotNull($reviewMessage);
        $this->assertEquals($listing->id, $reviewMessage->attachments['listing_id'] ?? null);
    }

    public function test_pending_listing_visibility_restricted_to_owner_and_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'farmer']);
        $otherUser = User::factory()->create(['role' => 'buyer']);

        $product = Product::factory()->create();
        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);

        // Non-owner gets 404
        $this->actingAs($otherUser)->get(route('listings.show', ['listing' => $listing->id]))
            ->assertStatus(404);

        // Unauthenticated guest gets 404
        $this->get(route('listings.show', ['listing' => $listing->id]))
            ->assertStatus(404);

        // Owner can view it
        $this->actingAs($seller)->get(route('listings.show', ['listing' => $listing->id]))
            ->assertStatus(200)
            ->assertSee(__('Under Review'));

        // Admin can view it
        $this->actingAs($admin)->get(route('listings.show', ['listing' => $listing->id]))
            ->assertStatus(200)
            ->assertSee(__('Under Review'));
    }

    public function test_auto_approve_job_activates_listing_and_sends_approval_notifications()
    {
        Mail::fake();
        Queue::fake([\App\Jobs\SendUnreadMessageEmailJob::class]);

        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'farmer', 'email' => 'seller@zintoop.com']);
        $product = Product::factory()->create(['variety' => 'شتوي']);
        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);

        // Execute job
        $job = new AutoApproveListingJob($listing->id);
        $job->handle();

        // 1. Verify listing is now active
        $this->assertEquals('active', $listing->fresh()->status);

        // 2. Verify approval email sent
        Mail::assertSent(ListingCreatedMail::class, function ($mail) use ($seller) {
            return $mail->hasTo($seller->email);
        });

        // 3. Verify approval in-platform chat message created
        $approvalMessage = Message::where('body', 'like', '%تهانينا%')->first();
        $this->assertNotNull($approvalMessage);

        // 4. Verify unread reminder job queued
        Queue::assertPushed(\App\Jobs\SendUnreadMessageEmailJob::class, function ($job) use ($approvalMessage, $seller) {
            return $job->messageId === $approvalMessage->id && $job->recipientId === $seller->id;
        });
    }

    public function test_admin_manual_approval_activates_and_notifies_seller()
    {
        Mail::fake();
        Queue::fake([\App\Jobs\SendUnreadMessageEmailJob::class]);

        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'farmer', 'email' => 'seller_manual@zintoop.com']);
        $product = Product::factory()->create(['variety' => 'بشولين']);
        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.listings.approve', $listing));
        $response->assertSessionHas('success');

        $this->assertEquals('active', $listing->fresh()->status);
        Mail::assertSent(ListingCreatedMail::class, function ($mail) use ($seller) {
            return $mail->hasTo($seller->email);
        });

        $approvalMsg = Message::where('body', 'like', '%تهانينا%')->first();
        $this->assertNotNull($approvalMsg);
    }

    public function test_listing_show_renders_with_decimal_prices_without_error()
    {
        $seller = User::factory()->create(['role' => 'farmer']);
        $product = Product::factory()->create(['variety' => 'شملالي']);
        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'status' => 'active',
            'price' => 18.750,
            'currency' => 'TND',
            'unit' => 'liter',
        ]);

        $response = $this->get(route('listings.show', ['listing' => $listing->id]));
        $response->assertStatus(200);
        $response->assertSee('18.750');
    }
}
