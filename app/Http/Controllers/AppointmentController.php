<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $appointments = Auth::user()->appointments()
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        // 📜 History (past appointments, excluding today, grouped by date)
        $history = Auth::user()->appointments()
            ->whereDate('date', '<', $today)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->groupBy(function ($a) {
                return \Carbon\Carbon::parse($a->date)->format('Y-m-d');
            });

        return view('appointments.index', compact('appointments', 'history'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time' => 'required',
        ]);

        Auth::user()->appointments()->create([
            'title' => $request->title,
            'time' => $request->time,
            'date' => now()->toDateString(),
        ]);

        return back();
    }

    public function toggle(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update([
            'is_completed' => !$appointment->is_completed
        ]);

        return back();
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->delete();

        return back();
    }
}