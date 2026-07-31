<x-app-layout>
    <div class="max-w-xl mx-auto py-6">

        <h1 class="text-xl font-bold mb-4">Daily Journal</h1>

        <form method="POST" action="{{ route('journal.store') }}">
            @csrf

            <textarea 
                name="content"
                rows="10"
                placeholder="Reflect on your day..."
                class="w-full border rounded p-3"
            >{{ $journal->content ?? '' }}</textarea>

            <button class="mt-3 bg-blue-500 text-white px-4 py-2 rounded">
                Save Entry
            </button>
        </form>

    </div>
</x-app-layout>