<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\DailyStat;
use App\Models\DeclarationRead;
use Carbon\Carbon;
use Illuminate\View\View;

class RecapController extends Controller
{
    /**
     * 🌱 Growth titles by level.
     */
    protected array $growthTitles = [
        1  => 'Seedling',
        3  => 'Sprout',
        5  => 'Sapling',
        10 => 'Evergreen',
        20 => 'Mountain',
        50 => 'Legend',
    ];

    public function index(): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        $weekStart = $today->copy()->subDays(6)->toDateString();
        $weekEnd = $today->toDateString();

        // ── Active days & longest streak run this week (from DailyStat activity) ──
        $activeDates = DailyStat::where('user_id', $user->id)
            ->whereDate('date', '>=', $weekStart)
            ->whereDate('date', '<=', $weekEnd)
            ->where('score', '>', 0)
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d));

        $activeDays = $activeDates->count();

        $longestRun = 0;
        $currentRun = 0;
        $prev = null;
        foreach ($activeDates as $date) {
            $currentRun = ($prev && $date->diffInDays($prev) === 1) ? $currentRun + 1 : 1;
            $longestRun = max($longestRun, $currentRun);
            $prev = $date;
        }

        // ── ⭐ Best habit this week ──
        $bestHabit = null;
        $bestHabitCount = 0;
        foreach ($user->habits()->where('is_active', true)->get() as $habit) {
            $count = $habit->completions()
                ->whereDate('date', '>=', $weekStart)
                ->whereDate('date', '<=', $weekEnd)
                ->get()
                ->filter(fn($c) => $c->value >= ($habit->target_value ?? 1))
                ->count();

            if ($count > $bestHabitCount) {
                $bestHabitCount = $count;
                $bestHabit = $habit;
            }
        }

        // ── 📖 Chapters declared this week ──
        $chaptersDeclared = DeclarationRead::where('user_id', $user->id)
            ->whereDate('date', '>=', $weekStart)
            ->whereDate('date', '<=', $weekEnd)
            ->distinct('reference')
            ->count('reference');

        // ── Other weekly stats ──
        $tasksCompleted = $user->tasks()
            ->whereBetween('due_date', [$weekStart, $weekEnd])
            ->where('is_completed', true)
            ->count();

        $focusMinutes = (int) $user->focusSessions()
            ->whereDate('started_at', '>=', $weekStart)
            ->sum('duration');

        $examensWritten = $user->examens()
            ->whereDate('date', '>=', $weekStart)
            ->count();

        // ── 🌱 Growth title ──
        $level = $user->level ?? 1;
        $growthTitle = 'Seedling';
        foreach ($this->growthTitles as $minLevel => $title) {
            if ($level >= $minLevel) {
                $growthTitle = $title;
            }
        }

        return view('recap.index', compact(
            'longestRun',
            'bestHabit',
            'bestHabitCount',
            'chaptersDeclared',
            'tasksCompleted',
            'focusMinutes',
            'examensWritten',
            'activeDays',
            'level',
            'growthTitle',
            'weekStart',
            'weekEnd'
        ));
    }
}