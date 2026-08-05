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

        <!-- Daily Rhythm (first) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Daily rhythm</p>
            <p class="mt-1 text-lg font-medium text-white">A snapshot of your day</p>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <!-- 📖 Devotional verse -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                    <p class="font-medium text-white">📖 Devotional</p>
                    @if($devotional)
                        <p class="mt-2 text-sm italic text-slate-300 line-clamp-3">{{ $devotional->content }}</p>
                        <a href="{{ route('devotional.today') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">See more →</a>
                    @else
                        <p class="mt-2 text-sm text-slate-400">No verse for today.</p>
                        <a href="{{ route('devotional.today') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">Open devotional →</a>
                    @endif
                </div>

                <!-- 📝 Journal -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                    <p class="font-medium text-white">📝 Journal</p>
                    <p class="mt-2 text-sm text-slate-300">{{ $journalExists ? 'You have an entry for today.' : 'No entry yet today.' }}</p>
                    <a href="{{ route('journal.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">{{ $journalExists ? 'View / edit entry →' : 'Write today’s entry →' }}</a>
                </div>

                <!-- ✅ Tasks -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                    <p class="font-medium text-white">✅ Tasks</p>
                    <p class="mt-2 text-sm text-slate-300">{{ $taskCompleted }}/{{ $taskTotal }} completed</p>
                    @forelse($tasksToday->take(3) as $task)
                        <p class="mt-1 text-xs {{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">• {{ $task->title }}</p>
                    @empty
                        <p class="mt-1 text-xs text-slate-500">No tasks yet</p>
                    @endforelse
                    <a href="{{ route('tasks.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">See more →</a>
                </div>

                <!-- 🔁 Routines -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                    <p class="font-medium text-white">🔁 Routines</p>
                    <p class="mt-2 text-sm text-slate-300">{{ $routineCompleted }}/{{ $routineTotal }} completed</p>
                    @forelse($routines->take(3) as $routine)
                        <p class="mt-1 text-xs {{ $routine->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">• {{ $routine->title }}</p>
                    @empty
                        <p class="mt-1 text-xs text-slate-500">No routines today</p>
                    @endforelse
                    <a href="{{ route('routines.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">See more →</a>
                </div>

                <!-- 🗓️ Appointments -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                    <p class="font-medium text-white">🗓️ Appointments</p>
                    @forelse($appointments->take(2) as $a)
                        <p class="mt-2 text-sm text-slate-300">{{ \Carbon\Carbon::parse($a->time)->format('H:i') }} — {{ $a->title }}</p>
                    @empty
                        <p class="mt-2 text-sm text-slate-400">No appointments today</p>
                    @endforelse
                    <a href="{{ route('appointments.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">See more →</a>
                </div>

                <!-- 📊 Progress link -->
                <div class="rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-4">
                    <p class="font-medium text-white">📊 Progress</p>
                    <p class="mt-2 text-sm text-indigo-100">View your discipline score, streak, level, focus time, charts and rewards.</p>
                    <a href="{{ route('progress.index') }}" class="mt-3 inline-block text-xs font-semibold text-indigo-300 hover:text-indigo-200">Open progress →</a>
                </div>
            </div>
        </section>

        <!-- Today's momentum (Eisenhower Matrix) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Today's momentum</p>
                    <p class="mt-1 text-lg font-medium text-white">The next moves that matter most</p>
                </div>
            </div>
            <div class="mt-5 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4">
                    <p class="text-sm font-semibold text-rose-200">⏱️ Do now</p>
                    <p class="text-xs text-rose-300/70">Important & urgent</p>
                    @foreach($doNow as $task)
                        <p class="mt-2 text-sm text-rose-100">• {{ $task->title }}</p>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4">
                    <p class="text-sm font-semibold text-amber-200">📅 Schedule</p>
                    <p class="text-xs text-amber-300/70">Important, not urgent</p>
                    @foreach($schedule as $task)
                        <p class="mt-2 text-sm text-amber-100">• {{ $task->title }}</p>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-sky-400/20 bg-sky-500/10 p-4">
                    <p class="text-sm font-semibold text-sky-200">🤝 Delegate</p>
                    <p class="text-xs text-sky-300/70">Urgent, not important</p>
                    @foreach($delegate as $task)
                        <p class="mt-2 text-sm text-sky-100">• {{ $task->title }}</p>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-slate-400/20 bg-slate-800/70 p-4">
                    <p class="text-sm font-semibold text-slate-200">🗑️ Eliminate</p>
                    <p class="text-xs text-slate-400">Not important, not urgent</p>
                    @foreach($eliminate as $task)
                        <p class="mt-2 text-sm text-slate-100">• {{ $task->title }}</p>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>