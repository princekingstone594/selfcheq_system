<x-app-layout>
    <div class="max-w-2xl mx-auto py-6 space-y-6">

        <h1 class="text-2xl font-bold">SelfCheq Dashboard</h1>

        {{-- 🔥 Streak --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">🔥 Streak</p>
            <p class="text-xl font-bold">{{ auth()->user()->streak }} days</p>
        </div>

        {{-- ✅ Tasks --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Tasks Progress</p>
            <p class="text-lg font-bold">{{ $taskCompleted }}/{{ $taskTotal }}</p>
            <div class="w-full bg-gray-200 rounded h-2 mt-2">
                <div class="bg-green-500 h-2 rounded" style="width: {{ $taskProgress }}%"></div>
            </div>
        </div>

        {{-- 🔁 Routines --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Routine</p>
            <p class="text-lg font-bold">{{ $routineCompleted }}/{{ $routineTotal }}</p>
        </div>

        {{-- ⏰ Appointments --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600 mb-2">Today's Appointments</p>
            @forelse($appointments as $a)
                <p class="text-sm">
                    {{ \Carbon\Carbon::parse($a->time)->format('H:i') }} — {{ $a->title }}
                </p>
            @empty
                <p class="text-sm text-gray-400">No appointments</p>
            @endforelse
        </div>

        {{-- 🧠 Focus --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Focus Time</p>
            <p class="text-lg font-bold">{{ $focusMinutes }} mins</p>
        </div>

        {{-- 📓 Journal --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Journal</p>
            <a href="{{ route('journal.index') }}" class="text-blue-500">
                {{ $journalExists ? 'View / Edit Entry' : 'Write Today’s Entry' }}
            </a>
        </div>

    </div>
</x-app-layout>