<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Spirit</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Daily Devotional</h1>
            <p class="mt-2 text-sm text-slate-400">A moment of stillness and guidance for your day.</p>
        </section>

        <!-- Devotional Content -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-8 shadow-xl">
            @if($devotional)
                <!-- Verse -->
                <div class="relative">
                    <span class="absolute -top-4 -left-2 text-6xl text-indigo-500/30 select-none">"</span>
                    <p class="relative text-lg italic leading-relaxed text-slate-200">
                        {{ $devotional->content }}
                    </p>
                </div>

                <!-- Passage explaining the verse -->
                @if($devotional->passage)
                    <div class="mt-6 border-t border-white/10 pt-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-300">Reflection</p>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">
                            {{ $devotional->passage }}
                        </p>
                    </div>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-white/10 p-8 text-center text-sm text-slate-500">
                    No message for today. Check back tomorrow.
                </div>
            @endif
        </section>
    </div>
</x-app-layout>