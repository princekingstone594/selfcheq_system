<nav x-data="{ open: false, notifOpen: false, unreadCount: {{ auth()->user()->unreadNotifications->count() }}, markAllRead() { fetch('/notifications/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } }).catch(() => {}); } }" class="sticky top-0 z-50 border-b {{ auth()->user()->theme === 'light' ? 'border-slate-200 bg-white/80' : 'border-white/10 bg-slate-950/80' }} backdrop-blur">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14" style="display: block !important; visibility: visible !important; opacity: 1 !important;" />
                    <span class="hidden sm:inline text-sm font-semibold uppercase tracking-[0.25em] text-indigo-300">SelfCheq</span>
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="hidden items-center space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">Tasks</x-nav-link>
                    <x-nav-link :href="route('routines.index')" :active="request()->routeIs('routines.*')">Routine</x-nav-link>
                    <x-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">Journal</x-nav-link>
                    <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">Appointment</x-nav-link>
                    <x-nav-link :href="route('financials.index')" :active="request()->routeIs('financials.*')">Financials</x-nav-link>
                    <x-nav-link :href="route('devotional.today')" :active="request()->routeIs('devotional.*')" class="hidden lg:inline-flex">Devotional</x-nav-link>
                    <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')">My Calendar</x-nav-link>
                    
                    <!-- See More Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
                            See More
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute top-full left-0 mt-2 w-48 rounded-2xl border border-white/10 bg-slate-900 shadow-2xl z-50 overflow-hidden">
                            <div class="py-2">
                                <a href="{{ route('notes.index') }}" :class="request()->routeIs('notes.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Notepad</a>
                                <a href="{{ route('focus.index') }}" :class="request()->routeIs('focus.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Focus</a>
                                <a href="{{ route('progress.index') }}" :class="request()->routeIs('progress.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Progress</a>
                                <a href="{{ route('weekly-review.index') }}" :class="request()->routeIs('weekly-review.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Weekly Review</a>
                                <a href="{{ route('export.index') }}" :class="request()->routeIs('export.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Export Data</a>
                                <a href="{{ route('goals.index') }}" :class="request()->routeIs('goals.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Goals</a>
                                <a href="{{ route('settings.index') }}" :class="request()->routeIs('settings.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Settings</a>
                                <a href="{{ route('devotional.today') }}" :class="request()->routeIs('bible-chapter.*') || request()->routeIs('devotional.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Declaration Chapters</a>
                                <a href="{{ route('examen.today') }}" :class="request()->routeIs('examen.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">🌙 Evening Examen</a>
                                <a href="{{ route('profile.edit') }}" :class="request()->routeIs('profile.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white'" class="block px-4 py-2.5 text-sm transition">Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">

                <!-- ← BACK BUTTON (all pages except dashboard) -->
                @if(!request()->routeIs('dashboard'))
                    <button onclick="event.preventDefault(); event.stopPropagation(); history.back();" class="cursor-pointer flex items-center justify-center h-10 w-10 rounded-2xl border border-white/10 bg-slate-800/70 text-indigo-300 hover:text-white hover:bg-white/5 transition" title="Go Back">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                @endif

                <!-- 🔔 NOTIFICATIONS -->
                <div class="relative">
                    <button @click="notifOpen = !notifOpen; if(notifOpen) { unreadCount = 0; markAllRead(); }" class="relative text-xl hover:text-indigo-300 transition">
                        🔔

                        <!-- 🔴 Unread badge (bounces when unread exist) -->
                        <span x-show="unreadCount > 0"
                              x-bind:class="unreadCount > 0 ? 'bell-bounce' : ''"
                              class="absolute -top-2 -right-2 bg-rose-500 text-white text-[10px] font-semibold px-1.5 rounded-full">
                            <span x-text="unreadCount"></span>
                        </span>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="notifOpen" @click.outside="notifOpen = false"
                         x-transition
                         class="absolute right-0 mt-2 w-80 rounded-2xl border border-white/10 bg-slate-900 shadow-2xl z-50 overflow-hidden">

                        <!-- ✅ Mark as read -->
                        <form method="POST" action="{{ route('notifications.read') }}">
                            @csrf
                            <button class="w-full text-left text-xs text-indigo-300 px-4 py-3 border-b border-white/10 hover:bg-white/5 transition">
                                Mark all as read
                            </button>
                        </form>

                        <!-- Notifications -->
                        <div class="max-h-80 overflow-y-auto">
                            @forelse(auth()->user()->notifications->take(8) as $notification)
                                <div class="px-4 py-3 text-sm text-slate-300 border-b border-white/5 hover:bg-white/5 transition">
                                    {{ $notification->data['message'] ?? 'Notification' }}
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-slate-500 text-sm">
                                    No notifications
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 👤 USER DROPDOWN -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-2 py-1.5 text-sm text-slate-300 hover:text-white transition">
                        @if(Auth::user()->profile_photo_url && file_exists(public_path('storage/' . Auth::user()->profile_photo_url)))
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_url) }}"
                                 alt="{{ Auth::user()->name }}"
                                 class="h-8 w-8 rounded-full border border-white/10 object-cover" />
                        @else
                            @php
                                $nameParts = explode(' ', Auth::user()->name);
                                $initials = strtoupper(substr($nameParts[0], 0, 1)) . ($nameParts[1] ? strtoupper(substr($nameParts[1], 0, 1)) : '');
                            @endphp
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border border-white/10 bg-indigo-500/20 text-xs font-semibold text-indigo-300">
                                {{ $initials }}
                            </div>
                        @endif
                            <span class="hidden sm:inline">{{ explode(' ', Auth::user()->name)[0] }}</span>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- MOBILE MENU BUTTON + BACK BUTTON -->
            <div class="-me-2 flex items-center gap-1 sm:hidden">
                @if(!request()->routeIs('dashboard'))
                    <button onclick="event.preventDefault(); event.stopPropagation(); history.back();" class="cursor-pointer flex items-center justify-center h-9 w-9 rounded-xl border border-white/10 bg-slate-800/70 text-indigo-300 hover:text-white hover:bg-white/5 transition" title="Go Back">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                @endif
                <button @click="open = ! open" @click.outside="open = false" class="p-2 text-slate-300 hover:text-white transition">
                    <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- MOBILE MENU -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/10">
                <div class="pt-2 pb-3 space-y-1 px-4">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">Tasks</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('routines.index')" :active="request()->routeIs('routines.*')">Routine</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">Journal</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">Appointment</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('financials.index')" :active="request()->routeIs('financials.*')">Financials</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('devotional.today')" :active="request()->routeIs('devotional.*')">Devotional</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')">My Calendar</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notes.index')" :active="request()->routeIs('notes.*')">Notepad</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('focus.index')" :active="request()->routeIs('focus.*')">Focus</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">Progress</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('weekly-review.index')" :active="request()->routeIs('weekly-review.*')">Weekly Review</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('export.index')" :active="request()->routeIs('export.*')">Export Data</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')">Goals</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">Settings</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('devotional.today')" :active="request()->routeIs('bible-chapter.*') || request()->routeIs('devotional.*')">Declaration Chapters</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('examen.today')" :active="request()->routeIs('examen.*')">Evening Examen 🌙</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Profile</x-responsive-nav-link>
                </div>
            </div>
</nav>
