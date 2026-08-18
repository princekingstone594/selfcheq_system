<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HabitController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $habits = $user->habits()
            ->where('is_active', true)
            ->get();

        $todayDate = $today;

        // Get completion data for the last 7 days for the chart
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $chartLabels[] = Carbon::parse($date)->format('D');
            $completed = 0;
            foreach ($habits as $habit) {
                $completion = $habit->completions()
                    ->whereDate('date', $date)
                    ->first();
                if ($completion && $completion->value >= ($habit->target_value ?? 1)) {
                    $completed++;
                }
            }
            $chartData[] = count($habits) > 0
                ? round(($completed / count($habits)) * 100)
                : 0;
        }

        return view('habits.index', compact(
            'habits',
            'todayDate',
            'chartLabels',
            'chartData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'emoji' => 'nullable|string',
            'description' => 'nullable|string',
            'target_value' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'frequency' => 'nullable|in:daily,weekday,weekend,weekly,monthly',
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        Auth::user()->habits()->create([
            'title' => $request->title,
            'emoji' => $request->emoji ?? '✅',
            'description' => $request->description,
            'target_value' => $request->target_value ?? 1,
            'unit' => $request->unit ?? 'times',
            'frequency' => $request->frequency ?? 'daily',
            'reminder_time' => $request->reminder_time,
            'is_active' => true,
        ]);

        return back()->with('success', 'Habit created! 🎯');
    }

    public function toggle(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Check if the user already completed this habit today
        $existingCompletion = HabitCompletion::where('habit_id', $habit->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingCompletion) {
            // Uncomplete
            $existingCompletion->delete();
            if ($existingCompletion->value >= ($habit->target_value ?? 1)) {
                GamificationService::deductXp($user, $habit->xp_reward, 'Habit uncompleted: ' . $habit->title);
            }
        } else {
            // Complete — increment value (supports partial completion)
            HabitCompletion::create([
                'habit_id' => $habit->id,
                'user_id' => $user->id,
                'date' => $today,
                'value' => 1,
            ]);

            // Award XP if the target is met
            if (1 >= ($habit->target_value ?? 1)) {
                GamificationService::awardXp($user, $habit->xp_reward, 'Habit completed: ' . $habit->title);
                GamificationService::recordDailyActivity($user);
            }

            // Check for streak milestone badges
            $streak = $habit->currentStreak();
            $habitBadges = [
                3  => '🔥 Habit Streak: 3 days',
                7  => '🔥 Habit Streak: 7 days',
                14 => '🔥 Habit Streak: 14 days',
                30 => '🔥 Habit Streak: 30 days',
            ];
            foreach ($habitBadges as $milestone => $badgeName) {
                if ($streak >= $milestone) {
                    GamificationService::awardBadge($user, $badgeName, "Maintained habit '{$habit->title}' for {$milestone} days");
                }
            }
        }

        return back()->with('success', $existingCompletion
            ? '✅ Habit marked incomplete'
            : '✅ Habit completed! +' . $habit->xp_reward . ' XP');
    }

    public function destroy(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }

        $habit->completions()->delete();
        $habit->delete();

        return back()->with('success', 'Habit removed. 🗑️');
    }
}
