<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiCoachService;
use Illuminate\View\View;

class AiCoachController extends Controller
{
    /**
     * Display the Coach Zoe landing page.
     */
    public function index(): View
    {
        $history = auth()->user()->chatMessages()
            ->latest()
            ->take(30)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => ['from' => $m->role === 'user' ? 'user' : 'zoe', 'text' => $m->content])
            ->all();

        return view('coach.index', ['history' => $history]);
    }

    public function chat(Request $request, AiCoachService $ai)
    {
        $request->validate(['message' => 'required|string|max:4000']);

        $user = auth()->user();

        // Recent history (before this message) for context
        $history = $user->chatMessages()
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->all();

        $user->chatMessages()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        $reply = $ai->chat($request->message, $history);

        $user->chatMessages()->create([
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return response()->json([
            'reply' => $reply
        ]);
    }

    public function setMode(Request $request)
    {
        $user = auth()->user();
        $user->coach_mode = $request->mode;
        $user->save();

        return back();
    }
}