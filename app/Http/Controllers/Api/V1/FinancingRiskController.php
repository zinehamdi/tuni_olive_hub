<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Financing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancingRiskController extends ApiController
{
    public function show(Request $request, Financing $financing)
    {
        $user = $request->user();
        if ($user->role !== 'admin' && (int)$user->id !== (int)$financing->financer_id) abort(403, trans('auth.forbidden_action'));
        $events = DB::table('financing_risk_events')->where('financing_id', $financing->id)->orderByDesc('created_at')->get(['type','weight','meta','created_at']);
        return $this->ok(['risk_score' => (int)($financing->risk_score ?? 0), 'events' => $events]);
    }
}
