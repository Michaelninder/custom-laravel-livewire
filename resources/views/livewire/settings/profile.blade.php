<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('nav.profile')" :subheading="__('Update your account information, public profile details, and email address.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <!-- Avatar -->
            <div class="flex items-center gap-4">
                <div class="shrink-0">
                    <img class="h-20 w-20 rounded-full object-cover" src="{{ $this->user->avatar }}" alt="{{ $this->user->username }}">
                </div>

                <div class="flex flex-col gap-2">
                    <input type="file" wire:model="newAvatar" id="avatar" accept="image/*" class="hidden">
                    <label for="avatar" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-md font-semibold text-xs text-gray-700 dark:text-zinc-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Select New Avatar') }}
                    </label>

                    @if ($newAvatar)
                        <img src="{{ $newAvatar->temporaryUrl() }}" class="h-16 w-16 rounded-full object-cover mt-2 border border-gray-300 dark:border-zinc-600">
                        <button type="button" wire:click="removeNewAvatar" class="text-xs text-red-500 hover:underline mt-1">{{ __('Remove new avatar') }}</button>
                    @endif

                    @error('newAvatar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Username -->
            <div>
                <flux:input
                    wire:model="username"
                    :label="__('Username')"
                    type="text"
                    required
                    autofocus
                    autocomplete="username"
                    :placeholder="__('Unique username')"
                />
            </div>

            <!-- First Name & Last Name (Side by Side) -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <flux:input
                        wire:model="name_first"
                        :label="__('First Name')"
                        type="text"
                        autocomplete="given-name"
                        :placeholder="__('Your first name (optional)')"
                    />
                </div>
                <div class="flex-1">
                    <flux:input
                        wire:model="name_last"
                        :label="__('Last Name')"
                        type="text"
                        autocomplete="family-name"
                        :placeholder="__('Your last name (optional)')"
                    />
                </div>
            </div>

            <!-- Email Address -->
            <div>
                <flux:input
                    wire:model="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4 text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Handle -->
            <div>
                <flux:input
                    wire:model="handle"
                    :label="__('Profile Handle (@)')"
                    type="text"
                    autocomplete="off"
                    :placeholder="__('e.g. your_username')"
                />
                 <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Your unique public profile handle (e.g., ' . env('APP_URL') . '/@your_username). Leave empty for default.') }}
                </p>
            </div>

            <!-- Bio -->
            <div>
                <flux:input
                    wire:model="bio"
                    :label="__('Bio')"
                    type="textarea"
                    rows="3"
                    :placeholder="__('Tell us about yourself')"
                />
            </div>

            <!-- Location -->
            <div>
                <flux:input
                    wire:model="location"
                    :label="__('Location')"
                    type="text"
                    :placeholder="__('e.g. London, UK')"
                />
            </div>

            <!-- Website -->
            <div>
                <flux:input
                    wire:model="website"
                    :label="__('Website')"
                    type="url"
                    :placeholder="__('https://yourwebsite.com')"
                />
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>