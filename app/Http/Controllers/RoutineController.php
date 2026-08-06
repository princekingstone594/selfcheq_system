<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Routine;

class RoutineController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // If no routines today → copy from yesterday
        $exists = $user->routines()->whereDate('date', $today)->exists();

        if (!$exists) {
            $yesterday = now()->subDay()->toDateString();

            $yesterdayRoutines = $user->routines()
                ->whereDate('date', $yesterday)
                ->get();

            foreach ($yesterdayRoutines as $routine) {
                $user->routines()->create([
                    'title' => $routine->title,
                    'date' => $today,
                    'is_completed' => false,
                ]);
            }
        }

        $routines = $user->routines()
            ->whereDate('date', $today)
            ->get();

        // 📜 History (past routines, excluding today, grouped by date)
        $history = $user->routines()
            ->whereDate('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->groupBy(function ($r) {
                return \Carbon\Carbon::parse($r->date)->format('Y-m-d');
            });

        return view('routines.index', compact('routines', 'history'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Auth::user()->routines()->create([
            'title' => $request->title,
            'date' => now()->toDateString(),
        ]);

        return back();
    }

    public function toggle(Routine $routine)
    {
        if ($routine->user_id !== Auth::id()) {
            abort(403);
        }

        $routine->update([
            'is_completed' => !$routine->is_completed
        ]);

        return back();
    }
}