<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6" x-data="{ historyOpen: false }">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Reflection</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">Daily Journal</h1>
                    <p class="mt-2 text-sm text-slate-400">Capture how your day went, what you're grateful for, and what you learned.</p>
                </div>

                <!-- History button -->
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

        <!-- Journal Form -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <form method="POST" action="{{ route('journal.store') }}" class="space-y-5">
                @csrf

                <!-- Main Entry -->
                <div>
                    <label for="entry" class="block text-sm font-medium text-slate-300">How was your day?</label>
                    <textarea
                        id="entry"
                        name="entry"
                        rows="5"
                        placeholder="Write freely about your day..."
                        class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-800/80 p-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    >{{ $journal->content ?? '' }}</textarea>
                </div>

                <!-- Mood -->
                <div>
                    <label for="mood" class="block text-sm font-medium text-slate-300">Mood</label>
                    <select
                        id="mood"
                        name="mood"
                        class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-800/80 p-3 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="">Select Mood</option>
                        <option value="1" {{ ($journal->mood ?? '') == 1 ? 'selected' : '' }}>😞 Bad</option>
                        <option value="2" {{ ($journal->mood ?? '') == 2 ? 'selected' : '' }}>😐 Okay</option>
                        <option value="3" {{ ($journal->mood ?? '') == 3 ? 'selected' : '' }}>🙂 Good</option>
                        <option value="4" {{ ($journal->mood ?? '') == 4 ? 'selected' : '' }}>😄 Great</option>
                        <option value="5" {{ ($journal->mood ?? '') == 5 ? 'selected' : '' }}>🔥 Excellent</option>
                    </select>
                </div>

                <!-- Gratitude -->
                <div>
                    <label for="gratitude" class="block text-sm font-medium text-slate-300">Gratitude</label>
                    <textarea
                        id="gratitude"
                        name="gratitude"
                        rows="3"
                        placeholder="What are you grateful for?"
                        class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-800/80 p-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    >{{ $journal->gratitude ?? '' }}</textarea>
                </div>

                <!-- Reflection -->
                <div>
                    <label for="reflection" class="block text-sm font-medium text-slate-300">Reflection</label>
                    <textarea
                        id="reflection"
                        name="reflection"
                        rows="3"
                        placeholder="What did you learn today?"
                        class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-800/80 p-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    >{{ $journal->reflection ?? '' }}</textarea>
                </div>

                <!-- Submit -->
                <button class="w-full rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Save Journal
                </button>
            </form>
        </section>

        <!-- History -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <button @click="historyOpen = !historyOpen" class="flex w-full items-center justify-between text-left">
                <span class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">📜 History</span>
                <svg x-show="!historyOpen" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <svg x-show="historyOpen" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </button>

            <div x-show="historyOpen" x-transition class="mt-4 space-y-4" style="display:none;">
                @forelse($history as $date => $dayJournals)
                    <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                        <p class="text-sm font-medium text-indigo-300">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</p>
                        <div class="mt-2 space-y-2">
                            @foreach($dayJournals as $journalEntry)
                                <div class="text-sm text-slate-300">
                                    @if($journalEntry->mood)
                                        <span class="mr-2">
                                            @switch($journalEntry->mood)
                                                @case(1) 😞 @break
                                                @case(2) 😐 @break
                                                @case(3) 🙂 @break
                                                @case(4) 😄 @break
                                                @case(5) 🔥 @break
                                            @endswitch
                                        </span>
                                    @endif
                                    <span class="line-clamp-2">{{ $journalEntry->content ?? 'No entry text' }}</span>
                                    @if($journalEntry->gratitude)
                                        <p class="mt-1 text-xs text-slate-500">🙏 {{ $journalEntry->gratitude }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No past journal entries yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>