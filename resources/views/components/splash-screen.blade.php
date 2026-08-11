@php
    // Check if splash has been shown before (persists across sessions)
    $splashShown = isset($_COOKIE['selfcheq_splash_shown']);
@endphp

@if(!$splashShown)
<div id="splash-screen" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white transition-opacity duration-500">
    <div class="flex flex-col items-center">
        <img src="{{ asset('icon-192.png') }}" alt="SelfCheq Logo" class="h-32 w-32 object-contain sm:h-40 sm:w-40" />
        <h1 class="mt-4 text-2xl font-bold tracking-[0.3em] text-slate-800 sm:text-3xl">SELFCHEQ</h1>
    </div>
    <p class="absolute bottom-6 text-xs text-slate-400">© 2026 SelfCheq. All rights reserved.</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const splash = document.getElementById('splash-screen');
        if (!splash) return;

        // Set cookie so splash only shows once ever (like WhatsApp first launch)
        document.cookie = 'selfcheq_splash_shown=1; path=/; max-age=31536000';

        // Show splash for 5 seconds then fade out
        setTimeout(function() {
            splash.style.opacity = '0';
            setTimeout(function() {
                splash.style.display = 'none';
            }, 500);
        }, 5000);
    });
</script>
@endif