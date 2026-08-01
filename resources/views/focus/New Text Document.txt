<x-app-layout>
    <div class="max-w-xl mx-auto py-6">

        <h1 class="text-xl font-bold mb-4">Focus Mode</h1>

        <form method="POST" action="{{ route('focus.start') }}" class="mb-4">
            @csrf
            <input type="number" name="duration" placeholder="Minutes (e.g. 25)"
                class="border rounded px-3 py-2 w-full mb-2">
            <button class="bg-green-500 text-white px-4 py-2 rounded w-full">
                Start Focus Session
            </button>
        </form>

        <h2 class="font-semibold mt-6 mb-2">Recent Sessions</h2>

        <ul>
            @foreach($sessions as $session)
                <li class="mb-2 text-sm text-gray-600">
                    {{ $session->duration }} mins —
                    {{ $session->started_at->format('H:i') }}
                    @if($session->ended_at)
                        ✔ done
                    @else
                        <form method="POST" action="{{ route('focus.stop', $session) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button class="text-red-500 ml-2">Stop</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

    </div>
</x-app-layout>