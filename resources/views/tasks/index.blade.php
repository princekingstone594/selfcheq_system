<x-app-layout>
    <div class="max-w-2xl mx-auto p-4">

        <h1 class="text-2xl font-bold mb-4">Today's Tasks</h1>

        <!-- Add Task -->
        <form method="POST" action="{{ route('tasks.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="text" name="title" placeholder="New task..."
                class="flex-1 border rounded px-3 py-2">
            <button class="bg-blue-500 text-white px-4 py-2 rounded">Add</button>
        </form>

        <!-- Task List -->
        @foreach ($tasks as $task)
            <div class="flex items-center justify-between border-b py-2">
                
                <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                    @csrf
                    @method('PATCH')
                    <button>
                        @if ($task->is_completed)
                            ✅
                        @else
                            ⬜
                        @endif
                    </button>
                </form>

                <span class="{{ $task->is_completed ? 'line-through text-gray-400' : '' }}">
                    {{ $task->title }}
                </span>

                <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-500">X</button>
                </form>

            </div>
        @endforeach

    </div>
</x-app-layout>