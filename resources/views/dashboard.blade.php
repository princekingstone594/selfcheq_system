<x-app-layout>
    <div class="space-y-6">
        <!-- Hero -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 shadow-2xl shadow-indigo-950/30">
            <!-- Background image (focus/discipline) -->
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&w=1600&q=80"
                     alt="Focused work at dawn"
                     class="h-full w-full object-cover opacity-40" />
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-slate-900/40"></div>
            </div>

            <!-- Content -->
            <div class="relative p-6 sm:p-8 lg:p-10">
                <!-- Profile photo - pinned to top-right corner on all screens -->
                <img src="{{ auth()->user()->profile_photo_url }}"
                     alt="{{ auth()->user()->name }}"
                     class="absolute right-6 top-6 h-14 w-14 rounded-2xl border-2 border-white/10 object-cover shadow-lg sm:right-8 sm:top-8 sm:h-16 sm:w-16 lg:right-10 lg:top-10" />

                <!-- Brand + headline -->
                <div class="space-y-3 pr-16 sm:pr-20">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">SelfCheq</p>
                    <h1 class="text-2xl font-semibold leading-tight text-white sm:text-3xl lg:text-4xl">
                        Own your Day,<br />Stay Ahead.
                    </h1>
                </div>

                <!-- Welcome + quote -->
                <div class="mt-4 space-y-2">
                    <p class="text-lg text-slate-200">Welcome back, {{ auth()->user()->name }} 👋</p>
                    <p class="max-w-2xl text-sm italic text-slate-400">"Discipline is the bridge between goals and accomplishment." — Jim Rohn</p>
                </div>

                <!-- Today's focus card -->
                <div class="mt-5 inline-block rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-4 text-sm text-indigo-100 backdrop-blur">
                    <p class="font-medium">Today's focus</p>
                    <p class="mt-1 text-xl font-semibold">{{ $taskCompleted }}/{{ $taskTotal }} tasks completed</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Discipline score</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $disciplineScore }}/100</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Current streak</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ auth()->user()->streak }} days</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Level</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ auth()->user()->level }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Focus time</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $focusMinutes }} mins</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Today's momentum</p>
                            <p class="mt-1 text-lg font-medium text-white">The next moves that matter most</p>
                        </div>
                    </div>
                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4">
                            <p class="text-sm font-semibold text-rose-200">⏱️ Do now</p>
                            @foreach($doNow as $task)
                                <p class="mt-2 text-sm text-rose-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4">
                            <p class="text-sm font-semibold text-amber-200">📅 Schedule</p>
                            @foreach($schedule as $task)
                                <p class="mt-2 text-sm text-amber-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-sky-400/20 bg-sky-500/10 p-4">
                            <p class="text-sm font-semibold text-sky-200">🤝 Delegate</p>
                            @foreach($delegate as $task)
                                <p class="mt-2 text-sm text-sky-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-slate-400/20 bg-slate-800/70 p-4">
                            <p class="text-sm font-semibold text-slate-200">🗑️ Eliminate</p>
                            @foreach($eliminate as $task)
                                <p class="mt-2 text-sm text-slate-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Daily rhythm</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">📖 Devotional</p>
                            <a href="{{ route('devotional.today') }}" class="mt-2 inline-block text-indigo-300">Open today's guide</a>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">📝 Journal</p>
                            <a href="{{ route('journal.index') }}" class="mt-2 inline-block text-indigo-300">{{ $journalExists ? 'View / edit entry' : 'Write today’s entry' }}</a>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🔁 Routines</p>
                            <p class="mt-2">{{ $routineCompleted }}/{{ $routineTotal }} completed</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🗓️ Appointments</p>
                            @forelse($appointments as $a)
                                <p class="mt-2 text-sm">{{ \Carbon\Carbon::parse($a->time)->format('H:i') }} — {{ $a->title }}</p>
                            @empty
                                <p class="mt-2 text-sm text-slate-400">No appointments today</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress & rewards</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🌟 Smart nudges</p>
                            @foreach($nudges as $nudge)
                                <p class="mt-2">• {{ $nudge }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🎖️ Badges</p>
                            @forelse(auth()->user()->badges as $badge)
                                <p class="mt-2">• {{ $badge->name }}</p>
                            @empty
                                <p class="mt-2 text-slate-400">No badges yet</p>
                            @endforelse
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🎂 Birthdays</p>
                            @forelse($birthdays as $b)
                                <p class="mt-2">• {{ $b->name }} ({{ $b->relationship }})</p>
                            @empty
                                <p class="mt-2 text-slate-400">No birthdays today</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const taskCtx = document.getElementById('taskChart');

    if (taskCtx) {
        new Chart(taskCtx, {
            type: 'bar',
            data: {
                labels: @json($taskLabels),
                datasets: [{
                    label: 'Tasks Completed',
                    data: @json($taskChart),
                    borderWidth: 1,
                    backgroundColor: 'rgba(129,140,248,0.7)',
                    borderColor: 'rgba(129,140,248,1)'
                }]
            }
        });
    }

    const moodCtx = document.getElementById('moodChart');

    if (moodCtx) {
        new Chart(moodCtx, {
            type: 'line',
            data: {
                labels: @json($taskLabels),
                datasets: [{
                    label: 'Mood',
                    data: @json($moodChart),
                    borderWidth: 2,
                    borderColor: 'rgba(45,212,191,1)',
                    backgroundColor: 'rgba(45,212,191,0.2)'
                }]
            }
        });
    }
    </script>
</x-app-layout>