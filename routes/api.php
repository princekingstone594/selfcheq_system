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

    // 🌅 Also check the morning devotion wake-up time (linked routine is permanent daily)
    $morningRoutine = \App\Models\Routine::where('user_id', $user->id)
        ->where('reference_type', 'morning_devotion')
        ->where('is_permanent', true)
        ->where('is_completed', false)
        ->first();

    if ($morningRoutine && $morningRoutine->reminder_time &&
        Carbon::parse($morningRoutine->reminder_time)->format('H:i') === $now) {
        $reminders[] = [
            'type' => 'routine',
            'title' => $morningRoutine->title,
        ];
    }

    // 📬 Unread database notifications
    $unreadNotifications = $user->unreadNotifications;
    foreach ($unreadNotifications as $notification) {
        $data = $notification->data;
        $reminders[] = [
            'type' => $data['type'] ?? 'notification',
            'title' => $data['message'] ?? ($data['title'] ?? 'New notification'),
            'notification_id' => $notification->id,
        ];
    }

    return response()->json($reminders);
});

// ✅ Mark a notification as read
Route::post('/notifications/{notification}/read', function ($notification) {
    if (!Auth::check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $userNotification = Auth::user()->notifications()->where('id', $notification)->first();
    if ($userNotification) {
        $userNotification->markAsRead();
    }

    return response()->json(['success' => true]);
});
