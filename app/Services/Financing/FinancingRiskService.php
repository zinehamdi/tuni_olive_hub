<?php

declare(strict_types=1);

namespace App\Services\Financing;

use App\Models\Financing;
use Illuminate\Support\Facades\DB;

class FinancingRiskService
{
    public function record(Financing $f, string $type, array $meta = [], int $weight = 0): void
    {
        DB::table('financing_risk_events')->insert([
            'financing_id' => $f->id,
            'type' => $type,
            'weight' => $weight,
            'meta' => json_encode($meta),
            'created_at' => now(),
        ]);
        $sum = (int) DB::table('financing_risk_events')->where('financing_id', $f->id)->sum('weight');
        DB::table('financings')->where('id', $f->id)->update(['risk_score' => $sum]);
        if (function_exists('audit')) { audit('financing.risk_event', 'financing', $f->id); }
    }
}
