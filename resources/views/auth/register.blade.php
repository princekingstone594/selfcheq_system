<x-guest-layout>
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-indigo-950/40 backdrop-blur">
        <div class="mb-8 space-y-2">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-indigo-300">Start your discipline system</p>
            <h2 class="text-2xl font-semibold text-white">Create your account</h2>
            <p class="text-sm text-slate-400">Join a calm, structured daily operating system for your life.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-slate-300" />
                <x-text-input id="name" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-300" />
                <x-text-input id="email" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Contact (Phone)')" class="text-slate-300" />
                <x-text-input id="phone" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white" type="tel" name="phone" :value="old('phone')" autocomplete="tel" placeholder="+254 712 345 678" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="birthday" :value="__('Date of Birth')" class="text-slate-300" />
                <x-text-input id="birthday" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white" type="date" name="birthday" :value="old('birthday')" autocomplete="bday" />
                <x-input-error :messages="$errors->get('birthday')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="bio" :value="__('Profile (Short Bio)')" class="text-slate-300" />
                <textarea id="bio" name="bio" rows="3" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tell us a little about yourself...">{{ old('bio') }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-slate-300" />
                <x-text-input id="password" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-300" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-2xl border-slate-700 bg-slate-800/80 text-white"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <a class="text-sm text-slate-300 hover:text-white" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>