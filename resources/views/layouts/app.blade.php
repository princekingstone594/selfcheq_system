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

    {{-- Confetti for celebrations --}}
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.7.0/dist/confetti.browser.min.js"></script>

    {{-- Shared Micro-Interactions --}}
    <script>
        window.celebrate = function(message) {
            const duration = 2 * 1000;
            const end = Date.now() + duration;
            (function frame() {
                window.requestAnimationFrame(frame);
                confetti({ particleCount: 3, angle: 60, spread: 55, origin: { x: 0, y: 0.7 }, zIndex: 9999 });
                confetti({ particleCount: 3, angle: 120, spread: 55, origin: { x: 1, y: 0.7 }, zIndex: 9999 });
                if (Date.now() > end) return;
            })();
            if (message) {
                window.dispatchEvent(new CustomEvent('selfcheq:celebrate', { detail: { message } }));
            }
        };
        window.addEventListener('selfcheq:celebrate', (e) => {
            const msg = e.detail.message;
            if (!msg) return;
            let existing = document.querySelector('#selfcheq-toast');
            if (existing) existing.remove();
            const toast = document.createElement('div');
            toast.id = 'selfcheq-toast';
            toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 rounded-xl border border-indigo-400/30 bg-indigo-500/90 px-5 py-2.5 text-xs font-semibold text-white shadow-xl shadow-indigo-900/40 backdrop-blur';
            toast.textContent = msg;
            toast.style.zIndex = '9998';
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 2000);
        });
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button[type="submit"], button[data-press]');
            if (btn && !btn.classList.contains('no-press')) {
                btn.classList.add('active-button');
                setTimeout(() => btn.classList.remove('active-button'), 150);
            }
        });
    </script>
    <style>
        .active-button { transform: scale(0.96); transition: transform 0.15s ease; }

        /* 🔔 Notification bell badge bounce */
        @keyframes bell-bounce {
            0%, 100% { transform: scale(1) rotate(0deg); }
            40% { transform: scale(1.35) rotate(-12deg); }
            60% { transform: scale(1.25) rotate(8deg); }
        }
        .bell-bounce {
            animation: bell-bounce 0.6s ease-in-out infinite;
        }

        /* ✅ Check pop animation — green glow burst on completion toggle */
        .check-pop {
            animation: checkPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        @keyframes checkPop {
            0% {
                transform: scale(1) rotate(0deg);
                filter: brightness(1) saturate(1);
            }
            25% {
                transform: scale(1.4) rotate(-8deg);
                filter: brightness(1.6) saturate(180%) drop-shadow(0 0 16px #10b981);
            }
            50% {
                transform: scale(1.25) rotate(5deg);
                filter: brightness(1.4) saturate(160%) drop-shadow(0 0 12px #10b981);
            }
            100% {
                transform: scale(1) rotate(0deg);
                filter: brightness(1) saturate(1);
            }
        }

        /* Button press effect (pure CSS — works for all primary buttons) */
        .btn-press {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-press:active {
            transform: scale(0.95);
        }
    </style>

    @stack('scripts')
    
    {{-- Smooth Page Transitions --}}
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        #page-content {
            opacity: 1;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        
        .page-transitioning #page-content {
            opacity: 0;
            transform: translateY(-5px);
            transition: opacity 0.08s ease-in-out, transform 0.08s ease-in-out;
        }
        
        .smooth-redirect #page-content {
            animation: smoothFadeIn 0.3s ease-in-out;
        }
        
        @keyframes smoothFadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Prevent flash during page loads */
        html.splash-skip #splash-screen {
            display: none !important;
        }
    </style>
    
    
    <script>
        // Smooth page transitions for all internal links and form submissions
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.getElementById('page-content') || document.querySelector('main');
            if (!content) return;
            
            // Wrap main content for transitions
            if (!content.id) {
                content.id = 'page-content';
            }
            
            // Check for smooth redirect from server
            const hasSmoothRedirect = sessionStorage.getItem('selfcheq_smooth_transition');
            if (hasSmoothRedirect) {
                sessionStorage.removeItem('selfcheq_smooth_transition');
                document.documentElement.classList.add('splash-skip');
                content.classList.add('smooth-redirect');
            }
            
            // Check for redirect flag from splash screen
            const isRedirecting = sessionStorage.getItem('selfcheq_redirecting');
            if (isRedirecting) {
                sessionStorage.removeItem('selfcheq_redirecting');
                document.documentElement.classList.add('splash-skip');
            }
            
            // Intercept all internal link clicks
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    // Only handle internal links (not external, anchors, or special URLs)
                    if (!href || 
                        href.startsWith('http') || 
                        href.startsWith('//') || 
                        href.startsWith('#') || 
                        href.startsWith('mailto:') || 
                        href.startsWith('tel:') ||
                        href.includes('javascript:')) {
                        return;
                    }
                    
                    // Mark as redirecting
                    sessionStorage.setItem('selfcheq_redirecting', 'true');
                    
                    // Add transition class for smooth fade out
                    document.documentElement.classList.add('page-transitioning');
                    
                    // Navigate immediately for faster loading
                    window.location.href = href;
                    
                    e.preventDefault();
                });
            });
            
            // Intercept all form submissions for smooth transitions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Mark as redirecting to skip splash screen
                    sessionStorage.setItem('selfcheq_redirecting', 'true');
                    
                    // Add transition class for smooth fade out
                    document.documentElement.classList.add('page-transitioning');
                    
                    // Remove transitioning class quickly for faster loading
                    setTimeout(() => {
                        document.documentElement.classList.remove('page-transitioning');
                    }, 80);
                    
                    // Don't prevent submission - let it proceed naturally
                    // The server will redirect and the next page will handle smooth fade in
                });
            });
        });
    </script>
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

        <main id="page-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t {{ auth()->check() && auth()->user()->theme === 'light' ? 'border-slate-200 bg-white' : 'border-white/10 bg-slate-950/80' }} backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-16 w-16 sm:h-20 sm:w-20" style="display: block !important; visibility: visible !important; opacity: 1 !important;" />
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
            const icon = reminder.type === 'routine' ? '⏰' : reminder.type === 'birthday_reminder' ? '🎂' : '🔔';
            const label = reminder.type === 'routine' ? 'Routine' : reminder.type === 'birthday_reminder' ? 'Birthday' : 'Task';
            const title = `${icon} ${label} Reminder`;

            const notification = new Notification(title, {
                body: reminder.title,
                icon: '/icon-192.png',
                tag: reminder.notification_id || 'selfcheq-' + reminder.type,
            });

            // Mark database notification as read when clicked
            if (reminder.notification_id) {
                notification.onclick = () => {
                    fetch(`/api/notifications/${reminder.notification_id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                    }).catch(() => {});
                };
            }
        };

        const checkReminders = async () => {
            try {
                const response = await fetch('/api/reminders');
                if (!response.ok) return;
                const reminders = await response.json();

                // Only show new reminders (track by notification_id or title+type)
                reminders.forEach(reminder => {
                    const key = reminder.notification_id || (reminder.type + ':' + reminder.title);
                    if (!shownReminders.has(key)) {
                        shownReminders.add(key);
                        showReminder(reminder);
                    }
                });
            } catch (e) {
                // Silently ignore network errors
            }
        };

        const shownReminders = new Set();

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

    {{-- 🔔 Global toast (Alpine) — call window.toast('message', 'success'|'error') anywhere --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-init="window.toast = (msg, type = 'success') => { message = msg; type = type; show = true; clearTimeout(window.__toastTimer); window.__toastTimer = setTimeout(() => show = false, 3000); }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-20 left-1/2 z-[100] w-[90%] max-w-sm -translate-x-1/2 rounded-2xl border px-4 py-3 text-sm font-semibold shadow-2xl backdrop-blur-md sm:left-auto sm:right-6 sm:translate-x-0"
         :class="type === 'error'
             ? 'border-red-400/30 bg-red-950/90 text-red-100'
             : 'border-emerald-400/30 bg-slate-950/90 text-emerald-100'"
         x-cloak>
        <span class="mr-1.5" x-text="type === 'error' ? '⚠️' : '✅'"></span><span x-text="message"></span>
    </div>
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>