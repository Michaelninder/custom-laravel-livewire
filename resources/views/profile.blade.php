@extends('layouts.app') {{-- Extend the new main layout --}}

@section('content') {{-- Define the content section --}}
    <x-slot name="header"> {{-- This slot would typically be defined in layouts/app for page titles --}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $user->display_name }}'s Profile
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <x-breadcrumbs :items="$breadcrumbs" />

        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg p-6 space-y-6">
            <div class="flex items-center space-x-6">
                <img class="h-24 w-24 rounded-full object-cover" src="{{ $user->avatar }}" alt="{{ $user->display_name }}">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $user->display_name }}</h1>
                    @if ($user->handle)
                        <p class="text-gray-600 dark:text-gray-400 text-lg">@<span class="font-semibold">{{ $user->handle }}</span></p>
                    @endif
                    <p class="text-gray-700 dark:text-gray-300 mt-2">{{ $user->bio ?? __('profile.no_bio_set') }}</p>

                    <div class="flex items-center mt-4 space-x-4 text-sm text-gray-600 dark:text-gray-400">
                        @if ($user->location)
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $user->location }}
                            </div>
                        @endif
                        @if ($user->website)
                            <a href="{{ $user->website }}" target="_blank" class="flex items-center hover:underline">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-.758l-.207.207m0 0l-1.102 1.101m-.758-.758a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.102 1.101m-.758-.758l.207-.207M9.293 13.707l1.414-1.414A5 5 0 0011 10c0-1.291-.84-2.38-2-2.732"></path></svg>
                                {{ str_replace(['http://', 'https://'], '', $user->website) }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-6 mt-6">
                <button
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400"
                    onclick="Livewire.find('{{ \Livewire\str()->kebab(\App\Livewire\UserProfileFollowersList::class) }}').dispatch('openFollowersModal')"
                >
                    <span class="font-semibold">{{ $user->followers()->count() }}</span> {{ __('profile.followers') }}
                </button>

                <button
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400"
                    onclick="Livewire.find('{{ \Livewire\str()->kebab(\App\Livewire\UserProfileFollowingList::class) }}').dispatch('openFollowingModal')"
                >
                    <span class="font-semibold">{{ $user->following()->count() }}</span> {{ __('profile.following') }}
                </button>

                @auth
                    @if (Auth::id() !== $user->id)
                        <livewire:user-profile-follow-button :user="$user" />
                    @endif
                @endauth
            </div>

            <div class="mt-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('profile.recent_activity') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('profile.activity_placeholder') }}</p>
            </div>
        </div>
    </div>

    {{-- Manual Livewire Modals --}}
    <livewire:user-profile-followers-list :user-id="$user->id" />
    <livewire:user-profile-following-list :user-id="$user->id" />

@endsection