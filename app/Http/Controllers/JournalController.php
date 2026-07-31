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

        $journal = Auth::user()->journals()
            ->whereDate('date', $today)
            ->first();

        return view('journals.index', compact('journal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
        ]);

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