<x-app-layout>
    <div class="mx-auto max-w-6xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">My Calendar</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">
                        @if(request('view') === 'month')
                            {{ $start->format('F Y') }}
                        @else
                            Your Week at a Glance
                        @endif
                    </h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- View Toggle -->
                    <div class="flex rounded-2xl border border-white/10 bg-slate-800/80 p-1">
                        <a href="{{ route('calendar.index', array_merge(request()->query(), ['view' => 'week'])) }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-medium transition {{ (request('view') ?? 'week') === 'week' ? 'bg-indigo-500 text-white' : 'text-slate-300 hover:text-white' }}">
                            Week
                        </a>
                        <a href="{{ route('calendar.index', array_merge(request()->query(), ['view' => 'month'])) }}"
                           class="rounded-xl px-3 py-1.5 text-xs font-medium transition {{ request('view') === 'month' ? 'bg-indigo-500 text-white' : 'text-slate-300 hover:text-white' }}">
                            Month
                        </a>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center gap-2">
                        @if(request('view') === 'month')
                            <a href="{{ route('calendar.index', array_merge(request()->query(), ['month' => $prevMonth])) }}"
                               class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                                ← Prev
                            </a>
                            <a href="{{ route('calendar.index', ['view' => 'month']) }}"
                               class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                                Today
                            </a>
                            <a href="{{ route('calendar.index', array_merge(request()->query(), ['month' => $nextMonth])) }}"
                               class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                                Next →
                            </a>
                        @else
                            <a href="{{ route('calendar.index', ['week' => $prevWeek]) }}"
                               class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                                ← Prev
                            </a>
                            <a href="{{ route('calendar.index') }}"
                               class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                                Today
                            </a>
                            <a href="{{ route('calendar.index', ['week' => $nextWeek]) }}"
                               class="rounded-2xl border border-white/10 bg-slate-800/80 px-3 py-1.5 text-sm text-slate-300 hover:text-white hover:border-indigo-400/30 transition">
                                Next →
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Calendar grid -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            @if(request('view') === 'month')
                <!-- Month View -->
                <div class="grid grid-cols-7 gap-px text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                    <div>Sun</div>
                </div>
                <div class="grid grid-cols-7 gap-px border-t border-white/10">
                    @php
                        $today = \Carbon\Carbon::today();
                    @endphp

                    @for ($i = 0; $i < $days->count(); $i++)
                        @php
                            $day = $days[$i];
                            $isToday = $day->isSameDay($today);
                            $isPast = $day->lt($today->copy()->startOfDay());
                            $dayStr = $day->toDateString();
                            $dayAppointments = $appointments[$dayStr] ?? collect();
                            $dayTasks = $tasks[$dayStr] ?? collect();
                            $dayRoutines = $routines[$dayStr] ?? collect();
                        @endphp

                        <div class="border-r border-b border-white/5 p-2 align-top text-left min-h-[100px] {{ $isPast ? 'opacity-50' : '' }}">
                            <div class="mb-1 text-right text-xs font-semibold {{ $isToday ? 'text-indigo-400' : 'text-slate-500' }}">
                                {{ $day->format('d') }}
                            </div>

                            @if($dayAppointments->isNotEmpty())
                                <div class="space-y-0.5">
                                    @foreach($dayAppointments->take(2) as $appt)
                                        <div class="rounded-md bg-indigo-500/10 px-1.5 py-0.5 text-xs text-indigo-200 truncate"
                                             title="{{ $appt->title }} at {{ $appt->formatted_time }}">
                                            🗓️ {{ \Carbon\Carbon::parse($appt->time)->format('g:i a') }} {{ Str::limit($appt->title, 10) }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($dayRoutines->isNotEmpty())
                                <div class="space-y-0.5">
                                    @foreach($dayRoutines->take(2) as $routine)
                                        <div class="rounded-md bg-purple-500/10 px-1.5 py-0.5 text-xs text-purple-200 truncate"
                                             title="{{ $routine->title }}">
                                            🔁 {{ Str::limit($routine->title, 10) }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($dayTasks->isNotEmpty())
                                <div class="mt-1 space-y-0.5">
                                    @foreach($dayTasks->take(2) as $task)
                                        <div class="rounded-md bg-slate-700/50 px-1.5 py-0.5 text-xs text-slate-300 truncate"
                                             title="{{ $task->title }}">
                                            ✅ {{ Str::limit($task->title, 10) }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            @else
                <!-- Week View -->
                <div class="grid grid-cols-7 gap-px text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                    <div>Sun</div>
                </div>

                <div class="grid grid-cols-7 gap-px border-t border-white/10">
                    @php
                        $today = \Carbon\Carbon::today();
                    @endphp

                    @for ($i = 0; $i < $days->count(); $i++)
                        @php
                            $day = $days[$i];
                            $isToday = $day->isSameDay($today);
                            $isPast = $day->lt($today->copy()->startOfDay());
                            $dayStr = $day->toDateString();
                            $dayAppointments = $appointments[$dayStr] ?? collect();
                            $dayTasks = $tasks[$dayStr] ?? collect();
                            $dayRoutines = $routines[$dayStr] ?? collect();
                        @endphp

                        <div class="border-r border-b border-white/5 p-2 align-top text-left min-h-[120px] {{ $isPast ? 'opacity-50' : '' }}">
                            <div class="mb-1 text-right text-xs font-semibold {{ $isToday ? 'text-indigo-400' : 'text-slate-500' }}">
                                {{ $day->format('d') }}
                            </div>

                            @if($dayAppointments->isNotEmpty())
                                <div class="space-y-0.5">
                                    @foreach($dayAppointments->take(3) as $appt)
                                        <div class="rounded-md bg-indigo-500/10 px-1.5 py-0.5 text-xs text-indigo-200 truncate"
                                             title="{{ $appt->title }} at {{ $appt->formatted_time }}">
                                            🗓️ {{ \Carbon\Carbon::parse($appt->time)->format('g:i a') }} {{ Str::limit($appt->title, 12) }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($dayRoutines->isNotEmpty())
                                <div class="space-y-0.5">
                                    @foreach($dayRoutines->take(2) as $routine)
                                        <div class="rounded-md bg-purple-500/10 px-1.5 py-0.5 text-xs text-purple-200 truncate"
                                             title="{{ $routine->title }}">
                                            🔁 {{ Str::limit($routine->title, 12) }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($dayTasks->isNotEmpty())
                                <div class="mt-1 space-y-0.5">
                                    @foreach($dayTasks->take(2) as $task)
                                        <div class="rounded-md bg-slate-700/50 px-1.5 py-0.5 text-xs text-slate-300 truncate"
                                             title="{{ $task->title }}">
                                            ✅ {{ Str::limit($task->title, 12) }}
                                        </div>
                                    @endforeach
                                    @if($dayTasks->count() > 2)
                                        <div class="text-xs text-slate-500">+{{ $dayTasks->count() - 2 }} more</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            @endif
        </section>

        <!-- Upcoming (list view) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Up next</p>

            @php
                // Merge all items into a single sorted list
                $upcoming = collect();

                foreach ($appointments->flatten() as $appt) {
                    $upcoming->push((object) [
                        'sort_date' => $appt->date . ' ' . ($appt->time ?? '00:00'),
                        'type' => 'appointment',
                        'title' => $appt->title,
                        'date' => $appt->date,
                        'time' => $appt->time,
                        'is_completed' => $appt->is_completed,
                    ]);
                }

                foreach ($routines->flatten() as $routine) {
                    $upcoming->push((object) [
                        'sort_date' => \Carbon\Carbon::parse($routine->date)->format('Y-m-d') . ' 00:00',
                        'type' => 'routine',
                        'title' => $routine->title,
                        'date' => $routine->date,
                        'time' => null,
                        'is_completed' => $routine->is_completed,
                    ]);
                }

                foreach ($tasks->flatten() as $task) {
                    $upcoming->push((object) [
                        'sort_date' => \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') . ' 00:00',
                        'type' => 'task',
                        'title' => $task->title,
                        'date' => $task->due_date,
                        'time' => null,
                        'is_completed' => $task->is_completed,
                    ]);
                }

                $upcoming = $upcoming->sortBy('sort_date')->take(15);
            @endphp

            <div class="mt-4 space-y-3">
                @forelse($upcoming as $item)
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-800/50 p-3">
                        <span class="rounded-xl px-2.5 py-1 text-xs font-medium {{ $item->type === 'appointment' ? 'bg-indigo-500/10 text-indigo-300' : ($item->type === 'routine' ? 'bg-purple-500/10 text-purple-300' : 'bg-slate-700/50 text-slate-300') }}">
                            {{ \Carbon\Carbon::parse($item->date)->format('D d') }}
                            @if($item->time)
                                · {{ \Carbon\Carbon::parse($item->time)->format('g:i a') }}
                            @endif
                        </span>
                        <span class="text-sm {{ $item->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">
                            @if($item->type === 'routine') 🔁 @endif
                            @if($item->type === 'appointment') 🗓️ @endif
                            {{ $item->title }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                        No appointments, routines or tasks scheduled for this period.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
