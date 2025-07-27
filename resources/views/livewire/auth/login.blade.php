<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
    <div class="mt-6 border-t border-gray-200 pt-6">
        <p class="text-center text-gray-500 mb-4">Or login with social accounts</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('socialite.redirect', ['provider' => 'github']) }}"
               class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
        <div class="mt-4 text-red-500 text-sm">
            {{ session('error') }}
        </div>
    @endif
</div>
