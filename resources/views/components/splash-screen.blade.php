<noscript></noscript>
<script>
    // Hide splash immediately before any content renders (prevents white flash)
    (function() {
        if (localStorage.getItem('selfcheq_splash_shown')) {
            document.documentElement.classList.add('selfcheq-splash-done');
        }
    })();
</script>

<div id="splash-screen" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white transition-opacity duration-500">
    <div class="flex flex-col items-center">
        <img src="{{ asset('icon-192.png') }}" alt="SelfCheq Logo" class="h-32 w-32 object-contain sm:h-40 sm:w-40" />
        <h1 class="mt-4 text-2xl font-bold tracking-[0.3em] text-slate-800 sm:text-3xl">SELFCHEQ</h1>
    </div>
    <p class="absolute bottom-6 text-xs text-slate-400">© 2026 SelfCheq. All rights reserved.</p>
</div>

<script>
    (function() {
        var splash = document.getElementById('splash-screen');
        if (!splash) return;

        // If already marked done, hide immediately and skip
        if (document.documentElement.classList.contains('selfcheq-splash-done')) {
            splash.style.display = 'none';
            return;
        }

        // First time: mark as shown so next load skips splash
        localStorage.setItem('selfcheq_splash_shown', 'true');

        // Show for 5 seconds then fade out
        setTimeout(function() {
            splash.style.opacity = '0';
            setTimeout(function() {
                splash.style.display = 'none';
            }, 500);
        }, 5000);
    })();
</script>