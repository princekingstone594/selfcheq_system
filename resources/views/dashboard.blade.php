<x-app-layout>
    <div class="space-y-6">
        <!-- Hero Section -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-indigo-950/20 to-slate-900 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl">
            <!-- Animated background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-indigo-500/10 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-purple-500/10 blur-3xl"></div>
            </div>

            <!-- Content -->
            <div class="relative p-6 sm:p-8 lg:p-10">
                <!-- Profile photo -->
                @php
                    $nameParts = explode(' ', auth()->user()->name);
                    $initials = strtoupper(substr($nameParts[0], 0, 1)) . ($nameParts[1] ? strtoupper(substr($nameParts[1], 0, 1)) : '');
                @endphp
                
                @if(auth()->user()->profile_photo_url && file_exists(public_path('storage/' . auth()->user()->profile_photo_url)))
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_url) }}"
                         alt="{{ auth()->user()->name }}"
                         class="absolute right-6 top-6 h-14 w-14 rounded-2xl border-2 border-white/20 object-cover shadow-xl ring-4 ring-indigo-500/20 sm:right-8 sm:top-8 sm:h-16 sm:w-16 lg:right-10 lg:top-10" />
                @else
                    <div class="absolute right-6 top-6 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-white/20 bg-indigo-500/20 text-xl font-semibold text-indigo-300 shadow-xl ring-4 ring-indigo-500/20 sm:right-8 sm:top-8 sm:h-16 sm:w-16 lg:right-10 lg:top-10">
                        {{ $initials }}
                    </div>
                @endif

                <!-- Brand + headline -->
                <div class="space-y-3 pr-16 sm:pr-20">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-400/30 bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-300 backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-indigo-400 animate-pulse"></span>
                        SelfCheq
                    </div>
                    <h1 class="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                        Own your Day,<br />Stay Ahead.
                    </h1>
                </div>

                <!-- Welcome + quote -->
                <div class="mt-4 space-y-2">
                    <p class="text-xl font-semibold text-white sm:text-2xl">Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</p>
                    <p class="max-w-2xl text-sm italic text-slate-300">"Discipline is the bridge between goals and accomplishment." — Jim Rohn</p>
                </div>

                <!-- Today's focus button (redirects to daily summary page) -->
                <div class="mt-6">
                    <a href="{{ route('focus.today') }}"
                       class="group inline-flex items-center gap-2 rounded-2xl border border-indigo-400/30 bg-indigo-500/10 px-5 py-3 text-sm font-semibold text-indigo-100 backdrop-blur-sm transition-all hover:scale-105 hover:bg-indigo-500/20 hover:border-indigo-400/50 hover:shadow-lg hover:shadow-indigo-500/20">
                        <svg class="h-5 w-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Today's focus</span>
                        <span class="ml-1 text-xs text-indigo-300/80 group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>

                <!-- 🕐 Live Clock & Date (bottom-right) -->
                <div class="absolute bottom-4 right-4 sm:bottom-8 sm:right-8 lg:bottom-10 lg:right-10"
                     x-data="{ now: new Date() }"
                     x-init="setInterval(() => now = new Date(), 1000)">
                    <div class="rounded-xl border border-white/10 bg-slate-950/70 px-3 py-2 text-right backdrop-blur-md sm:rounded-2xl sm:px-5 sm:py-3 lg:rounded-2xl">
                        <p class="text-sm font-bold tracking-wider text-white sm:text-lg lg:text-xl xl:text-3xl"
                           x-text="now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true })">
                        </p>
                        <p class="mt-0.5 text-[8px] font-medium text-slate-300 sm:mt-1 sm:text-[10px] lg:text-xs xl:text-sm"
                           x-text="now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })">
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Daily Rhythm -->
        <section class="space-y-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Daily rhythm</p>
                <p class="mt-1 text-lg font-semibold text-white">A snapshot of your day</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <!-- ✅ Tasks -->
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-emerald-400/30 hover:shadow-xl hover:shadow-emerald-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-white">✅ Tasks</p>
                            <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-300">{{ $taskCompleted }}/{{ $taskTotal }}</span>
                        </div>
                        @forelse($tasksToday->take(3) as $task)
                            <div class="mt-3 flex items-center justify-between text-xs {{ $task->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">
                                <span class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $task->is_completed ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                    {{ $task->title }}
                                </span>
                                @if($task->alarm_enabled)
                                    <span class="text-amber-400" title="Alarm: {{ $task->formatted_alarm_time }}">⏰</span>
                                @endif
                            </div>
                        @empty
                            <p class="mt-3 text-xs text-slate-500">No tasks yet</p>
                        @endforelse
                        <a href="{{ route('tasks.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                            See more <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <!-- 🔁 Routines -->
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-purple-400/30 hover:shadow-xl hover:shadow-purple-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-white">🔁 Routines</p>
                            <span class="rounded-full bg-purple-500/20 px-2.5 py-0.5 text-xs font-bold text-purple-300">{{ $routineCompleted }}/{{ $routineTotal }}</span>
                        </div>
                        @forelse($routines->take(3) as $routine)
                            <div class="mt-3 flex items-center justify-between text-xs {{ $routine->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">
                                <span class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $routine->is_completed ? 'bg-purple-400' : 'bg-slate-500' }}"></span>
                                    {{ $routine->title }}
                                </span>
                                @if($routine->reminder_time)
                                    <span class="text-amber-400" title="Alarm: {{ $routine->formatted_alarm_time }}">⏰</span>
                                @endif
                            </div>
                        @empty
                            <p class="mt-3 text-xs text-slate-500">No routines today</p>
                        @endforelse
                        <a href="{{ route('routines.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                            See more <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <!-- 🗓️ Appointments -->
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-sky-400/30 hover:shadow-xl hover:shadow-sky-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">🗓️ Appointments</p>
                        @forelse($appointments->take(2) as $a)
                            <div class="mt-3 flex items-center gap-2 text-sm text-slate-300">
                                <span class="rounded-lg bg-sky-500/20 px-2 py-1 text-xs font-medium text-sky-300">{{ \Carbon\Carbon::parse($a->time)->format('H:i') }}</span>
                                <span>{{ $a->title }}</span>
                            </div>
                        @empty
                            <p class="mt-3 text-sm text-slate-400">No appointments today</p>
                        @endforelse
                        <a href="{{ route('appointments.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                            See more <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <!-- 📖 Devotional verse -->
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-indigo-400/30 hover:shadow-xl hover:shadow-indigo-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">📖 Devotional</p>
                        @if($devotional)
                            <p class="mt-3 text-sm italic text-slate-300 line-clamp-3">{{ $devotional->content }}</p>
                            <a href="{{ route('devotional.today') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                                See more <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        @else
                            <p class="mt-3 text-sm text-slate-400">No verse for today.</p>
                            <a href="{{ route('devotional.today') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                                Open devotional <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 📝 Journal -->
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-amber-400/30 hover:shadow-xl hover:shadow-amber-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">📝 Journal</p>
                        @if($todayJournal)
                            <p class="mt-2 text-xs text-indigo-300">{{ \Carbon\Carbon::parse($todayJournal->date)->format('d M Y') }}</p>
                            <p class="mt-1 text-sm text-slate-300 line-clamp-2">{{ $todayJournal->snip }}</p>
                            <a href="{{ route('journal.index') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                                View / edit entry <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        @else
                            <p class="mt-3 text-sm text-slate-400">No entry yet today.</p>
                            <a href="{{ route('journal.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                                Write today's entry <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 🗓️ My Calendar -->
                <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-rose-400/30 hover:shadow-xl hover:shadow-rose-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">🗓️ My Calendar</p>
                        @forelse($upcomingAppointments->take(2) as $a)
                            <div class="mt-3 flex items-start gap-2 text-sm text-slate-300">
                                <span class="rounded-lg bg-rose-500/20 px-2 py-1 text-xs font-medium text-rose-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($a->date)->format('D d M') }}</span>
                                <span class="flex-1">{{ $a->formatted_time }} — {{ $a->title }}</span>
                            </div>
                        @empty
                            <p class="mt-3 text-xs text-slate-500">No upcoming appointments.</p>
                        @endforelse
                        <a href="{{ route('calendar.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                            See more <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <!-- 📊 Progress -->
                <div class="group relative overflow-hidden rounded-2xl border border-indigo-400/20 bg-gradient-to-br from-indigo-500/10 to-indigo-600/5 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-indigo-400/50 hover:shadow-xl hover:shadow-indigo-500/20">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">📊 Progress</p>
                        <p class="mt-2 text-sm text-indigo-100">View your discipline score, streak, level, focus time, charts and rewards.</p>
                        <a href="{{ route('progress.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 hover:text-indigo-200 transition">
                            Open progress <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <!-- 💰 Financials -->
                <div class="group relative overflow-hidden rounded-2xl border border-emerald-400/20 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-emerald-400/30 hover:shadow-xl hover:shadow-emerald-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">💰 Financials</p>
                        @if($financials->count() > 0)
                            <div class="mt-3 space-y-2">
                                @foreach($financials->take(3) as $financial)
                                    <div class="flex items-center gap-2 text-xs {{ $financial->is_completed ? 'line-through text-slate-500' : 'text-slate-300' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $financial->is_completed ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                        {{ $financial->title }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-3 text-xs text-slate-500">No financial entries yet.</p>
                        @endif
                        <a href="{{ route('financials.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-300 hover:text-emerald-200 transition">
                            Manage finances <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <!-- 📝 Notepad -->
                <div class="group relative overflow-hidden rounded-2xl border border-amber-400/20 bg-gradient-to-br from-slate-800/80 to-slate-800/40 p-5 backdrop-blur-sm transition-all hover:scale-[1.02] hover:border-amber-400/30 hover:shadow-xl hover:shadow-amber-500/10">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <p class="font-semibold text-white">📝 Notepad</p>
                        @if($recentNote)
                            @if($recentNote->title)
                                <p class="mt-2 text-sm font-medium text-amber-200">{{ $recentNote->title }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-300 line-clamp-2">{{ $recentNote->content }}</p>
                            <a href="{{ route('notes.index') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-amber-300 hover:text-amber-200 transition">
                                Open notepad <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        @else
                            <p class="mt-3 text-xs text-slate-500">No notes yet.</p>
                            <a href="{{ route('notes.index') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-amber-300 hover:text-amber-200 transition">
                                Create note <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
