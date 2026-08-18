<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <!-- Animated background -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Scripture</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">{{ $chapter->reference }}</h1>
                <p class="mt-2 text-slate-300">Daily devotional passage for offline reflection.</p>
            </div>
        </section>

        <!-- Chapter Content -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300 mt-2">Chapter Content</p>
            
            <div class="mt-4 prose prose-invert lg:prose-lg leading-relaxed">
                <p>{{ $chapter->content }}</p>
            </div>

            @if($declarationText)
                <div class="mt-6 rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4 mt-4" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                    <p class="text-sm font-medium text-emerald-400 uppercase tracking-widest mb-2">Personalized Declaration</p>
                    <p class="italic text-emerald-300 leading-relaxed">{{ $declarationText }}</p>
                    <p class="text-xs text-emerald-400 mt-2">First-person confession for daily declaration practice</p>
                </div>
            @endif

            @if($wakeUpTime)
                <div class="mt-6 rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300 mb-2">Your Morning Routine</p>
                    <p class="text-lg font-semibold text-indigo-300">{{ \Carbon\Carbon::parse($wakeUp_time)->format('g:i A') }}</p>
                </div>
            @endif
        </section>

        <!-- Chapter Navigation -->
        <section class="border-t border-white/10 pt-6">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400 mb-4">Available Chapters</p>
            <div class="grid gap-2 sm:grid-cols-3">
                @foreach(app('app')->make('App\Http\Controllers\BibleChapterController')->availableReferences() as $ref => $label)
                    <a href="{{ route('bible-chapter.show', $ref) }}" 
                       class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-indigo-300 hover:text-white transition hover:bg-white/5 group">
                        {{ $ref }}
                        @if($label !== $ref)
                            <span class="ml-2 text-xs opacity-60">(\(label\))</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

    </div>
</x-app-layout>
