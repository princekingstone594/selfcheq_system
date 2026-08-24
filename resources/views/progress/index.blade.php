<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <!-- 🧑‍🚀 Reactive avatar — reflects your streak state -->
                    @php
                        $avatarMeta = match($avatarState) {
                            'glowing' => ['emoji' => '😄', 'ring' => 'ring-emerald-400/60', 'glow' => 'shadow-emerald-500/40', 'label' => 'Streak intact — keep it burning! 🔥'],
                            'tired'   => ['emoji' => '🥱', 'ring' => 'ring-rose-400/50',  'glow' => 'shadow-rose-500/30',  'label' => 'Streak broke — today is a fresh restart'],
                            'sleepy'  => ['emoji' => '😴', 'ring' => 'ring-indigo-400/40', 'glow' => 'shadow-indigo-500/20', 'label' => 'Early bird — your day is waiting'],
                            default   => ['emoji' => '🙂', 'ring' => 'ring-slate-500/40',  'glow' => '',                    'label' => 'Ready when you are'],
                        };
                    @endphp
                    <span title="{{ $avatarMeta['label'] }}"
                          class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-slate-800 text-2xl ring-4 {{ $avatarMeta['ring'] }} shadow-lg {{ $avatarMeta['glow'] }}">
                        {{ $avatarMeta['emoji'] }}
                        @if($avatarState === 'glowing')
                            <span class="absolute inset-0 animate-ping rounded-2xl bg-emerald-400/10"></span>
                        @endif
                    </span>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Progress</p>
                        <h1 class="mt-1 text-2xl font-semibold text-white">Your discipline journey at a glance</h1>
                        <p class="mt-2 text-sm text-slate-400">{{ $avatarMeta['label'] }}</p>
                    </div>
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

        <!-- 🔥 Discipline Heatmap -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">🔥 Discipline Heatmap</p>
                    <p class="mt-2 text-sm text-slate-400">Every square is a day. Tap one to revisit it.</p>
                </div>
                <div class="hidden items-center gap-1 text-[10px] text-slate-500 sm:flex">
                    Less
                    <span class="h-3 w-3 rounded-[4px] bg-slate-800"></span>
                    <span class="h-3 w-3 rounded-[4px] bg-indigo-950"></span>
                    <span class="h-3 w-3 rounded-[4px] bg-indigo-800"></span>
                    <span class="h-3 w-3 rounded-[4px] bg-indigo-500"></span>
                    <span class="h-3 w-3 rounded-[4px] bg-emerald-400"></span>
                    More
                </div>
            </div>

            @php
                $levelClass = fn($score) => match(true) {
                    $score === null => 'bg-slate-800 hover:bg-slate-700',
                    $score <= 0     => 'bg-slate-800 hover:bg-slate-700',
                    $score < 40     => 'bg-indigo-950 hover:bg-indigo-900',
                    $score < 70     => 'bg-indigo-800 hover:bg-indigo-700',
                    $score < 90     => 'bg-indigo-500 hover:bg-indigo-400',
                    default         => 'bg-emerald-400 hover:bg-emerald-300',
                };
            @endphp

            <div class="mt-5 overflow-x-auto pb-1">
                <div class="grid grid-flow-col grid-rows-7 gap-1" style="width: max-content;">
                    @foreach($heatmap as $cell)
                        <a href="{{ route('focus.today', ['date' => $cell['date']]) }}"
                           title="{{ $cell['day'] }} — {{ $cell['score'] !== null ? 'score '.$cell['score'] : 'no data' }}"
                           class="h-3.5 w-3.5 rounded-[4px] transition {{ $levelClass($cell['score']) }} {{ $cell['is_today'] ? 'ring-1 ring-white/60' : '' }}">
                            <span class="sr-only">{{ $cell['day'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 📈 Weekly Trend -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">📈 Weekly Trend</p>
            <p class="mt-2 text-sm text-slate-400">Discipline score over the last 7 days.</p>

            @php
                $maxScore = 100;
                $w = 280; $h = 90; $pad = 6;
                $step = count($trendScores) > 1 ? ($w - 2 * $pad) / (count($trendScores) - 1) : 0;
                $points = [];
                foreach ($trendScores as $i => $s) {
                    $x = $pad + $i * $step;
                    $y = $h - $pad - ($s / $maxScore) * ($h - 2 * $pad);
                    $points[] = [$x, $y, $s];
                }
                $polyline = collect($points)->map(fn($p) => round($p[0], 1).','.round($p[1], 1))->implode(' ');
            @endphp

            <div class="mt-5">
                <svg viewBox="0 0 {{ $w }} {{ $h }}" class="h-24 w-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="trendFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="rgb(99 102 241)" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="rgb(99 102 241)" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <polygon points="{{ $pad }},{{ $h - $pad }} {{ $polyline }} {{ $w - $pad }},{{ $h - $pad }}" fill="url(#trendFill)"/>
                    <polyline points="{{ $polyline }}" fill="none" stroke="rgb(129 140 248)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @foreach($points as $p)
                        <circle cx="{{ round($p[0], 1) }}" cy="{{ round($p[1], 1) }}" r="3"
                                fill="{{ $p[2] >= 70 ? 'rgb(52 211 153)' : 'rgb(129 140 248)' }}">
                            <title>{{ $p[2] }}/100</title>
                        </circle>
                    @endforeach
                </svg>
                <div class="mt-1 flex justify-between text-[10px] text-slate-500">
                    <span>7d ago</span>
                    <span>Today</span>
                </div>
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

            <!-- Shareable post preview (square format for stories/status) -->
            <div id="sharePostPreview" class="p-4">
                <div class="relative aspect-square rounded-3xl overflow-hidden bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 p-6 flex flex-col items-center justify-center text-center border border-white/10">
                    <!-- Decorative glow -->
                    <div class="absolute -top-16 -right-16 h-48 w-48 rounded-full bg-indigo-500/20 blur-3xl"></div>
                    <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-purple-500/20 blur-3xl"></div>

                    <!-- Profile pic - central -->
                    @if(Auth::user()->profile_photo_url && file_exists(public_path('storage/' . Auth::user()->profile_photo_url)))
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_url) }}" alt="{{ Auth::user()->name }}" class="relative h-20 w-20 rounded-full border-[3px] border-indigo-400/70 object-cover shadow-xl shadow-indigo-500/30" />
                    @else
                        @php
                            $nameParts = explode(' ', Auth::user()->name);
                            $initials = strtoupper(substr($nameParts[0], 0, 1)) . ($nameParts[1] ? strtoupper(substr($nameParts[1], 0, 1)) : '');
                        @endphp
                        <div class="relative flex h-20 w-20 items-center justify-center rounded-full border-[3px] border-indigo-400/70 bg-indigo-500/20 text-2xl font-bold text-indigo-300 shadow-xl shadow-indigo-500/30">
                            {{ $initials }}
                        </div>
                    @endif

                    <!-- Name -->
                    <p class="relative mt-3 text-base font-semibold text-white">{{ Auth::user()->name }}</p>
                    <p class="relative text-[10px] uppercase tracking-[0.2em] text-indigo-300/80">Discipline Report</p>

                    <!-- Stats -->
                    <div class="relative mt-4 w-full grid grid-cols-3 gap-2">
                        <div class="rounded-xl bg-white/5 backdrop-blur border border-white/10 p-2.5">
                            <p class="text-xl font-bold text-white">{{ auth()->user()->streak ?? 0 }}</p>
                            <p class="text-[9px] uppercase tracking-widest text-slate-400">Streak</p>
                        </div>
                        <div class="rounded-xl bg-white/5 backdrop-blur border border-white/10 p-2.5">
                            <p class="text-xl font-bold text-white">{{ auth()->user()->level ?? 1 }}</p>
                            <p class="text-[9px] uppercase tracking-widest text-slate-400">Level</p>
                        </div>
                        <div class="rounded-xl bg-indigo-500/10 backdrop-blur border border-indigo-400/30 p-2.5">
                            <p class="text-xl font-bold text-indigo-300">{{ $disciplineScore ?? 0 }}</p>
                            <p class="text-[9px] uppercase tracking-widest text-indigo-300/70">Score</p>
                        </div>
                    </div>

                    <!-- Quote -->
                    <p class="relative mt-3 text-[11px] italic text-slate-400">"Building better habits, one day at a time."</p>

                    <!-- SelfCheq branding -->
                    <div class="relative mt-4 flex items-center gap-2">
                        <x-application-logo class="h-6 w-6" style="display: block !important; visibility: visible !important; opacity: 1 !important;" />
                        <span class="text-[11px] font-bold uppercase tracking-[0.25em] text-indigo-300">SelfCheq</span>
                    </div>
                </div>
            </div>

            <div class="px-6 pb-4 flex flex-col gap-2.5">
                <!-- Download / Save Image -->
                <button onclick="downloadImage()" class="rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    💾 Download Image
                </button>

                <!-- Platform-specific sharing -->
                <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-white/10">
                    <button onclick="shareToWhatsAppStatus()" class="flex items-center justify-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2.5 text-sm font-semibold text-emerald-300 hover:bg-emerald-500/20 transition">
                        <span class="text-base">⬛</span> WhatsApp Status
                    </button>
                    <button onclick="shareToStories()" class="flex items-center justify-center gap-2 rounded-2xl border border-pink-500/20 bg-pink-500/10 px-3 py-2.5 text-sm font-semibold text-pink-300 hover:bg-pink-500/20 transition">
                        <span class="text-base">📸</span> Instagram / FB Stories
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
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

    // Convert the share post into a PNG blob
    async function generatePostImage() {
        const preview = document.getElementById('sharePostPreview');
        const canvas = await html2canvas(preview, { scale: 2, useSVGDataSet: true });
        return new Promise(resolve => {
            canvas.toBlob(blob => {
                resolve({ blob, url: URL.createObjectURL(blob) });
            }, 'image/png');
        });
    }

    function downloadImage() {
        generatePostImage().then(({ url }) => {
            const a = document.createElement('a');
            a.href = url;
            a.download = 'selfcheq-progress.png';
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    function shareToWhatsAppStatus() {
        // On mobile, the Web Share API includes WhatsApp Status
        if (navigator.canShare) {
            generatePostImage().then(({ blob }) => {
                const file = new File([blob], 'selfcheq-progress.png', { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                    navigator.share({
                        files: [file],
                        title: 'My SelfCheq Progress',
                        text: getShareText()
                    }).catch(() => downloadImage());
                } else {
                    downloadImage();
                }
            });
        } else {
            // Desktop: provide text link for WhatsApp
            const text = encodeURIComponent(getShareText());
            window.open('https://wa.me/?text=' + text, '_blank');
        }
    }

    function shareToStories() {
        // On mobile, the Web Share API includes Instagram & Facebook Stories
        if (navigator.canShare) {
            generatePostImage().then(({ blob }) => {
                const file = new File([blob], 'selfcheq-progress.png', { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                    navigator.share({
                        files: [file],
                        title: 'My SelfCheq Progress',
                        text: getShareText()
                    }).catch(() => downloadImage());
                } else {
                    downloadImage();
                }
            });
        } else {
            // Desktop fallback: download image so user can post manually
            downloadImage();
        }
    }
    </script>
</x-app-layout>
