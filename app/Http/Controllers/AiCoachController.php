<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiCoachService;

class AiCoachController extends Controller
{
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
