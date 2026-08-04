<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Deep Work</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Focus Timer</h1>
            <p class="mt-2 text-sm text-slate-400">Start a focus session and protect your attention. Small blocks of deep work compound.</p>
        </section>

        <!-- Timer -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-8 shadow-xl text-center"
                 x-data="{
                    running: false,
                    secondsLeft: 25 * 60,
                    interval: null,
                    selected: 25,
                    setDuration(m) {
                        this.selected = m;
                        if (!this.running) {
                            this.secondsLeft = m * 60;
                        }
                    },
                    startTimer() {
                        this.running = true;
                        this.secondsLeft = this.selected * 60;
                        if (this.interval) clearInterval(this.interval);
                        this.interval = setInterval(() => {
                            this.secondsLeft--;
                            if (this.secondsLeft <= 0) {
                                clearInterval(this.interval);
                                this.running = false;
                            }
                        }, 1000);
                    },
                    formatTime(total) {
                        const m = Math.floor(total / 60).toString().padStart(2, '0');
                        const s = (total % 60).toString().padStart(2, '0');
                        return m + ':' + s;
                    }
                 }">

            <!-- Display -->
            <div class="relative mx-auto h-48 w-48">
                <div class="absolute inset-0 rounded-full border-4 border-slate-800"></div>
                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-indigo-500 transition-transform duration-300"
                     :class="{ 'animate-spin': running }"
                     style="border-right-color: rgba(99,102,241,0.3); border-bottom-color: rgba(99,102,241,0.2); border-left-color: rgba(99,102,241,0.1);"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-bold text-white tabular-nums" x-text="formatTime(secondsLeft)">25:00</span>
                    <span class="mt-1 text-xs uppercase tracking-widest text-slate-500" x-text="running ? 'Focusing' : 'Ready'">Ready</span>
                </div>
            </div>

            <!-- Preset durations -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <template x-for="m in [15, 25, 45, 60, 90]" :key="m">
                    <button @click="setDuration(m)"
                            class="rounded-xl px-3 py-1.5 text-sm font-medium transition"
                            :class="selected === m ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'"
                            x-text="m + ' min'">15 min</button>
                </template>
            </div>

            <!-- Start -->
            <form method="POST" action="{{ route('focus.start') }}" class="mt-6 flex justify-center">
                @csrf
                <input type="hidden" name="duration" :value="selected">
                <button type="submit"
                        @click="startTimer()"
                        class="rounded-2xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Start Session
                </button>
            </form>
        </section>

        <!-- Recent Sessions -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Recent sessions</p>

            <div class="mt-4 space-y-2">
                @forelse($sessions as $session)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                        <div class="flex items-center gap-3">
                            <span class="rounded-xl bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                                {{ $session->duration }} min
                            </span>
                            <span class="text-sm text-slate-300">
                                {{ \Carbon\Carbon::parse($session->started_at)->format('d M, H:i') }}
                            </span>
                        </div>

                        @if($session->ended_at)
                            <span class="text-xs text-emerald-400">Completed</span>
                        @else
                            <form method="POST" action="{{ route('focus.stop', $session) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-xl bg-rose-500/20 px-3 py-1.5 text-xs font-medium text-rose-300 hover:bg-rose-500/30 transition">
                                    Stop
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-8 text-center text-sm text-slate-500">
                        No focus sessions yet. Start your first one above.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>