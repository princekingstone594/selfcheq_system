<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Welcome -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🧑‍🏫</span>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">SelfCheq Coach</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">Hi, I'm Coach Zoe 👋</h1>
                    <p class="mt-2 text-sm text-slate-300">How do I help you today?</p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- AI options -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">What can Zoe do?</p>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <span class="text-xl">💬</span>
                    <p class="mt-1 font-medium text-white">Daily guidance</p>
                    <p class="text-xs text-slate-400">Get personalised tips for today's tasks and routines.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <span class="text-xl">🎯</span>
                    <p class="mt-1 font-medium text-white">Goal planning</p>
                    <p class="text-xs text-slate-400">Break down big goals into daily discipline steps.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <span class="text-xl">🧠</span>
                    <p class="mt-1 font-medium text-white">Mindset coaching</p>
                    <p class="text-xs text-slate-400">Overcome procrastination and build mental resilience.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <span class="text-xl">🎤</span>
                    <p class="mt-1 font-medium text-white">Voice chat</p>
                    <p class="text-xs text-slate-400">Talk to Zoe hands-free using voice recognition.</p>
                </div>
            </div>
        </section>

        <!-- Coach mode selector -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Coach mode</p>
            <form method="POST" action="{{ route('coach.mode') }}" class="mt-3">
                @csrf
                <select name="mode" onchange="this.form.submit()" class="w-full rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="strict" {{ auth()->user()->coach_mode == 'strict' ? 'selected' : '' }}>Strict</option>
                    <option value="calm" {{ auth()->user()->coach_mode == 'calm' ? 'selected' : '' }}>Calm</option>
                    <option value="aggressive" {{ auth()->user()->coach_mode == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                </select>
            </form>
        </section>

        <!-- Chat -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl" x-data="coachPage()">
            <!-- Not started: show Start Chatting button -->
            <div x-show="!started" class="py-8 text-center">
                <p class="text-sm text-slate-400">Ready when you are.</p>
                <button @click="started = true"
                        class="mt-4 rounded-2xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Start Chatting
                </button>
            </div>

            <!-- Chat interface -->
            <div x-show="started" x-cloak class="space-y-4">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Chat with Zoe</p>

                <!-- Messages -->
                <div class="max-h-80 space-y-3 overflow-y-auto rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <template x-for="(msg, i) in messages" :key="i">
                        <div :class="msg.from === 'user' ? 'text-right' : 'text-left'">
                            <span :class="msg.from === 'user' ? 'bg-indigo-500 text-white' : 'bg-slate-700 text-slate-100'"
                                  class="inline-block rounded-2xl px-3 py-2 text-sm" x-text="msg.text"></span>
                        </div>
                    </template>
                </div>

                <!-- Input -->
                <form @submit.prevent="send()" class="flex gap-2">
                    <input type="text" x-model="input" placeholder="Type your message..."
                           class="flex-1 rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500" />
                    <button type="submit" :disabled="sending"
                            class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 disabled:opacity-50 transition">
                        <span x-show="!sending">Send</span>
                        <span x-show="sending" x-cloak>...</span>
                    </button>
                </form>

                <!-- Voice button -->
                <button @click="startListening()" class="text-sm text-slate-400 hover:text-white transition">
                    🎤 Or talk to Zoe
                </button>
            </div>
        </section>
    </div>

    <script>
    function coachPage() {
        return {
            messages: [],
            input: '',
            sending: false,
            started: false,
            send() {
                if (!this.input.trim()) return;
                const text = this.input;
                this.messages.push({ from: 'user', text });
                this.input = '';
                this.sending = true;

                fetch('{{ route('coach.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(r => r.json())
                .then(data => {
                    this.messages.push({ from: 'zoe', text: data.reply });
                    speak(data.reply);
                })
                .catch(() => {
                    this.messages.push({ from: 'zoe', text: "I'm here for you. Stay focused 💪" });
                })
                .finally(() => this.sending = false);
            },
            startListening() {
                const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                recognition.lang = 'en-US';
                recognition.start();
                recognition.onresult = (e) => {
                    this.input = e.results[0][0].transcript;
                };
            }
        }
    }
    function speak(text) {
        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = 'en-US';
        speech.rate = 1;
        window.speechSynthesis.speak(speech);
    }
    </script>
</x-app-layout>
