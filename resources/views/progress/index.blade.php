<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Your discipline journey at a glance</h1>
            <p class="mt-2 text-sm text-slate-400">Track your scores, streaks, mood and momentum over time.</p>
        </section>

        <!-- Stat cards -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Discipline score</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ $disciplineScore ?? 0 }}/100
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Current streak</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ auth()->user()->streak ?? 0 }} days
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Level</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ auth()->user()->level ?? 1 }}
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Focus time</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ $focusMinutes ?? 0 }} mins
                </p>
            </div>
        </section>

        <!-- Simple Stats -->
        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">This Week</p>
                <p class="mt-3 text-2xl font-bold text-white">{{ array_sum($taskChart ?? []) }}</p>
                <p class="text-xs text-slate-400">tasks completed</p>
                <div class="mt-3 h-2 rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500 transition-all" style="width: {{ $taskTotal > 0 ? min(100, (array_sum($taskChart ?? []) / max(1, $taskTotal)) * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Mood Trend</p>
                <p class="mt-3 text-2xl font-bold text-white">{{ $moodAvg ? round($moodAvg, 1) : 'N/A' }}/5</p>
                <p class="text-xs text-slate-400">average mood</p>
                <div class="mt-3 flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <div class="flex-1 h-2 rounded-full {{ $i <= ($moodAvg ?? 0) ? 'bg-emerald-500' : 'bg-slate-800' }}"></div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- Nudges & rewards -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress & rewards</p>

            <div class="mt-4 space-y-3 text-sm text-slate-300">

                <!-- Nudges -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                    <p class="font-medium text-white">Smart nudges</p>

                    @forelse($nudges ?? [] as $nudge)
                        <p class="mt-2">• {{ $nudge }}</p>
                    @empty
                        <p class="mt-2 text-slate-400">No nudges available</p>
                    @endforelse
                </div>

                <!-- Badges -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                    <p class="font-medium text-white">Badges</p>

                    @forelse(auth()->user()->badges ?? [] as $badge)
                        <p class="mt-2">• {{ $badge->name }}</p>
                    @empty
                        <p class="mt-2 text-slate-400">No badges yet</p>
                    @endforelse
                </div>

                <!-- Birthdays -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                    <p class="font-medium text-white">Birthdays</p>

                    @forelse($birthdays ?? [] as $b)
                        <p class="mt-2">• {{ $b->name }} ({{ $b->relationship }})</p>
                    @empty
                        <p class="mt-2 text-slate-400">No birthdays today</p>
                    @endforelse
                </div>

            </div>
        </section>

    </div>
</x-app-layout>