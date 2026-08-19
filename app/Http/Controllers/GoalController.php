<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class GoalController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $goals = $user->goals()
            ->orderBy('is_completed')
            ->orderBy('end_date')
            ->get();

        // Calculate current discipline score for today
        $today = Carbon::today()->toDateString();
        $tasksToday = $user->tasks()->whereDate('due_date', $today)->get();
        $taskTotal = $tasksToday->count();
        $taskCompleted = $tasksToday->where('is_completed', true)->count();

        $routines = $user->routines()->whereDate('date', $today)->get();
        $routineCompleted = $routines->where('is_completed', true)->count();
        $routineTotal = $routines->count();

        $focusMinutes = $user->focusSessions()->whereDate('started_at', $today)->sum('duration');
        $journalExists = $user->journals()->whereDate('date', $today)->exists();

        $taskScore = $taskTotal > 0 ? ($taskCompleted / $taskTotal) * 40 : 0;
        $routineScore = $routineTotal > 0 ? ($routineCompleted / $routineTotal) * 20 : 0;
        $journalScore = $journalExists ? 20 : 0;
        $focusScore = min($focusMinutes / 60, 1) * 20;

        $currentScore = round($taskScore + $routineScore + $journalScore + $focusScore);

        return view('goals.index', compact('goals', 'currentScore'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_score' => 'required|integer|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Auth::user()->goals()->create([
            'title' => $request->title,
            'description' => $request->description,
            'target_score' => $request->target_score,
            'current_score' => 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Goal created! 🎯');
    }

    public function updateScore(Request $request, Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'current_score' => 'required|integer|min:0|max:100',
        ]);

        $goal->update([
            'current_score' => $request->current_score,
        ]);

        // Check if goal is now complete
        if ($goal->current_score >= $goal->target_score && !$goal->is_completed) {
            $goal->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Award XP for completing a goal
            GamificationService::awardXp(Auth::user(), 50, 'Goal completed: ' . $goal->title);
            GamificationService::awardBadge(Auth::user(), 'Goal Achiever', 'Completed a goal with a target discipline score');
        }

        return back()->with('success', 'Goal progress updated! 📈');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return back()->with('success', 'Goal removed. 🗑️');
    }
}