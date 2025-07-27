<div class="container mx-auto p-6 flex flex-col h-full">
    <x-breadcrumbs :items="$breadcrumbs" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</h1>
        <a href="{{ route('support.tickets.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600">{{ __('Back to Tickets') }}</a>
    </div>

    {{-- Flash message display area --}}
    <div x-data="{
        show: false,
        type: '',
        message: '',
        colorClasses: {
            success: 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200',
            info: 'bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200',
            error: 'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200'
        },
        timeout: null,
        init() {
            Livewire.on('show-flash-message', ({ type, message }) => {
                this.type = type;
                this.message = message;
                this.show = true;
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.show = false;
                }, 3000); // Message visible for 3 seconds
            });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-y-full"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-full"
    class="p-4 rounded-md mb-4"
    :class="colorClasses[type]"
    style="display: none;"
    >
        <span x-text="message"></span>
    </div>
    {{-- End Flash message display area --}}


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
                        $isLogMessage = Str::startsWith($message->message, '[LOG] ');

                        $bubbleClasses = 'max-w-[80%] rounded-xl p-3 shadow-sm';
                        $timestampClasses = 'font-normal text-[10px] ml-2';

                        if ($isLogMessage) {
                            $wrapperClasses = 'flex justify-center w-full';
                            $bubbleClasses = 'bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-400 text-xs px-3 py-1.5 rounded-full italic max-w-lg';
                            $timestampClasses = 'hidden';
                        } elseif ($isAuthUser) {
                            $bubbleClasses .= ' bg-blue-600 text-white dark:bg-blue-700';
                            $timestampClasses .= ' text-blue-200 dark:text-blue-300';
                        } else {
                            $bubbleClasses .= ' bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200';
                            $timestampClasses .= ' text-gray-400 dark:text-gray-500';
                        }

                        $messageContainerClasses = 'flex items-start'; // Flex container for avatar and bubble
                        $avatarClasses = 'h-8 w-8 rounded-full object-cover';
                        $bubbleContentOrder = ''; // Order within the message container

                        if ($isLogMessage) {
                             // No avatar or specific ordering for log messages
                        } else {
                            if ($isAuthAdmin) {
                                if ($isAdminUser) {
                                    // Admin message from Admin viewer: Image on right, bubble on left
                                    $messageContainerClasses .= ' justify-end';
                                    $avatarClasses .= ' order-2 ml-2';
                                    $bubbleContentOrder = 'order-1';
                                } else {
                                    // User message from Admin viewer: Image on left, bubble on right
                                    $messageContainerClasses .= ' justify-start';
                                    $avatarClasses .= ' order-1 mr-2';
                                    $bubbleContentOrder = 'order-2';
                                }
                            } else {
                                // If current viewer is a regular user
                                if ($isAuthUser) {
                                     // User's own message: Image on right, bubble on left
                                     $messageContainerClasses .= ' justify-end';
                                     $avatarClasses .= ' order-2 ml-2';
                                     $bubbleContentOrder = 'order-1';
                                } else {
                                     // Admin/Other user's message: Image on left, bubble on right
                                     $messageContainerClasses .= ' justify-start';
                                     $avatarClasses .= ' order-1 mr-2';
                                     $bubbleContentOrder = 'order-2';
                                }
                            }
                        }
                    @endphp

                    @if ($isLogMessage)
                        <div class="{{ $wrapperClasses }}">
                            <p class="{{ $bubbleClasses }} whitespace-pre-wrap">{{ Str::after($message->message, '[LOG] ') }}</p>
                        </div>
                    @else
                        <div class="{{ $messageContainerClasses }}">
                            <img class="{{ $avatarClasses }}" src="{{ $message->user->avatar }}" alt="{{ $message->user->display_name }}">
                            <div class="{{ $bubbleClasses }} {{ $bubbleContentOrder }}">
                                <p class="text-xs font-semibold {{ $isAuthUser ? 'text-white' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $message->user->display_name }}
                                    <span class="{{ $timestampClasses }}">{{ $message->created_at->format('M d, H:i') }}</span>
                                </p>
                                <p class="mt-1 {{ $isAuthUser ? 'text-white' : 'text-gray-800 dark:text-gray-200' }} whitespace-pre-wrap">{{ $message->message }}</p>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-center text-gray-600 dark:text-gray-400">{{ __('No messages yet. Send the first one!') }}</p>
                @endforelse
            </div>

            <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2">
                <textarea
                    wire:model.live="messageContent"
                    placeholder="{{ __('Type your message...') }}"
                    rows="2"
                    class="p-2 flex-grow rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none"
                    @if($ticket->status === 'closed' || $ticket->status === 'resolved') disabled @endif
                ></textarea>
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    @if($ticket->status === 'closed' || $ticket->status === 'resolved') disabled @endif
                >
                    {{ __('Send') }}
                </button>
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
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">{{ __('Assigned to') }}: <span class="font-normal">{{ $ticket->assignedAgent->display_name ?? __('None') }}</span></p>
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
                                <option value="{{ $agent->id }}">{{ $agent->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>