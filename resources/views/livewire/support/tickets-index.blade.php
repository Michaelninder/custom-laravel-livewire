<div class="container mx-auto p-6">
    <x-breadcrumbs :items="$breadcrumbs" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('support.tickets_index_title') }}</h1>
        <a href="{{ route('support.tickets.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('strings.create') }} {{ __('strings.ticket') }}</a>
    </div>

    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" wire:model.live="search" placeholder="{{ __('strings.search_placeholder') }} {{ __('strings.tickets') }}..." class="p-2 border border-gray-300 rounded-md shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

        <select wire:model.live="statusFilter" class="p-2 border border-gray-300 rounded-md shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">{{ __('support.all_statuses_option') }}</option>
            <option value="open">{{ __('strings.status_open') }}</option>
            <option value="pending">{{ __('strings.status_pending') }}</option>
            <option value="closed">{{ __('strings.status_closed') }}</option>
            <option value="resolved">{{ __('strings.status_resolved') }}</option>
        </select>

        <select wire:model.live="priorityFilter" class="p-2 border border-gray-300 rounded-md shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">{{ __('support.all_priorities_option') }}</option>
            <option value="low">{{ __('strings.priority_low') }}</option>
            <option value="medium">{{ __('strings.priority_medium') }}</option>
            <option value="high">{{ __('strings.priority_high') }}</option>
            <option value="urgent">{{ __('strings.priority_urgent') }}</option>
        </select>
    </div>

    @if ($tickets->isEmpty())
        <p class="text-center text-gray-600 dark:text-gray-400">{{ __('support.no_tickets_found') }}</p>
    @else
        <div class="bg-white dark:bg-zinc-800 shadow overflow-hidden sm:rounded-lg">
            <ul class="divide-y divide-gray-200 dark:divide-zinc-700">
                @foreach ($tickets as $ticket)
                    <li class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 transition ease-in-out duration-150">
                        <a href="{{ route('support.tickets.show', $ticket) }}" class="block">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $ticket->subject }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('strings.opened_by') }} {{ $ticket->user->display_name }} |
                                        {{ __('support.status_title') }}: <span class="capitalize {{ [
                                            'open' => 'text-green-600',
                                            'pending' => 'text-yellow-600',
                                            'closed' => 'text-red-600',
                                            'resolved' => 'text-purple-600',
                                        ][$ticket->status] ?? '' }}">{{ __('strings.status_' . $ticket->status) }}</span> |
                                        {{ __('strings.priority_' . $ticket->priority) }}: <span class="capitalize">{{ __('strings.priority_' . $ticket->priority) }}</span> |
                                        @if ($ticket->assignedAgent)
                                            {{ __('strings.assigned_to') }}: {{ $ticket->assignedAgent->display_name }} |
                                        @endif
                                        {{ $ticket->last_replied_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div>
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif
</div>