<div class="container mx-auto p-6">
    <x-breadcrumbs :items="$breadcrumbs" />

    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">{{ __('nav.admin_dashboard') }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Total Users --}}
        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('strings.total_users') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                </div>
                <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.29-6-3.22.03-1.99 4-3.08 6-3.08s5.97 1.09 6 3.08c-1.29 1.93-3.5 3.22-6 3.22z"/></svg>
            </div>
        </div>

        {{-- Card 2: Total Tickets --}}
        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('strings.total_tickets') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalTickets }}</p>
                </div>
                <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 14v-2h8v2H6zm10 0h2v-2h-2v2zM6 10V8h12v2H6z"/></svg>
            </div>
        </div>

        {{-- Card 3: Open Tickets --}}
        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('strings.open_tickets') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $openTickets }}</p>
                </div>
                <svg class="h-8 w-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-10h2V7h-2v4z"/></svg>
            </div>
        </div>

        {{-- Card 4: Closed Tickets --}}
        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('strings.closed_tickets') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $closedTickets }}</p>
                </div>
                <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M10 18h4v-2h-4v2zm-6-2h4V8H4v6zm12 0h4V8h-4v6zM20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            </div>
        </div>
    </div>
</div>