<div class="container mx-auto p-6">
    <x-breadcrumbs :items="$breadcrumbs" />

    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">{{ __('support.create_ticket_title') }}</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="createTicket" class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 space-y-6">
        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('support.field_subject') }}</label>
            <input type="text" id="subject" wire:model="subject" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none">
            @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="first_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('support.field_first_message') }}</label>
            <textarea id="first_message" wire:model="first_message" rows="5" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none"></textarea>
            @error('first_message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('strings.priority') }}</label>
            <select id="priority" wire:model="priority" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none">
                <option value="low">{{ __('strings.priority_low') }}</option>
                <option value="medium">{{ __('strings.priority_medium') }}</option>
                <option value="high">{{ __('strings.priority_high') }}</option>
                <option value="urgent">{{ __('strings.priority_urgent') }}</option>
            </select>
            @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('strings.submit_button') }} {{ __('strings.ticket') }}</button>
        </div>
    </form>
</div>