<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-amber-400/20 bg-gradient-to-br from-amber-900/30 via-slate-900 to-orange-900/30 p-6 shadow-2xl shadow-amber-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-amber-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-orange-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Quick Notes</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Notepad</h1>
                <p class="mt-2 text-slate-300">Capture your thoughts, ideas, and reminders instantly.</p>
            </div>
        </section>

        <!-- Note Editor -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <form method="POST" action="{{ route('notes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300">Title (optional)</label>
                    <input type="text" name="title" placeholder="Give your note a title..." 
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300">Content</label>
                    <textarea name="content" rows="8" required placeholder="Start typing your note here..."
                        class="mt-1 rounded-2xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 resize-none"></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-500">Notes are saved automatically when you click save</p>
                    <button type="submit" class="rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                        💾 Save Note
                    </button>
                </div>
            </form>
        </section>

        <!-- Notes List -->
        <section class="space-y-4">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Your Notes</p>

            @forelse($notes as $note)
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-lg hover:border-amber-400/30 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            @if($note->title)
                                <h3 class="text-lg font-semibold text-white">{{ $note->title }}</h3>
                            @endif
                            <p class="mt-2 text-sm text-slate-300 whitespace-pre-wrap leading-relaxed">{{ $note->content }}</p>
                            <p class="mt-3 text-xs text-slate-500">
                                {{ $note->created_at->format('M d, Y g:i A') }}
                                @if($note->updated_at != $note->created_at)
                                    · Updated {{ $note->updated_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('notes.destroy', $note) }}" class="inline" onsubmit="return confirm('Delete this note?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-rose-500/20 bg-slate-800 p-2 text-rose-400 hover:text-rose-300 transition" title="Delete">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-white/10 p-12 text-center">
                    <p class="text-6xl mb-4">📝</p>
                    <p class="text-slate-400">No notes yet. Start capturing your thoughts above.</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app-layout>