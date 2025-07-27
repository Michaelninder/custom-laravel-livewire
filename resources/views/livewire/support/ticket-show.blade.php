<div class="container mx-auto p-6 flex flex-col h-full">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</h1>
        <a href="{{ route('support.tickets.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600">{{ __('Back to Tickets') }}</a>
    </div>

    @if (session()->has('settings_updated'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-md mb-4">
            {{ session('settings_updated') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 mb-6 flex-grow flex flex-col">
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Opened by') }}: {{ $ticket->user->username }} |
                {{ __('Status') }}: <span class="capitalize {{ [
                    'open' => 'text-green-600',
                    'pending' => 'text-yellow-600',
                    'closed' => 'text-red-600',
                    'resolved' => 'text-purple-600',
                ][$ticket->status] ?? '' }}">{{ $ticket->status }}</span> |
                {{ __('Priority') }}: <span class="capitalize">{{ $ticket->priority }}</span> |
                @if ($ticket->assignedAgent)
                    {{ __('Assigned to') }}: {{ $ticket->assignedAgent->username }}
                @else
                    {{ __('Assigned to') }}: {{ __('None') }}
                @endif
            </p>
            @if ($ticket->description)
                <p class="mt-2 text-gray-800 dark:text-gray-200">{{ $ticket->description }}</p>
            @endif
        </div>

        @if (Auth::user()->isAdmin())
            <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded-md mb-4 flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="selectedStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Update Status') }}</label>
                    <select wire:model="selectedStatus" wire:change="updateTicketSettings" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100">
                        <option value="open">{{ __('Open') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                        <option value="resolved">{{ __('Resolved') }}</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="selectedPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Update Priority') }}</label>
                    <select wire:model="selectedPriority" wire:change="updateTicketSettings" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100">
                        <option value="low">{{ __('Low') }}</option>
                        <option value="medium">{{ __('Medium') }}</option>
                        <option value="high">{{ __('High') }}</option>
                        <option value="urgent">{{ __('Urgent') }}</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="selectedAgentId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Assign Agent') }}</label>
                    <select wire:model="selectedAgentId" wire:change="updateTicketSettings" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach ($supportAgents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->username }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="flex-grow overflow-y-auto p-4 border border-gray-200 dark:border-zinc-700 rounded-md bg-gray-50 dark:bg-zinc-900 mb-4 space-y-4">
            @forelse ($ticket->messages->sortBy('created_at') as $message)
                @php
                    $isAuthUser = ($message->user->id === Auth::id());
                    $isAdminUser = $message->user->isAdmin();
                    $isAuthAdmin = Auth::user()->isAdmin();

                    $bubbleClasses = 'max-w-[80%] rounded-xl p-3 shadow-sm';
                    $wrapperClasses = 'flex';

                    if ($isAuthUser) {
                        $wrapperClasses .= ' justify-end';
                        $bubbleClasses .= ' bg-blue-600 text-white dark:bg-blue-700';
                    } else {
                        $wrapperClasses .= ' justify-start';
                        $bubbleClasses .= ' bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200'; // Other user's color
                    }

                    // Admin vs User side if authenticated user is viewing AND message sender is admin/user
                    if ($isAuthAdmin) {
                        // Admin viewing: admin messages on one side, user messages on other
                        if ($isAdminUser) {
                            $wrapperClasses = 'flex justify-end'; // Admin sender is on the right for current admin viewer
                        } else {
                            $wrapperClasses = 'flex justify-start'; // User sender is on the left for current admin viewer
                        }
                    } else {
                        // User viewing: current user's messages on right, admin/other user's on left
                        if ($isAuthUser) {
                             $wrapperClasses = 'flex justify-end';
                        } else {
                             $wrapperClasses = 'flex justify-start';
                        }
                    }
                @endphp
                <div class="{{ $wrapperClasses }}">
                    <div class="{{ $bubbleClasses }}">
                        <p class="text-xs font-semibold {{ $isAuthUser ? 'text-white' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $message->user->username }}
                            <span class="font-normal text-gray-400 dark:text-gray-500 text-[10px] ml-2">{{ $message->created_at->format('M d, H:i') }}</span>
                        </p>
                        <p class="mt-1 {{ $isAuthUser ? 'text-white' : 'text-gray-800 dark:text-gray-200' }}">{{ $message->message }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-600 dark:text-gray-400">{{ __('No messages yet. Send the first one!') }}</p>
            @endforelse
        </div>

        {{-- Message Input --}}
        <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2">
            <textarea wire:model.live="messageContent" placeholder="{{ __('Type your message...') }}" rows="2" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100"></textarea>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('Send') }}</button>
        </form>
        @error('messageContent') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
    </div>
</div>