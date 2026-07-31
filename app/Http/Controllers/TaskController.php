<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Get today's tasks
        $tasks = $user->tasks()
            ->whereDate('due_date', $today)
            ->latest()
            ->get();

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

            } elseif ($user->last_completed_date !== $today) {
                $user->streak = 1;
            }

            $user->last_completed_date = $today;
            $user->save();
        }

        return view('tasks.index', compact(
            'tasks',
            'today',
            'total',
            'completed',
            'progress'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        Auth::user()->tasks()->create([
            'title' => $request->title,
            'due_date' => $request->due_date ?? now()->toDateString(),
        ]);

        return back();
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

        return back();
    }

    public function destroy(Task $task)
    {
        // 🔒 Security: ensure task belongs to user
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return back();
    }
}