<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Carbon\Carbon;

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
                return Carbon::parse($a->date)->format('Y-m-d');
            });

        // ✅ Completed appointments (for history tab — ticked items)
        $completed = Auth::user()->appointments()
            ->where('is_completed', true)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(30)
            ->get();

        return view('appointments.index', compact('appointments', 'history', 'completed'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time' => 'required',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Auth::user()->appointments()->create([
            'title' => $request->title,
            'time' => $request->time,
            'date' => $request->date ? Carbon::parse($request->date)->toDateString() : now()->toDateString(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Appointment added! 🗓️');
    }

    public function toggle(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update([
            'is_completed' => !$appointment->is_completed
        ]);

        $message = $appointment->is_completed
            ? 'Appointment marked as done. It will appear in history. ✅'
            : 'Appointment marked as incomplete. 🔄';

        return back()->with('success', $message);
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->delete();

        return back()->with('success', 'Appointment removed. 🗑️');
    }
}
