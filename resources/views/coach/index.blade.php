<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Welcome -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">SelfCheq Coach</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Welcome, {{ auth()->user()->name }} 👋</h1>
        </section>

        <!-- Coach Zoe intro -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 shadow-xl">
            <div class="grid gap-0 sm:grid-cols-[1fr_1.4fr]">
                <!-- Coach image (waving) -->
                <div class="relative flex items-center justify-center bg-gradient-to-br from-indigo-500/20 to-slate-900 p-8">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80"
                             alt="Coach Zoe waving"
                             class="h-40 w-40 rounded-3xl border-2 border-white/10 object-cover shadow-2xl" />
                        <!-- Waving hand badge -->
                        <span class="absolute -right-2 -top-2 text-3xl">👋</span>
                    </div>
                </div>

                <!-- Intro text -->
                <div class="p-6 sm:p-8">
                    <h2 class="text-xl font-semibold text-white">Hi, I'm Coach Zoe</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Zoe is SelfCheq's online coach. He works 24/7 to enhance your discipline journey and help you own your day. He's available at the touch of a button — whenever you need a nudge, a plan, or a word of encouragement.
                    </p>

                    <!-- AI options -->
                    <div class="mt-5 space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Available options</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-xl bg-slate-800/80 px-3 py-1.5 text-xs text-slate-300">💬 Daily guidance</span>
                            <span class="rounded-xl bg-slate-800/80 px-3 py-1.5 text-xs text-slate-300">🎯 Goal planning</span>
                            <span class="rounded-xl bg-slate-800/80 px-3 py-1.5 text-xs text-slate-300">🧠 Mindset coaching</span>
                            <span class="rounded-xl bg-slate-800/80 px-3 py-1.5 text-xs text-slate-300">🎙️ Voice chat</span>
                        </div>
                    </div>

                    <!-- Coach mode selector -->
                    <form method="POST" action="{{ route('coach.mode') }}" class="mt-5">
                        @csrf
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Coach mode</p>
                        <select name="mode" onchange="this.form.submit()" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
                            <option value="strict" {{ auth()->user()->coach_mode == 'strict' ? 'selected' : '' }}>Strict</option>
                            <option value="calm" {{ auth()->user()->coach_mode == 'calm' ? 'selected' : '' }}>Calm</option>
                            <option value="aggressive" {{ auth()->user()->coach_mode == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                        </select>
                    </form>
                </div>
            </div>
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
                    🎙️ Or talk to Zoe
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