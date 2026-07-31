<x-app-layout>
    <div class="max-w-2xl mx-auto p-4">

        <h1 class="text-2xl font-bold mb-4">Today's Tasks</h1>
        
        <p class="text-gray-500 mb-4">
            {{ $today->format('1, d M Y') }}
        </p>

        <div class="mb-4">
            <p class="text-sm text-gray-600">
                🔥 Streak: {{ auth()->user()->streak }} days 
            </p>
        </div>

        @if(auth()->user()->streak > 0)
            <p class="text-xs text-gray-500 mb-2">
                Keep going. Don't break the chain.
            </p>
        @endif

        <div class="mb-4">
            <p class="text-sm text-gray-600">
                Progress: {{ $completed }} / {{ $total }} ({{ $progress }}%)
           </p>

           <div class="w-full bg-gray-200 rounded-full h-3 mt-1">
               <div class="bg-green-500 h-3 rounded-full"
                    style="width: {{ $progress }}%">
                </div>
           </div>
        
        @if($total > 0 && $completed === $total)
           <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
              🎉 You completed all your tasks today. Stay disciplined. You can do this.
           </div>
        @endif

        <!-- Add Task -->
        <form method="POST" action="{{ route('tasks.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="text" name="title" placeholder="New task..."
                class="flex-1 border rounded px-3 py-2">
            <input type="date" name="due_date"
                class="border rounded px-2 py-2">
            <button class="bg-blue-500 text-white px-4 py-2 rounded">Add</button>
        </form>

        <!-- Task List -->
        @foreach ($tasks->sortBy('is_completed') as $task)
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