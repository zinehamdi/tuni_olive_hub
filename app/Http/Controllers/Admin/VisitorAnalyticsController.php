<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

class VisitorAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $period = $request->get('period', '7'); // default 7 days
        $startDate = Carbon::now()->subDays($period)->toDateString();

        $visitors = Visitor::where('visited_date', '>=', $startDate)->get();

        // Prepare data for charts
        $chartData = $visitors->groupBy('visited_date')->map(function ($group) {
            return $group->count();
        });

        // Device stats
        $deviceStats = $visitors->groupBy('device')->map(function ($group) {
            return $group->count();
        });

        // Total for period
        $totalPeriod = $visitors->count();
        
        // Today vs Yesterday
        $today = Visitor::where('visited_date', Carbon::today()->toDateString())->count();
        $yesterday = Visitor::where('visited_date', Carbon::yesterday()->toDateString())->count();
        $growth = $yesterday > 0 ? (($today - $yesterday) / $yesterday) * 100 : 0;

        // Recent visitors list
        $recentVisitors = Visitor::orderBy('updated_at', 'desc')->take(20)->get();

        return view('admin.analytics.visitors', compact(
            'chartData', 'deviceStats', 'totalPeriod', 'period', 'today', 'growth', 'recentVisitors'
        ));
    }
}
