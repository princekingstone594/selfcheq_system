<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">Your discipline journey at a glance</h1>
                    <p class="mt-2 text-sm text-slate-400">Track your scores, streaks, mood and momentum over time.</p>
                </div>
                <button onclick="openShareModal()"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:border-indigo-400/30 transition"
                        title="Share your progress">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Share
                </button>
            </div>
        </section>

        <!-- Stat cards -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Discipline score</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ $disciplineScore ?? 0 }}/100
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Current streak</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ auth()->user()->streak ?? 0 }} days
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Level</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ auth()->user()->level ?? 1 }}
                </p>
                <p class="text-xs text-slate-400">XP: {{ auth()->user()->xp ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                <p class="text-sm text-slate-400">Focus time</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ $focusMinutes ?? 0 }} mins
                </p>
            </div>
            </section>

            <!-- Simple Stats -->
            <section class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">This Week</p>
                    <p class="mt-3 text-2xl font-bold text-white">{{ array_sum($taskChart ?? []) }}</p>
                    <p class="text-xs text-slate-400">tasks completed</p>
                    <div class="mt-3 h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full rounded-full bg-indigo-500 transition-all" style="width: {{ $taskTotal > 0 ? min(100, (array_sum($taskChart ?? []) / max(1, $taskTotal)) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Mood Trend</p>
                    <p class="mt-3 text-2xl font-bold text-white">{{ $moodAvg ? round($moodAvg, 1) : 'N/A' }}/5</p>
                    <p class="text-xs text-slate-400">average mood</p>
                    <div class="mt-3 flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="flex-1 h-2 rounded-full {{ $i <= ($moodAvg ?? 0) ? 'bg-emerald-500' : 'bg-slate-800' }}"></div>
                        @endfor
                    </div>
                </div>
            </section>

        <!-- Nudges & rewards -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress & rewards</p>

            <div class="mt-4 space-y-3 text-sm text-slate-300">

                <!-- Nudges -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                    <p class="font-medium text-white">Smart nudges</p>

                    @forelse($nudges ?? [] as $nudge)
                        <p class="mt-2">• {{ $nudge }}</p>
                    @empty
                        <p class="mt-2 text-slate-400">No nudges available</p>
                    @endforelse
                </div>

                <!-- Badges -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                    <p class="font-medium text-white">Badges</p>

                    @php
                        $badges = auth()->user()->badges ?? collect();
                    @endphp

                    @forelse($badges as $badge)
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-2xl">{{ $badge->icon ?? '🏅' }}</span>
                            <div>
                                <p class="font-medium text-white">{{ $badge->name }}</p>
                                <p class="text-xs text-slate-400">{{ $badge->description ?? '' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="mt-2 text-slate-400">No badges yet — complete tasks to earn them! 🏅</p>
                    @endforelse
                </div>

                <!-- Birthdays -->
                <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-3">
                    <p class="font-medium text-white">Birthdays</p>

                    @forelse($birthdays ?? [] as $b)
                        <p class="mt-2">• {{ $b->name }} ({{ $b->relationship }})</p>
                    @empty
                        <p class="mt-2 text-slate-400">No birthdays today</p>
                    @endforelse
                </div>

            </div>
        </section>

    </div>

    <!-- Share Modal -->
    <div id="shareModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeShareModal()"></div>
        <div class="relative w-full max-w-md rounded-3xl border border-white/10 bg-slate-900 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                <p class="text-sm font-semibold text-white">Share your progress</p>
                <button onclick="closeShareModal()" class="text-slate-400 hover:text-white transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(Auth::user()->profile_photo_url && file_exists(public_path('storage/' . Auth::user()->profile_photo_url)))
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_url) }}" alt="{{ Auth::user()->name }}" class="h-12 w-12 rounded-full border-2 border-indigo-400/50 object-cover" />
                            @else
                                @php
                                    $nameParts = explode(' ', Auth::user()->name);
                                    $initials = strtoupper(substr($nameParts[0], 0, 1)) . ($nameParts[1] ? strtoupper(substr($nameParts[1], 0, 1)) : '');
                                @endphp
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-indigo-400/50 bg-indigo-500/20 text-lg font-bold text-indigo-300">
                                    {{ $initials }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-400">SelfCheq Discipline Report</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-application-logo class="h-8 w-8" style="display: block !important; visibility: visible !important; opacity: 1 !important;" />
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-300">SelfCheq</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <div class="rounded-xl border border-white/10 bg-slate-800/50 p-3 text-center">
                            <p class="text-2xl font-bold text-white">{{ auth()->user()->streak ?? 0 }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400">Day Streak</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-slate-800/50 p-3 text-center">
                            <p class="text-2xl font-bold text-white">{{ auth()->user()->level ?? 1 }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400">Level</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-slate-800/50 p-3 text-center">
                            <p class="text-2xl font-bold text-indigo-300">{{ $disciplineScore ?? 0 }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400">Score /100</p>
                        </div>
                    </div>

                    <p class="mt-4 text-center text-xs italic text-slate-400">"Building better habits, one day at a time."</p>
                </div>
            </div>

            <div class="px-6 pb-6 flex gap-3">
                <button onclick="shareProgress()" class="flex-1 rounded-2xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Share
                </button>
                <button onclick="copyShareText()" class="flex-1 rounded-2xl border border-white/10 bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                    Copy Text
                </button>
            </div>
        </div>
    </div>

    <script>
    function openShareModal() {
        document.getElementById('shareModal').classList.remove('hidden');
        document.getElementById('shareModal').classList.add('flex');
    }

    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
        document.getElementById('shareModal').classList.remove('flex');
    }

    function getShareText() {
        const streak = {{ auth()->user()->streak ?? 0 }};
        const level = {{ auth()->user()->level ?? 1 }};
        const xp = {{ auth()->user()->xp ?? 0 }};
        const score = {{ $disciplineScore ?? 0 }};
        const name = '{{ Auth::user()->name }}';

        return `🏆 ${name} is on a ${streak}-day discipline streak on SelfCheq!\n\n📊 Level ${level} • ${xp} XP\n🎯 Discipline Score: ${score}/100\n\nBuilding better habits, one day at a time. #SelfCheq #Discipline`;
    }

    function shareProgress() {
        const text = getShareText();

        if (navigator.share) {
            navigator.share({
                title: 'My SelfCheq Progress',
                text: text,
                url: window.location.origin
            }).catch(() => {});
        } else {
            navigator.clipboard.writeText(text).then(() => {
                alert('Progress copied to clipboard!');
            }).catch(() => {
                window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text), '_blank');
            });
        }
    }

    function copyShareText() {
        const text = getShareText();
        navigator.clipboard.writeText(text).then(() => {
            alert('Share text copied to clipboard!');
        }).catch(() => {
            window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text), '_blank');
        });
    }
    </script>
</x-app-layout>
