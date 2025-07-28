<div x-data="{ show: @entangle('showModal') }" x-cloak>
    <div x-show="show" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto my-8 transform transition-all">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $profileUser->display_name ?? '' }}'s {{ __('profile.followers_modal_title') }}</h2>
                <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            @if ($followers->isEmpty())
                <p class="text-gray-600 dark:text-gray-400">{{ __('profile.no_public_followers') }}</p>
            @else
                <ul class="divide-y divide-gray-200 dark:divide-zinc-700 max-h-80 overflow-y-auto">
                    @foreach ($followers as $follower)
                        <li class="py-3 flex items-center space-x-3">
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $follower->avatar }}" alt="{{ $follower->display_name }}">
                            <div>
                                <a href="{{ $follower->profile_link }}" wire:navigate class="text-gray-900 dark:text-gray-100 font-medium hover:underline">{{ $follower->display_name }}</a>
                                @if ($follower->handle)
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">@{{ $follower->handle }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>