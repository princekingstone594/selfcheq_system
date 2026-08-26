<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'SelfCheq'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine JS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

        /* PWA Mode: Hide duplicate logo in navigation */
        @media (display-mode: standalone) {
            .pwa-mode .nav-logo-img {
                display: none;
            }
        }
        
        /* Also detect via navigator.standalone for iOS */
        .pwa-mode .nav-logo-img {
            display: none;
        }
    </style>
    
    <script>
        // Detect PWA standalone mode
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            document.documentElement.classList.add('pwa-mode');
        }
    </script>
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100">
    <div id="page-content" class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.16),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_100%)] px-4">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
