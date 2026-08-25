<?php

namespace App\Http\Controllers;

use App\Models\FitnessPlan;
use App\Services\FitnessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FitnessController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $plan = $user->fitnessPlans()
            ->where('is_active', true)
            ->latest()
            ->first();

        $entries = $user->fitnessEntries()
            ->with(['linkedTask', 'linkedRoutine'])
            ->orderBy('type')
            ->orderByRaw('day_of_week IS NULL, day_of_week')
            ->get();

        return view('fitness.index', [
            'plan' => $plan,
            'todayIndex' => (int) (Carbon::today()->dayOfWeekIso - 1), // 0 = Monday
            'entries' => $entries,
            'tasks' => $user->tasks()->orderByDesc('created_at')->limit(50)->get(['id', 'title']),
            'routines' => $user->routines()->orderByDesc('created_at')->limit(50)->get(['id', 'title']),
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

    /**
     * Create a manual fitness entry (nutrition / workout / gym).
     */
    public function storeEntry(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:nutrition,workout,gym',
            'title' => 'required|string|max:255',
            'details' => 'nullable|string|max:5000',
            'day_of_week' => 'nullable|integer|min:1|max:7',
            'linked_task_id' => 'nullable|exists:tasks,id',
            'linked_routine_id' => 'nullable|exists:routines,id',
        ]);

        auth()->user()->fitnessEntries()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'details' => $validated['details'] ?? null,
            'day_of_week' => $validated['day_of_week'] ?? null,
            'linked_task_id' => $validated['linked_task_id'] ?? null,
            'linked_routine_id' => $validated['linked_routine_id'] ?? null,
        ]);

        return back()->with('success', 'Entry added to your fitness plan! ✅');
    }

    /**
     * Toggle an entry's done state (manual entries only).
     */
    public function toggleEntry(FitnessEntry $entry)
    {
        abort_unless($entry->user_id === auth()->id(), 403);

        $entry->update(['is_done' => !$entry->is_done]);

        return back();
    }

    /**
     * Delete a fitness entry.
     */
    public function destroyEntry(FitnessEntry $entry)
    {
        abort_unless($entry->user_id === auth()->id(), 403);

        $entry->delete();

        return back()->with('success', 'Entry removed.');
    }
}
