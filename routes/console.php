<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily price ingestion job
Schedule::job(new \App\Jobs\IngestDailyPrices)->dailyAt('06:00')->onOneServer()->withoutOverlapping();
