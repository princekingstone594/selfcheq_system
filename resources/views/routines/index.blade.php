<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6" x-data="{ historyOpen: false }">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Rituals</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">Morning Routine</h1>
                    <p class="mt-2 text-sm text-slate-400">Build the habits that set your day in motion.</p>
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
                @forelse($history as $date => $dayRoutines)
                    <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                        <p class="text-sm font-medium text-indigo-300">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</p>
                        <div class="mt-2 space-y-1.5">
                            @foreach($dayRoutines as $rtn)
                                <div class="flex items-center gap-2 text-sm">
                                    <span>{{ $rtn->is_completed ? '✅' : '⬜' }}</span>
                                    <span class="{{ $rtn->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">{{ $rtn->title }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No past routines yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>