<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Jobs\SendUnreadMessageEmailJob;
use App\Mail\ListingCreatedMail;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use App\Services\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoApproveListingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $listingId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $listingId)
    {
        $this->listingId = $listingId;
    }

    /**
     * Execute the job after 10 minutes delay to auto-approve the listing.
     */
    public function handle(): void
    {
        $listing = Listing::with(['product', 'seller'])->find($this->listingId);

        if (!$listing) {
            Log::info("AutoApproveListingJob: Listing #{$this->listingId} not found. Skipping.");
            return;
        }

        // Only approve if it is still pending (admin might have rejected or deleted it)
        if ($listing->status !== 'pending') {
            Log::info("AutoApproveListingJob: Listing #{$this->listingId} is not in pending status (current: {$listing->status}). Skipping auto-approval.");
            return;
        }

        // 1. Update listing status to active
        $listing->update(['status' => 'active']);
        Cache::forget('home_featured_listings');
        Log::info("AutoApproveListingJob: Listing #{$this->listingId} approved and activated successfully after 10-minute review.");

        $seller = $listing->seller;
        if (!$seller) {
            return;
        }

        $admin = User::where('role', 'admin')->first() ?? User::find(1) ?? $seller;
        $locale = $seller->locale ?? 'ar';
        $variety = $listing->product?->variety ?? ($locale === 'fr' ? 'Huile d\'olive' : 'زيت زيتون');
        $listingUrl = url('/' . $locale . '/listings/' . $listing->id);

        // 2. Send email notifying the user that their listing is approved and live
        if (!empty($seller->email)) {
            try {
                Mail::to($seller->email)->send(new ListingCreatedMail($listing));
                Log::info("AutoApproveListingJob: Sent ListingCreatedMail approval email to {$seller->email}.");
            } catch (\Throwable $e) {
                Log::error("AutoApproveListingJob: Failed sending approval email: " . $e->getMessage());
            }
        }

        // 3. Send platform system message in chat
        try {
            $thread = Chat::ensureThread('direct_message', 0, [$admin->id, $seller->id]);

            $body = match ($locale) {
                'fr' => "🎉 **Félicitations !** Votre annonce pour **{$variety}** a été vérifiée et approuvée. Elle est désormais active et visible pour tous les acheteurs sur ZinToop !\n\n👉 [Voir mon annonce sur le marché]({$listingUrl})",
                'en' => "🎉 **Congratulations!** Your listing for **{$variety}** has been reviewed and approved. It is now live and visible to all buyers on ZinToop!\n\n👉 [View my listing on the marketplace]({$listingUrl})",
                default => "🎉 **تهانينا!** تمت مراجعة وقبول عرضك لـ **{$variety}** بنجاح. عرضك الآن نشط ومرئي لجميع المشترين في منصة زينتوب!\n\n👉 [مشاهدة العرض في السوق]({$listingUrl})",
            };

            $message = Message::create([
                'thread_id' => $thread->id,
                'sender_id' => $admin->id,
                'body' => $body,
                'attachments' => [
                    'is_system' => true,
                    'type' => 'listing_approved',
                    'listing_id' => $listing->id,
                ],
                'is_flagged' => false,
                'is_deleted' => false,
            ]);

            Log::info("AutoApproveListingJob: Created approval chat message #{$message->id} for Seller #{$seller->id}.");

            // 4. If the seller doesn't open the message in the platform within 5 minutes, send reminder email
            SendUnreadMessageEmailJob::dispatch($message->id, $admin->id, $seller->id)->delay(now()->addMinutes(5));

        } catch (\Throwable $e) {
            Log::error("AutoApproveListingJob: Failed creating approval chat message: " . $e->getMessage());
        }
    }
}
