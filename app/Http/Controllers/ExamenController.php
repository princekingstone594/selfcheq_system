<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Examen;
use App\Services\GamificationService;

class ExamenController extends Controller
{
    /**
     * Show the Evening Examen for today.
     */
    public function today()
    {
        $today = now()->toDateString();

        $examen = Examen::where('user_id', Auth::id())
            ->whereDate('date', $today)
            ->first();

        // 📜 History of past examens
        $history = Examen::where('user_id', Auth::id())
            ->whereDate('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->groupBy(fn($e) => \Carbon\Carbon::parse($e->date)->format('Y-m-d'));

        return view('examens.today', compact('examen', 'today', 'history'));
    }

    /**
     * Save (create or update) today's Evening Examen.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mood_rating' => 'nullable|integer|min:1|max:5',
            'high_point' => 'nullable|string|max:255',
            'reflection' => 'nullable|string|max:2000',
            'gratitude' => 'nullable|string|max:255',
            'gratitude_custom' => 'nullable|string|max:255',
        ]);

        // A free-typed gratitude takes precedence over a selected chip.
        // The view submits "custom" via the empty-valued radio + gratitude_custom text.
        if (trim($validated['gratitude_custom'] ?? '') !== '') {
            $validated['gratitude'] = trim($validated['gratitude_custom']);
        }
        unset($validated['gratitude_custom']);

        $validated['user_id'] = Auth::id();
        $validated['released'] = true;

        $examen = Examen::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => now()->toDateString()],
            $validated
        );

        // Reward the discipline loop only when a fresh entry is made (not on edits)
        if ($examen->wasRecentlyCreated) {
            GamificationService::awardXp(Auth::user(), 20, 'Evening Examen completed');
            GamificationService::recordDailyActivity(Auth::user());
        }

        return redirect()->route('examen.today')->with('success', $examen->wasRecentlyCreated
            ? 'Evening Examen saved. 🌙 You can keep editing until midnight.'
            : 'Evening Examen updated. 🌙');
    }
}