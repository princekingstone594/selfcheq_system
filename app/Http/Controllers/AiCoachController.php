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
        return view('coach.index');
    }

    public function chat(Request $request, AiCoachService $ai)
    {
        $reply = $ai->chat($request->message);

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