<x-app-layout>
<div class="mx-auto max-w-3xl space-y-8" x-data="personalizeChapter">

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('devotional.today') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-slate-800/70 px-4 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Devotional
        </a>
    </div>

    <!-- Header -->
    <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-8 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-10">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
        </div>

        <div class="relative">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Command Your Morning</p>
            <h1 class="mt-3 text-4xl font-bold text-white sm:text-5xl font-serif">{{ $chapter->reference }}</h1>
            <p class="mt-3 text-slate-300">Declare this scripture boldly over your day with faith and confidence.</p>

            <!-- Personalize Button -->
            <div class="mt-6">
                <button @click="personalizeScripture($el)"
                    class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:from-indigo-400 hover:to-purple-400 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="personalizing">
                    <svg x-show="!personalizing" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <svg x-show="personalizing" class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-show="!personalizing">Personalize Scripture ✨</span>
                    <span x-show="personalizing">Personalizing...</span>
                </button>
                <p class="mt-2 text-xs text-slate-400">AI transforms pronouns referring to you into first person while keeping God's references untouched.</p>
            </div>
        </div>
    </section>

    <!-- Chapter Content -->
    <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-8 shadow-xl sm:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Scripture Passage</p>
        
        <div class="mt-6">
            <!-- Original passage (hidden when personalized) -->
            <p x-show="!personalized" class="font-serif text-xl leading-loose text-slate-100">{{ $chapter->content }}</p>
            <!-- Personalized passage (shown in its place after clicking) -->
            <div x-show="personalized" x-cloak>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-400 mb-3">✨ Your Personalized Declaration</p>
                <p x-text="personalized" class="font-serif text-xl leading-loose text-emerald-200"></p>
                <button @click="personalized = null" class="mt-4 text-sm text-slate-400 hover:text-white transition">← Restore Original</button>
            </div>
        </div>

        @if($declarationText)
            <div class="mt-10 rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-6">
                <p class="text-sm font-medium text-emerald-400 uppercase tracking-widest mb-3">Personalized Declaration</p>
                <p class="italic text-emerald-300 leading-relaxed font-serif text-lg">{{ $declarationText }}</p>
                <p class="text-xs text-emerald-400 mt-4">First-person confession for daily declaration practice</p>
            </div>
        @endif

        @if($wakeUpTime)
            <div class="mt-8 rounded-2xl border border-white/10 bg-slate-800/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300 mb-2">Your Morning Routine</p>
                <p class="text-lg font-semibold text-indigo-300">{{ \Carbon\Carbon::parse($wakeUpTime)->format('g:i A') }}</p>
            </div>
        @endif
    </section>

    <!-- Chapter Navigation -->
    <section class="border-t border-white/10 pt-8">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 mb-4">Other Declaration Chapters</p>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($chapters as $ch)
                <a href="{{ route('bible-chapter.show', $ch->reference) }}" 
                   class="group rounded-2xl border {{ $ch->reference === $chapter->reference ? 'border-indigo-500/40 bg-indigo-500/10' : 'border-slate-700 bg-slate-800 hover:bg-white/5' }} px-5 py-4 transition">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-300 group-hover:text-amber-300 transition">📖</span>
                        <span class="font-medium {{ $ch->reference === $chapter->reference ? 'text-indigo-300' : 'text-slate-300 group-hover:text-white' }}">{{ $ch->reference }}</span>
                        @if($ch->reference === $chapter->reference)
                            <span class="ml-auto text-xs text-indigo-300">• Reading</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('personalizeChapter', () => ({
        personalizing: false,
        personalized: null,
        error: null,
        
        async personalizeScripture(el) {
            const root = el.closest('[x-data]');
            root.__x.$data.personalizing = true;
            
            try {
                const response = await fetch('{{ route("bible-chapter.personalize") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reference: '{{ $chapter->reference }}' })
                });
                
                const data = await response.json();
                root.__x.$data.personalizing = false;
                
                if (data.error) {
                    root.__x.$data.error = data.error;
                } else {
                    root.__x.$data.personalized = data.personalized;
                    root.__x.$data.error = null;
                }
            } catch (e) {
                root.__x.$data.personalizing = false;
                root.__x.$data.error = 'Failed to personalize. Please try again.';
            }
        }
    }));
});
</script>
</x-app-layout>