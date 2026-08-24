<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6" x-data="morningFlow()">

        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Spirit</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Command Your Morning</h1>
                <p class="mt-2 text-slate-300">A guided, step-by-step ritual to ground your day.</p>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- ============ GUIDED MORNING FLOW ============ -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <!-- Step indicator -->
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300" x-text="stepLabel"></p>
                <div class="flex gap-1.5">
                    <template x-for="n in 5" :key="n">
                        <span class="h-2.5 w-2.5 rounded-full transition"
                              :class="n <= step ? 'bg-indigo-400' : 'bg-slate-700'"></span>
                    </template>
                </div>
            </div>

            <!-- STEP 1: Wake-up check & mood -->
                <div x-show="step === 1" x-transition>
                    <h2 class="text-lg font-semibold text-white">👋 How are you starting today?</h2>
                    <p class="mt-1 text-sm text-slate-400">Check in with your morning energy before you dive in.</p>

                    <div class="mt-5 flex items-center justify-center gap-2">
                        <template x-for="m in moods" :key="m.value">
                            <button @click="mood = m.value"
                                    class="flex flex-1 flex-col items-center gap-1 rounded-2xl border px-2 py-3 text-center transition"
                                    :class="mood === m.value ? 'border-indigo-400/50 bg-indigo-500/15' : 'border-white/10 bg-slate-800/50 hover:border-white/25'">
                                <span class="text-2xl" x-text="m.emoji"></span>
                                <span class="text-[10px] text-slate-400" x-text="m.label"></span>
                            </button>
                        </template>
                    </div>

                    @if($morningDevotion)
                        <div class="mt-5 flex items-center justify-between rounded-2xl border border-indigo-400/20 bg-indigo-500/5 p-4">
                            <div>
                                <p class="text-xs text-slate-400">Your wake-up time</p>
                                <p class="text-lg font-semibold text-indigo-300">{{ \Carbon\Carbon::parse($morningDevotion->wake_up_time)->format('g:i A') }}</p>
                            </div>
                            <span class="rounded-full {{ $morningDevotion->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-400' }} px-3 py-1 text-xs font-medium">
                                {{ $morningDevotion->is_active ? 'Active' : 'Paused' }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- STEP 2: Read chapter -->
                <div x-show="step === 2" x-transition>
                    <h2 class="text-lg font-semibold text-white">📖 Choose today's declaration chapter</h2>
                    <p class="mt-1 text-sm text-slate-400">Tap one to read it aloud. It'll be your anchor for today.</p>

                    <div class="mt-5 grid gap-2 sm:grid-cols-2">
                        <template x-for="ch in chapters" :key="ch">
                            <a :href="'{{ route('bible-chapter.show', 'PLACEHOLDER') }}'.replace('PLACEHOLDER', encodeURIComponent(ch))"
                               @click.prevent="selectChapter(ch)"
                               class="rounded-2xl border px-4 py-3 text-sm font-medium transition"
                               :class="selectedChapter === ch ? 'border-indigo-400/50 bg-indigo-500/15 text-white' : 'border-white/10 bg-slate-800/50 text-slate-300 hover:border-white/25'">
                                <span class="mr-2">📖</span><span x-text="ch"></span>
                            </a>
                        </template>
                    </div>

                    <div x-show="selectedChapter && !personalizing && !personalized" class="mt-5 rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                        <p class="text-sm italic text-slate-300"><span x-text="selectedChapter"></span></p>
                        <p class="mt-2 text-xs text-slate-500">Read it slowly. Let the words land before you move on.</p>
                        <a :href="'{{ route('bible-chapter.show', 'PLACEHOLDER') }}'.replace('PLACEHOLDER', encodeURIComponent(selectedChapter))"
                           target="_blank"
                           class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                            Open full chapter in reader <span>↗</span>
                        </a>
                    </div>
                </div>

                <!-- STEP 3: Personalize -->
                <div x-show="step === 3" x-transition>
                    <h2 class="text-lg font-semibold text-white">✨ Personalize your declaration</h2>
                    <p class="mt-1 text-sm text-slate-400">Transform <span x-text="selectedChapter"></span> into a first-person declaration you can speak over your day.</p>

                    <button @click="personalize()"
                            :disabled="personalizing"
                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:from-indigo-400 hover:to-purple-400 disabled:opacity-50">
                        <svg x-show="!personalizing" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <svg x-show="personalizing" class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span x-text="personalizing ? 'Personalizing...' : (personalized ? 'Re-personalize' : 'Personalize this chapter')"></span>
                    </button>

                    <p x-show="error" class="mt-3 text-sm text-rose-400" x-text="error"></p>

                    <div x-show="personalized" class="mt-5 rounded-2xl border border-emerald-400/20 bg-emerald-500/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-400">Your Personalized Declaration</p>
                        <p class="mt-2 font-serif text-base italic leading-relaxed text-emerald-200" x-text="personalized"></p>
                    </div>
                </div>

                <!-- STEP 4: Declare / Affirmations -->
                <div x-show="step === 4" x-transition>
                    <h2 class="text-lg font-semibold text-white">🙏 Declare your affirmations</h2>
                    <p class="mt-1 text-sm text-slate-400">Tap each one as you speak it. Lighting them up builds your rhythm.</p>

                    <div class="mt-5 grid gap-2 sm:grid-cols-2">
                        <template x-for="aff in affirmations" :key="aff.ref">
                            <button @click="toggleAffirmation(aff.ref)"
                                    class="rounded-2xl border p-4 text-left transition"
                                    :class="declared.includes(aff.ref) ? 'border-emerald-400/40 bg-emerald-500/10' : 'border-white/10 bg-slate-800/50 hover:border-white/25'">
                                <p class="text-xs font-semibold uppercase tracking-widest" :class="declared.includes(aff.ref) ? 'text-emerald-300' : 'text-indigo-300'" x-text="aff.ref"></p>
                                <p class="mt-1 text-xs italic leading-relaxed text-slate-400" x-text="aff.text"></p>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- STEP 5: Done -->
                <div x-show="step === 5" x-transition>
                    <div class="py-6 text-center">
                        <p class="text-5xl">🔥</p>
                        <h2 class="mt-4 text-xl font-bold text-white">You've commanded your morning.</h2>
                        <p class="mt-2 text-sm text-slate-400">That's another intentional start to your day.</p>

                        <button @click="finish()"
                                class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-400 active:scale-95">
                            ✅ I'm done — my day is set
                        </button>
                        <p x-show="finished" class="mt-4 text-sm font-semibold text-emerald-300">+1 to your discipline streak 🔥</p>
                    </div>
                </div>
            </div>

            <!-- Nav buttons -->
            <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-4">
                <button @click="step > 1 && step--"
                        class="rounded-xl px-4 py-2 text-sm text-slate-300 transition hover:bg-white/5"
                        :class="step === 1 ? 'invisible' : ''">← Back</button>
                <button @click="step < 5 && step++"
                        class="rounded-xl bg-indigo-500/15 px-5 py-2 text-sm font-semibold text-indigo-200 transition hover:bg-indigo-500/25"
                        :class="step === 5 ? 'invisible' : ''">Continue →</button>
            </div>
        </section>

        <!-- Today's Verse -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Today's Verse</p>

            @if($devotional)
                <div class="relative mt-4">
                    <span class="absolute -top-4 -left-2 text-6xl text-indigo-500/30 select-none">"</span>
                    <p class="relative text-lg italic leading-relaxed text-slate-200">
                        {{ $devotional->content }}
                    </p>
                </div>

                @if($devotional->passage)
                    <div class="mt-6 border-t border-white/10 pt-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">Reflection</p>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">
                            {{ $devotional->passage }}
                        </p>
                    </div>
                @endif
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-8 text-center">
                    <p class="text-lg italic text-slate-200">"Through desire a man, having separated himself, seeketh and intermeddleth with all wisdom."</p>
                    <p class="mt-3 text-sm font-semibold text-indigo-300">— Proverbs 18:1 (KJV)</p>
                </div>
            @endif
        </section>

        <!-- Prayer Plan -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Prayer Plan</p>
                    <p class="mt-2 text-sm text-slate-400">Create prayer points with duration and reminders to build a consistent prayer habit.</p>
                </div>
            </div>

            <div class="mt-4">
                <form method="POST" action="{{ route('devotional.prayer.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300">Prayer Point</label>
                        <input type="text" name="prayer_point" required placeholder="e.g. Wisdom for today's decisions"
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Duration (days)</label>
                        <input type="number" name="days" value="1" min="1" max="365" required
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Reminder Time (optional)</label>
                        <input type="time" name="reminder_time"
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                    </div>

                    <div class="flex items-center gap-2 sm:col-span-2">
                        <input type="checkbox" name="reminder_enabled" id="reminder_enabled" class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                        <label for="reminder_enabled" class="text-sm text-slate-300">Enable daily reminder</label>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                            Add Prayer Point
                        </button>
                    </div>
                </form>

                @if($prayerPlans->count() > 0)
                    <div class="mt-6 space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Prayer Points</p>
                        @foreach($prayerPlans as $plan)
                            <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <p class="text-sm text-white">{{ $plan->prayer_point }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                            <span class="rounded-full bg-slate-700 px-2.5 py-1">{{ $plan->days }} day{{ $plan->days > 1 ? 's' : '' }}</span>
                                            @if($plan->reminder_time && $plan->reminder_enabled)
                                                <span class="rounded-full bg-indigo-500/20 px-2.5 py-1 text-indigo-300">⏰ {{ \Carbon\Carbon::parse($plan->reminder_time)->format('g:i A') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('devotional.prayer.complete', $plan) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-2 text-emerald-300 hover:bg-emerald-500/20 transition" title="Mark as completed">✓</button>
                                        </form>
                                        <form method="POST" action="{{ route('devotional.prayer.destroy', $plan) }}" class="inline" onsubmit="return confirm('Remove this prayer point?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-rose-400/30 bg-rose-500/10 p-2 text-rose-300 hover:bg-rose-500/20 transition" title="Remove">✕</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-6 text-center">
                        <p class="text-sm text-slate-400">No active prayer points. Create one above to get started.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Morning Devotion Setup -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Morning Devotion Setup</p>
                    <p class="mt-2 text-sm text-slate-400">Set your wake-up time, alarm, and a morning declaration to carry into the day.</p>
                </div>
                @if($morningDevotion)
                    <form method="POST" action="{{ route('devotional.morning.toggle', $morningDevotion) }}" class="inline shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-xl border border-white/10 bg-slate-800 px-3 py-1.5 text-xs text-slate-300 hover:text-white transition">
                            {{ $morningDevotion->is_active ? 'Pause' : 'Activate' }}
                        </button>
                    </form>
                @endif
            </div>

            <form method="POST" action="{{ route('devotional.morning.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300">Wake Up Time</label>
                    <input type="time" name="wake_up_time" required value="{{ $morningDevotion->wake_up_time ?? '06:00' }}"
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="alarm_enabled" id="alarm_enabled" {{ ($morningDevotion->alarm_enabled ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                    <label for="alarm_enabled" class="text-sm text-slate-300">Enable daily alarm</label>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-300">Morning Declaration (optional)</label>
                    <textarea name="declaration" rows="2" placeholder="I am blessed, I am favored, I am successful..."
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ $morningDevotion->declaration ?? '' }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                        {{ $morningDevotion ? 'Update Morning Devotion' : 'Set Morning Devotion' }}
                    </button>
                </div>
            </form>
        </section>

        <!-- Fasting Plan -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Fasting Plan</p>
            <p class="mt-2 text-sm text-slate-400">Create a fasting plan with purpose and reminders to keep you accountable.</p>

            <form method="POST" action="{{ route('devotional.fasting.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-300">Purpose for Fasting</label>
                    <input type="text" name="purpose" required placeholder="e.g., Seeking God's direction, Spiritual breakthrough..."
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300">Number of Days</label>
                    <input type="number" name="days" value="1" min="1" max="365" required
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300">Reminder Time (optional)</label>
                    <input type="time" name="reminder_time"
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                </div>

                <div class="flex items-center gap-2 sm:col-span-2">
                    <input type="checkbox" name="reminder_enabled" id="fasting_reminder_enabled" class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                    <label for="fasting_reminder_enabled" class="text-sm text-slate-300">Enable daily reminders</label>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                        Create Fasting Plan
                    </button>
                </div>
            </form>

            @if($fastingPlans->count() > 0)
                <div class="mt-6 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Fasting Plans</p>
                    @foreach($fastingPlans as $plan)
                        <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <p class="text-sm text-white">{{ $plan->purpose }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-400">
                                        <span class="rounded-full bg-slate-700 px-2.5 py-1">{{ $plan->days }} day{{ $plan->days > 1 ? 's' : '' }}</span>
                                        @if($plan->reminder_time && $plan->reminder_enabled)
                                            <span class="rounded-full bg-indigo-500/20 px-2.5 py-1 text-indigo-300">⏰ {{ \Carbon\Carbon::parse($plan->reminder_time)->format('g:i A') }}</span>
                                        @endif
                                        @if($plan->started_at)
                                            <span class="rounded-full bg-emerald-500/20 px-2.5 py-1 text-emerald-300">Started {{ \Carbon\Carbon::parse($plan->started_at)->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if(!$plan->started_at)
                                        <form method="POST" action="{{ route('devotional.fasting.start', $plan) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl border border-amber-400/30 bg-amber-500/10 p-2 text-amber-300 hover:bg-amber-500/20 transition" title="Start fasting plan">▶</button>
                                        </form>
                                    @endif
                                    @if(!$plan->is_completed)
                                        <form method="POST" action="{{ route('devotional.fasting.complete', $plan) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-2 text-emerald-300 hover:bg-emerald-500/20 transition" title="Mark as completed">✓</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('devotional.fasting.destroy', $plan) }}" class="inline" onsubmit="return confirm('Remove this fasting plan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-rose-400/30 bg-rose-500/10 p-2 text-rose-300 hover:bg-rose-500/20 transition" title="Remove">✕</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-6 text-center">
                    <p class="text-sm text-slate-400">No active fasting plans yet.</p>
                </div>
            @endif
        </section>

        <!-- Study Plan -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Study Plan</p>
            <p class="mt-2 text-sm text-slate-400">Grow in knowledge with structured Bible reading and inspirational resources.</p>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="font-medium text-white">📖 Bible Reading</p>
                    <div class="mt-3 space-y-2">
                        <a href="https://www.biblegateway.com/reading-plans/one-year-bible" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• Bible in 365 Days →</a>
                        <a href="https://www.biblegateway.com/reading-plans/new-testament-90-days" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• New Testament in 3 Months →</a>
                        <a href="https://www.biblegateway.com/reading-plans/proverb-a-day" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• Proverbs in 31 Days →</a>
                        <a href="https://www.biblegateway.com/reading-plans/psalms-7-days" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• Psalms in 7 Days →</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="font-medium text-white">🎧 Resources</p>
                    <div class="mt-3 space-y-2">
                        <a href="https://www.desiringgod.org" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• Desiring God Books & Sermons →</a>
                        <a href="https://open.spotify.com/show/5m1CsPq3wBl4mORiwy8cUj" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• Gospel Podcasts →</a>
                        <a href="https://www.christianbook.com" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• Christian Book Store →</a>
                        <a href="https://www.crossway.org" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">• ESV Study Bible →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Worship & Community (consolidated) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <button type="button" class="flex w-full items-center justify-between text-left" @click="worshipOpen = !worshipOpen">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">🎵 Worship & Community</p>
                    <p class="mt-2 text-sm text-slate-400">Live devotion communities, instrumentals, soaking worship, and praise sessions.</p>
                </div>
                <svg class="h-6 w-6 shrink-0 text-indigo-300 transition-transform" :class="worshipOpen ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="worshipOpen" x-transition class="mt-4 space-y-4" style="display:none;">
                <!-- Live Devotion Communities -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">🎥 Live Devotion Communities</p>
                    <div class="mt-3 space-y-3">
                        <a href="https://www.youtube.com/results?search_query=Triump30+International+Ministries" target="_blank" class="group flex items-center gap-3 text-sm text-indigo-300 hover:text-white transition">
                            <span>Triumph30 International Ministries · by Apostle Emmanuel Iren</span>
                            <span class="text-xs text-slate-400">· Live daily 7am EAT / 6am WAT</span>
                        </a>
                        <a href="https://www.youtube.com/@PastorJerryEze" target="_blank" class="group flex items-center gap-3 text-sm text-indigo-300 hover:text-white transition">
                            <span>NSPPD by Pastor Jerry Eze</span>
                        </a>
                        <a href="https://www.youtube.com/results?search_query=Hallelujah+Challenge+Nathaniel+Bassey" target="_blank" class="group flex items-center gap-3 text-sm text-indigo-300 hover:text-white transition">
                            <span>Hallelujah Challenge - Nathaniel Bassey</span>
                        </a>
                        <a href="https://www.youtube.com/results?search_query=Upper+room+Dunsin+Oyekan" target="_blank" class="group flex items-center gap-3 text-sm text-indigo-300 hover:text-white transition">
                            <span>Upper Room - Dunsin Oyekan</span>
                        </a>
                        <a href="https://www.youtube.com/results?search_query=Qavah+Abbey+Ojomu" target="_blank" class="group flex items-center gap-3 text-sm text-indigo-300 hover:text-white transition">
                            <span>Qavah - Abbey Ojomu</span>
                        </a>
                    </div>
                </div>

                <!-- Instrumentals -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">🎹 Instrumentals</p>
                    <div class="mt-3 space-y-2">
                        <a href="https://www.youtube.com/watch?v=5sQkG6NJv6g" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Soaking Worship Instrumentals</a>
                        <a href="https://www.youtube.com/watch?v=3Bs5wJ7TnLQ" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Peaceful Piano Worship</a>
                        <a href="https://www.youtube.com/watch?v=8gVfJ6Vo2yU" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Meditative Piano for Devotion</a>
                    </div>
                </div>

                <!-- Soaking Worship -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">🌊 Soaking Worship</p>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <a href="https://www.youtube.com/results?search_query=Dunsin+Oyekan+soaking+worship" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Dunsin Oyekan</a>
                        <a href="https://www.youtube.com/results?search_query=Nathaniel+Bassey+soaking+worship" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Nathaniel Bassey</a>
                        <a href="https://www.youtube.com/results?search_query=Theophilus+Sunday+soaking+worship" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Theophilus Sunday</a>
                        <a href="https://www.youtube.com/results?search_query=Minister+GUC+soaking+worship" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Minister GUC</a>
                        <a href="https://www.youtube.com/results?search_query=Abbey+Ojomu+soaking+worship" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Abbey Ojomu</a>
                        <a href="https://www.youtube.com/results?search_query=Ebuka+Songs+soaking+worship" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Ebuka Songs</a>
                    </div>
                </div>

                <!-- Sacrifices of Praise -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">🥁 Sacrifices of Praise</p>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <a href="https://www.youtube.com/results?search_query=Nathaniel+Bassey+praise+session" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Nathaniel Bassey - Praise Session</a>
                        <a href="https://www.youtube.com/results?search_query=Dunsin+Oyekan+praise+session" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Dunsin Oyekan - Praise Session</a>
                        <a href="https://www.youtube.com/results?search_query=Bidemi+Olaoba+praise" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Bidemi Olaoba - Praise</a>
                        <a href="https://www.youtube.com/results?search_query=Mercy+Chinwo+praise+session" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">Mercy Chinwo - Praise Session</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('morningFlow', () => ({
                // Flow state
                step: 1,
                finished: false,
                worshipOpen: false,

                moods: [
                    { value: 1, emoji: '😴', label: 'Tired' },
                    { value: 2, emoji: '😐', label: 'Okay' },
                    { value: 3, emoji: '🙂', label: 'Steady' },
                    { value: 4, emoji: '😄', label: 'Energised' },
                    { value: 5, emoji: '🔥', label: 'Fired up' },
                ],
                mood: 3,

                chapters: [
                    'Psalms 91', 'Psalms 23', 'Psalms 27',
                    'Deuteronomy 28', 'Psalms 121', 'Psalms 118', 'Isaiah 61',
                ],
                selectedChapter: null,

                personalizing: false,
                personalized: null,
                error: null,

                affirmations: [
                    { ref: 'Romans 8:28', text: '"All things work together for good to them that love God."' },
                    { ref: '1 John 5:4', text: '"This is the victory that overcometh the world, even our faith."' },
                    { ref: '2 Corinthians 5:17', text: '"Old things are passed away; behold, all things are become new."' },
                    { ref: 'Philippians 4:13', text: '"I can do all things through Christ which strengtheneth me."' },
                    { ref: 'Philippians 2:13', text: '"It is God which worketh in you both to will and to do of his good pleasure."' },
                ],
                declared: [],

                get stepLabel() {
                    return ['Wake-up Check', 'Read Chapter', 'Personalize', 'Declare', 'Done'][this.step - 1] + ` · Step ${this.step} of 5`;
                },

                selectChapter(chapter) {
                    this.selectedChapter = chapter;
                    this.personalized = null;
                    this.error = null;
                },

                toggleAffirmation(ref) {
                    const i = this.declared.indexOf(ref);
                    if (i === -1) {
                        this.declared.push(ref);
                        if (typeof window.celebrate === 'function') {
                            window.celebrate('Declaration received ✨');
                        }
                    } else {
                        this.declared.splice(i, 1);
                    }
                },

                async personalize() {
                    if (!this.selectedChapter) {
                        this.error = 'Pick a chapter in step 2 first.';
                        return;
                    }
                    this.personalizing = true;
                    this.error = null;
                    try {
                        const response = await fetch('{{ route('bible-chapter.personalize') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ reference: this.selectedChapter })
                        });
                        const data = await response.json();
                        if (data.error) {
                            this.error = data.error;
                        } else {
                            this.personalized = data.personalized;
                        }
                    } catch (e) {
                        this.error = 'Failed to personalize. Please try again.';
                    } finally {
                        this.personalizing = false;
                    }
                },

                finish() {
                    this.finished = true;
                    if (typeof window.celebrate === 'function') {
                        window.celebrate('+1 discipline streak 🔥');
                    }
                },
            }));
        });
    </script>
</x-app-layout>