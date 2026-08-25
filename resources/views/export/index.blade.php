<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">
        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Data Export</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Export your discipline journey</h1>
            <p class="mt-2 text-sm text-slate-400">Download your complete SelfCheq data — tasks, journals, focus sessions, habits, and more.</p>
        </section>

        <!-- Stats overview -->
        <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['tasks'] }}</p>
                <p class="text-xs text-slate-400">Tasks</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['journals'] }}</p>
                <p class="text-xs text-slate-400">Journals</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['focus_sessions'] }}</p>
                <p class="text-xs text-slate-400">Focus Sessions</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['habits'] }}</p>
                <p class="text-xs text-slate-400">Habits</p>
            </div>
        </section>

        <!-- Export option -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div>
                <p class="text-lg font-semibold text-white">📕 PDF Export</p>
                <p class="mt-1 text-sm leading-relaxed text-slate-400">Download your complete discipline journey as a beautifully formatted PDF — profile, tasks, routines, focus sessions, habits, journals, financials, badges, and daily stats.</p>
            </div>
            <a href="{{ route('export.pdf') }}"
               class="mt-4 block w-full rounded-2xl bg-indigo-500 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-400 transition">
                ⬇ Download PDF
            </a>
        </section>

        <!-- Privacy note -->
        <section class="rounded-2xl border border-white/10 bg-slate-800/50 p-4 text-sm text-slate-400">
            🔒 Your data is exported directly from your account. No data is sent to any third party.
        </section>
    </div>
</x-app-layout>