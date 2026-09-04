<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Bot\WhatsAppCloudApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppWelcomeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppCloudApiService $waService): void
    {
        $user = User::find($this->userId);
        if (!$user || empty($user->phone)) {
            Log::info("Welcome WhatsApp Job: User #{$this->userId} has no phone number.");
            return;
        }

        $userName = $user->name ?? 'عضو المنصة';
        $roleName = match ($user->role ?? '') {
            'producer', 'farmer' => 'فلاح ومنتج زيتون',
            'mill_owner', 'mill' => 'صاحب معصرة',
            'trader' => 'تاجر وموزع زيت',
            'exporter' => 'مصدر دولي',
            'broker' => 'وسيط تجاري',
            default => 'عضو في مجتمع زيت الزيتون',
        };

        $customWish = match ($user->role ?? '') {
            'producer', 'farmer' => 'إن شاء الله صابة مباركة وموسم خير وفير 🫒',
            'mill_owner', 'mill' => 'نتمنولك موسم طحن ممتاز وشراكات رابحة 🫒',
            'trader' => 'إن شاء الله صفقات موفقة وتجارة رابحة 🫒',
            'exporter' => 'نتمنولك عقود تصدير وشحنات ناجحة للأسواق العالمية 🫒',
            default => 'مرحباً بك في المنصة الوطنية الأولى لزيت الزيتون التونسي 🫒',
        };

        Log::info("Sending delayed Welcome WhatsApp to user #{$user->id} ({$user->phone})");

        // Send via WhatsApp Cloud API Template or Text fallback
        $waService->sendWelcomeTemplate(
            $user->phone,
            $userName,
            $roleName,
            $customWish
        );
    }
}
