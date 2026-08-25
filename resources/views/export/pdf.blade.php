<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SelfCheq Journey Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 24px; }
        h1 { font-size: 22px; color: #3730a3; margin: 0; }
        h2 { font-size: 15px; color: #3730a3; border-bottom: 2px solid #6366f1; padding-bottom: 4px; margin: 22px 0 8px; }
        .meta { color: #64748b; font-size: 10px; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #eef2ff; color: #3730a3; font-size: 10px; }
        .muted { color: #94a3b8; font-style: italic; }
        .badge { display: inline-block; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 2px 8px; margin: 2px; font-size: 10px; }
        .footer { margin-top: 28px; text-align: center; color: #64748b; font-size: 10px; border-top: 1px solid #cbd5e1; padding-top: 8px; }
        .page-break { page-break-before: always; }
        .done { color: #059669; } .open { color: #dc2626; }
    </style>
</head>
<body>
    <h1>⚡ SelfCheq — My Discipline Journey</h1>
    <p class="meta">
        {{ $user->name }} &lt;{{ $user->email }}&gt;
        &nbsp;·&nbsp; Level {{ $user->level }} · {{ $user->xp }} XP · 🔥 {{ $user->streak }}-day streak
        &nbsp;·&nbsp; Generated {{ $generatedAt }}
    </p>

    <h2>📋 Tasks ({{ $tasks->count() }})</h2>
    @if($tasks->isEmpty())
        <p class="muted">No tasks recorded.</p>
    @else
    <table>
        <tr><th style="width:45%">Title</th><th>Status</th><th>Due</th><th>Priority</th></tr>
        @foreach($tasks as $t)
            <tr>
                <td>{{ $t->title }}</td>
                <td class="{{ $t->is_completed ? 'done' : 'open' }}">{{ $t->is_completed ? '✓ Done' : 'Open' }}</td>
                <td>{{ $t->due_date?->format('M j, Y') ?? '—' }}</td>
                <td>{{ trim(($t->is_important ? 'Important ' : '') . ($t->is_urgent ? 'Urgent' : '')) ?: '—' }}</td>
            </tr>
        @endforeach
    </table>
    @endif

    <div class="page-break"></div>

    <h2>🔁 Routines ({{ $routines->count() }})</h2>
    @if($routines->isEmpty())
        <p class="muted">No routines recorded.</p>
    @else
    <table>
        <tr><th>Title</th><th>Status</th><th>Date</th><th>Frequency</th></tr>
        @foreach($routines as $r)
            <tr>
                <td>{{ $r->title }}</td>
                <td class="{{ $r->is_completed ? 'done' : 'open' }}">{{ $r->is_completed ? '✓ Done' : 'Open' }}</td>
                <td>{{ $r->date ?? '—' }}</td>
                <td>{{ $r->frequency ?? '—' }}</td>
            </tr>
        @endforeach
    </table>
    @endif

    <h2>⏳ Focus Sessions</h2>
    <p>Total focus time: <strong>{{ $focusTotalMinutes }} minutes</strong>. Latest sessions:</p>
    @if($focusSessions->isEmpty())
        <p class="muted">No focus sessions recorded.</p>
    @else
    <table>
        <tr><th>Started</th><th>Duration</th></tr>
        @foreach($focusSessions as $f)
            <tr>
                <td>{{ $f->started_at?->format('M j, Y g:i A') ?? '—' }}</td>
                <td>{{ $f->duration }} min</td>
            </tr>
        @endforeach
    </table>
    @endif

    <h2>🔥 Habits ({{ $habits->count() }})</h2>
    @if($habits->isEmpty())
        <p class="muted">No habits recorded.</p>
    @else
    <table>
        <tr><th>Habit</th><th>Completions</th><th>Active</th></tr>
        @foreach($habits as $h)
            <tr>
                <td>{{ $h->emoji }} {{ $h->title }}</td>
                <td>{{ $h->completions_count }}</td>
                <td>{{ $h->is_active ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
    </table>
    @endif

    <div class="page-break"></div>

    <h2>📝 Journal Entries ({{ $journals->count() }})</h2>
    @if($journals->isEmpty())
        <p class="muted">No journal entries recorded.</p>
    @else
        @foreach($journals as $j)
            <p style="margin-bottom:10px">
                <strong>{{ $j->date?->format('M j, Y') }}</strong>
                @if($j->mood) · Mood: {{ $j->mood }}/5 @endif<br>
                {!! nl2br(e($j->content)) !!}
                @if($j->gratitude) <span class="muted">🙏 Gratitude: {{ $j->gratitude }}</span><br>@endif
                @if($j->reflection) <span class="muted">💭 Reflection: {{ $j->reflection }}</span><br>@endif
            </p>
        @endforeach
    @endif

    <h2>💰 Financials ({{ $financials->count() }})</h2>
    @if($financials->isEmpty())
        <p class="muted">No financial items recorded.</p>
    @else
    <table>
        <tr><th>Type</th><th>Title</th><th>Amount</th><th>Due</th><th>Status</th></tr>
        @foreach($financials as $f)
            <tr>
                <td>{{ ucfirst($f->type) }}</td>
                <td>{{ $f->title }}</td>
                <td>{{ $f->amount ? '$' . number_format($f->amount, 2) : '—' }}</td>
                <td>{{ $f->due_date ?? '—' }}</td>
                <td class="{{ $f->is_completed ? 'done' : 'open' }}">{{ $f->is_completed ? '✓ Done' : 'Open' }}</td>
            </tr>
        @endforeach
    </table>
    @endif

    <div class="page-break"></div>

    <h2>🏅 Badges ({{ $badges->count() }})</h2>
    @forelse($badges as $b)
        <span class="badge">{{ $b->icon }} {{ $b->name }}</span>
    @empty
        <p class="muted">No badges earned yet.</p>
    @endforelse

    <h2>📊 Daily Stats ({{ $dailyStats->count() }} days tracked)</h2>
    @if($dailyStats->isNotEmpty())
    <table>
        <tr><th>Date</th><th>Score</th><th>Tasks</th><th>Focus</th><th>Journaled</th><th>Mood</th></tr>
        @foreach($dailyStats as $d)
            <tr>
                <td>{{ $d->date }}</td>
                <td>{{ $d->score }}/100</td>
                <td>{{ $d->tasks_completed }}/{{ $d->tasks_total }}</td>
                <td>{{ $d->focus_minutes }} min</td>
                <td>{{ $d->journaled ? '✓' : '' }}</td>
                <td>{{ $d->mood ? $d->mood . '/5' : '—' }}</td>
            </tr>
        @endforeach
    </table>
    @else
        <p class="muted">No daily stats recorded.</p>
    @endif

    <div class="footer">
        Generated by SelfCheq on {{ $generatedAt }} — Keep building your discipline! 💪
    </div>
</body>
</html>
