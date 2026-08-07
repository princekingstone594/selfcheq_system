<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Badge;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayDate = $today->toDateString();

        // Get today's tasks
        $tasks = $user->tasks()
            ->whereDate('due_date', $todayDate)
            ->latest()
            ->get();

        // 📜 History (past tasks, excluding today, grouped by date)
        $history = $user->tasks()
            ->whereDate('due_date', '<', $todayDate)
            ->orderBy('due_date', 'desc')
            ->take(30)
            ->get()
            ->groupBy(function ($task) {
                return Carbon::parse($task->due_date)->format('Y-m-d');
            });

        // Progress calculation
        $total = $tasks->count();
        $completed = $tasks->where('is_completed', true)->count();
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        // 🔥 Streak logic
        if ($total > 0 && $completed === $total) {

            if ($user->last_completed_date === null) {
                $user->streak = 1;

            } elseif ($user->last_completed_date === now()->subDay()->toDateString()) {
                $user->streak += 1;

            } elseif ($user->last_completed_date !== $todayDate) {
                $user->streak = 1;
            }

            $user->last_completed_date = $todayDate;
            $user->save();
        }

        return view('tasks.index', compact(
            'tasks',
            'today',
            'total',
            'completed',
            'progress',
            'history'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        $task = Auth::user()->tasks()->create([
            'title' => $request->title,
            'due_date' => $request->due_date ?? now()->toDateString(),
            'reminder_time' => $request->reminder_time,
            'is_important' => $request->has('is_important'),
            'is_urgent' => $request->has('is_urgent'),
            'alarm_enabled' => $request->has('alarm_enabled'),
            'alarm_time' => $request->alarm_time,
        ]);

        $msg = $task->alarm_enabled
            ? 'Task added with alarm! ⏰'
            : 'Task added! Tap the alarm button to set a reminder. 🔔';

        return back()->with('success', $msg);
    }

    public function toggle(Task $task)
    {
        // 🔒 Security: ensure task belongs to user
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        $user = Auth::user();

        if (!$task->is_completed) {
            // gaining xp when completing
            $user->xp += 10;
        } else {
            // optional: remove xp if unchecking
            $user->xp -= 10;
        }

        $this->updateLevel($user);

        $user->save();

        return back()->with('success', $task->is_completed
            ? '✅ Task completed! +10 XP'
            : '🔄 Task marked incomplete');
    }

    public function alarmToggle(Task $task)
    {
        // 🔒 Security: ensure task belongs to user
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update([
            'alarm_enabled' => !$task->alarm_enabled
        ]);

        return back()->with('success', $task->alarm_enabled
            ? '🔔 Alarm activated for this task!'
            : '🔕 Alarm deactivated for this task');
    }

    public function destroy(Task $task)
    {
        // 🔒 Security: ensure task belongs to user
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'Task removed. 🗑️');
    }

    private function updateLevel($user)
    {
        $user->level = floor($user->xp / 100) + 1;

        if ($user->xp >= 100 && !$user->badges()->where('name', 'First 100 XP')->exists()) {
            $badge = Badge::firstOrCreate([
                'name' => 'First 100 XP',
                'description' => 'Awarded for reaching 100 XP.',
            ]);
            $user->badges()->attach($badge);
        }
    }
}
