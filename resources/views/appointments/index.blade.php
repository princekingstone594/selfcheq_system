<x-app-layout>
    <div class="max-w-xl mx-auto py-6">

        <h1 class="text-xl font-bold mb-4">Today's Schedule</h1>

        <form method="POST" action="{{ route('appointments.store') }}" class="mb-4 flex gap-2">
            @csrf
            <input type="time" name="time" class="border rounded px-2 py-1">
            <input type="text" name="title" placeholder="Appointment..."
                class="border rounded px-3 py-1 flex-1">
            <button class="bg-blue-500 text-white px-3 rounded">Add</button>
        </form>

        <ul>
            @foreach($appointments as $appointment)
                <li class="flex justify-between items-center mb-2">
                    
                    <div>
                        <span class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                        </span>
                        <span class="{{ $appointment->is_completed ? 'line-through text-gray-400' : '' }}">
                            {{ $appointment->title }}
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('appointments.toggle', $appointment) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-green-500 text-sm">
                                {{ $appointment->is_completed ? 'Undo' : 'Done' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-sm">Delete</button>
                        </form>
                    </div>

                </li>
            @endforeach
        </ul>

    </div>
</x-app-layout>