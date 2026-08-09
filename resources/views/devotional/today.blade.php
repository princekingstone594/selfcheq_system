<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <!-- Animated background -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Spirit</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Daily Devotional</h1>
                <p class="mt-2 text-slate-300">A moment of stillness and guidance for your day.</p>
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
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Prayer Plan</p>
            <p class="mt-2 text-sm text-slate-400">Create prayer points with duration and reminders to build a consistent prayer habit.</p>

            <!-- Add Prayer Point Form -->
            <form method="POST" action="{{ route('devotional.prayer.store') }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-300">Prayer Point</label>
                        <textarea name="prayer_point" rows="2" required placeholder="Enter your prayer request or topic..."
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Days to Pray</label>
                        <input type="number" name="days" value="1" min="1" max="365" required
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Reminder Time (optional)</label>
                        <input type="time" name="reminder_time" 
                            class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="reminder_enabled" id="reminder_enabled" class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                    <label for="reminder_enabled" class="text-sm text-slate-300">Enable daily reminder</label>
                </div>

                <button type="submit" class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Add Prayer Point
                </button>
            </form>

            <!-- Active Prayer Plans -->
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
                                        <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-2 text-emerald-300 hover:bg-emerald-500/20 transition" title="Mark as completed">
                                            ✓
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('devotional.prayer.destroy', $plan) }}" class="inline" onsubmit="return confirm('Remove this prayer point?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-rose-400/30 bg-rose-500/10 p-2 text-rose-300 hover:bg-rose-500/20 transition" title="Remove">
                                            ✕
                                        </button>
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
        </section>

        <!-- Command Your Morning -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Command Your Morning</p>
            <p class="mt-2 text-sm text-slate-400">Set your wake-up time and start each day with a declaration of faith.</p>

            @if($morningDevotion)
                <div class="mt-4 rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white">Wake Up Time</p>
                            <p class="text-lg font-semibold text-indigo-300">{{ \Carbon\Carbon::parse($morningDevotion->wake_up_time)->format('g:i A') }}</p>
                            @if($morningDevotion->declaration)
                                <p class="mt-2 text-sm italic text-slate-300">"{{ $morningDevotion->declaration }}"</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full {{ $morningDevotion->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-400' }} px-3 py-1 text-xs font-medium">
                                {{ $morningDevotion->is_active ? 'Active' : 'Paused' }}
                            </span>
                            <form method="POST" action="{{ route('devotional.morning.toggle', $morningDevotion) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-xl border border-white/10 bg-slate-800 px-3 py-1.5 text-xs text-slate-300 hover:text-white transition">
                                    {{ $morningDevotion->is_active ? 'Pause' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('devotional.morning.store') }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
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
                </div>

                <button type="submit" class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    {{ $morningDevotion ? 'Update Morning Devotion' : 'Set Morning Devotion' }}
                </button>
            </form>
        </section>

        <!-- Fasting Plan -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Fasting Plan</p>
            <p class="mt-2 text-sm text-slate-400">Create a fasting plan with purpose and reminders to keep you accountable.</p>

            <form method="POST" action="{{ route('devotional.fasting.store') }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
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
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="reminder_enabled" id="fasting_reminder_enabled" class="h-4 w-4 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                    <label for="fasting_reminder_enabled" class="text-sm text-slate-300">Enable daily reminders</label>
                </div>

                <button type="submit" class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Create Fasting Plan
                </button>
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
                                            <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-2 text-emerald-300 hover:bg-emerald-500/20 transition" title="Start fasting">
                                                ▶
                                            </button>
                                        </form>
                                    @endif
                                    @if($plan->started_at && !$plan->completed_at)
                                        <form method="POST" action="{{ route('devotional.fasting.complete', $plan) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-2 text-emerald-300 hover:bg-emerald-500/20 transition" title="Mark as completed">
                                                ✓
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('devotional.fasting.destroy', $plan) }}" class="inline" onsubmit="return confirm('Remove this fasting plan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-rose-400/30 bg-rose-500/10 p-2 text-rose-300 hover:bg-rose-500/20 transition" title="Remove">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-dashed border-white/10 p-6 text-center">
                    <p class="text-sm text-slate-400">No active fasting plans. Create one above to begin your spiritual journey.</p>
                </div>
            @endif
        </section>

        <!-- Study Plan -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Study Plan</p>
            <p class="mt-2 text-sm text-slate-400">Grow in knowledge with structured Bible reading and inspirational resources.</p>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <!-- Bible Reading Plans -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="font-medium text-white">📖 Bible Reading</p>
                    <div class="mt-3 space-y-2">
                        <a href="https://www.biblegateway.com/reading-plans/one-year-bible" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • Bible in 365 Days →
                        </a>
                        <a href="https://www.biblegateway.com/reading-plans/new-testament-90-days" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • New Testament in 3 Months →
                        </a>
                        <a href="https://www.biblegateway.com/reading-plans/proverb-a-day" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • Proverbs in 31 Days →
                        </a>
                        <a href="https://www.biblegateway.com/reading-plans/psalms-7-days" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • Psalms in 7 Days →
                        </a>
                    </div>
                </div>

                <!-- Inspirational Resources -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="font-medium text-white">🎧 Resources</p>
                    <div class="mt-3 space-y-2">
                        <a href="https://www.desiringgod.org" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • Desiring God Books & Sermons →
                        </a>
                        <a href="https://open.spotify.com/show/5m1CsPq3wBl4mORiwy8cUj" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • Gospel Podcasts →
                        </a>
                        <a href="https://www.christianbook.com" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • Christian Book Store →
                        </a>
                        <a href="https://www.crossway.org" target="_blank" class="block text-sm text-indigo-300 hover:text-indigo-200 transition">
                            • ESV Study Bible →
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
