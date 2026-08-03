<x-app-layout>
    <div class="max-w-2xl mx-auto py-6 space-y-6">

        <h1 class="text-2xl font-bold">SelfCheq Dashboard</h1>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">🎯 Discipline Score</p>
            <p class="text-3xl font-bold">{{ $disciplineScore }}/100</p>
        </div>

        <div class="bg-indigo-100 p-4 rounded shadow">
            <p class="text-sm text-gray-600 mb-2">🤖 AI Coach</p>
            <p class="text-sm">{{ $coachMessage }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600 mb-2">💭 Smart Guidance</p>

            @foreach($nudges as $nudge)
                <p class="text-sm mb-1">• {{ $nudge }}</p>
            @endforeach
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">🏆 Level</p>
            <p class="text-3xl font-bold">Level {{ auth()->user()->level }}</p>

            <p class="text-sm text-gray-600">XP</p>
            <p class="text-lg font-bold">{{ auth()->user()->xp }} XP</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600 mb-2">🎖️ Badges</p>

            @forelse(auth()->user()->badges as $badge)
                <p class="text-sm">🎖️ {{$badhe->name }}</p>
            @empty
                <p class="text-sm text-gray-400">No badges earned yet</p>
            @endforelse
        </div>

        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600 mb-2">🎂 Today's Birthdays</p>

            @forelse($birthdays as $b)
                <p>🎉 {{ $b->name }} ({{ $b->relationship }})</p>
            @empty
                <p class="text-sm text-gray-400">No birthdays today</p>
            @endforelse
        </div>

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

        <div class="grid grid-cols-2 gap-4">

            <div class="bg-red-100 p-3 rounded">
                <p class="font-bold">⏱️ Do Now</p>
                @foreach($doNow as $task)
                    <p>{{ $task->title }}</p>
                @endforeach
            </div>

            <div class="bg-yellow-100 p-3 rounded">
                <p class="font-bold">📅 Schedule</p>
                @foreach($schedule as $task)
                    <p>{{ $task->title }}</p>
                @endforeach
            </div>

            <div class="bg-blue-100 p-3 rounded">
                <p class="font-bold">🤝 Delegate</p>
                @foreach($delegate as $task)
                    <p>{{ $task->title }}</p>
                @endforeach
            </div>

            <div class="bg-gray-100 p-3 rounded">
                <p class="font-bold">🗑️ Eliminate</p>
                @foreach($eliminate as $task)
                    <p>{{ $task->title }}</p>
                @endforeach
            </div>

        </div>

        {{-- 😊 Mood --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Avg Mood (7 days)</p>
            <p class="text-lg font-bold">
                {{ $moodAvg ?? '—' }}
            </p>
        </div>

        {{-- 📊 Weekly Tasks --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Tasks Completed (7 days)</p>
            <p class="text-lg font-bold">{{ $weeklyTasks }}</p>
        </div>

        {{-- 📖 Devotional --}}
        <div class="bg-white p-4 rounded shadow">
            <p class="text-sm text-gray-600">Daily Devotional</p>
            <a href="{{ route('devotional.today') }}" class="text-blue-500">
                View Today's Devotional
            </a>
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

    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-sm text-gray-600 mb-2">Tasks (Last 7 Days)</h2>
        <canvas id="taskChart" width="400" height="200"></canvas>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-sm text-gray-600 mb-2">Mood (Last 7 Days)</h2>
        <canvas id="moodChart" width="400" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const taskCtx = document.getElementById('taskChart');

    new Chart(taskCtx, {
        type: 'bar',
        data: {
            labels: @json($taskLabels),
            datasets: [{
                label: 'Tasks Completed',
                data: @json($taskChart),
                borderWidth: 1
            }]
        }
    });

    const moodCtx = document.getElementById('moodChart');

    new Chart(moodCtx, {
        type: 'line',
        data: {
            labels: @json($taskLabels),
            datasets: [{
               label: 'Mood',
               data: @json($moodChart),
               borderWidth: 2
            }]
        }
    });
    </script>
</x-app-layout>