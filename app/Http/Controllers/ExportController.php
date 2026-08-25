<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Show the export options page.
     */
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'tasks' => $user->tasks()->count(),
            'routines' => $user->routines()->count(),
            'journals' => $user->journals()->count(),
            'focus_sessions' => $user->focusSessions()->count(),
            'financials' => $user->financials()->count(),
            'habits' => $user->habits()->count(),
            'notes' => $user->notes()->count(),
            'appointments' => $user->appointments()->count(),
        ];

        return view('export.index', compact('stats'));
    }

    /**
     * Export the user's complete journey as a downloadable PDF.
     */
    public function exportPdf()
    {
        $user = Auth::user();

        $data = [
            'user' => $user,
            'tasks' => $user->tasks()->orderByDesc('created_at')->get(),
            'routines' => $user->routines()->orderByDesc('date')->get(),
            'journals' => $user->journals()->orderByDesc('date')->get(),
            'focusSessions' => $user->focusSessions()->orderByDesc('started_at')->limit(50)->get(),
            'focusTotalMinutes' => (int) $user->focusSessions()->sum('duration'),
            'financials' => $user->financials()->orderByDesc('due_date')->get(),
            'habits' => $user->habits()->withCount('completions')->get(),
            'badges' => $user->badges()->get(),
            'dailyStats' => $user->dailyStats()->orderBy('date')->get(),
            'generatedAt' => now()->format('F j, Y g:i A'),
        ];

        $filename = 'selfcheq-journey-' . now()->format('Y-m-d') . '.pdf';

        return Pdf::loadView('export.pdf', $data)
            ->setPaper('a4')
            ->download($filename);
    }
}
