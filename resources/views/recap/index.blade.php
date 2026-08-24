<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- 🎉 Full-screen Weekly Recap -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 p-8 shadow-2xl shadow-indigo-950/40 backdrop-blur-xl sm:p-10">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
            </div>

            <div id="recapCard" class="relative">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Week of {{ \Carbon\Carbon::parse($weekStart)->format('M j') }} – {{ \Carbon\Carbon::parse($weekEnd)->format('M j, Y') }}</p>
                <h1 class="mt-3 text-4xl font-bold text-white sm:text-5xl">🎉 Your Week in Discipline</h1>

                <div class="mt-10 space-y-5">
                    <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-slate-900/60 p-5">
                        <span class="text-3xl">🔥</span>
                        <div>
                            <p class="text-sm text-slate-400">Longest streak run this week</p>
                            <p class="text-xl font-bold text-white">{{ $longestRun }} day{{ $longestRun === 1 ? '' : 's' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-slate-900/60 p-5">
                        <span class="text-3xl">⭐</span>
                        <div>
                            <p class="text-sm text-slate-400">Best habit</p>
                            @if($bestHabit)
                                <p class="text-xl font-bold text-white">{{ $bestHabit->title }} ({{ $bestHabitCount }}/7 days)</p>
                            @else
                                <p class="text-xl font-bold text-white">No habit wins yet this week</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-slate-900/60 p-5">
                        <span class="text-3xl">🌱</span>
                        <div>
                            <p class="text-sm text-slate-400">You're growing</p>
                            <p class="text-xl font-bold text-white">Level {{ $level }} · {{ $growthTitle }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-slate-900/60 p-5">
                        <span class="text-3xl">📖</span>
                        <div>
                            <p class="text-sm text-slate-400">Chapters declared</p>
                            <p class="text-xl font-bold text-white">{{ $chaptersDeclared }} chapter{{ $chaptersDeclared === 1 ? '' : 's' }} this week</p>
                        </div>
                    </div>

                    <!-- Quick totals -->
                    <div class="grid grid-cols-3 gap-3 pt-2">
                        <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $tasksCompleted }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">Tasks done</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $focusMinutes }}m</p>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">Focus time</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $examensWritten }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">Examens 🌙</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Share actions --}}
            <div class="relative mt-8 flex flex-wrap gap-3">
                <button onclick="openRecapShare()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:from-indigo-400 hover:to-purple-400 active:scale-95">
                    📤 Share my week
                </button>
                                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-slate-800 px-6 py-3 text-sm font-semibold text-slate-300 transition hover:text-white">
                    Back to dashboard →
                </a>
            </div>
        </section>

        <!-- Share Modal (square format for stories/status) -->
        <div id="recapShareModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeRecapShare()"></div>
            <div class="relative w-full max-w-md rounded-3xl border border-white/10 bg-slate-900 shadow-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <p class="text-sm font-semibold text-white">Share your week</p>
                    <button onclick="closeRecapShare()" class="text-slate-400 hover:text-white transition">✕</button>
                </div>

                <div class="p-4">
                    <div id="recapSharePreview" class="relative aspect-square rounded-3xl overflow-hidden bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 p-6 flex flex-col items-center justify-center text-center border border-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">#SelfCheq Weekly Recap</p>
                        <h2 class="mt-2 text-2xl font-bold text-white">{{ Auth::user()->name }}'s Week</h2>
                        <p class="mt-6 text-3xl">🔥 {{ $longestRun }}d · ⭐ {{ $bestHabit ? $bestHabitCount.'/7' : '—' }} · 📖 {{ $chaptersDeclared }}</p>
                        <p class="mt-3 text-sm text-slate-400">Level {{ $level }} · {{ $growthTitle }} · {{ $tasksCompleted }} tasks · {{ $focusMinutes }}m focus</p>
                        <p class="mt-6 text-xs italic text-slate-500">Building better habits, one week at a time.</p>
                    </div>

                    <div class="mt-4 grid gap-2 sm:grid-cols-3">
                        <button onclick="shareRecapToStories()" class="rounded-xl bg-indigo-500/90 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">📲 Stories</button>
                        <button onclick="shareRecapWhatsApp()" class="rounded-xl bg-emerald-500/90 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-400 transition">💬 WhatsApp</button>
                        <button onclick="downloadRecapImage()" class="rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-600 transition">⬇️ Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function openRecapShare() {
            document.getElementById('recapShareModal').classList.remove('hidden');
            document.getElementById('recapShareModal').classList.add('flex');
        }

        function closeRecapShare() {
            document.getElementById('recapShareModal').classList.add('hidden');
            document.getElementById('recapShareModal').classList.remove('flex');
        }

        function getRecapText() {
            const name = '{{ Auth::user()->name }}';
            const run = {{ $longestRun }};
            const chapters = {{ $chaptersDeclared }};
            const growth = '{{ $growthTitle }}';
            const level = {{ $level }};

            return `🎉 ${name}'s Week in Discipline!\n\n🔥 Longest streak: ${run} day(s)\n📖 Chapters declared: ${chapters}\n🌱 Level ${level} · ${growth}\n\nOne week at a time. #SelfCheq #Discipline`;
        }

        async function generateRecapImage() {
            const preview = document.getElementById('recapSharePreview');
            const canvas = await html2canvas(preview, { scale: 2 });
            return new Promise(resolve => {
                canvas.toBlob(blob => {
                    resolve({ blob, url: URL.createObjectURL(blob) });
                }, 'image/png');
            });
        }

        function downloadRecapImage() {
            generateRecapImage().then(({ url }) => {
                const a = document.createElement('a');
                a.href = url;
                a.download = 'selfcheq-weekly-recap.png';
                a.click();
                URL.revokeObjectURL(url);
            });
        }

        function shareRecapToStories() {
            if (navigator.canShare) {
                generateRecapImage().then(({ blob }) => {
                    const file = new File([blob], 'selfcheq-weekly-recap.png', { type: 'image/png' });
                    if (navigator.canShare({ files: [file] })) {
                        navigator.share({ files: [file], title: 'My SelfCheq Week', text: getRecapText() })
                            .catch(() => downloadRecapImage());
                    } else {
                        downloadRecapImage();
                    }
                });
            } else {
                downloadRecapImage();
            }
        }

        function shareRecapWhatsApp() {
            const text = encodeURIComponent(getRecapText());
            window.open('https://wa.me/?text=' + text, '_blank');
        }

        // 🎉 Confetti when the recap opens on a Sunday
        document.addEventListener('DOMContentLoaded', function () {
            if (new Date().getDay() === 0 && typeof window.celebrate === 'function') {
                window.celebrate('Your week is complete 🎉');
            }
        });
    </script>
</x-app-layout>