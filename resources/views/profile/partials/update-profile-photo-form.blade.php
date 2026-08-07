<section>
    <header>
        <h2 class="text-lg font-semibold text-white">
            Profile Photo
        </h2>
        <p class="mt-1 text-sm text-slate-400">
            Upload a photo to personalize your account. This will be shown in the navigation and dashboard.
        </p>
    </header>

    <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row">
        <!-- Current photo -->
        <div class="relative">
            <img src="{{ auth()->user()->profile_photo_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="h-24 w-24 rounded-full border-4 border-white/20 object-cover shadow-xl" />
            <div class="absolute -bottom-1 -right-1 rounded-full bg-indigo-500 p-2">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        <!-- Upload form -->
        <form method="post" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="flex-1 w-full">
            @csrf
            <div class="space-y-3">
                <div class="relative">
                    <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" id="photo-upload"
                           class="hidden" onchange="this.nextElementSibling.querySelector('span').textContent = this.files[0]?.name || 'Choose file'" />
                    <label for="photo-upload" class="flex cursor-pointer items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-white/20 bg-slate-800/50 px-4 py-3 text-sm text-slate-300 transition hover:border-indigo-400/50 hover:bg-slate-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Choose file</span>
                    </label>
                </div>
                <x-primary-button class="w-full rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition">
                    Upload Photo
                </x-primary-button>
            </div>
            <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
            <p class="mt-2 text-xs text-slate-500">JPG, PNG, or WebP. Maximum 2MB.</p>
        </form>
    </div>
</section>
