<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Devotional;

class DevotionalController extends Controller
{
    public function today()
    {
        $today = now()->toDateString();

        $devotional = Devotional::whereDate('date', $today)->first();

        // fallback if none exists
        if (!$devotional) {
            $devotional = Devotional::inRandomOrder()->first();
        }

        return view('devotional.today', compact('devotional'));
    }
}