<?php

use App\Models\Task;
use Illuminate\Support\Carbon;

Route::get('/reminders', function () {
    $now = Carbon::now()->format('H:i');

    return Task::where('reminder_time', $now)
        ->where('is_completed', false)
        ->get(['id', 'title']);
});