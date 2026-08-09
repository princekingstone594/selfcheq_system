<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">
        <!-- Hero -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <!-- Animated background -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">AI Coach</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Coach Zoe</h1>
                <p class="mt-2 text-slate-300">Your personal discipline assistant. Ask me anything about your goals, routines, or mindset.</p>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Chat -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl" x-data="coachPage()">
            <div x-show="!started" class="py-8 text-center">
                <p class="text-sm text-slate-400">Ready when you are.</p>
                <button @click="started = true"
                        class="mt-4 rounded-2xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Start Chatting
                </button>
            </div>

            <div x-show="started" x-cloak class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Chat with Zoe</p>
                    
                    <!-- Settings Icon -->
                    <div x-data="{ settingsOpen: false }" class="relative">
                        <button @click="settingsOpen = !settingsOpen" @click.outside="settingsOpen = false" class="rounded-xl p-2 text-slate-400 hover:text-white hover:bg-white/5 transition" title="Response Settings">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                        
                        <div x-show="settingsOpen" @click.outside="settingsOpen = false" x-transition class="absolute top-full right-0 mt-2 w-72 rounded-2xl border border-white/10 bg-slate-800 shadow-2xl z-50 p-4 space-y-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Response Mode</p>
                                <div class="flex gap-2">
                                    <button @click="speechMode = false; settingsOpen = false" :class="!speechMode ? 'bg-indigo-500 text-white' : 'bg-slate-700 text-slate-300'" class="flex-1 rounded-xl px-3 py-2 text-xs font-medium transition">
                                        Text Only
                                    </button>
                                    <button @click="speechMode = true; settingsOpen = false" :class="speechMode ? 'bg-indigo-500 text-white' : 'bg-slate-700 text-slate-300'" class="flex-1 rounded-xl px-3 py-2 text-xs font-medium transition">
                                        Text + Speech
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Coaching Style</p>
                                <form method="POST" action="{{ route('coach.mode') }}">
                                    @csrf
                                    <select name="mode" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-xs text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        <option value="calm" {{ auth()->user()->coach_mode == 'calm' ? 'selected' : '' }}>Calm</option>
                                        <option value="strict" {{ auth()->user()->coach_mode == 'strict' ? 'selected' : '' }}>Strict</option>
                                        <option value="aggressive" {{ auth()->user()->coach_mode == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="max-h-96 space-y-3 overflow-y-auto rounded-2xl border border-white/10 bg-slate-800/50 p-4">
                    <template x-for="(msg, i) in messages" :key="i">
                        <div :class="msg.from === 'user' ? 'text-right' : 'text-left'">
                            <span :class="msg.from === 'user' ? 'bg-indigo-500 text-white' : 'bg-slate-700 text-slate-100'"
                                  class="inline-block rounded-2xl px-4 py-2.5 text-sm shadow-sm" x-text="msg.text"></span>
                        </div>
                    </template>
                </div>

                <!-- Input -->
                <form @submit.prevent="send()" class="flex gap-2">
                    <input type="text" x-model="input" placeholder="Type your message..."
                           class="flex-1 rounded-2xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500" />
                    
                    <!-- Microphone Button (Long press to speak) -->
                    <button type="button" 
                            @mousedown="startListening()" @mouseup="stopListening()" @mouseleave="stopListening()"
                            @touchstart.prevent="startListening()" @touchend.prevent="stopListening()"
                            class="rounded-2xl border border-white/10 bg-slate-800 px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700 transition"
                            title="Long press to speak">
                        🎤
                    </button>
                    
                    <button type="submit" :disabled="sending"
                            class="rounded-2xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400 disabled:opacity-50 transition">
                        <span x-show="!sending">Send</span>
                        <span x-show="sending" x-cloak>Sending...</span>
                    </button>
                </form>
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
            speechMode: false,
            recognition: null,
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
                    if (this.speechMode) {
                        speak(data.reply);
                    }
                })
                .catch(() => {
                    this.messages.push({ from: 'zoe', text: "I'm here for you. Stay focused" });
                })
                .finally(() => this.sending = false);
            },
            startListening() {
                if (!('SpeechRecognition' in window || 'webkitSpeechRecognition' in window)) {
                    alert('Speech recognition is not supported in this browser.');
                    return;
                }
                
                this.recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                this.recognition.lang = 'en-US';
                this.recognition.continuous = false;
                this.recognition.interimResults = false;
                this.recognition.start();
                
                this.recognition.onresult = (e) => {
                    this.input = e.results[0][0].transcript;
                };
                
                this.recognition.onerror = (e) => {
                    console.error('Speech recognition error:', e.error);
                    this.recognition = null;
                };
                
                this.recognition.onend = () => {
                    this.recognition = null;
                };
            },
            stopListening() {
                if (this.recognition) {
                    this.recognition.stop();
                    this.recognition = null;
                }
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
