<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">

        @php
            $navType = App\Models\SiteSetting::get('nav_type', 'sidebar'); // Get setting, default to sidebar
        @endphp

        @if ($navType === 'sidebar')
            {{-- Sidebar Layout (for desktop, often collapses on mobile) --}}
            <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>

                <flux:navlist variant="outline">
                    <flux:navlist.group :heading="__('nav.platform_group')" class="grid">
                        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('nav.dashboard') }}</flux:navlist.item>
                        <flux:navlist.item icon="ticket" :href="route('support.tickets.index')" :current="request()->routeIs('support.tickets.*')" wire:navigate>{{ __('nav.ticket_support') }}</flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>

                <flux:spacer />

                <flux:navlist variant="outline">
                    <flux:navlist.item icon="folder-git-2" href="https://github.com/Michaelninder/custom-laravel-livewire" target="_blank">
                        {{ __('nav.repository') }}
                    </flux:navlist.item>
                    @if (Auth::check() && Auth::user()->isAdmin())
                        <flux:navlist.item icon="cog" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>
                            {{ __('nav.admin_site_settings') }}
                        </flux:navlist.item>
                    @endif
                </flux:navlist>

                {{-- Desktop User Menu in Sidebar --}}
                <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                    <flux:profile
                        :name="auth()->user()->display_name"
                        :initials="auth()->user()->initials()"
                        icon:trailing="chevrons-up-down"
                    />

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->display_name }}" class="h-full w-full object-cover">
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->display_name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('nav.settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('nav.logout') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:sidebar>

            {{-- Mobile User Menu (for sidebar layout on mobile) --}}
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                <flux:spacer />
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />
                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->display_name }}" class="h-full w-full object-cover">
                                    </span>
                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->display_name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('nav.settings') }}</flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('nav.logout') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

        @elseif ($navType === 'topbar')
            {{-- Topbar Layout --}}
            <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                    <x-app-logo />
                </a>

                <flux:navbar class="-mb-px max-lg:hidden">
                    <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('nav.dashboard') }}
                    </flux:navbar.item>
                    <flux:navbar.item icon="ticket" :href="route('support.tickets.index')" :current="request()->routeIs('support.tickets.*')" wire:navigate>
                        {{ __('nav.ticket_support') }}
                    </flux:navbar.item>
                </flux:navbar>

                <flux:spacer />

                <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                    <flux:tooltip :content="__('strings.search_placeholder')" position="bottom">
                        <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('strings.search_placeholder')" />
                    </flux:tooltip>
                    <flux:tooltip :content="__('nav.repository')" position="bottom">
                        <flux:navbar.item
                            class="h-10 max-lg:hidden [&>div>svg]:size-5"
                            icon="folder-git-2"
                            href="https://github.com/Michaelninder/custom-laravel-livewire"
                            target="_blank"
                            :label="__('nav.repository')"
                        />
                    </flux:tooltip>
                     @if (Auth::check() && Auth::user()->isAdmin())
                        <flux:tooltip :content="__('nav.admin_site_settings')" position="bottom">
                            <flux:navbar.item icon="cog" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>{{ __('nav.admin_site_settings') }}</flux:navbar.item>
                        </flux:tooltip>
                    @endif
                </flux:navbar>

                {{-- Desktop User Menu in Topbar --}}
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        class="cursor-pointer"
                        :initials="auth()->user()->initials()"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->display_name }}" class="h-full w-full object-cover">
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->display_name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('nav.settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('nav.logout') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

            {{-- Mobile Menu (for topbar layout, appears as sidebar) --}}
            <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>

                <flux:navlist variant="outline">
                    <flux:navlist.group :heading="__('nav.platform_group')">
                        <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                          {{ __('nav.dashboard') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="ticket" :href="route('support.tickets.index')" :current="request()->routeIs('support.tickets.*')" wire:navigate>
                          {{ __('nav.ticket_support') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>

                <flux:spacer />

                <flux:navlist variant="outline">
                    <flux:navlist.item icon="folder-git-2" href="https://github.com/Michaelninder/custom-laravel-livewire" target="_blank">
                        {{ __('nav.repository') }}
                    </flux:navlist.item>
                </flux:navlist>
            </flux:sidebar>
        @endif

        <flux:main>
            @yield('content') {{-- Main content will be yielded here --}}
        </flux:main>

        @fluxScripts
    </body>
</html>