<x-app-layout>
    <div class="max-w-xl mx-auto py-6">

        <h1 class="text-xl font-bold mb-4">Morning Routine</h1>

        <form method="POST" action="{{ route('routines.store') }}" class="mb-4">
            @csrf
            <input type="text" name="title" placeholder="Add routine..."
                class="border rounded px-3 py-2 w-full">
        </form>

        <ul>
            @foreach($routines as $routine)
                <li class="flex justify-between items-center mb-2">
                    <span class="{{ $routine->is_completed ? 'line-through text-gray-400' : '' }}">
                        {{ $routine->title }}
                    </span>

                    <form method="POST" action="{{ route('routines.toggle', $routine) }}">
                        @csrf
                        @method('PATCH')
                        <button class="text-sm text-blue-500">
                            {{ $routine->is_completed ? 'Undo' : 'Done' }}
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>

    </div>
</x-app-layout>