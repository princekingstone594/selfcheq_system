<x-app-layout>
    <div class="max-w-xl mx-auto py-6">

        <h1 class="text-xl font-bold mb-4">Daily Journal</h1>

        @if(session('success'))
            <div class="mb-3 p-2 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('journal.store') }}">
            @csrf

            <!-- Main Entry -->
            <textarea 
                name="entry"
                rows="5"
                placeholder="How was your day?"
                class="w-full border rounded p-3"
            >{{ $journal->entry ?? '' }}</textarea>

            <!-- Mood -->
            <select 
                name="mood" 
                class="w-full mt-3 p-2 border rounded"
            >
                <option value="">Select Mood</option>
                <option value="1" {{ ($journal->mood ?? '') == 1 ? 'selected' : '' }}>😞 Bad</option>
                <option value="2" {{ ($journal->mood ?? '') == 2 ? 'selected' : '' }}>😐 Okay</option>
                <option value="3" {{ ($journal->mood ?? '') == 3 ? 'selected' : '' }}>🙂 Good</option>
                <option value="4" {{ ($journal->mood ?? '') == 4 ? 'selected' : '' }}>😄 Great</option>
                <option value="5" {{ ($journal->mood ?? '') == 5 ? 'selected' : '' }}>🔥 Excellent</option>
            </select>

            <!-- Gratitude -->
            <textarea 
                name="gratitude"
                rows="3"
                placeholder="What are you grateful for?"
                class="w-full mt-3 border rounded p-3"
            >{{ $journal->gratitude ?? '' }}</textarea>

            <!-- Reflection -->
            <textarea 
                name="reflection"
                rows="3"
                placeholder="What did you learn today?"
                class="w-full mt-3 border rounded p-3"
            >{{ $journal->reflection ?? '' }}</textarea>

            <!-- Submit -->
            <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded w-full">
                Save Journal
            </button>
        </form>

    </div>
</x-app-layout>