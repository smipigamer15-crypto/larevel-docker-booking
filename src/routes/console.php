<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('monitoring:clean --days=90')
    ->daily()
    ->at('03:00');

Schedule::command('subscriptions:deactivate-expired')
    ->hourly();

Schedule::command('subscriptions:check-expired')
    ->hourly();

Schedule::command('backup:clean')
    ->daily()
    ->at('02:30');
Schedule::command('backup:run')
    ->daily()
    ->at('03:00');
Schedule::command('backup:monitor')
    ->daily()
    ->at('04:00');
