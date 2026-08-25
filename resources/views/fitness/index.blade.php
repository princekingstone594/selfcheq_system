<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6" x-data="{ setupOpen: !plan }">
        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-emerald-900/30 via-slate-900 to-teal-900/20 p-6 shadow-2xl shadow-emerald-950/30 backdrop-blur sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-56 w-56 rounded-full bg-emerald-500/15 blur-3xl"></div>
            </div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Fitness</p>
                    <h1 class="mt-1 text-2xl font-bold text-white sm:text-3xl">Train. Eat. Repeat. 🏋️</h1>
                    <p class="mt-2 text-sm text-slate-300">Your AI-personalized weekly workout plan and nutrition guidance.</p>
                    @if($plan)
                        <span class="mt-2 inline-flex items-center gap-1 rounded-full border border-emerald-400/30 bg-emerald-500/10 px-3 py-1 text-xs text-emerald-200">
                            {{ ucfirst(str_replace('_',' ',$plan->goal)) }} · {{ ucfirst($plan->level) }} · Week of {{ $plan->week_start->format('M j') }}
                        </span>
                    @endif
                </div>
                <button @click="setupOpen = !setupOpen"
                        class="shrink-0 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-2.5 text-sm font-semibold text-emerald-100 hover:bg-emerald-500/20 transition">
                    <span x-text="setupOpen ? 'Close' : ($plan ? 'Regenerate' : 'Get My Plan')"></span>
                </button>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Goal / level form -->
        <section x-show="setupOpen" x-cloak x-transition class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Build my plan</p>
            <form method="POST" action="{{ route('fitness.generate') }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                @csrf
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Goal</label>
                    <select name="goal" required
                            class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="">Choose…</option>
                        <option value="lose_weight" {{ ($plan?->goal === 'lose_weight') ? 'selected' : '' }}>Lose weight</option>
                        <option value="build_muscle" {{ ($plan?->goal === 'build_muscle') ? 'selected' : '' }}>Build muscle</option>
                        <option value="endurance" {{ ($plan?->goal === 'endurance') ? 'selected' : '' }}>Improve endurance</option>
                        <option value="general" {{ ($plan?->goal ?? 'general') === 'general' ? 'selected' : '' }}>General fitness</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Level</label>
                    <select name="level" required
                            class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="">Choose…</option>
                        @foreach(['beginner','intermediate','advanced'] as $lvl)
                            <option value="{{ $lvl }}" {{ ($plan?->level === $lvl) ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full rounded-2xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                        ⚡ Generate Weekly Plan
                    </button>
                </div>
            </form>
        </section>

        @if(!$plan)
            <section class="rounded-3xl border border-dashed border-white/10 bg-slate-900/40 p-10 text-center">
                <p class="text-4xl">💪</p>
                <p class="mt-3 text-sm text-slate-400">No plan yet — pick your goal and level above, and Coach Zoe will build your week.</p>
            </section>
        @endif

        @if($plan)
        <!-- Progress -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            @php $done = count($plan->completed_days ?? []); @endphp
            <div class="flex items-center justify-between text-sm">
                <p class="font-semibold text-white">This week: <span class="text-emerald-300">{{ $done }}/7 days done</span></p>
                <p class="text-slate-400">{{ $done >= 7 ? 'Perfect week! 🏆' : 'Keep going!' }}</p>
            </div>
            <div class="mt-2 h-3 w-full overflow-hidden rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-400 transition-all duration-700"
                     style="width: {{ round($done / 7 * 100) }}%"></div>
            </div>
        </section>
        @endif

        @if($plan)
        <!-- Weekly plan -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($plan->plan['days'] ?? [] as $i => $day)
                @php
                    $isDone = in_array($i, $plan->completed_days ?? []);
                    $isToday = $i === $todayIndex;
                    $isRest = stripos($day['focus'], 'rest') !== false || stripos($day['focus'], 'recovery') !== false;
                @endphp
                <div class="rounded-3xl border p-5 transition {{ $isDone ? 'border-emerald-400/40 bg-emerald-500/5' : ($isToday ? 'border-indigo-400/40 bg-slate-900/80' : 'border-white/10 bg-slate-900/70') }} shadow-xl">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider {{ $isToday ? 'text-indigo-300' : 'text-slate-400' }}">
                                {{ ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][$i] }}{{ $isToday ? ' · Today' : '' }}
                            </p>
                            <p class="mt-0.5 font-bold text-white">{{ $day['focus'] }}</p>
                        </div>
                        <form method="POST" action="{{ route('fitness.toggle-day') }}">
                            @csrf
                            <input type="hidden" name="day" value="{{ $i }}">
                            <button type="submit" title="{{ $isDone ? 'Mark as not done' : 'Mark day complete' }}"
                                    class="flex h-9 w-9 items-center justify-center rounded-full border text-sm font-bold transition {{ $isDone ? 'border-emerald-400 bg-emerald-500 text-white' : 'border-white/20 bg-slate-800 text-slate-400 hover:border-emerald-400/50 hover:text-emerald-300' }}">
                                ✓
                            </button>
                        </form>
                    </div>

                    <ul class="mt-3 space-y-1.5">
                        @foreach($day['workout'] as $exercise)
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <span class="mt-0.5 text-xs {{ $isRest ? 'text-teal-300' : 'text-emerald-300' }}">{{ $isRest ? '🌙' : '▸' }}</span>
                                {{ $exercise }}
                            </li>
                        @endforeach
                    </ul>

                    @if(!empty($day['tip']))
                        <p class="mt-3 rounded-xl bg-white/5 px-3 py-2 text-xs italic text-slate-400">💡 {{ $day['tip'] }}</p>
                    @endif
                </div>
            @endforeach
        </section>

        <!-- Diet guidance -->
        <section class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-teal-950/10 to-slate-900 p-6 shadow-xl">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🥗</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Proposed diet</p>
                    <p class="text-sm text-slate-400">Nutrition guidelines matched to your goal</p>
                </div>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach($plan->plan['diet'] ?? [] as $tip)
                    <div class="flex items-start gap-2 rounded-2xl border border-white/5 bg-slate-800/40 p-3.5 text-sm text-slate-300">
                        <span class="mt-0.5">🍽️</span>{{ $tip }}
                    </div>
                @endforeach
            </div>
            @if(!empty($plan->plan['summary']))
                <p class="mt-4 rounded-xl bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">🧑‍⚕️ {{ $plan->plan['summary'] }}</p>
            @endif
            <p class="mt-3 text-xs text-slate-500">
                Plan source: {{ ($plan->plan['source'] ?? '') === 'ai' ? 'AI-generated ✨' : 'built-in preset' }} —
                always consult a professional before major dietary changes.
            </p>
        </section>
        @endif

        <!-- 📋 My Fitness Entries -->
        <section x-data="{ addOpen: false }" class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📋</span>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">My fitness entries</p>
                        <p class="text-sm text-slate-400">Your own nutrition, workout & gym plans — link them to tasks or routines</p>
                    </div>
                </div>
                <button @click="addOpen = !addOpen"
                        class="shrink-0 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                    <span x-text="addOpen ? 'Close' : '+ Add Entry'">+ Add Entry</span>
                </button>
            </div>

            <!-- Add form -->
            <form x-show="addOpen" x-cloak x-transition method="POST" action="{{ route('fitness.entries.store') }}"
                  class="mt-5 space-y-4 rounded-2xl border border-white/10 bg-slate-800/50 p-5">
                @csrf
                <!-- Type selector -->
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['nutrition' => '🥗 Nutrition', 'workout' => '💪 Workout', 'gym' => '🏋️ Gym'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="{{ $value }}" class="peer sr-only" {{ $value === 'nutrition' ? 'checked' : '' }}>
                            <span class="block rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-center text-xs font-semibold text-slate-400 transition peer-checked:border-emerald-400 peer-checked:bg-emerald-500/15 peer-checked:text-emerald-200">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Title *</label>
                        <input type="text" name="title" required maxlength="255" placeholder="e.g. High-protein cutting day / Push Day A / Leg Day at gym"
                               class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Details</label>
                        <textarea name="details" rows="3" placeholder="Meals & macros, exercise list with sets/reps, gym schedule…"
                                  class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Day</label>
                        <select name="day_of_week"
                                class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">Any day</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $i => $d)
                                <option value="{{ $i + 1 }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Link to Task (optional)</label>
                        <select name="linked_task_id"
                                class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">None</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}">{{ \Illuminate\Support\Str::limit($task->title, 45) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-400">Link to Routine (optional)</label>
                        <select name="linked_routine_id"
                                class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">None</option>
                            @foreach($routines as $routine)
                                <option value="{{ $routine->id }}">{{ \Illuminate\Support\Str::limit($routine->title, 45) }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-slate-500">Linking keeps this entry connected to your existing plans.</p>
                    </div>
                </div>

                <button type="submit"
                        class="w-full rounded-2xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-400 transition">
                    Save Entry
                </button>
            </form>

            <!-- Entries grouped by type -->
            @php
                $entryMeta = [
                    'nutrition' => ['🥗', 'Nutrition Plans', 'emerald'],
                    'workout'   => ['💪', 'Workout Plans', 'indigo'],
                    'gym'       => ['🏋️', 'Gym Plans', 'amber'],
                ];
            @endphp
            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                @foreach($entryMeta as $type => [$icon, $label, $color])
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $icon }} {{ $label }} ({{ $entries->where('type', $type)->count() }})</p>
                        <div class="space-y-3">
                            @forelse($entries->where('type', $type) as $entry)
                                <div class="rounded-2xl border p-4 transition {{ $entry->is_done ? 'border-emerald-400/30 bg-emerald-500/5' : 'border-white/10 bg-slate-800/50' }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-sm font-semibold {{ $entry->is_done ? 'text-slate-400 line-through' : 'text-white' }}">{{ $entry->title }}</p>
                                        <form method="POST" action="{{ route('fitness.entries.destroy', $entry) }}"
                                              onsubmit="return confirm('Delete this entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete entry"
                                                    class="rounded-lg p-1 text-slate-500 transition hover:bg-rose-500/10 hover:text-rose-400">✕</button>
                                        </form>
                                    </div>

                                    <span class="mt-1 inline-block rounded-full bg-white/5 px-2 py-0.5 text-[11px] text-slate-400">
                                        📅 {{ \App\Models\FitnessEntry::dayName($entry->day_of_week) }}
                                    </span>

                                    @if($entry->details)
                                        <p class="mt-2 whitespace-pre-line text-xs leading-relaxed text-slate-300">{{ $entry->details }}</p>
                                    @endif

                                    {{-- Links to task/routine --}}
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @if($entry->linkedTask)
                                            <a href="{{ route('tasks.index') }}"
                                               class="inline-flex items-center gap-1 rounded-full bg-indigo-500/15 px-2.5 py-1 text-[11px] font-medium text-indigo-200 transition hover:bg-indigo-500/25">
                                                ✅ Task: {{ \Illuminate\Support\Str::limit($entry->linkedTask->title, 28) }}
                                            </a>
                                        @endif
                                        @if($entry->linkedRoutine)
                                            <a href="{{ route('routines.index') }}"
                                               class="inline-flex items-center gap-1 rounded-full bg-purple-500/15 px-2.5 py-1 text-[11px] font-medium text-purple-200 transition hover:bg-purple-500/25">
                                                🔁 Routine: {{ \Illuminate\Support\Str::limit($entry->linkedRoutine->title, 28) }}
                                            </a>
                                        @endif
                                    </div>

                                    @if(!$entry->linked_task_id || !$entry->linked_routine_id)
                                        <form method="POST" action="{{ route('fitness.entries.toggle', $entry) }}" class="mt-3">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full rounded-xl px-3 py-2 text-xs font-semibold transition {{ $entry->is_done ? 'bg-slate-700 text-slate-300 hover:bg-slate-600' : ($color === 'emerald' ? 'bg-emerald-500/20 text-emerald-200 hover:bg-emerald-500/30' : ($color === 'amber' ? 'bg-amber-500/20 text-amber-200 hover:bg-amber-500/30' : 'bg-indigo-500/20 text-indigo-200 hover:bg-indigo-500/30')) }}">
                                                {{ $entry->is_done ? '↺ Mark as not done' : '✓ Mark done' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="rounded-xl border border-dashed border-white/10 py-6 text-center text-xs text-slate-500">Nothing here yet</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>

