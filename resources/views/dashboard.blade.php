<x-app-layout>
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-3">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Discipline OS</p>
                    <h1 class="text-3xl font-semibold text-white">Your day, organized with intention.</h1>
                    <p class="max-w-2xl text-sm text-slate-400">Stay focused on the promises that matter, keep your calendar aligned, and let your routine carry you through the day.</p>
                </div>

                <div class="rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-4 text-sm text-indigo-100">
                    <p class="font-medium">Today’s focus</p>
                    <p class="mt-1 text-xl font-semibold">{{ $taskCompleted }}/{{ $taskTotal }} tasks completed</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Discipline score</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $disciplineScore }}/100</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Current streak</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ auth()->user()->streak }} days</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Level</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ auth()->user()->level }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Focus time</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $focusMinutes }} mins</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">AI coach</p>
                            <p class="mt-1 text-lg font-medium text-white">Your calm accountability partner</p>
                        </div>
                        <form method="POST" action="{{ route('coach.mode') }}">
                            @csrf
                            <select name="mode" onchange="this.form.submit()" class="rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
                                <option value="strict" {{ auth()->user()->coach_mode == 'strict' ? 'selected' : '' }}>Strict</option>
                                <option value="calm" {{ auth()->user()->coach_mode == 'calm' ? 'selected' : '' }}>Calm</option>
                                <option value="aggressive" {{ auth()->user()->coach_mode == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                            </select>
                        </form>
                    </div>

                    <div class="mt-5 rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-4 text-sm text-indigo-100">
                        <p class="font-medium">Today’s guidance</p>
                        <p class="mt-2">{{ $coachMessage }}</p>
                    </div>

                    <div class="mt-5 rounded-2xl border border-white/10 bg-slate-800/70 p-4">
                        <p id="coachMessage" class="text-sm text-slate-300">{{ $coachMessage }}</p>
                        <button onclick="startListening()" class="mt-4 rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                            🎙️ Talk to coach
                        </button>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Today’s momentum</p>
                            <p class="mt-1 text-lg font-medium text-white">The next moves that matter most</p>
                        </div>
                    </div>
                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4">
                            <p class="text-sm font-semibold text-rose-200">⏱️ Do now</p>
                            @foreach($doNow as $task)
                                <p class="mt-2 text-sm text-rose-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4">
                            <p class="text-sm font-semibold text-amber-200">📅 Schedule</p>
                            @foreach($schedule as $task)
                                <p class="mt-2 text-sm text-amber-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-sky-400/20 bg-sky-500/10 p-4">
                            <p class="text-sm font-semibold text-sky-200">🤝 Delegate</p>
                            @foreach($delegate as $task)
                                <p class="mt-2 text-sm text-sky-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-slate-400/20 bg-slate-800/70 p-4">
                            <p class="text-sm font-semibold text-slate-200">🗑️ Eliminate</p>
                            @foreach($eliminate as $task)
                                <p class="mt-2 text-sm text-slate-100">• {{ $task->title }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Daily rhythm</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">📖 Devotional</p>
                            <a href="{{ route('devotional.today') }}" class="mt-2 inline-block text-indigo-300">Open today’s guide</a>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">📝 Journal</p>
                            <a href="{{ route('journal.index') }}" class="mt-2 inline-block text-indigo-300">{{ $journalExists ? 'View / edit entry' : 'Write today’s entry' }}</a>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🔁 Routines</p>
                            <p class="mt-2">{{ $routineCompleted }}/{{ $routineTotal }} completed</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🗓️ Appointments</p>
                            @forelse($appointments as $a)
                                <p class="mt-2 text-sm">{{ \Carbon\Carbon::parse($a->time)->format('H:i') }} — {{ $a->title }}</p>
                            @empty
                                <p class="mt-2 text-sm text-slate-400">No appointments today</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress & rewards</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🌟 Smart nudges</p>
                            @foreach($nudges as $nudge)
                                <p class="mt-2">• {{ $nudge }}</p>
                            @endforeach
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🎖️ Badges</p>
                            @forelse(auth()->user()->badges as $badge)
                                <p class="mt-2">• {{ $badge->name }}</p>
                            @empty
                                <p class="mt-2 text-slate-400">No badges yet</p>
                            @endforelse
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                            <p class="font-medium text-white">🎂 Birthdays</p>
                            @forelse($birthdays as $b)
                                <p class="mt-2">• {{ $b->name }} ({{ $b->relationship }})</p>
                            @empty
                                <p class="mt-2 text-slate-400">No birthdays today</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Tasks (last 7 days)</p>
                <canvas id="taskChart" class="mt-4 h-64 w-full"></canvas>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Mood (last 7 days)</p>
                <canvas id="moodChart" class="mt-4 h-64 w-full"></canvas>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const taskCtx = document.getElementById('taskChart');

    if (taskCtx) {
        new Chart(taskCtx, {
            type: 'bar',
            data: {
                labels: @json($taskLabels),
                datasets: [{
                    label: 'Tasks Completed',
                    data: @json($taskChart),
                    borderWidth: 1,
                    backgroundColor: 'rgba(129,140,248,0.7)',
                    borderColor: 'rgba(129,140,248,1)'
                }]
            }
        });
    }

    const moodCtx = document.getElementById('moodChart');

    if (moodCtx) {
        new Chart(moodCtx, {
            type: 'line',
            data: {
                labels: @json($taskLabels),
                datasets: [{
                    label: 'Mood',
                    data: @json($moodChart),
                    borderWidth: 2,
                    borderColor: 'rgba(45,212,191,1)',
                    backgroundColor: 'rgba(45,212,191,0.2)'
                }]
            }
        });
    }
    </script>

    <script>
    function startListening() {
        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'en-US';
        recognition.start();
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            sendToCoach(transcript);
        };
    }
    </script>

    <script>
    function sendToCoach(message) {
        fetch('/ai-coach-chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message })
        })
        .then(res => res.json())
        .then(data => {
            speak(data.reply);
            document.getElementById('coachMessage').innerText = data.reply;
        });
    }
    </script>

    <script>
    function speak(text) {
        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = 'en-US';
        speech.rate = 1;
        window.speechSynthesis.speak(speech);
    }
    </script>

    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
    </script>
</x-app-layout>