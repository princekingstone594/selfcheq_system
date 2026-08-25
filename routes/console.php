<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
| Laravel 12 slim skeleton: schedules live here (the legacy
| app/Console/Kernel.php is not wired into the application bootstrap).
*/
Schedule::command('reminders:tasks')->everyMinute();
Schedule::command('reminders:send')->dailyAt('08:00');
Schedule::command('streak:remind')->dailyAt('19:00');
Schedule::command('nudges:send')->dailyAt('20:30');
Schedule::command('wins:send')->dailyAt('21:00');
Schedule::command('recap:send')->sundays()->at('19:00');
