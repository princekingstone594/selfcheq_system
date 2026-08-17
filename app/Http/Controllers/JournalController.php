<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GamificationService;
use App\Models\Journal;

class JournalController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $journal = Auth::user()
            ->journals()
            ->whereDate('date', $today)
            ->first();

        // 📜 History (past journals, excluding today, grouped by date)
        $history = Auth::user()
            ->journals()
            ->whereDate('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->groupBy(function ($j) {
                return \Carbon\Carbon::parse($j->date)->format('Y-m-d');
            });

        return view('journals.index', compact('journal', 'today', 'history'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry' => 'nullable|string',
            'mood' => 'nullable|integer|min:1|max:5',
            'gratitude' => 'nullable|string',
            'reflection' => 'nullable|string',
        ]);

        $journal = Auth::user()->journals()->updateOrCreate(
            ['date' => now()->toDateString()],
            [
                'content' => $request->entry,
                'mood' => $request->mood,
                'gratitude' => $request->gratitude,
                'reflection' => $request->reflection,
            ]
        );

        // Inform the user whether they created or updated today's entry
        $isNew = $journal->wasRecentlyCreated;

        if ($isNew) {
            GamificationService::awardXp(Auth::user(), 20, 'Journal entry created');
            GamificationService::recordDailyActivity(Auth::user());
        }

        return back()->with('success', $isNew
            ? 'Journal entry saved! 📝 +20 XP'
            : 'Journal entry updated! You can keep editing until midnight. 🧠');
    }
}
