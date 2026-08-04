<x-guest-layout>
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-indigo-950/40 backdrop-blur">
        <div class="mb-8 space-y-2">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">SelfCheq</p>
            <h2 class="text-2xl font-semibold text-white">Welcome back</h2>
            <p class="text-sm text-slate-400">Build discipline, keep your promises, and stay grounded one day at a time.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-300" />
                <x-text-input id="email" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-slate-300" />
                <x-text-input id="password" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center text-slate-400">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-700 bg-slate-800 text-indigo-500 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-indigo-300 hover:text-indigo-200" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-between pt-2">
                @if (Route::has('register'))
                    <a class="text-sm font-medium text-slate-300 hover:text-white" href="{{ route('register') }}">
                        {{ __('Create an account') }}
                    </a>
                @endif

                <x-primary-button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
