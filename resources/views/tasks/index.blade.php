<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Tasks</p>
                    <h1 class="text-2xl font-semibold text-white">Today's Tasks</h1>
                    <p class="text-sm text-slate-400">{{ $today->format('l, d M Y') }}</p>
                </div>

                <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4 text-sm text-amber-100">
                    <p class="font-medium">🔥 Streak</p>
                    <p class="mt-1 text-xl font-semibold">{{ auth()->user()->streak }} days</p>
                    @if(auth()->user()->streak > 0)
                        <p class="mt-1 text-xs text-amber-200/80">Keep going. Don't break the chain.</p>
                    @endif
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
                </div>

                <button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Add Task
                </button>
            </form>
        </section>

        <!-- Task List -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Your tasks</p>

            <div class="mt-4 space-y-2">
                @forelse ($tasks->sortBy('is_completed') as $task)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3 transition hover:bg-slate-800">

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

                        <span class="flex-1 text-sm {{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">
                            {{ $task->title }}
                        </span>

                        @php
                            if ($task->is_important && $task->is_urgent) {
                                $label = 'Do now';
                                $badgeClass = 'bg-rose-500/20 text-rose-300 border-rose-400/30';
                            } elseif ($task->is_important && !$task->is_urgent) {
                                $label = 'Schedule';
                                $badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-400/30';
                            } elseif (!$task->is_important && $task->is_urgent) {
                                $label = 'Delegate';
                                $badgeClass = 'bg-sky-500/20 text-sky-300 border-sky-400/30';
                            } else {
                                $label = 'Eliminate';
                                $badgeClass = 'bg-slate-600/40 text-slate-300 border-slate-500/30';
                            }
                        @endphp
                        <span class="rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $badgeClass }}">{{ $label }}</span>

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
    </div>
</x-app-layout>