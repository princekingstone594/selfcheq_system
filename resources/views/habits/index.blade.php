<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Habit Tracker</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Daily habits that build discipline</h1>
            <p class="mt-2 text-sm text-slate-400">Track your daily habits and earn XP for each completion. Build streaks and unlock badges!</p>
        </section>

        <!-- Add Habit Form -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Add a new habit</p>

            <form method="POST" action="{{ route('habits.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <input type="text" name="title" required placeholder="What habit do you want to build? e.g. Drink 8 glasses of water"
                           class="w-full rounded-xl border border-white/10 bg-slate-800/50 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div class="flex gap-2">
                    <input type="text" name="emoji" placeholder="😴" maxlength="3"
                           class="w-16 rounded-xl border border-white/10 bg-slate-800/50 px-3 py-3 text-center text-xl text-white placeholder-slate-500 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <input type="text" name="unit" placeholder="unit (e.g. times, glasses, pages)"
                           class="flex-1 rounded-xl border border-white/10 bg-slate-800/50 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div>
                    <input type="number" name="target_value" placeholder="target (default: 1)" min="1"
                           class="w-full rounded-xl border border-white/10 bg-slate-800/50 px-4 py-3 text-white placeholder-slate-500 focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div class="sm:col-span-2">
                    <select name="frequency"
                            class="w-full rounded-xl border border-white/10 bg-slate-800/50 px-4 py-3 text-white focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="daily">Every day</option>
                        <option value="weekday">Weekdays only</option>
                        <option value="weekend">Weekends only</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <input type="time" name="reminder_time"
                           class="w-full rounded-xl border border-white/10 bg-slate-800/50 px-4 py-3 text-white focus:border-indigo-500/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div class="sm:col-span-2">
                    <button type="submit"
                            class="w-full rounded-xl bg-indigo-600/90 px-6 py-3 font-semibold text-white transition hover:bg-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20">
                        Create habit ✨
                    </button>
                </div>
            </form>
        </section>

        <!-- Today's Habits -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Today's Habits</p>

            @forelse($habits as $habit)
                @php
                    $completed = $habit->isCompletedToday($todayDate);
                    $streak = $habit->currentStreak();
                @endphp
                <div class="mt-4 flex items-center gap-3 rounded-xl border border-white/10 bg-slate-800/50 p-4">
                    <span class="text-2xl">{{ $habit->emoji ?? '✅' }}</span>
                    <div class="flex-1">
                        <p class="font-medium text-white">{{ $habit->title }}</p>
                        <p class="text-xs text-slate-400">Target: {{ $habit->target_value }} {{ $habit->unit }} · Streak: {{ $streak }} 🔥</p>
                    </div>
                    <form method="POST" action="{{ route('habits.toggle', $habit) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="{{ $completed
                                    ? 'rounded-xl bg-emerald-500/20 px-4 py-2 text-emerald-300'
                                    : 'rounded-xl bg-slate-700/50 px-4 py-2 text-slate-300 hover:bg-slate-700' }}">
                            {{ $completed ? '✅ Completed' : '○ Mark done' }}
                        </button>
                    </form>
                </div>
            @empty
                <p class="mt-4 text-sm text-slate-400">No habits yet — add one above to get started!</p>
            @endforelse
        </section>

        <!-- Weekly Completion Chart -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Weekly completion rate</p>

            @php
                $maxVal = max($chartData) ?: 100;
            @endphp

            <div class="mt-4 flex items-end justify-between gap-1 h-32">
                @foreach($chartLabels as $index => $label)
                    <div class="flex flex-col items-center flex-1">
                        <div class="relative w-full">
                            <div class="h-full w-full rounded-t-sm bg-gradient-to-t from-indigo-600/80 to-indigo-500"
                                 style="height: {{ ($chartData[$index] / $maxVal) * 100 }}%">
                            </div>
                        </div>
                        <span class="mt-1 text-[10px] text-slate-400">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-slate-400">{{ $habits->avg(fn($h) => $h->completionRate(7)) ?? 0 }}% avg completion this week</p>
        </section>
    </div>
</x-app-layout>
