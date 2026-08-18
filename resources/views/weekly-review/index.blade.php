<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-indigo-950/20 to-slate-900 p-6 shadow-2xl shadow-indigo-950/30 sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Weekly Review</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Your week at a glance</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-400">
                Comparing discipline scores, mood trends, task completion rates, and AI coach insights
                from the past 7 days.
            </p>
        </section>

        <!-- Key Metrics -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Avg. Discipline Score</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $avgScore }}/100</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Tasks Completed</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $totalTasks }}</p>
                <p class="text-xs text-slate-400">of {{ $totalTasksPossible }} assigned</p>
                <div class="mt-2 h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500"
                         style="width: {{ $taskCompletionRate }}%"></div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Focus Time</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $totalFocus }}</p>
                <p class="text-xs text-slate-400">minutes this week</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Journal Entries</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $totalJournal }}</p>
                <p class="text-xs text-slate-400">of 7 days</p>
            </div>
        </section>

        <!-- Discipline Score Trend (Bar Chart) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Discipline Score Trend</p>
            <p class="mt-1 text-sm text-slate-400">Your daily discipline score over the past 7 days</p>

            @php
                $maxScore = max($scoreChart) ?: 100;
            @endphp

            <div class="mt-4 flex items-end justify-between gap-2 h-40">
                @foreach($chartLabels as $index => $label)
                    <div class="flex flex-1 flex-col items-center">
                        <span class="mb-1 text-xs font-medium text-slate-300">{{ $scoreChart[$index] ?? 0 }}</span>
                        <div class="relative w-full">
                            <div class="h-full w-full rounded-t-sm bg-gradient-to-t from-indigo-600/80 to-indigo-500"
                                 style="height: {{ (($scoreChart[$index] ?? 0) / $maxScore) * 100 }}%">
                            </div>
                        </div>
                        <span class="mt-1 text-[10px] text-slate-400">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Two-column: Mood Trend + Task Completion -->
        <section class="grid gap-6 lg:grid-cols-2">

            <!-- Mood Trend -->
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Mood Trend</p>
                <p class="mt-1 text-sm text-slate-400">How your mood fluctuated over the past week</p>

                <div class="mt-4 space-y-2">
                    @foreach($moodLabels as $index => $label)
                        @php
                            $mood = $moodData[$index] ?? null;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-8 text-[10px] text-slate-400">{{ $label }}</span>
                            @if($mood)
                                @php
                                    $moodColors = [
                                        1 => 'bg-rose-500', 2 => 'bg-orange-500',
                                        3 => 'bg-amber-500', 4 => 'bg-emerald-500', 5 => 'bg-green-500',
                                    ];
                                    $colorClass = $moodColors[(int)round($mood)] ?? 'bg-slate-500';
                                @endphp
                                <div class="flex-1">
                                    <div class="h-3 rounded-full bg-slate-800 overflow-hidden">
                                        <div class="{{ $colorClass }} h-full"
                                             style="width: {{ ($mood / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                                <span class="w-10 text-right text-xs font-medium text-slate-300">{{ $mood }}/5</span>
                            @else
                                <div class="flex-1">
                                    <div class="h-3 rounded-full bg-slate-800"></div>
                                </div>
                                <span class="w-10 text-right text-xs text-slate-500">—</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Task Completion Rate -->
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Task Completion Rate</p>
                <p class="mt-1 text-sm text-slate-400">Tasks completed vs. assigned each day</p>

                <div class="mt-4 space-y-3">
                    @foreach($chartLabels as $index => $label)
                        @php
                            $completed = $taskCompletedChart[$index] ?? 0;
                            $total = $taskTotalChart[$index] ?? 0;
                            $rate = $total > 0 ? ($completed / $total) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-12 text-[10px] text-slate-400">{{ $label }}</span>
                            <div class="flex-1">
                                <div class="h-4 rounded-full bg-slate-800 overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-500"
                                         style="width: {{ $rate }}%"></div>
                                </div>
                            </div>
                            <span class="w-16 text-right text-xs font-medium text-slate-300">{{ $completed }}/{{ $total }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-xl border border-white/10 bg-slate-800/50 p-3">
                    <p class="text-xs text-slate-400">Weekly average</p>
                    <p class="text-2xl font-bold text-white">{{ $taskCompletionRate }}%</p>
                </div>
            </section>
        </section>

        <!-- Habit Completion Summary -->
        @if($habitSummaries->isNotEmpty())
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Habit Completion</p>
                <p class="mt-1 text-sm text-slate-400">How you did on your daily habits this week</p>

                <div class="mt-4 space-y-4">
                    @foreach($habitSummaries as $summary)
                        @php
                            $percentage = round(($summary['completed'] / $summary['total']) * 100);
                        @endphp
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">{{ $summary['habit']->emoji ?? '✅' }}</span>
                                    <span class="font-medium text-white">{{ $summary['habit']->title }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-slate-300">{{ $summary['completed'] }}/{{ $summary['total'] }} days</span>
                                    @if($summary['streak'] > 0)
                                        <span class="rounded-full bg-rose-500/20 px-2 py-0.5 text-xs font-bold text-rose-300">{{ $summary['streak'] }} 🔥</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-1">
                                @foreach($summary['weekData'] as $dayCompleted)
                                    <div class="flex-1 h-5 rounded {{ $dayCompleted ? 'bg-emerald-500/60' : 'bg-slate-700/50' }}"></div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- AI Coach Weekly Insights -->
        <section class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-indigo-950/10 to-slate-900 p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl">🧑‍🏫</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Coach Zoe says</p>
                    <p class="text-sm text-slate-400">Weekly insights & recommendations</p>
                </div>
            </div>

            <div class="mt-2 rounded-2xl border border-white/5 bg-slate-800/30 p-5">
                <p class="text-sm leading-relaxed text-slate-300 whitespace-pre-line">
                    {{ $weeklyInsights }}
                </p>
            </div>
        </section>

        <!-- Journal Entries -->
        @if($journalEntries->isNotEmpty())
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Journal Entries This Week</p>

                <div class="mt-4 space-y-3">
                    @foreach($journalEntries as $journal)
                        <div class="rounded-xl border border-white/10 bg-slate-800/50 p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-white">
                                    {{ \Carbon\Carbon::parse($journal->date)->format('D, M j') }}
                                </p>
                                @if($journal->mood)
                                    <span class="text-xs text-slate-400">
                                        Mood: {{ $journal->mood }}/5
                                        @php
                                            $moodIcons = [1 => '😞', 2 => '😕', 3 => '😐', 4 => '😊', 5 => '😄'];
                                        @endphp
                                        {{ $moodIcons[$journal->mood] ?? '' }}
                                    </span>
                                @endif
                            </div>
                            @if($journal->snip)
                                <p class="mt-1 text-xs text-slate-400 line-clamp-2">{{ $journal->snip }}</p>
                            @endif
                            @if($journal->gratitude)
                                <p class="mt-2 text-xs italic text-emerald-300/70">🙏 {{ \Str::limit($journal->gratitude, 80) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Best Day Highlight -->
        @if($bestDay)
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Best Day</p>
                <div class="mt-2 flex items-center gap-4 rounded-2xl border border-amber-400/20 bg-gradient-to-r from-amber-500/10 to-transparent p-4">
                    <span class="text-4xl">🏆</span>
                    <div>
                        <p class="text-lg font-semibold text-white">
                            {{ \Carbon\Carbon::parse($bestDay->date)->format('l, M j') }}
                        </p>
                        <p class="text-sm text-slate-300">
                            Score: {{ $bestDay->score }}/100 · Tasks: {{ $bestDay->tasks_completed }}/{{ $bestDay->tasks_total }} · Focus: {{ $bestDay->focus_minutes }} min
                        </p>
                    </div>
                </div>
            </section>
        @endif

    </div>
</x-app-layout>
