<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user && $user->onboarding_complete) {
            return redirect()->route('dashboard');
        }

        return view('onboarding', [
            'user' => $user,
        ]);
    }

    public function complete(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->forceFill([
                'onboarding_complete' => true,
            ])->save();
        }

        return redirect()->route('dashboard');
    }
}
