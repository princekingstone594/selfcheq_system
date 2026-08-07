<x-app-layout>
    <div class="space-y-6">

```
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

    <!-- Charts -->
    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Tasks (last 7 days)</p>
            <canvas id="taskChart" class="mt-4 h-64 w-full"></canvas>
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Mood (last 7 days)</p>
            <canvas id="moodChart" class="mt-4 h-64 w-full"></canvas>
        </div>
    </section>

    <!-- Nudges & rewards -->
    <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress & rewards</p>

        <div class="mt-4 space-y-3 text-sm text-slate-300">

            <!-- Nudges -->
            <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                <p class="font-medium text-white">🌟 Smart nudges</p>

                @forelse($nudges ?? [] as $nudge)
                    <p class="mt-2">• {{ $nudge }}</p>
                @empty
                    <p class="mt-2 text-slate-400">No nudges available</p>
                @endforelse
            </div>

            <!-- Badges -->
            <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                <p class="font-medium text-white">🎖️ Badges</p>

                @forelse(auth()->user()->badges ?? [] as $badge)
                    <p class="mt-2">• {{ $badge->name }}</p>
                @empty
                    <p class="mt-2 text-slate-400">No badges yet</p>
                @endforelse
            </div>

            <!-- Birthdays -->
            <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                <p class="font-medium text-white">🎂 Birthdays</p>

                @forelse($birthdays ?? [] as $b)
                    <p class="mt-2">• {{ $b->name }} ({{ $b->relationship }})</p>
                @empty
                    <p class="mt-2 text-slate-400">No birthdays today</p>
                @endforelse
            </div>

        </div>
    </section>

</div>

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const taskCtx = document.getElementById('taskChart');

    if (taskCtx) {
        new Chart(taskCtx, {
            type: 'bar',
            data: {
                labels: @json($taskLabels ?? []),
                datasets: [{
                    label: 'Tasks Completed',
                    data: @json($taskChart ?? []),
                    borderWidth: 1,
                    backgroundColor: 'rgba(129,140,248,0.7)',
                    borderColor: 'rgba(129,140,248,1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const moodCtx = document.getElementById('moodChart');

    if (moodCtx) {
        new Chart(moodCtx, {
            type: 'line',
            data: {
                labels: @json($taskLabels ?? []),
                datasets: [{
                    label: 'Mood',
                    data: @json($moodChart ?? []),
                    borderWidth: 2,
                    borderColor: 'rgba(45,212,191,1)',
                    backgroundColor: 'rgba(45,212,191,0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
</script>
```

</x-app-layout>
