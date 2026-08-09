<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Devotional;
use App\Models\PrayerPlan;
use App\Models\MorningDevotion;
use App\Models\FastingPlan;
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

        $morningDevotion = MorningDevotion::where('user_id', Auth::id())->first();
        $fastingPlans = FastingPlan::where('user_id', Auth::id())
            ->whereNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('devotional.today', compact('devotional', 'prayerPlans', 'morningDevotion', 'fastingPlans'));
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

    public function storeMorningDevotion(Request $request)
    {
        $validated = $request->validate([
            'wake_up_time' => 'required|date_format:H:i',
            'alarm_enabled' => 'boolean',
            'declaration' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['alarm_enabled'] = $request->has('alarm_enabled');

        MorningDevotion::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect()->route('devotional.today')->with('success', 'Morning devotion updated successfully.');
    }

    public function toggleMorningDevotion(MorningDevotion $morningDevotion)
    {
        $this->authorize('view', $morningDevotion);
        
        $morningDevotion->update(['is_active' => !$morningDevotion->is_active]);

        return redirect()->route('devotional.today')->with('success', $morningDevotion->is_active ? 'Morning devotion activated.' : 'Morning devotion deactivated.');
    }

    public function storeFastingPlan(Request $request)
    {
        $validated = $request->validate([
            'purpose' => 'required|string|max:500',
            'days' => 'required|integer|min:1|max:365',
            'reminder_time' => 'nullable|date_format:H:i',
            'reminder_enabled' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['reminder_enabled'] = $request->has('reminder_enabled');
        $validated['started_at'] = now();

        FastingPlan::create($validated);

        return redirect()->route('devotional.today')->with('success', 'Fasting plan created successfully.');
    }

    public function startFastingPlan(FastingPlan $fastingPlan)
    {
        $this->authorize('view', $fastingPlan);
        
        $fastingPlan->update(['started_at' => now()]);

        return redirect()->route('devotional.today')->with('success', 'Fasting plan started. Stay strong!');
    }

    public function completeFastingPlan(FastingPlan $fastingPlan)
    {
        $this->authorize('view', $fastingPlan);
        
        $fastingPlan->update(['completed_at' => now()]);

        return redirect()->route('devotional.today')->with('success', 'Fasting completed. Congratulations!');
    }

    public function destroyFastingPlan(FastingPlan $fastingPlan)
    {
        $this->authorize('view', $fastingPlan);
        
        $fastingPlan->delete();

        return redirect()->route('devotional.today')->with('success', 'Fasting plan removed.');
    }
}
