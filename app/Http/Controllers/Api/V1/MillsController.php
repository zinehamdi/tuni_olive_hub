<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\MillStorageMovement;
use Illuminate\Http\Request;

class MillsController extends ApiController
{
    public function storageOverview(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $monthStart = now()->startOfMonth();
        $olive_in_month = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','olive')->where('type','in')->where('created_at','>=',$monthStart)->sum('qty');
        $oil_produced_month = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','oil')->where('type','in')->where('created_at','>=',$monthStart)->sum('qty');
        if ($oil_produced_month <= 0 && $olive_in_month > 0) {
            // Fallback to derive production using default extraction rate
            $oil_produced_month = round($olive_in_month * 0.22, 3);
        }
        $oil_out_month = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','oil')->where('type','out')->where('created_at','>=',$monthStart)->sum('qty');
        $oil_in_total = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','oil')->where('type','in')->sum('qty');
        $oil_out_total = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','oil')->where('type','out')->sum('qty');
        $oil_stock_current = $oil_in_total - $oil_out_total;
        return $this->ok([
            'olive_in_month' => $olive_in_month,
            'oil_produced_month' => $oil_produced_month,
            'oil_out_month' => $oil_out_month,
            'oil_stock_current' => (float) $oil_stock_current,
            'extraction_rate_default' => 0.22,
        ]);
    }

    public function yieldSummary(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $request->validate(['from' => ['required','date'], 'to' => ['required','date']]);
        $from = \Carbon\Carbon::parse($request->input('from'));
        $to = \Carbon\Carbon::parse($request->input('to'))->endOfDay();
        if ($to->lt($from)) return response()->json(['message' => 'Invalid range', 'errors' => ['to' => ['Invalid range']]], 422);
        $olive_in = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','olive')->whereBetween('created_at', [$from,$to])->where('type','in')->sum('qty');
        // Interpret oil 'in' during range as produced oil for yield purposes
        $oil_out = (float) MillStorageMovement::where('mill_id',$user->id)->where('product','oil')->whereBetween('created_at', [$from,$to])->where('type','in')->sum('qty');
        if ($oil_out <= 0 && $olive_in > 0) {
            $oil_out = round($olive_in * 0.22, 3);
        }
        $yield = $olive_in > 0 ? round($oil_out / $olive_in, 2) : 0.22;
        return $this->ok([
            'olive_in' => $olive_in,
            'oil_out' => $oil_out,
            'yield_pct' => $yield,
        ]);
    }
}
