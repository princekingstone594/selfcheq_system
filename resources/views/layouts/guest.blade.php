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
        #page-content {
            opacity: 0;
            animation: fadeInPage 0.3s ease-in-out forwards;
        }
        
        @keyframes fadeInPage {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .page-transitioning #page-content {
            animation: fadeOutPage 0.15s ease-in-out forwards;
        }
        
        @keyframes fadeOutPage {
            to {
                opacity: 0;
                transform: translateY(-4px);
            }
        }
    </style>
    
    <script>
        // Smooth page transitions for all internal links and form submissions
        document.addEventListener('DOMContentLoaded', function() {
            // Check if this is a smooth redirect from a command
            @if(session('selfcheq_smooth_redirect'))
                sessionStorage.setItem('selfcheq_redirecting', 'true');
                sessionStorage.setItem('selfcheq_smooth_transition', 'true');
            @endif
            
            const content = document.getElementById('page-content') || document.querySelector('.sm\\:max-w-md');
            if (!content) return;
            
            // Wrap content for transitions
            if (!content.id) {
                content.id = 'page-content';
            }
            
            // Handle smooth transitions from server-side redirects
            const isSmoothRedirect = sessionStorage.getItem('selfcheq_smooth_transition');
            if (isSmoothRedirect) {
                sessionStorage.removeItem('selfcheq_smooth_transition');
                content.style.opacity = '0';
                content.style.transform = 'translateY(-4px)';
                
                // Trigger reflow
                content.offsetHeight;
                
                // Animate in
                content.style.transition = 'opacity 0.3s ease-in-out, transform 0.3s ease-in-out';
                content.style.opacity = '1';
                content.style.transform = 'translateY(0)';
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
                    
                    // Mark as redirecting for splash screen skip
                    sessionStorage.setItem('selfcheq_redirecting', 'true');
                    
                    // Add transition class
                    document.documentElement.classList.add('page-transitioning');
                    
                    // Allow animation to complete before navigation
                    setTimeout(() => {
                        window.location.href = href;
                    }, 150);
                    
                    e.preventDefault();
                });
            });
            
            // Intercept all form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Mark as redirecting for splash screen skip
                    sessionStorage.setItem('selfcheq_redirecting', 'true');
                    
                    // Add transition class
                    document.documentElement.classList.add('page-transitioning');
                    
                    // Allow animation to complete, then let form submit naturally
                    setTimeout(() => {
                        document.documentElement.classList.remove('page-transitioning');
                    }, 150);
                    
                    // Don't prevent submission - let it proceed naturally
                });
            });
        });
    </script>
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100">
    <x-splash-screen />

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
