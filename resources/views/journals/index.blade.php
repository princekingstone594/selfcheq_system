<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Reflection</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Daily Journal</h1>
            <p class="mt-2 text-sm text-slate-400">Capture how your day went, what you're grateful for, and what you learned.</p>
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
                    >{{ $journal->entry ?? '' }}</textarea>
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
    </div>
</x-app-layout>