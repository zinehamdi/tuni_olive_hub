<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Visitor;
use Carbon\Carbon;

$total = Visitor::count();
$today = Visitor::where('visited_date', Carbon::today()->toDateString())->count();
$yesterday = Visitor::where('visited_date', Carbon::yesterday()->toDateString())->count();

$deviceStats = Visitor::select('device', \DB::raw('count(*) as total'))
    ->groupBy('device')
    ->get()
    ->toArray();
    
$recentVisitors = Visitor::orderBy('updated_at', 'desc')->take(10)->get()->toArray();

echo json_encode([
    'total' => $total,
    'today' => $today,
    'yesterday' => $yesterday,
    'deviceStats' => $deviceStats,
    'recentVisitors' => $recentVisitors
], JSON_PRETTY_PRINT);
