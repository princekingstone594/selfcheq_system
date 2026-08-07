<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6" x-data="{ historyOpen: false }">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Tasks</p>
                    <h1 class="text-2xl font-semibold text-white">Today's Tasks</h1>
                    <p class="text-sm text-slate-400">{{ $today->format('l, d M Y') }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:items-end">
                    <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4 text-sm text-amber-100">
                        <p class="font-medium">🔥 Streak</p>
                        <p class="mt-1 text-xl font-semibold">{{ auth()->user()->streak }} days</p>
                        @if(auth()->user()->streak > 0)
                            <p class="mt-1 text-xs text-amber-200/80">Keep going. Don't break the chain.</p>
                        @endif
                    </div>

                    <!-- History button -->
                    <button @click="historyOpen = !historyOpen" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-slate-800/80 px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                        <span class="rounded-full bg-indigo-500/20 px-2 py-0.5 text-[10px] text-indigo-300">{{ collect($history)->flatten()->count() }}</span>
                    </button>
                </div>
            </div>

            <!-- Progress -->
            <div class="mt-5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">Progress</span>
                    <span class="font-medium text-white">{{ $completed }} / {{ $total }} ({{ $progress }}%)</span>
                </div>
                <div class="mt-2 w-full bg-slate-800 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-3 rounded-full transition-all duration-500"
                         style="width: {{ $progress }}%"></div>
                </div>
            </div>

            @if($total > 0 && $completed === $total)
                <div class="mt-4 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                    🎉 You completed all your tasks today. Stay disciplined. You can do this.
                </div>
            @endif
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add Task -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Add a task</p>

            <form method="POST" action="{{ route('tasks.store') }}" class="mt-4 space-y-4">
                @csrf
                <div class="flex flex-col gap-3 sm:flex-row">
                    <input type="text" name="title" placeholder="New task..."
                        class="flex-1 rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    <input type="date" name="due_date"
                        class="rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    <input type="time" name="reminder_time"
                        class="rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="is_important" class="rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                        Important
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="is_urgent" class="rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                        Urgent
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="alarm_enabled" class="rounded border-slate-700 bg-slate-800 text-amber-500 focus:ring-amber-500">
                        ⏰ Set alarm
                    </label>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <input type="time" name="alarm_time" placeholder="Alarm time"
                        class="rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        placeholder="Alarm time">
                    <button type="submit" class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                        Add Task
                    </button>
                </div>
            </form>
        </section>

        <!-- Task List -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Your tasks</p>

            <div class="mt-4 space-y-2">
                @forelse ($tasks->sortBy('is_completed') as $task)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3 transition hover:bg-slate-800">

                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                                @csrf
                                @method('PATCH')
                                <button class="text-xl transition hover:scale-110" title="{{ $task->is_completed ? 'Mark incomplete' : 'Mark complete' }}">
                                    @if ($task->is_completed)
                                        ✅
                                    @else
                                        ⬜
                                    @endif
                                </button>
                            </form>

                            <div class="flex-1 min-w-0">
                                <span class="block text-sm {{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">
                                    {{ $task->title }}
                                </span>
                                @if($task->alarm_enabled)
                                    <p class="mt-0.5 flex items-center gap-1 text-xs text-amber-300">
                                        <span>⏰</span>
                                        Alarm at {{ $task->formatted_alarm_time }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Alarm toggle button -->
                        <form method="POST" action="{{ route('tasks.alarmToggle', $task) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-xl px-2.5 py-1 text-xs font-medium transition"
                                    title="{{ $task->alarm_enabled ? 'Disable alarm' : 'Enable alarm' }}"
                                    onclick="event.preventDefault(); if(confirm('{{ $task->alarm_enabled ? 'Disable alarm for this task?' : 'Enable alarm for this task?' }}')) { this.closest('form').submit(); }">
                                @if($task->alarm_enabled)
                                    <span class="text-amber-400">🔔</span>
                                @else
                                    <span class="text-slate-500">🔕</span>
                                @endif
                            </button>
                        </form>

                        @php
                            if ($task->is_important && $task->is_urgent) {
                                $label = 'Do now';
                                $badgeClass = 'bg-rose-500/20 text-rose-300 border-rose-400/30';
                            } elseif ($task->is_important && !$task->is_urgent) {
                                $label = 'Scheduled';
                                $badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-400/30';
                            } elseif (!$task->is_important && $task->is_urgent) {
                                $label = 'Delegated';
                                $badgeClass = 'bg-sky-500/20 text-sky-300 border-sky-400/30';
                            } else {
                                $label = 'Eliminate';
                                $badgeClass = 'bg-slate-600/40 text-slate-300 border-slate-500/30';
                            }
                        @endphp
                        <span class="rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $badgeClass }}" title="Task classification">{{ $label }}</span>

                        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-400 hover:text-rose-300 transition" title="Delete task">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-8 text-center text-sm text-slate-500">
                        No tasks yet. Add your first one above.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- History -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <button @click="historyOpen = !historyOpen" class="flex w-full items-center justify-between text-left">
                <span class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">📜 History</span>
                <svg x-show="!historyOpen" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <svg x-show="historyOpen" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </button>

            <div x-show="historyOpen" x-transition class="mt-4 space-y-4" style="display:none;">
                @forelse($history as $date => $dayTasks)
                    <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                        <p class="text-sm font-medium text-indigo-300">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</p>
                        <div class="mt-2 space-y-1.5">
                            @foreach($dayTasks as $task)
                                <div class="flex items-center gap-2 text-sm">
                                    <span>{{ $task->is_completed ? '✅' : '⬜' }}</span>
                                    <span class="{{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">{{ $task->title }}</span>
                                    @if($task->alarm_enabled)
                                        <span class="text-xs text-amber-400" title="Alarm on">⏰</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No past tasks yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
