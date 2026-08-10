<x-app-layout>
    <div class="mx-auto max-w-4xl">
        <!-- Header with Create Button -->
        <section class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Quick Notes</p>
                <h1 class="mt-1 text-3xl font-bold text-white sm:text-4xl">Notepad</h1>
            </div>
            <button onclick="document.getElementById('noteEditor').classList.remove('hidden')" 
                    class="rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                + Create New Note
            </button>
        </section>

        <!-- Note Editor (Hidden by default) -->
        <section id="noteEditor" class="hidden mb-6 rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <form method="POST" action="{{ route('notes.store') }}" class="space-y-4">
                @csrf
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-slate-300">Title</label>
                        <input type="text" name="title" placeholder="Note title..." 
                            class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" />
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button type="submit" class="rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                            💾 Save
                        </button>
                        <button type="button" onclick="document.getElementById('noteEditor').classList.add('hidden')" 
                                class="rounded-2xl border border-white/10 bg-slate-800 px-4 py-2.5 text-sm text-slate-300 hover:text-white transition">
                            Cancel
                        </button>
                    </div>
                </div>
                
                <div>
                    <textarea name="content" rows="12" required placeholder="Start typing your note here..."
                        class="w-full rounded-2xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 resize-none"></textarea>
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
                        <div class="flex gap-2">
                            <button onclick="editNote({{ $note->id }}, '{{ addslashes($note->title) }}', `{{ addslashes($note->content) }}`)" 
                                    class="rounded-xl border border-white/10 bg-slate-800 p-2 text-slate-300 hover:text-white transition" title="Edit">
                                ✏️
                            </button>
                            <form method="POST" action="{{ route('notes.destroy', $note) }}" class="inline" onsubmit="return confirm('Delete this note?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-500/20 bg-slate-800 p-2 text-rose-400 hover:text-rose-300 transition" title="Delete">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-white/10 p-12 text-center">
                    <p class="text-6xl mb-4">📝</p>
                    <p class="text-slate-400">No notes yet. Click "Create New Note" to get started.</p>
                </div>
            @endforelse
    </div>
</x-app-layout>

<script>
function editNote(id, title, content) {
    const editor = document.getElementById('noteEditor');
    editor.classList.remove('hidden');
    
    // Create edit form
    editor.innerHTML = `
        <form method="POST" action="/notes/${id}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-300">Title</label>
                    <input type="text" name="title" value="${title}" placeholder="Note title..." 
                        class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" />
                </div>
                <div class="flex gap-2 ml-4">
                    <button type="submit" class="rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                        💾 Update
                    </button>
                    <button type="button" onclick="location.reload()" 
                            class="rounded-2xl border border-white/10 bg-slate-800 px-4 py-2.5 text-sm text-slate-300 hover:text-white transition">
                        Cancel
                    </button>
                </div>
            </div>
            
            <div>
                <textarea name="content" rows="12" required placeholder="Start typing your note here..."
                    class="w-full rounded-2xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 resize-none">${content}</textarea>
            </div>
        </form>
    `;
}
</script>
