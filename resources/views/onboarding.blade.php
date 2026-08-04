<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6 rounded-3xl border border-white/10 bg-slate-900/70 p-8 shadow-2xl shadow-indigo-950/30 backdrop-blur">
        <div class="space-y-3">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Welcome to SelfCheq</p>
            <h1 class="text-3xl font-semibold text-white">Build a life you can trust.</h1>
            <p class="max-w-2xl text-sm text-slate-400">This is your operating system for discipline, focus, promises, routines, appointments, reflection, and growth. We are building it with you, one layer at a time.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-5">
                <p class="text-lg font-semibold text-white">One day at a time</p>
                <p class="mt-2 text-sm text-slate-400">Focus on what matters today, not everything at once.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-5">
                <p class="text-lg font-semibold text-white">Keep every promise</p>
                <p class="mt-2 text-sm text-slate-400">Turn commitments into rituals that keep you accountable.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-5">
                <p class="text-lg font-semibold text-white">Grow with clarity</p>
                <p class="mt-2 text-sm text-slate-400">Track routines, journal your reflections, and stay aligned with your purpose.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-5 text-sm text-indigo-100">
            <p class="font-semibold">What comes next</p>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-indigo-50/90">
                <li>Smarter reminders and habit pacing</li>
                <li>Screen-time and focus protection</li>
                <li>Health and wellness tracking</li>
                <li>Deeper AI guidance and weekly reviews</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('onboarding.complete') }}" class="flex justify-end">
            @csrf
            <x-primary-button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400">
                Start using SelfCheq
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
