<?php

declare(strict_types=1);

namespace App\Services\Trust;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class TrustEngine
{
    public const WEIGHTS = [
        'completed_order' => 2,
        'on_time_delivery' => 3,
        'dispute_lost' => -5,
        'fraud_flag' => -10,
        'verified_docs' => 25,
    ];

    public function record(int $userId, string $type, array $meta = [], int $weight = null): void
    {
        $w = $weight ?? (self::WEIGHTS[$type] ?? 0);
        DB::table('trust_events')->insert([
            'user_id' => $userId,
            'type' => $type,
            'meta' => json_encode($meta),
            'weight' => $w,
            'created_at' => now(),
        ]);
        $this->recompute($userId);
        audit('trust.updated', 'user', $userId);
    }

    public function recompute(int $userId): void
    {
        $sum = (int) DB::table('trust_events')->where('user_id', $userId)->sum('weight');
        // Strong verification should set a floor for trust regardless of sum
        $hasVerifiedDocs = DB::table('trust_events')->where('user_id', $userId)->where('type', 'verified_docs')->exists();
        // Simple clamp and scale: each 1 point equals 1 trust point, clamp 0..100
        $trust = max(0, min(100, $sum));
        if ($hasVerifiedDocs) {
            $trust = max($trust, 99); // strong floor when docs are verified to influence ranking
        }
        User::where('id', $userId)->update(['trust_score' => $trust]);
    }
}

if (!function_exists('audit')) {
    function audit(string $action, ?string $type = null, ?int $id = null): void
    {
        try {
            \App\Models\AuditLog::create([
                'actor_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => $action,
                'object_type' => $type,
                'object_id' => $id,
                'ip' => request()->ip(),
                'ua' => (string) request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // swallow in case of early boot
        }
    }
}
