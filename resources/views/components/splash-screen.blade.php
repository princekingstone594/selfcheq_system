<div id="splash-screen" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white transition-opacity duration-500">
    <!-- Transparent logo centered -->
    <div class="flex flex-col items-center">
        <img src="{{ asset('icon-192.png') }}" alt="SelfCheq Logo" class="h-32 w-32 object-contain animate-pulse sm:h-40 sm:w-40" />
        <h1 class="mt-4 text-2xl font-bold tracking-[0.3em] text-slate-800 sm:text-3xl">SELFCHEQ</h1>
    </div>

    <!-- Copyright at bottom -->
    <p class="absolute bottom-6 text-xs text-slate-400">© 2026 SelfCheq. All rights reserved.</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const splash = document.getElementById('splash-screen');
        if (splash) {
            // Show splash for 5 seconds then fade out
            // Duration will later be determined by how fast the app prepares to function
            setTimeout(function() {
                splash.style.opacity = '0';
                setTimeout(function() {
                    splash.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });
</script>
