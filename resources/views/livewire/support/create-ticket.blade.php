<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">{{ __('Create New Ticket') }}</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="createTicket" class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 space-y-6">
        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Subject') }}</label>
            <input type="text" id="subject" wire:model="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100">
            @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description (Optional)') }}</label>
            <textarea id="description" wire:model="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100"></textarea>
            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Priority') }}</label>
            <select id="priority" wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100">
                <option value="low">{{ __('Low') }}</option>
                <option value="medium">{{ __('Medium') }}</option>
                <option value="high">{{ __('High') }}</option>
                <option value="urgent">{{ __('Urgent') }}</option>
            </select>
            @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('Submit Ticket') }}</button>
        </div>
    </form>
</div>