<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @isset($handle)
                <livewire:user-profile :handle="$handle" />
            @else
                <livewire:user-profile :user="$user" />
            @endisset
        </div>
    </div>
</x-app-layout>