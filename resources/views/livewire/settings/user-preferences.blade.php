<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Preferences')" :subheading="__('Manage your personal application preferences.')">
        <form wire:submit="updatePreferences" class="my-6 w-full space-y-6">

            <div>
                <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Application Theme') }}</label>
                <flux:select wire:model="theme" placeholder="{{ __('Select a theme') }}" class="w-full">
                    <flux:select.option value="system">{{ __('System default') }}</flux:select.option>
                    <flux:select.option value="light">{{ __('Light') }}</flux:select.option>
                    <flux:select.option value="dark">{{ __('Dark') }}</flux:select.option>
                </flux:select>
            </div>

            <div>
                <flux:checkbox wire:model="email_notifications_enabled" :label="__('Enable Email Notifications')" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Receive important updates and activity alerts via email.') }}</p>
            </div>

            <div>
                <flux:checkbox wire:model="newsletter_opt_in" :label="__('Subscribe to Newsletter')" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Get occasional news and promotions.') }}</p>
            </div>

            <div>
                <label for="preferred_language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Preferred Language') }}</label>
                <flux:select wire:model="preferred_language" placeholder="{{ __('Select a language') }}" class="w-full">
                    <flux:select.option value="en">{{ __('English') }}</flux:select.option>
                    <flux:select.option value="de">{{ __('Deutsch') }}</flux:select.option>
                    <flux:select.option value="fr">{{ __('Français') }}</flux:select.option>
                </flux:select>
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save Preferences') }}</flux:button>

                <x-action-message class="me-3" on="preferences-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>