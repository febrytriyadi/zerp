<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup database setiap hari jam 02:00
Schedule::command('backup:clean')->daily()->at('01:30');
Schedule::command('backup:run')->daily()->at('02:00');

// Pulse monitoring every minute
Schedule::command('pulse:check')->everyMinute();

// Queue worker (pastikan sudah dijalankan di background)
// Schedule::command('queue:work --stop-when-empty --tries=3')->everyMinute();
