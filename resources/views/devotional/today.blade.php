<x-app-layout>
    <div class="max-w-xl mx-auto py-10 text-center">

        <h1 class="text-xl font-bold mb-6">Daily Devotional</h1>

        @if($devotional)
            <div class="bg-white p-6 rounded shadow">
                <p class="text-lg italic text-gray-700">
                    "{{ $devotional->content }}"
                </p>
            </div>
        @else
            <p class="text-gray-400">No message for today</p>
        @endif

    </div>
</x-app-layout>