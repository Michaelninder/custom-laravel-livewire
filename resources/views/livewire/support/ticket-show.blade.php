<div class="container mx-auto p-6 flex flex-col h-full">
    <x-breadcrumbs :items="$breadcrumbs" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</h1>
        <a href="{{ route('support.tickets.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600">{{ __('Back to Tickets') }}</a>
    </div>

    @if (session()->has('settings_updated') || session()->has('status_updated'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-md mb-4">
            {{ session('settings_updated') ?? session('status_updated') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 flex-grow flex flex-col md:flex-row gap-6">
        {{-- Chat/Messages Section --}}
        <div class="flex-1 flex flex-col">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Messages') }}</h2>
            <div class="flex-grow overflow-y-auto p-4 border border-gray-200 dark:border-zinc-700 rounded-md bg-gray-50 dark:bg-zinc-900 mb-4 space-y-4">
                @forelse ($ticket->messages->sortBy('created_at') as $message)
                    @php
                        $isAuthUser = ($message->user->id === Auth::id());
                        $isAdminUser = $message->user->isAdmin();
                        $isAuthAdmin = Auth::user()->isAdmin();

                        $bubbleClasses = 'max-w-[80%] rounded-xl p-3 shadow-sm';
                        $wrapperClasses = 'flex';
                        $timestampClasses = 'font-normal text-[10px] ml-2';

                        // Base coloring
                        if ($isAuthUser) {
                            $bubbleClasses .= ' bg-blue-600 text-white dark:bg-blue-700';
                            $timestampClasses .= ' text-blue-200 dark:text-blue-300';
                        } else {
                            $bubbleClasses .= ' bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200';
                            $timestampClasses .= ' text-gray-400 dark:text-gray-500';
                        }

                        // Positioning logic
                        if ($isAuthAdmin) {
                            if ($isAdminUser) {
                                $wrapperClasses .= ' justify-end';
                            } else {
                                $wrapperClasses .= ' justify-start';
                            }
                        } else {
                            if ($isAuthUser) {
                                 $wrapperClasses .= ' justify-end';
                            } else {
                                 $wrapperClasses .= ' justify-start';
                            }
                        }
                    @endphp
                    <div class="{{ $wrapperClasses }}">
                        <div class="{{ $bubbleClasses }}">
                            <p class="text-xs font-semibold {{ $isAuthUser ? 'text-white' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $message->user->username }}
                                <span class="{{ $timestampClasses }}">{{ $message->created_at->format('M d, H:i') }}</span>
                            </p>
                            <p class="mt-1 {{ $isAuthUser ? 'text-white' : 'text-gray-800 dark:text-gray-200' }}">{{ $message->message }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-600 dark:text-gray-400">{{ __('No messages yet. Send the first one!') }}</p>
                @endforelse
            </div>

            <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2">
                <textarea wire:model.live="messageContent" placeholder="{{ __('Type your message...') }}" rows="2" class="p-2 flex-grow rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none"></textarea>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('Send') }}</button>
            </form>
            @error('messageContent') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Settings Section --}}
        <div class="w-full md:w-80 flex-shrink-0 bg-gray-50 dark:bg-zinc-700 rounded-lg p-6 space-y-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Ticket Details') }}</h2>

            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Current Status') }}: <span class="capitalize font-normal {{ [
                    'open' => 'text-green-600',
                    'pending' => 'text-yellow-600',
                    'closed' => 'text-red-600',
                    'resolved' => 'text-purple-600',
                ][$ticket->status] ?? '' }}">{{ $ticket->status }}</span></p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">{{ __('Current Priority') }}: <span class="capitalize font-normal">{{ $ticket->priority }}</span></p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">{{ __('Assigned to') }}: <span class="font-normal">{{ $ticket->assignedAgent->username ?? __('None') }}</span></p>
            </div>

            @if ($ticket->status === 'closed' || $ticket->status === 'resolved')
                <button wire:click="reopenTicket" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-center">{{ __('Reopen Ticket') }}</button>
            @else
                <button wire:click="closeTicket" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-center">{{ __('Close Ticket') }}</button>
            @endif


            @if (Auth::user()->isAdmin())
                <div class="mt-6 space-y-4 pt-4 border-t border-gray-200 dark:border-zinc-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Admin Actions') }}</h3>

                    <div>
                        <label for="selectedStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Update Status') }}</label>
                        <select wire:model="selectedStatus" wire:change="updateTicketSettings" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="open">{{ __('Open') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="closed">{{ __('Closed') }}</option>
                            <option value="resolved">{{ __('Resolved') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="selectedPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Update Priority') }}</label>
                        <select wire:model="selectedPriority" wire:change="updateTicketSettings" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="low">{{ __('Low') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="high">{{ __('High') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="selectedAgentId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Assign Agent') }}</label>
                        <select wire:model="selectedAgentId" wire:change="updateTicketSettings" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach ($supportAgents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>