<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FocusSession;

class FocusController extends Controller
{
    public function index()
    {
        $sessions = Auth::user()->focusSessions()
            ->latest()
            ->take(5)
            ->get();

        return view('focus.index', compact('sessions'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:180',
        ]);

        FocusSession::create([
            'user_id' => Auth::id(),
            'duration' => $request->duration,
            'started_at' => now(),
        ]);

        return back();
    }

    public function stop(FocusSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->update([
            'ended_at' => now(),
        ]);

        return back();
    }
}