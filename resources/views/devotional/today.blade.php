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
            <p class="mt-2 text-sm text-slate-400">Set aside dedicated time for prayer. Create a routine to build a consistent prayer habit.</p>

            <div class="mt-4 space-y-3">
                <a href="{{ route('routines.index') }}" class="flex items-center justify-between rounded-2xl border border-white/10 bg-slate-800/50 p-4 transition hover:border-indigo-400/30 hover:bg-slate-800">
                    <div>
                        <p class="font-medium text-white">Create Prayer Routine</p>
                        <p class="text-xs text-slate-400">Set a daily prayer time with reminders</p>
                    </div>
                    <span class="text-indigo-300">→</span>
                </a>

                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="font-medium text-white">Prayer Topics</p>
                    <div class="mt-3 space-y-2 text-sm text-slate-300">
                        <p class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                            Gratitude - Thank God for today's blessings
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-purple-400"></span>
                            Guidance - Ask for wisdom in decisions
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                            Strength - Pray for discipline and focus
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            Others - Intercede for family and friends
                        </p>
                    </div>
                </div>
            </div>
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
