<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->check() && auth()->user()->theme === 'light' ? 'light' : 'dark' }}">
<head>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="/icon-192.png">

    <title>@yield('title', config('app.name', 'SelfCheq'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Keep existing colors
                    }
                }
            }
        }
    </script>

    <!-- Alpine JS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</head>
<body class="font-sans antialiased {{ auth()->check() && auth()->user()->theme === 'light' ? 'bg-slate-100 text-slate-900' : 'bg-slate-950 text-slate-100' }}">
    <div class="min-h-screen {{ auth()->check() && auth()->user()->theme === 'light'
        ? 'bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.10),_transparent_30%),linear-gradient(135deg,_#f1f5f9_0%,_#e2e8f0_100%)]'
        : 'bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.16),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_100%)]' }}">
        @include('layouts.navigation')

        @isset($header)
            <header class="border-b {{ auth()->check() && auth()->user()->theme === 'light' ? 'border-slate-200 bg-white/60' : 'border-white/10 bg-white/5' }} backdrop-blur">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t {{ auth()->check() && auth()->user()->theme === 'light' ? 'border-slate-200 bg-white/80' : 'border-white/10 bg-slate-950/80' }} backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-8 w-8 rounded-lg object-contain" />
                        <span class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-300">SelfCheq</span>
                    </div>
                    <p class="text-xs {{ auth()->check() && auth()->user()->theme === 'light' ? 'text-slate-500' : 'text-slate-500' }}">© 2026 SelfCheq. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Floating Coach Zoe button (fixed, follows scroll) -->
    @auth
    <a href="{{ route('coach.index') }}"
       class="group fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-full border border-white/10 bg-indigo-600/90 p-3 pr-4 text-white shadow-2xl shadow-indigo-900/50 backdrop-blur transition hover:bg-indigo-500 hover:scale-105 sm:bottom-6 sm:right-6"
       aria-label="Chat with Coach Zoe"
       title="Coach Zoe">
        <span class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-xl">
            🧑‍🏫
            <!-- Pulsing ring -->
            <span class="absolute inset-0 animate-ping rounded-full bg-indigo-400/40"></span>
        </span>
        <span class="hidden text-sm font-semibold sm:inline">Coach Zoe</span>
    </a>
    @endauth

    <script>
    if ('Notification' in window) {
        Notification.requestPermission();
    }
    </script>
    <script>
    setInterval(async () => {
        try {
            const response = await fetch('/api/reminders');
            const tasks = await response.json();

            tasks.forEach(task => {
                new Notification("⏰ Reminder", {
                    body: task.title
                });
            });
        } catch (e) {
            // silently ignore network errors
        }
    }, 60000);
    </script>
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
    </script>
</body>
</html>