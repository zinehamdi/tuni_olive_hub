<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Load;
use Illuminate\Http\Request;

class MillOverviewController extends ApiController
{
    public function show(Request $request)
    {
        $user = $request->user();
        $addrId = $user->default_mill_addr_id;
        if (!$addrId) return $this->ok([
            'pressed_today' => 0,
            'pressed_month' => 0,
            'stored_current' => 0,
            'deals_today' => 0,
            'meta' => ['interval' => ['today','month']],
        ]);

        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $deliveredLoads = Load::query()
            ->where('dropoff_addr_id', $addrId)
            ->where('status', 'delivered');

        $pressed_today = (clone $deliveredLoads)->where('updated_at', '>=', $today)->sum('qty');
        $pressed_month = (clone $deliveredLoads)->where('updated_at', '>=', $monthStart)->sum('qty');
        $deals_today = (clone $deliveredLoads)->where('updated_at', '>=', $today)->count();
        $stored_current = max(0, (int) $pressed_month - (int) ($deals_today * 10)); // rough stub

        return $this->ok([
            'pressed_today' => (float) $pressed_today,
            'pressed_month' => (float) $pressed_month,
            'stored_current' => (float) $stored_current,
            'deals_today' => (int) $deals_today,
            'meta' => ['interval' => ['today','month']],
        ]);
    }
}
