<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('presensi:auto-alpha')->everyFifteenMinutes();
\Illuminate\Support\Facades\Schedule::command('appraisal:send-reminders')->dailyAt('08:00');
\Illuminate\Support\Facades\Schedule::command('presensi:piket-reminder')->dailyAt('17:00');
