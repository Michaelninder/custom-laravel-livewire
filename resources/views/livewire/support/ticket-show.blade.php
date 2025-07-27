<div class="container mx-auto p-6 flex flex-col h-full">
    <x-breadcrumbs :items="$breadcrumbs" />

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</h1>
        <a href="{{ route('support.tickets.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600">{{ __('strings.back_button') }} {{ __('strings.to') }} {{ __('strings.tickets') }}</a>
    </div>

    @if (session()->has('settings_updated') || session()->has('status_updated'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-md mb-4">
            {{ session('settings_updated') ?? session('status_updated') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200 p-4 rounded-md mb-4">
            {{ session('info') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 p-4 rounded-md mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 flex-grow flex flex-col md:flex-row gap-6">
        {{-- Chat/Messages Section --}}
        <div class="flex-1 flex flex-col">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('support.conversation_title') }}</h2>
            <div class="flex-grow overflow-y-auto p-4 border border-gray-200 dark:border-zinc-700 rounded-md bg-gray-50 dark:bg-zinc-900 mb-4 space-y-4">
                @forelse ($combinedFeed as $item)
                    @if ($item->is_log)
                        {{-- Log Message --}}
                        <div class="flex justify-center w-full">
                            <p class="bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-400 text-xs px-3 py-1.5 rounded-full italic max-w-lg whitespace-pre-wrap">
                                {{ $item->formatted_message }}
                                <span class="font-normal text-[10px] ml-2">{{ $item->created_at->format('M d, H:i') }}</span>
                            </p>
                        </div>
                    @else
                        {{-- Regular Message --}}
                        @php
                            $message = $item;
                            $isAuthUser = ($message->user->id === Auth::id());
                            $isAdminUser = $message->user->isAdmin();
                            $isAuthAdmin = Auth::user()->isAdmin();

                            $bubbleClasses = 'max-w-[80%] rounded-xl p-3 shadow-sm';
                            $messageContainerClasses = 'flex items-start';
                            $avatarClasses = 'h-8 w-8 rounded-full object-cover';
                            $timestampClasses = 'font-normal text-[10px] ml-2';
                            $bubbleContentOrder = '';

                            if ($isAuthUser) {
                                $bubbleClasses .= ' bg-blue-600 text-white dark:bg-blue-700';
                                $timestampClasses .= ' text-blue-200 dark:text-blue-300';
                            } else {
                                $bubbleClasses .= ' bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200';
                                $timestampClasses .= ' text-gray-400 dark:text-gray-500';
                            }

                            if ($isAuthAdmin) {
                                if ($isAdminUser) {
                                    $messageContainerClasses .= ' justify-end';
                                    $avatarClasses .= ' order-2 ml-2';
                                    $bubbleContentOrder = 'order-1';
                                } else {
                                    $messageContainerClasses .= ' justify-start';
                                    $avatarClasses .= ' order-1 mr-2';
                                    $bubbleContentOrder = 'order-2';
                                }
                            } else {
                                if ($isAuthUser) {
                                     $messageContainerClasses .= ' justify-end';
                                     $avatarClasses .= ' order-2 ml-2';
                                     $bubbleContentOrder = 'order-1';
                                } else {
                                     $messageContainerClasses .= ' justify-start';
                                     $avatarClasses .= ' order-1 mr-2';
                                     $bubbleContentOrder = 'order-2';
                                }
                            }
                        @endphp
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
                    <p class="text-center text-gray-600 dark:text-gray-400">{{ __('support.no_conversation_history') }}</p>
                @endforelse
            </div>

            <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2">
                <textarea
                    wire:model.live="messageContent"
                    placeholder="{{ __('strings.your_message') }}"
                    rows="2"
                    class="p-2 flex-grow rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-700 dark:border-zinc-600 dark:text-gray-100 focus:outline-none"
                    @if($ticket->status === 'closed' || $ticket->status === 'resolved') disabled @endif
                ></textarea>
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    @if($ticket->status === 'closed' || $ticket->status === 'resolved') disabled @endif
                >
                    {{ __('strings.send_button') }}
                </button>
            </form>
            @error('messageContent') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Settings Section --}}
        <div class="w-full md:w-80 flex-shrink-0 bg-gray-50 dark:bg-zinc-700 rounded-lg p-6 space-y-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('support.ticket_details_title') }}</h2>

            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('support.current_status') }}: <span class="capitalize font-normal {{ [
                    'open' => 'text-green-600',
                    'pending' => 'text-yellow-600',
                    'closed' => 'text-red-600',
                    'resolved' => 'text-purple-600',
                ][$ticket->status] ?? '' }}">{{ __('strings.status_' . $ticket->status) }}</span></p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">{{ __('support.current_priority') }}: <span class="capitalize font-normal">{{ __('strings.priority_' . $ticket->priority) }}</span></p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">{{ __('strings.assigned_to') }}: <span class="font-normal">{{ $ticket->assignedAgent->display_name ?? __('strings.none') }}</span></p>
            </div>

            @if ($ticket->status === 'closed' || $ticket->status === 'resolved')
                <button wire:click="reopenTicket" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-center">{{ __('support.reopen_ticket_button') }}</button>
            @else
                <button wire:click="closeTicket" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-center">{{ __('support.close_ticket_button') }}</button>
            @endif

            @if (Auth::user()->isAdmin())
                <div class="mt-6 space-y-4 pt-4 border-t border-gray-200 dark:border-zinc-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('support.admin_actions_title') }}</h3>

                    <div>
                        <label for="selectedStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('support.update_status_field') }}</label>
                        <select wire:model="selectedStatus" wire:change="updateTicketSettings" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="open">{{ __('strings.status_open') }}</option>
                            <option value="pending">{{ __('strings.status_pending') }}</option>
                            <option value="closed">{{ __('strings.status_closed') }}</option>
                            <option value="resolved">{{ __('strings.status_resolved') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="selectedPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('support.update_priority_field') }}</label>
                        <select wire:model="selectedPriority" wire:change="updateTicketSettings" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="low">{{ __('strings.priority_low') }}</option>
                            <option value="medium">{{ __('strings.priority_medium') }}</option>
                            <option value="high">{{ __('strings.priority_high') }}</option>
                            <option value="urgent">{{ __('strings.priority_urgent') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="selectedAgentId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('support.assign_agent_field') }}</label>
                        <select wire:model="selectedAgentId" wire:change="updateTicketSettings" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('strings.unassigned') }}</option>
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