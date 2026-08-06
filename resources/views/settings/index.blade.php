<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">

        <!-- Header -->
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Settings</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">Preferences</h1>
            <p class="mt-2 text-sm text-slate-400">Manage your appearance, security, and permissions.</p>
        </section>

        @if(session('status') === 'theme-updated')
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                ✅ Theme updated successfully.
            </div>
        @endif

        @if(session('status') === 'permissions-updated')
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                ✅ Permissions updated successfully.
            </div>
        @endif

        <!-- Appearance (Light/Dark Mode) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Appearance</p>
            <h2 class="mt-1 text-lg font-medium text-white">Theme</h2>
            <p class="mt-1 text-sm text-slate-400">Choose how SelfCheq looks for you.</p>

            <form method="POST" action="{{ route('settings.theme') }}" class="mt-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="theme" value="dark" class="peer sr-only"
                               {{ auth()->user()->theme === 'dark' ? 'checked' : '' }}>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4 text-center transition peer-checked:border-indigo-400/50 peer-checked:bg-indigo-500/10">
                            <span class="text-2xl">🌙</span>
                            <p class="mt-2 text-sm font-medium text-white">Dark Mode</p>
                            <p class="text-xs text-slate-500">Easy on the eyes</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="theme" value="light" class="peer sr-only"
                               {{ auth()->user()->theme === 'light' ? 'checked' : '' }}>
                        <div class="rounded-2xl border border-white/10 bg-slate-800/70 p-4 text-center transition peer-checked:border-amber-400/50 peer-checked:bg-amber-500/10">
                            <span class="text-2xl">☀️</span>
                            <p class="mt-2 text-sm font-medium text-white">Light Mode</p>
                            <p class="text-xs text-slate-500">Bright and clean</p>
                        </div>
                    </label>
                </div>
                <button class="mt-4 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Save Theme
                </button>
            </form>
        </section>

        <!-- Security (Password) -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Security</p>
            <h2 class="mt-1 text-lg font-medium text-white">Password</h2>
            <p class="mt-1 text-sm text-slate-400">Update your password to keep your account secure.</p>

            <div class="mt-4">
                @include('profile.partials.update-password-form')
            </div>
        </section>

        <!-- Permissions -->
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Permissions</p>
            <h2 class="mt-1 text-lg font-medium text-white">Privacy & Access</h2>
            <p class="mt-1 text-sm text-slate-400">Control what SelfCheq can access and notify you about.</p>

            <form method="POST" action="{{ route('settings.permissions') }}" class="mt-4 space-y-4">
                @csrf

                <label class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-800/70 p-4 cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-white">🔔 Notifications</p>
                        <p class="text-xs text-slate-500">Allow browser notifications for reminders and updates.</p>
                    </div>
                    <input type="checkbox" name="notifications_enabled" class="h-5 w-5 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500"
                           {{ auth()->user()->notifications_enabled ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-800/70 p-4 cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-white">👥 Contacts</p>
                        <p class="text-xs text-slate-500">Allow SelfCheq to access your contacts for birthday reminders.</p>
                    </div>
                    <input type="checkbox" name="contacts_enabled" class="h-5 w-5 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500"
                           {{ auth()->user()->contacts_enabled ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-800/70 p-4 cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-white">⏰ Reminders</p>
                        <p class="text-xs text-slate-500">Allow task deadline and appointment reminders.</p>
                    </div>
                    <input type="checkbox" name="reminders_enabled" class="h-5 w-5 rounded border-slate-700 bg-slate-800 text-indigo-500 focus:ring-indigo-500"
                           {{ auth()->user()->reminders_enabled ? 'checked' : '' }}>
                </label>

                <button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Save Permissions
                </button>
            </form>
        </section>
    </div>
</x-app-layout>