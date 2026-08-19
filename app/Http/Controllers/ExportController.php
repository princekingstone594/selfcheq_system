<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Carbon\Carbon;

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
     * Export all user data as JSON.
     */
    public function exportJson(): Response
    {
        $user = Auth::user();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'birthday' => $user->birthday?->toDateString(),
                'bio' => $user->bio,
                'xp' => $user->xp,
                'level' => $user->level,
                'streak' => $user->streak,
                'last_completed_date' => $user->last_completed_date?->toDateString(),
                'coach_mode' => $user->coach_mode,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'tasks' => $user->tasks()->get()->map(fn($t) => [
                'title' => $t->title,
                'is_completed' => $t->is_completed,
                'due_date' => $t->due_date?->toDateString(),
                'reminder_time' => $t->reminder_time,
                'is_important' => $t->is_important,
                'is_urgent' => $t->is_urgent,
                'alarm_enabled' => $t->alarm_enabled,
                'created_at' => $t->created_at?->toIso8601String(),
            ]),
            'routines' => $user->routines()->get()->map(fn($r) => [
                'title' => $r->title,
                'description' => $r->description,
                'is_completed' => $r->is_completed,
                'date' => $r->date,
                'reminder_time' => $r->reminder_time,
                'frequency' => $r->frequency,
                'is_permanent' => $r->is_permanent,
                'created_at' => $r->created_at?->toIso8601String(),
            ]),
            'journals' => $user->journals()->get()->map(fn($j) => [
                'date' => $j->date,
                'content' => $j->content,
                'mood' => $j->mood,
                'gratitude' => $j->gratitude,
                'reflection' => $j->reflection,
                'created_at' => $j->created_at?->toIso8601String(),
            ]),
            'focus_sessions' => $user->focusSessions()->get()->map(fn($f) => [
                'duration' => $f->duration,
                'started_at' => $f->started_at?->toIso8601String(),
                'ended_at' => $f->ended_at?->toIso8601String(),
            ]),
            'financials' => $user->financials()->get()->map(fn($f) => [
                'type' => $f->type,
                'title' => $f->title,
                'description' => $f->description,
                'amount' => $f->amount,
                'frequency' => $f->frequency,
                'due_date' => $f->due_date,
                'is_completed' => $f->is_completed,
                'is_recurring' => $f->is_recurring,
                'reminder_days' => $f->reminder_days,
                'created_at' => $f->created_at?->toIso8601String(),
            ]),
            'habits' => $user->habits()->get()->map(fn($h) => [
                'title' => $h->title,
                'emoji' => $h->emoji,
                'description' => $h->description,
                'target_value' => $h->target_value,
                'unit' => $h->unit,
                'frequency' => $h->frequency,
                'reminder_time' => $h->reminder_time,
                'is_active' => $h->is_active,
                'xp_reward' => $h->xp_reward,
                'completions' => $h->completions()->get()->map(fn($c) => [
                    'date' => $c->date,
                    'value' => $c->value,
                ]),
                'created_at' => $h->created_at?->toIso8601String(),
            ]),
            'notes' => $user->notes()->get()->map(fn($n) => [
                'title' => $n->title,
                'content' => $n->content,
                'created_at' => $n->created_at?->toIso8601String(),
            ]),
            'appointments' => $user->appointments()->get()->map(fn($a) => [
                'title' => $a->title,
                'date' => $a->date,
                'time' => $a->time,
                'notes' => $a->notes,
                'is_completed' => $a->is_completed,
                'created_at' => $a->created_at?->toIso8601String(),
            ]),
            'badges' => $user->badges()->get()->map(fn($b) => [
                'name' => $b->name,
                'description' => $b->description,
                'icon' => $b->icon,
            ]),
            'daily_stats' => $user->dailyStats()->get()->map(fn($d) => [
                'date' => $d->date,
                'score' => $d->score,
                'tasks_completed' => $d->tasks_completed,
                'tasks_total' => $d->tasks_total,
                'focus_minutes' => $d->focus_minutes,
                'journaled' => $d->journaled,
                'mood' => $d->mood,
            ]),
        ];

        $filename = 'selfcheq-export-' . now()->format('Y-m-d-His') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export a human-readable text summary.
     */
    public function exportText(): Response
    {
        $user = Auth::user();

        $lines = [];
        $lines[] = '========================================';
        $lines[] = '  SELFCHEQ — DISCIPLINE JOURNEY EXPORT';
        $lines[] = '========================================';
        $lines[] = '';
        $lines[] = 'Exported: ' . now()->format('F j, Y g:i A');
        $lines[] = '';
        $lines[] = '--- USER PROFILE ---';
        $lines[] = 'Name: ' . $user->name;
        $lines[] = 'Email: ' . $user->email;
        $lines[] = 'Phone: ' . ($user->phone ?? 'N/A');
        $lines[] = 'Birthday: ' . ($user->birthday?->format('F j, Y') ?? 'N/A');
        $lines[] = 'Bio: ' . ($user->bio ?? 'N/A');
        $lines[] = 'Level: ' . ($user->level ?? 1) . ' (XP: ' . ($user->xp ?? 0) . ')';
        $lines[] = 'Streak: ' . ($user->streak ?? 0) . ' days';
        $lines[] = 'Coach Mode: ' . ($user->coach_mode ?? 'strict');
        $lines[] = 'Member Since: ' . $user->created_at?->format('F j, Y');
        $lines[] = '';

        // Tasks
        $tasks = $user->tasks()->orderByDesc('due_date')->get();
        $lines[] = '--- TASKS (' . $tasks->count() . ') ---';
        foreach ($tasks as $task) {
            $status = $task->is_completed ? '[✓]' : '[ ]';
            $lines[] = "  {$status} {$task->title}" . ($task->due_date ? ' — ' . $task->due_date->format('M j, Y') : '');
        }
        $lines[] = '';

        // Routines
        $routines = $user->routines()->orderByDesc('date')->get();
        $lines[] = '--- ROUTINES (' . $routines->count() . ') ---';
        foreach ($routines as $routine) {
            $status = $routine->is_completed ? '[✓]' : '[ ]';
            $lines[] = "  {$status} {$routine->title}" . ($routine->date ? ' — ' . $routine->date : '') . ($routine->frequency ? ' (' . $routine->frequency . ')' : '');
        }
        $lines[] = '';

        // Journals
        $journals = $user->journals()->orderByDesc('date')->get();
        $lines[] = '--- JOURNAL ENTRIES (' . $journals->count() . ') ---';
        foreach ($journals as $journal) {
            $lines[] = "  📅 " . $journal->date . ($journal->mood ? ' — Mood: ' . $journal->mood . '/5' : '');
            if ($journal->content) $lines[] = "    " . $journal->content;
            if ($journal->gratitude) $lines[] = "    🙏 " . $journal->gratitude;
            if ($journal->reflection) $lines[] = "    💭 " . $journal->reflection;
            $lines[] = '';
        }

        // Focus sessions
        $focus = $user->focusSessions()->orderByDesc('started_at')->get();
        $totalFocus = $focus->sum('duration');
        $lines[] = '--- FOCUS SESSIONS (' . $focus->count() . ' total, ' . $totalFocus . ' min) ---';
        foreach ($focus->take(20) as $session) {
            $lines[] = "  ⏳ " . $session->started_at?->format('M j, Y g:i A') . ' — ' . $session->duration . ' min';
        }
        if ($focus->count() > 20) {
            $lines[] = "  ... and " . ($focus->count() - 20) . " more";
        }
        $lines[] = '';

        // Financials
        $financials = $user->financials()->get();
        $lines[] = '--- FINANCIALS (' . $financials->count() . ') ---';
        foreach ($financials as $financial) {
            $status = $financial->is_completed ? '[✓]' : '[ ]';
            $lines[] = "  {$status} [" . strtoupper($financial->type) . "] {$financial->title}" . ($financial->amount ? ' — $' . number_format($financial->amount, 2) : '') . ($financial->due_date ? ' — ' . $financial->due_date : '');
        }
        $lines[] = '';

        // Habits
        $habits = $user->habits()->get();
        $lines[] = '--- HABITS (' . $habits->count() . ') ---';
        foreach ($habits as $habit) {
            $completions = $habit->completions()->count();
            $lines[] = "  {$habit->emoji} {$habit->title} — {$completions} completions" . ($habit->is_active ? '' : ' (inactive)');
        }
        $lines[] = '';

        // Badges
        $badges = $user->badges()->get();
        $lines[] = '--- BADGES (' . $badges->count() . ') ---';
        foreach ($badges as $badge) {
            $lines[] = "  {$badge->icon} {$badge->name}" . ($badge->description ? ' — ' . $badge->description : '');
        }
        $lines[] = '';

        // Daily stats
        $stats = $user->dailyStats()->orderBy('date')->get();
        $lines[] = '--- DAILY STATS (' . $stats->count() . ') ---';
        foreach ($stats as $stat) {
            $lines[] = "  {$stat->date} — Score: {$stat->score}/100, Tasks: {$stat->tasks_completed}/{$stat->tasks_total}, Focus: {$stat->focus_minutes}min" . ($stat->journaled ? ', Journaled' : '') . ($stat->mood ? ', Mood: ' . $stat->mood . '/5' : '');
        }
        $lines[] = '';
        $lines[] = '========================================';
        $lines[] = '  Keep building your discipline! 💪';
        $lines[] = '========================================';

        $content = implode("\n", $lines);
        $filename = 'selfcheq-journey-' . now()->format('Y-m-d') . '.txt';

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}