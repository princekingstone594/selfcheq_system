<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-6">

        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Today's Focus</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">{{ $day->format('l, d M Y') }}</h1>
                <p class="mt-2 text-slate-300">Everything you need to know for this day — at a glance.</p>

                <!-- Date navigation -->
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('focus.today', ['date' => $day->copy()->subDay()->toDateString()]) }}"
                       class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                        ← Prev
                    </a>
                    <a href="{{ route('focus.today') }}"
                       class="rounded-2xl border border-indigo-400/30 bg-indigo-500/10 px-3 py-1.5 text-sm text-indigo-200 hover:bg-indigo-500/20 transition">
                        Today
                    </a>
                    <a href="{{ route('focus.today', ['date' => $day->copy()->addDay()->toDateString()]) }}"
                       class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                        Next →
                    </a>
                </div>

                <!-- Summary stats -->
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-bold text-white">{{ $totalItems }}</p>
                        <p class="mt-1 text-xs text-slate-400">Total items</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-bold text-emerald-300">{{ $completedItems }}</p>
                        <p class="mt-1 text-xs text-slate-400">Completed</p>
                    </div>
                    <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-bold text-amber-300">{{ $totalItems - $completedItems }}</p>
                        <p class="mt-1 text-xs text-slate-400">Remaining</p>
                    </div>
                    <div class="rounded-2xl border border-sky-400/20 bg-sky-500/10 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-bold text-sky-300">{{ $focusMinutes }}m</p>
                        <p class="mt-1 text-xs text-slate-400">Focus time</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ✅ Tasks -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">✅ Tasks</p>
                <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-300">
                    {{ $tasks->where('is_completed', true)->count() }}/{{ $tasks->count() }}
                </span>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($tasks as $task)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">{{ $task->is_completed ? '✅' : '⬜' }}</span>
                            <div>
                                <p class="text-sm {{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">{{ $task->title }}</p>
                                @if($task->reminder_time)
                                    <p class="mt-0.5 text-xs text-amber-300">⏰ {{ $task->formatted_alarm_time }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="text-xs text-indigo-300 hover:text-indigo-200 transition">Manage →</a>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                        No tasks for this day.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 🔁 Routines -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-purple-300">🔁 Routines</p>
                <span class="rounded-full bg-purple-500/20 px-2.5 py-0.5 text-xs font-bold text-purple-300">
                    {{ $routines->where('is_completed', true)->count() }}/{{ $routines->count() }}
                </span>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($routines as $routine)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">{{ $routine->is_completed ? '✅' : '⬜' }}</span>
                            <div>
                                <p class="text-sm {{ $routine->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">{{ $routine->title }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $routine->frequency_label }}
                                    @if($routine->reminder_time)
                                        · <span class="text-amber-300">⏰ {{ $routine->formatted_alarm_time }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('routines.index') }}" class="text-xs text-indigo-300 hover:text-indigo-200 transition">Manage →</a>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                        No routines for this day.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 🗓️ Appointments -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-300">🗓️ Appointments</p>
                <span class="rounded-full bg-sky-500/20 px-2.5 py-0.5 text-xs font-bold text-sky-300">
                    {{ $appointments->where('is_completed', true)->count() }}/{{ $appointments->count() }}
                </span>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($appointments as $appointment)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                        <div class="flex items-center gap-3">
                            <span class="rounded-xl bg-sky-500/10 px-2.5 py-1 text-xs font-medium text-sky-300">
                                {{ $appointment->formatted_time }}
                            </span>
                            <div>
                                <p class="text-sm {{ $appointment->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">{{ $appointment->title }}</p>
                                @if($appointment->notes)
                                    <p class="mt-0.5 text-xs text-slate-500">{{ Str::limit($appointment->notes, 60) }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('appointments.index') }}" class="text-xs text-indigo-300 hover:text-indigo-200 transition">Manage →</a>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                        No appointments for this day.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 💰 Financials -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">💰 Financials</p>
                <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-300">
                    {{ $financials->where('is_completed', true)->count() }}/{{ $financials->count() }}
                </span>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($financials as $financial)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">{{ $financial->is_completed ? '✅' : '⬜' }}</span>
                            <div>
                                <p class="text-sm {{ $financial->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">
                                    {{ $financial->title }}
                                </p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ ucfirst($financial->type) }}
                                    @if($financial->amount)
                                        · Ksh {{ number_format($financial->amount, 2) }}
                                    @endif
                                    @if($financial->frequency)
                                        · {{ ucfirst($financial->frequency) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('financials.index') }}" class="text-xs text-indigo-300 hover:text-indigo-200 transition">Manage →</a>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                        No financial items due for this day.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 📝 Journal -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">📝 Journal</p>

            <div class="mt-4">
                @if($journal)
                    <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                        @if($journal->mood)
                            <p class="text-sm text-slate-300">
                                Mood:
                                @switch($journal->mood)
                                    @case(1) 😞 @break
                                    @case(2) 😐 @break
                                    @case(3) 🙂 @break
                                    @case(4) 😄 @break
                                    @case(5) 🔥 @break
                                @endswitch
                            </p>
                        @endif
                        @if($journal->content)
                            <p class="mt-2 text-sm text-slate-300 line-clamp-3">{{ $journal->content }}</p>
                        @endif
                        @if($journal->gratitude)
                            <p class="mt-2 text-xs text-slate-500">🙏 {{ $journal->gratitude }}</p>
                        @endif
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                        No journal entry for this day.
                    </div>
                @endif
                <a href="{{ route('journal.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                    Open journal <span>→</span>
                </a>
            </div>
        </section>

        <!-- 🧠 Focus -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">🧠 Focus</p>
            <div class="mt-4 flex items-center justify-between rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $focusMinutes }} minutes</p>
                    <p class="mt-1 text-xs text-slate-500">Total focus time for this day</p>
                </div>
                <a href="{{ route('focus.index') }}" class="rounded-2xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Start Session
                </a>
            </div>
        </section>
    </div>
</x-app-layout>