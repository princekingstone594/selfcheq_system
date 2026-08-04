<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __('Upload a photo to personalize your account. Shown in the navigation and dashboard.') }}
        </p>
    </header>

    <div class="mt-6 flex items-center gap-6">
        <!-- Current photo -->
        <img src="{{ auth()->user()->profile_photo_url }}"
             alt="{{ auth()->user()->name }}"
             class="h-20 w-20 rounded-full border-2 border-white/10 object-cover" />

        <!-- Upload form -->
        <form method="post" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="flex-1">
            @csrf
            <div class="flex items-center gap-4">
                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-sm text-slate-300 file:mr-4 file:rounded-2xl file:border-0 file:bg-indigo-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-400" />
                <x-primary-button class="rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400">
                    {{ __('Upload') }}
                </x-primary-button>
            </div>
            <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
            <p class="mt-2 text-xs text-slate-500">JPG, PNG, or WebP. Max 2MB.</p>
        </form>
    </div>
</section>