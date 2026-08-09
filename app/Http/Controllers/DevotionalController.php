<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Devotional;
use App\Models\PrayerPlan;
use Illuminate\Http\Request;

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

        $prayerPlans = PrayerPlan::where('user_id', Auth::id())
            ->whereNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('devotional.today', compact('devotional', 'prayerPlans'));
    }

    public function storePrayerPlan(Request $request)
    {
        $validated = $request->validate([
            'prayer_point' => 'required|string|max:500',
            'days' => 'required|integer|min:1|max:365',
            'reminder_time' => 'nullable|date_format:H:i',
            'reminder_enabled' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['reminder_enabled'] = $request->has('reminder_enabled');

        PrayerPlan::create($validated);

        return redirect()->route('devotional.today')->with('success', 'Prayer plan created successfully.');
    }

    public function completePrayerPlan(PrayerPlan $prayerPlan)
    {
        $this->authorize('view', $prayerPlan);
        
        $prayerPlan->update(['completed_at' => now()]);

        return redirect()->route('devotional.today')->with('success', 'Prayer point completed. Glory!');
    }

    public function destroyPrayerPlan(PrayerPlan $prayerPlan)
    {
        $this->authorize('view', $prayerPlan);
        
        $prayerPlan->delete();

        return redirect()->route('devotional.today')->with('success', 'Prayer plan removed.');
    }
}
