<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Rituals</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Morning Routine</h1>
            <p class="mt-2 text-sm text-slate-400">Build the habits that set your day in motion.</p>
        </section>

        <!-- Add Routine -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Add routine</p>

            <form method="POST" action="{{ route('routines.store') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                @csrf
                <input type="text" name="title" placeholder="Add routine..."
                    class="flex-1 rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                <button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Add
                </button>
            </form>
        </section>

        <!-- Routine List -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Your routines</p>

            <div class="mt-4 space-y-2">
                @forelse($routines as $routine)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3 transition hover:bg-slate-800">
                        <span class="text-sm {{ $routine->is_completed ? 'line-through text-slate-500' : 'text-slate-100' }}">
                            {{ $routine->title }}
                        </span>

                        <form method="POST" action="{{ route('routines.toggle', $routine) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-xl px-3 py-1.5 text-xs font-medium {{ $routine->is_completed ? 'bg-slate-700 text-slate-300 hover:bg-slate-600' : 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30' }} transition">
                                {{ $routine->is_completed ? 'Undo' : 'Done' }}
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-8 text-center text-sm text-slate-500">
                        No routines yet. Add one above.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>