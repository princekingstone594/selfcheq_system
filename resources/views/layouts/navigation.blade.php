<nav x-data="{ open: false, notifOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- LEFT -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">

                <!-- 🔔 NOTIFICATIONS -->
                <div class="relative">
                    <button @click="notifOpen = !notifOpen" class="relative text-xl">
                        🔔

                        <!-- 🔴 Unread badge -->
                        @if(auth()->user()->unreadNotifications->count())
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown -->
                    <div x-show="notifOpen" @click.outside="notifOpen = false"
                         class="absolute right-0 mt-2 w-72 bg-white rounded shadow z-50">

                        <!-- ✅ Mark as read -->
                        <form method="POST" action="{{ route('notifications.read') }}">
                            @csrf
                            <button class="w-full text-left text-xs text-blue-500 px-3 py-2 border-b hover:bg-gray-100">
                                Mark all as read
                            </button>
                        </form>

                        <!-- Notifications -->
                        @forelse(auth()->user()->notifications->take(5) as $notification)
                            <div class="px-3 py-2 text-sm border-b">
                                {{ $notification->data['message'] }}
                            </div>
                        @empty
                            <div class="px-3 py-2 text-gray-400 text-sm">
                                No notifications
                            </div>
                        @endforelse

                    </div>
                </div>

                <!-- 👤 USER DROPDOWN -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm text-gray-500 bg-white hover:text-gray-700">
                            <div>{{ Auth::user()->name }}</div>

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
                <button @click="open = ! open" class="p-2 text-gray-400 hover:text-gray-500">
                    ☰
                </button>
            </div>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')">
                Dashboard
            </x-responsive-nav-link>
        </div>
    </div>
</nav>