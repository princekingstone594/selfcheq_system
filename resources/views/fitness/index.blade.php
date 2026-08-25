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
    </div>
</x-app-layout>

