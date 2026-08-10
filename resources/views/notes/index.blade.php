<x-app-layout>
    <div class="mx-auto max-w-6xl">
        <!-- Header -->
        <section class="mb-4">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Quick Notes</p>
            <h1 class="mt-1 text-3xl font-bold text-white sm:text-4xl">Notepad</h1>
        </section>

        <!-- Notes List (shown when not editing) -->
        <section id="notesList" class="space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">Your Notes</p>
                <button onclick="showEditor()" 
                        class="rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                    + Create New Note
                </button>
            </div>

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
        </section>

        <!-- Note Editor (Full screen overlay) -->
        <section id="noteEditor" class="hidden fixed inset-0 bg-slate-950 z-50 flex flex-col">
            <div class="bg-slate-900 border-b border-white/10 px-6 py-4">
                <div class="max-w-5xl mx-auto flex items-center justify-between">
                    <input type="text" id="noteTitle" placeholder="Note title..." 
                           class="flex-1 bg-transparent text-lg font-semibold text-white placeholder-slate-500 focus:outline-none" />
                    <div class="flex gap-2 ml-4">
                        <button type="button" onclick="saveNote()" 
                                class="rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 transition">
                            💾 Save
                        </button>
                        <button type="button" onclick="closeEditor()" 
                                class="rounded-2xl border border-white/10 bg-slate-800 px-4 py-2.5 text-sm text-slate-300 hover:text-white transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-5xl mx-auto">
                    <textarea id="noteContent" placeholder="Start typing your note here..." 
                              class="w-full h-full min-h-[calc(100vh-200px)] rounded-2xl border border-slate-700 bg-slate-800 px-6 py-4 text-base text-white placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 resize-none"></textarea>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

<script>
let currentEditId = null;

function showEditor() {
    currentEditId = null;
    document.getElementById('noteTitle').value = '';
    document.getElementById('noteContent').value = '';
    document.getElementById('noteEditor').classList.remove('hidden');
    document.getElementById('noteTitle').focus();
}

function closeEditor() {
    document.getElementById('noteEditor').classList.add('hidden');
    currentEditId = null;
}

function saveNote() {
    const title = document.getElementById('noteTitle').value;
    const content = document.getElementById('noteContent').value;
    
    if (!content.trim()) {
        alert('Please enter some content for your note.');
        return;
    }
    
    const url = currentEditId ? `/notes/${currentEditId}` : '{{ route('notes.store') }}';
    const method = currentEditId ? 'PUT' : 'POST';
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    if (currentEditId) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
    }
    
    const titleInput = document.createElement('input');
    titleInput.type = 'hidden';
    titleInput.name = 'title';
    titleInput.value = title;
    form.appendChild(titleInput);
    
    const contentInput = document.createElement('input');
    contentInput.type = 'hidden';
    contentInput.name = 'content';
    contentInput.value = content;
    form.appendChild(contentInput);
    
    document.body.appendChild(form);
    form.submit();
}

function editNote(id, title, content) {
    currentEditId = id;
    document.getElementById('noteTitle').value = title;
    document.getElementById('noteContent').value = content;
    document.getElementById('noteEditor').classList.remove('hidden');
    document.getElementById('noteTitle').focus();
}
</script>
