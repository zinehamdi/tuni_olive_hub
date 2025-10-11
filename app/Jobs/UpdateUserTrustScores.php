<?php
/**
 * Job: UpdateUserTrustScores
 * مهمة تحديث نقاط الثقة للمستخدمين بناءً على دقة التوصيل
 * Updates user trust_score based on trip ETA accuracy
 */
namespace App\Jobs;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class UpdateUserTrustScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get trips delivered in last 24h with both ETAs
        $trips = Trip::whereNotNull('expected_eta')
            ->whereNotNull('actual_eta')
            ->where('actual_eta', '>=', Carbon::now()->subDay())
            ->get();
        foreach ($trips as $trip) {
            $carrier = $trip->carrier;
            if (!$carrier) continue;
            $onTime = $trip->actual_eta <= $trip->expected_eta;
            // تعليق: زيادة أو تقليل نقاط الثقة حسب الالتزام بالوقت
            // EN: Increase/decrease trust_score based on ETA match
            if ($onTime) {
                $carrier->trust_score += 2;
            } else {
                $carrier->trust_score = max(0, $carrier->trust_score - 1);
            }
            $carrier->save();
        }
    }
}
