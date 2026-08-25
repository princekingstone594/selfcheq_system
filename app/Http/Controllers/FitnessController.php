<?php

namespace App\Http\Controllers;

use App\Models\FitnessPlan;
use App\Services\FitnessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FitnessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $plan = auth()->user()->fitnessPlans()
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('fitness.index', [
            'plan' => $plan,
            'todayIndex' => (int) (Carbon::today()->dayOfWeekIso - 1), // 0 = Monday
        ]);
    }

    /**
     * Generate a new weekly plan for the chosen goal/level.
     */
    public function generate(Request $request, FitnessService $fitness)
    {
        $validated = $request->validate([
            'goal' => 'required|in:lose_weight,build_muscle,endurance,general',
            'level' => 'required|in:beginner,intermediate,advanced',
        ]);

        $user = auth()->user();

        // Deactivate old plans
        $user->fitnessPlans()->update(['is_active' => false]);

        $data = $fitness->generateWeeklyPlan($validated['goal'], $validated['level']);

        FitnessPlan::create([
            'user_id' => $user->id,
            'goal' => $validated['goal'],
            'level' => $validated['level'],
            'week_start' => Carbon::today()->startOfWeek()->toDateString(),
            'plan' => $data,
            'completed_days' => [],
            'is_active' => true,
        ]);

        return redirect()
            ->route('fitness.index')
            ->with('success', 'Your weekly fitness plan is ready! 🏋️');
    }

    /**
     * Toggle completion of a plan day.
     */
    public function toggleDay(Request $request)
    {
        $request->validate(['day' => 'required|integer|min:0|max:6']);

        $plan = auth()->user()->fitnessPlans()
            ->where('is_active', true)
            ->latest()
            ->firstOrFail();

        $completed = collect($plan->completed_days ?? []);
        $day = (int) $request->day;

        if ($completed->contains($day)) {
            $completed = $completed->reject(fn ($d) => $d === $day)->values();
        } else {
            $completed->push($day);
        }

        $plan->update(['completed_days' => $completed->all()]);

        return back();
    }
}
