<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6" x-data="{ showForm: false }">
        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Goals</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">Set your discipline milestones</h1>
                    <p class="mt-2 text-sm text-slate-400">Create multi-week goals with target discipline scores and track your progress.</p>
                </div>
                <button @click="showForm = !showForm"
                        class="shrink-0 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    {{ showForm ? 'Close' : '+ New Goal' }}
                </button>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Today's Score -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-400">Today's Discipline Score</p>
                    <p class="mt-1 text-3xl font-bold text-white">{{ $currentScore }}/100</p>
                </div>
                <div class="h-16 w-16 rounded-full border-4 {{ $currentScore >= 80 ? 'border-emerald-500' : ($currentScore >= 50 ? 'border-amber-500' : 'border-rose-500') }} flex items-center justify-center">
                    <span class="text-lg font-bold text-white">{{ $currentScore }}</span>
                </div>
            </div>
        </section>

        <!-- New Goal Form -->
        <section x-show="showForm" x-cloak x-transition class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Create Goal</p>
            <form method="POST" action="{{ route('goals.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-400">Goal Title</label>
                    <input type="text" name="title" required placeholder="e.g. Reach 80 discipline score"
                           class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400">Description (optional)</label>
                    <textarea name="description" rows="2" placeholder="What does this goal mean to you?"
                              class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-400">Target Score</label>
                        <input type="number" name="target_score" min="1" max="100" value="80" required
                               class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400">Start Date</label>
                        <input type="date" name="start_date" value="{{ now()->toDateString() }}" required
                               class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400">End Date</label>
                        <input type="date" name="end_date" value="{{ now()->addWeeks(2)->toDateString() }}" required
                               class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Create Goal
                </button>
            </form>
        </section>

        <!-- Goals List -->
        <section class="space-y-4">
            @forelse($goals as $goal)
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-semibold text-white">{{ $goal->title }}</h3>
                                @if($goal->is_completed)
                                    <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-medium text-emerald-300">✓ Completed</span>
                                @elseif($goal->is_on_track)
                                    <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-medium text-emerald-300">On Track</span>
                                @else
                                    <span class="rounded-full bg-amber-500/20 px-2.5 py-0.5 text-xs font-medium text-amber-300">Behind</span>
                                @endif
                            </div>
                            @if($goal->description)
                                <p class="mt-1 text-sm text-slate-400">{{ $goal->description }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-500">
                                {{ $goal->start_date->format('M j') }} → {{ $goal->end_date->format('M j, Y') }}
                                · {{ $goal->days_remaining }} days left
                            </p>
                        </div>
                        <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-xl p-2 text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Delete goal">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Progress: {{ $goal->current_score }}/{{ $goal->target_score }}</span>
                            <span>{{ $goal->progress }}%</span>
                        </div>
                        <div class="mt-1 h-2.5 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full {{ $goal->is_completed ? 'bg-emerald-500' : ($goal->is_on_track ? 'bg-indigo-500' : 'bg-amber-500') }} transition-all"
                                 style="width: {{ $goal->progress }}%"></div>
                        </div>
                    </div>

                    <!-- Update score -->
                    @if(!$goal->is_completed)
                        <form method="POST" action="{{ route('goals.update-score', $goal) }}" class="mt-4 flex items-end gap-2">
                            @csrf
                            @method('PATCH')
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-slate-400">Current Score</label>
                                <input type="number" name="current_score" min="0" max="100" value="{{ $goal->current_score }}"
                                       class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <button type="submit" class="rounded-2xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                                Update
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-white/10 bg-slate-900/50 p-8 text-center">
                    <p class="text-3xl">🎯</p>
                    <p class="mt-2 text-sm text-slate-400">No goals yet. Create your first discipline goal to start tracking!</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app-layout>