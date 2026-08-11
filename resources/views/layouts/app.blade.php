<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->check() && auth()->user()->theme === 'light' ? 'light' : 'dark' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'SelfCheq'))</title>

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- PWA Theme --}}
    <meta name="theme-color" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">

    {{-- Apple PWA Support --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SelfCheq">

    {{-- PWA Icons --}}
    <link rel="apple-touch-icon" href="{{ asset('icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icon-512.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS --}}
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

    {{-- Alpine JS --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</head>
<body class="font-sans antialiased {{ auth()->check() && auth()->user()->theme === 'light' ? 'bg-slate-50 text-slate-900' : 'bg-slate-950 text-slate-100' }}">
    <x-splash-screen />

    <div class="min-h-screen {{ auth()->check() && auth()->user()->theme === 'light'
        ? 'bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-100'
        : 'bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.16),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_100%)]' }}">
        @include('layouts.navigation')

        @isset($header)
            <header class="border-b {{ auth()->check() && auth()->user()->theme === 'light' ? 'border-slate-200 bg-white/90 shadow-sm' : 'border-white/10 bg-white/5' }} backdrop-blur">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t {{ auth()->check() && auth()->user()->theme === 'light' ? 'border-slate-200 bg-white' : 'border-white/10 bg-slate-950/80' }} backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-24 w-24" />
                        <span class="text-sm font-semibold uppercase tracking-[0.25em] {{ auth()->check() && auth()->user()->theme === 'light' ? 'text-indigo-600' : 'text-indigo-300' }}">SelfCheq</span>
                    </div>
                    <p class="text-xs {{ auth()->check() && auth()->user()->theme === 'light' ? 'text-slate-500' : 'text-slate-400' }}">© 2026 SelfCheq. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    {{-- Floating Coach Zoe button --}}
    @auth
    <a href="{{ route('coach.index') }}"
       class="group fixed bottom-5 right-5 z-50 flex items-center gap-3 rounded-full border {{ auth()->check() && auth()->user()->theme === 'light' ? 'border-indigo-200 bg-indigo-600 shadow-lg shadow-indigo-200/50' : 'border-white/10 bg-indigo-600/90 shadow-2xl shadow-indigo-900/50' }} p-3 pr-4 text-white backdrop-blur transition hover:bg-indigo-500 hover:scale-105 sm:bottom-6 sm:right-6"
       aria-label="Chat with Coach Zoe"
       title="Coach Zoe">
        <span class="relative flex h-10 w-10 items-center justify-center rounded-full {{ auth()->check() && auth()->user()->theme === 'light' ? 'bg-indigo-500' : 'bg-white/15' }} text-xl">
            🧑‍🏫
            <span class="absolute inset-0 animate-ping rounded-full bg-indigo-400/40"></span>
        </span>
        <span class="hidden text-sm font-semibold sm:inline">Coach Zoe</span>
    </a>
    @endauth

    {{-- Browser Notifications for alarms & reminders --}}
    <script>
        const showReminder = (reminder) => {
            const icon = reminder.type === 'routine' ? '⏰' : '🔔';
            const label = reminder.type === 'routine' ? 'Routine' : 'Task';
            new Notification(`${icon} ${label} Reminder`, {
                body: reminder.title,
                icon: '/icon-192.png'
            });
        };

        const checkReminders = async () => {
            try {
                const response = await fetch('/api/reminders');
                if (!response.ok) return;
                const reminders = await response.json();
                reminders.forEach(showReminder);
            } catch (e) {
                // Silently ignore network errors
            }
        };

        if ('Notification' in window) {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    checkReminders();
                    setInterval(checkReminders, 60000);
                }
            });
        }
    </script>

    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SelfCheq service worker registered:', registration.scope);
                    })
                    .catch(error => {
                        console.error('SelfCheq service worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>