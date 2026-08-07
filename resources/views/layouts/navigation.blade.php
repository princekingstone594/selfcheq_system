<nav x-data="{ open: false, notifOpen: false }" class="sticky top-0 z-50 border-b {{ auth()->user()->theme === 'light' ? 'border-slate-200 bg-white/80' : 'border-white/10 bg-slate-950/80' }} backdrop-blur">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-20 w-20" />
                    <span class="hidden sm:inline text-sm font-semibold uppercase tracking-[0.25em] text-indigo-300">SelfCheq</span>
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">Tasks</x-nav-link>
                    <x-nav-link :href="route('routines.index')" :active="request()->routeIs('routines.*')">Routines</x-nav-link>
                    <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">Schedule</x-nav-link>
                    <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')">My Calendar</x-nav-link>
                    <x-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">Journal</x-nav-link>
                    <x-nav-link :href="route('focus.index')" :active="request()->routeIs('focus.*')">Focus</x-nav-link>
                    <x-nav-link :href="route('devotional.today')" :active="request()->routeIs('devotional.*')">Devotional</x-nav-link>
                    <x-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">Progress</x-nav-link>
                    <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">Settings</x-nav-link>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">

                <!-- 🔔 NOTIFICATIONS -->
                <div class="relative">
                    <button @click="notifOpen = !notifOpen" class="relative text-xl hover:text-indigo-300 transition">
                        🔔

                        <!-- 🔴 Unread badge -->
                        @if(auth()->user()->unreadNotifications->count())
                            <span class="absolute -top-2 -right-2 bg-rose-500 text-white text-[10px] font-semibold px-1.5 rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
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
                            <img src="{{ Auth::user()->profile_photo_url }}"
                                 alt="{{ Auth::user()->name }}"
                                 class="h-8 w-8 rounded-full border border-white/10 object-cover" />
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>

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

            <!-- MOBILE MENU BUTTON -->
            <div class="-me-2 flex items-center sm:hidden">
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
            <x-responsive-nav-link :href="route('routines.index')" :active="request()->routeIs('routines.*')">Routines</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">Schedule</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')">My Calendar</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">Journal</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('focus.index')" :active="request()->routeIs('focus.*')">Focus</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('devotional.today')" :active="request()->routeIs('devotional.*')">Devotional</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">Progress</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">Settings</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Profile</x-responsive-nav-link>
        </div>
    </div>
</nav>