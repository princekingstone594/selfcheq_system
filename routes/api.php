<?php

use App\Models\Task;
use App\Models\Routine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

Route::get('/reminders', function () {
    if (!Auth::check()) {
        return response()->json([]);
    }

    $user = Auth::user();

    // Don't send reminders if the user disabled reminders
    if (!$user->reminders_enabled) {
        return response()->json([]);
    }

    $now = Carbon::now()->format('H:i');
    $today = Carbon::now()->toDateString();

    $reminders = [];

    // 🔔 Tasks with alarm enabled
    $tasks = Task::where('user_id', $user->id)
        ->where('alarm_enabled', true)
        ->where(function ($q) use ($now) {
            $q->where('alarm_time', $now)
              ->orWhere('reminder_time', $now);
        })
        ->where('is_completed', false)
        ->get(['id', 'title']);

    foreach ($tasks as $task) {
        $reminders[] = [
            'type' => 'task',
            'title' => $task->title,
        ];
    }

    // ⏰ Routines (every routine has an alarm — discipline)
    $routines = Routine::where('user_id', $user->id)
        ->whereDate('date', $today)
        ->where('is_completed', false)
        ->get(['id', 'title', 'reminder_time']);

    foreach ($routines as $routine) {
        if ($routine->reminder_time &&
            Carbon::parse($routine->reminder_time)->format('H:i') === $now) {
            $reminders[] = [
                'type' => 'routine',
                'title' => $routine->title,
            ];
        }
    }

    return response()->json($reminders);
});
