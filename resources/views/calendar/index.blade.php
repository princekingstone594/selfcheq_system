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

            <div class="mt-4 space-y-3">
                @forelse($appointments->flatten()->sortBy(function ($a) {
                        return $a->date . ' ' . $a->time;
                    }) as $appt)
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-800/50 p-3">
                        <span class="rounded-xl bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                            {{ \Carbon\Carbon::parse($appt->date)->format('D d') }} · {{ $appt->formatted_time }}
                        </span>
                        <span class="text-sm {{ $appt->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">
                            {{ $appt->title }}
                        </span>
                    </div>
                @empty
                    @forelse($tasks->flatten()->sortBy('due_date') as $task)
                        <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-800/50 p-3">
                            <span class="rounded-xl bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                                {{ \Carbon\Carbon::parse($task->due_date)->format('D d') }}
                            </span>
                            <span class="text-sm {{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">
                                {{ $task->title }}
                            </span>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/10 p-6 text-center text-sm text-slate-500">
                            No appointments or tasks scheduled for this week.
                        </div>
                    @endforelse
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
