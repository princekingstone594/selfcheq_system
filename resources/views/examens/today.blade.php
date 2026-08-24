<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6" x-data="{ mood: {{ $examen->mood_rating ?? 3 }}, historyOpen: false }">

        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-slate-900 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/15 blur-3xl"></div>
            </div>

            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">🌙 Evening</p>
                    <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Evening Examen</h1>
                    <p class="mt-2 text-slate-300">A two-minute ritual to close today and release what you don't need to carry.</p>
                </div>
                <button @click="historyOpen = !historyOpen" class="inline-flex shrink-0 items-center gap-2 rounded-2xl border border-white/10 bg-slate-800/80 px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History
                    <span class="rounded-full bg-indigo-500/20 px-2 py-0.5 text-[10px] text-indigo-300">{{ collect($history)->flatten()->count() }}</span>
                </button>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Step 1: Mood / High point -->
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Step 1 · One high point</p>
                    <p class="mt-1 text-sm text-slate-400">How did this day go, looking back?</p>
                </div>

                @php
                    $moods = [1 => '😞', 2 => '😐', 3 => '🙂', 4 => '😄', 5 => '🔥'];
                @endphp
                <div class="flex items-center justify-between gap-2">
                    @foreach($moods as $value => $emoji)
                        <label class="flex flex-1 cursor-pointer flex-col items-center gap-1 rounded-2xl border px-2 py-3 text-center transition"
                               :class="mood === {{ $value }} ? 'border-indigo-400/50 bg-indigo-500/15' : 'border-white/10 bg-slate-800/50 hover:border-white/25'"
                               @click="mood = {{ $value }}">
                            <input type="radio" name="mood_rating" value="{{ $value }}" class="sr-only" :checked="mood === {{ $value }}">
                            <span class="text-2xl">{{ $emoji }}</span>
                        </label>
                    @endforeach
                </div>

                <input type="text" name="high_point" value="{{ $examen->high_point ?? '' }}" placeholder="What was your high point today? (optional)"
                       class="mt-4 w-full rounded-2xl border border-slate-700 bg-slate-800/80 p-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
            </section>
<!-- Step 2: Reflection -->
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Step 2 · Reflection</p>
                    <p class="mt-1 text-sm text-slate-400">Where did you feel most alive or closest to your purpose today?</p>
                </div>
                <textarea name="reflection" rows="3" placeholder="A moment, a conversation, a win…"
                          class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-800/80 p-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">{{ $examen->reflection ?? '' }}</textarea>
            </section>

            <!-- Step 3: Gratitude -->
            <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Step 3 · Gratitude</p>
                    <p class="mt-1 text-sm text-slate-400">One thing you're grateful for today.</p>
                </div>

                @php
                    $chips = ['Health', 'Family', 'Growth', 'Rest', 'God', 'Work', 'Friends', 'Peace'];
                    $isChip = in_array($examen->gratitude ?? '', $chips, true);
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($chips as $chip)
                        <label class="cursor-pointer">
                            <input type="radio" name="gratitude" value="{{ $chip }}" class="sr-only peer"
                                   @checked(($examen->gratitude ?? '') === $chip)>
                            <span class="inline-block rounded-full border border-white/10 bg-slate-800/60 px-4 py-2 text-sm text-slate-300 transition peer-checked:border-indigo-400/50 peer-checked:bg-indigo-500/15 peer-checked:text-white">{{ $chip }}</span>
                        </label>
                    @endforeach
                    <label class="cursor-pointer">
                        <input type="radio" name="gratitude" value="" class="sr-only peer"
                               @checked($isChip === false && ($examen->gratitude ?? '') !== '')>
                        <span class="inline-block rounded-full border border-white/10 bg-slate-800/60 px-4 py-2 text-sm text-slate-300 transition peer-checked:border-amber-400/50 peer-checked:bg-amber-500/15 peer-checked:text-white">✍️ Custom</span>
                    </label>
                </div>

                <input type="text" name="gratitude_custom" value="{{ $isChip ? '' : ($examen->gratitude ?? '') }}" placeholder="…or type your own"
                       class="mt-3 w-full rounded-2xl border border-slate-700 bg-slate-800/80 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
            </section>

            <!-- Step 4: Release -->
            <section class="rounded-3xl border border-emerald-400/20 bg-emerald-500/5 p-6 shadow-xl">
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-400">Step 4 · Release</p>
                    <p class="mt-1 text-sm text-slate-400">Let go of today's struggles. They've served their purpose.</p>
                </div>

                <button type="submit"
                        class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:from-indigo-400 hover:to-purple-400 active:scale-95">
                    🌙 I release today's struggles
                </button>
            </section>
        </form>
<!-- History -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <button @click="historyOpen = !historyOpen" class="flex w-full items-center justify-between text-left">
                <span class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">📜 Past Examen</span>
                <svg x-show="!historyOpen" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <svg x-show="historyOpen" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </button>

            <div x-show="historyOpen" x-transition class="mt-4 space-y-4" style="display:none;">
                @forelse($history as $date => $dayExamens)
                    <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                        <p class="text-sm font-medium text-indigo-300">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</p>
                        @foreach($dayExamens as $entry)
                            <div class="mt-2 space-y-1 text-sm text-slate-300">
                                <p>
                                    <span>{{ $entry->mood_emoji }}</span>
                                    @if($entry->high_point)
                                        <span>· {{ $entry->high_point }}</span>
                                    @endif
                                    @if($entry->gratitude)
                                        <span class="text-emerald-300">🙏 {{ $entry->gratitude }}</span>
                                    @endif
                                </p>
                                @if($entry->reflection)
                                    <p class="text-slate-400">{{ $entry->reflection }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No past examens yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>