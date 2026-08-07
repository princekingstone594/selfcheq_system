<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-900/40 via-slate-900 to-purple-900/30 p-6 shadow-2xl shadow-indigo-950/30 backdrop-blur-xl sm:p-8">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-purple-500/20 blur-3xl"></div>
            </div>

            <div class="relative">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Account</p>
                <h1 class="mt-2 text-3xl font-bold text-white sm:text-4xl">Profile Settings</h1>
                <p class="mt-2 text-slate-300">Manage your account information and preferences.</p>
            </div>
        </section>

        <!-- Profile Photo -->
        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-photo-form')
            </div>
        </div>

        <!-- Profile Information -->
        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="rounded-3xl border border-rose-500/20 bg-slate-900/70 p-6 shadow-xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
