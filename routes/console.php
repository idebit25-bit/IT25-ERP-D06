<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('induccion:respaldo')->dailyAt('02:00')
    ->when(fn() => app()->environment('local'))->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/respaldos.log'));

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
