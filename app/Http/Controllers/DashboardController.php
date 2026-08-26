<?php

namespace App\Http\Controllers;

use App\Services\AiCoachService;
use App\Services\DashboardData;
use App\Models\DailyStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(AiCoachService $aiCoach, DashboardData $dashboardData)
    {
        $user = Auth::user();
        $data = $dashboardData->build();

        // 💾 Save today's stats snapshot (guarded internally against schema drift).
        $dashboardData->persistDailyStat($user, Carbon::today()->toDateString(), [
            'score' => $data['disciplineScore'],
            'tasks_completed' => $data['taskCompleted'],
            'tasks_total' => $data['taskTotal'],
            'focus_minutes' => $data['focusMinutes'],
            'journaled' => $data['journalExists'],
            'mood' => $data['moodAvg'],
        ]);

        return view('dashboard', $data);
    }

    /**
     * Clears the cached daily AI coach message so the next dashboard load
     * generates a fresh one.
     */
    public function refreshCoach(DashboardData $dashboardData)
    {
        $user = Auth::user();

        Cache::forget('ai_coach_' . $user->id . '_' . Carbon::today()->toDateString());

        return redirect()
            ->route('dashboard')
            ->with('status', 'coach-refreshed');
    }
}
