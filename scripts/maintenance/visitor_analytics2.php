<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Visitor;
use Carbon\Carbon;

$last7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = Carbon::today()->subDays($i)->toDateString();
    $count = Visitor::where('visited_date', $date)->count();
    $last7Days[$date] = $count;
}

$topAgents = Visitor::select('user_agent', \DB::raw('count(*) as total'))
    ->groupBy('user_agent')
    ->orderByDesc('total')
    ->limit(10)
    ->get()
    ->toArray();

echo json_encode([
    'last7Days' => $last7Days,
    'topAgents' => $topAgents,
], JSON_PRETTY_PRINT);
