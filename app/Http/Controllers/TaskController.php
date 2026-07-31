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
        $today = now()->toDateString();

        $tasks = Auth::user()
          ->tasks()
          ->whereDate('due_date', $today)
          ->latest()
          ->get();

        $total = $tasks->count();
        $completed = $tasks->where('is_completed', true)->count();

        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

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
        ]);

        Auth::user()->tasks()->create([
            'title' => $request->title,
            'due_date' => $request->due_date ?? now()->toDateString(),
        ]);

        return back();
    }

    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
}