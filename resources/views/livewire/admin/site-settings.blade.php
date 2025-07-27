<div class="container mx-auto p-6">
    <x-breadcrumbs :items="$breadcrumbs" />

    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">{{ __('Site Settings') }}</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 p-4 rounded-md mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 space-y-6">
        @foreach ($settings as $key => $setting)
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="setting-{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                        {{ str_replace('_', ' ', $key) }}
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $setting['description'] }}</p>

                    @if ($setting['type'] === 'boolean')
                        <input type="checkbox" id="setting-{{ $key }}" wire:model.live="settings.{{ $key }}.value" wire:change="updateSetting('{{ $key }}')" class="form-checkbox h-5 w-5 text-blue-600 rounded">
                    @elseif ($setting['type'] === 'enum')
                        <select id="setting-{{ $key }}" wire:model.live="settings.{{ $key }}.value" wire:change="updateSetting('{{ $key }}')" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach ($setting['options'] as $option)
                                <option value="{{ $option }}">{{ Str::title($option) }}</option>
                            @endforeach
                        </select>
                    @elseif ($setting['type'] === 'text')
                        <textarea id="setting-{{ $key }}" wire:model.live="settings.{{ $key }}.value" wire:change="updateSetting('{{ $key }}')" rows="3" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    @else
                        <input type="text" id="setting-{{ $key }}" wire:model.live="settings.{{ $key }}.value" wire:change="updateSetting('{{ $key }}')" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @endif

                    @error('settings.' . $key . '.value') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                </div>
                <button type="button" wire:click="resetSetting('{{ $key }}')" class="px-3 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600 text-sm mt-8">{{ __('Reset') }}</button>
            </div>
            @unless ($loop->last)
                <hr class="border-gray-200 dark:border-zinc-700" />
            @endunless
        @endforeach
    </div>
</div>