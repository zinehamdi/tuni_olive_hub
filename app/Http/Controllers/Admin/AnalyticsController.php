<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\ExportShipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $salesTotal = Order::where('status', 'completed')->sum('total_amount');
        $exportTimeline = ExportShipment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        $trustEvolution = User::selectRaw('DATE(updated_at) as date, AVG(trust_score) as avg_trust')
            ->whereNotNull('trust_score')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        return view('admin.analytics.dashboard', [
            'salesTotal' => $salesTotal,
            'exportTimeline' => $exportTimeline,
            'trustEvolution' => $trustEvolution,
        ]);
    }
}
