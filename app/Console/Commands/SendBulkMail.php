<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkSubscriberEmail;
use App\Mail\PlatformUpdateAnnouncementMail;
use App\Mail\NewListingNotificationMail;
use App\Models\Listing;

class SendBulkMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-bulk {job_id : The unique ID of the bulk mail payload file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send bulk emails asynchronously in the background from a queued payload file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $jobId = $this->argument('job_id');
        $filePath = storage_path("app/bulk_mail_jobs/{$jobId}.json");

        if (!file_exists($filePath)) {
            $this->error("Bulk mail payload file not found: {$filePath}");
            Log::error("Bulk mail background task failed: payload file {$filePath} not found.");
            return 1;
        }

        $data = json_decode(file_get_contents($filePath), true);
        if (!$data || empty($data['contacts'])) {
            $this->error("Invalid or empty payload data.");
            @unlink($filePath);
            return 1;
        }

        $template = $data['template'] ?? '';
        $subject = $data['subject'] ?? 'منصة زيت الزيتون التونسي / زينتوب';
        $body = $data['body'] ?? '';
        $contacts = $data['contacts'] ?? [];

        Log::info("Starting background bulk email task [{$jobId}] for " . count($contacts) . " contacts.");
        $this->info("Sending bulk emails for job {$jobId} to " . count($contacts) . " contacts...");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($contacts as $contactString) {
            $parts = explode(':', $contactString, 3);
            if (count($parts) === 3) {
                $type = $parts[1];
                $value = trim($parts[2]);

                if ($type === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    try {
                        if ($template === 'update_announcement') {
                            Mail::to($value)->send(new PlatformUpdateAnnouncementMail($subject));
                        } elseif ($template === 'latest_listing') {
                            $listing = Listing::with(['product', 'seller'])->latest()->first();
                            if ($listing) {
                                Mail::to($value)->send(new NewListingNotificationMail($listing));
                            }
                        } else {
                            Mail::to($value)->send(new BulkSubscriberEmail($subject, $body));
                        }

                        $sentCount++;
                        // Small 50ms pause to avoid hitting strict SMTP rate limits
                        usleep(50000);
                    } catch (\Throwable $e) {
                        $failedCount++;
                        Log::error("Background bulk email failed for {$value}: " . $e->getMessage());
                    }
                }
            }
        }

        Log::info("Completed background bulk email task [{$jobId}]: {$sentCount} sent, {$failedCount} failed.");
        $this->info("Done! {$sentCount} sent, {$failedCount} failed.");

        // Delete payload file after completion
        @unlink($filePath);

        return 0;
    }
}
