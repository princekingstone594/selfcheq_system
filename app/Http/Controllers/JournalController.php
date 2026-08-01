<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('journals.index', compact('journal', 'today'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry' => 'nullable|string',
            'mood' => 'nullable|integer|min:1|max:5',
            'gratitude' => 'nullable|string',
            'reflection' => 'nullable|string',
        ]);

        Auth::user()->journals()->updateOrCreate(
            ['date' => now()->toDateString()],
            [
                'content' => $request->content,
                'mood' => $request->mood,
                'gratitude' => $request->gratitude,
                'reflection' => $request->reflection,
            ]
        );

        $today = now()->toDateString();

        Journal::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $today,
            ],
            [
                'content' => $request->content,
            ]
        );

        return back();
    }
}