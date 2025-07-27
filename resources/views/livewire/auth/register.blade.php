<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Username -->
        <flux:input
            wire:model="username"
            :label="__('Username')"
            type="text"
            autofocus
            autocomplete="username"
            :placeholder="__('Unique username')"
        />

        <!-- First Name & Last Name (Side by Side) -->
        <div class="flex gap-4">
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
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="mt-6 border-t border-gray-200 dark:border-zinc-700 pt-6">
        <p class="text-center text-zinc-600 dark:text-zinc-400 mb-4">{{ __('Or register with social accounts') }}</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('socialite.redirect', ['provider' => 'github']) }}"
               class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700">
                <img src="/icons/github-logo.svg" alt="GitHub" class="h-5 w-5 mr-2">
                GitHub
            </a>
            <a href="{{ route('socialite.redirect', ['provider' => 'discord']) }}"
               class="flex items-center justify-center px-4 py-2 border border-blue-600 rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <img src="/icons/discord-outline.svg" alt="Discord" class="h-5 w-5 mr-2">
                Discord
            </a>
            <a href="{{ route('socialite.redirect', ['provider' => 'twitch']) }}"
               class="flex items-center justify-center px-4 py-2 border border-purple-600 rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                <img src="/icons/twitch-logo.svg" alt="Twitch" class="h-5 w-5 mr-2">
                Twitch
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="mt-4 text-red-500 text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>