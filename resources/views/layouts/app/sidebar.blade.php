<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Menu')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar" :href="route('bookings.index')" :current="request()->routeIs('bookings.*')" wire:navigate>
                        {{ __('Bookings') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="banknotes" :href="route('payments.index')" :current="request()->routeIs('payments.*')" wire:navigate>
                        {{ __('Payments') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if(auth()->user()->isAdmin())
                    <flux:sidebar.group :heading="__('Management')" class="grid">
                        <flux:sidebar.item icon="tag" :href="route('room-categories.index')" :current="request()->routeIs('room-categories.*')" wire:navigate>
                            {{ __('Room Categories') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="building-office" :href="route('rooms-manage.index')" :current="request()->routeIs('rooms-manage.*')" wire:navigate>
                            {{ __('Rooms') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="chat-bubble-left-ellipsis" :href="route('complaints.index')" :current="request()->routeIs('complaints.*')" wire:navigate>
                            {{ __('Complaints') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="chart-bar" :href="route('reports.index')" :current="request()->routeIs('reports.*')" wire:navigate>
                            {{ __('Reports') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('staff.index')" :current="request()->routeIs('staff.*')" wire:navigate>
                            {{ __('Staff') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="hidden lg:flex items-center gap-2">
                <button
                    x-data
                    @click="
                        let isDark = document.documentElement.classList.contains('dark');
                        if (isDark) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('flux.appearance', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('flux.appearance', 'dark');
                        }
                    "
                    class="flex size-8 items-center justify-center rounded-md text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                    title="Toggle theme"
                >
                    <flux:icon.sun class="size-4 hidden dark:block" />
                    <flux:icon.moon class="size-4 dark:hidden" />
                </button>
                <x-desktop-user-menu :name="auth()->user()->name" />
            </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
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
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        {{-- Scroll to top --}}
        <div
            x-data="{ visible: false }"
            @scroll.window="visible = window.scrollY > 300"
            class="fixed bottom-6 right-6 z-50"
        >
            <button
                x-show="visible"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="flex size-10 items-center justify-center rounded-full bg-zinc-900 shadow-lg hover:bg-zinc-700 dark:bg-zinc-100 dark:hover:bg-zinc-300"
                title="Scroll to top"
            >
                <flux:icon.chevron-up class="size-5 text-white dark:text-zinc-900" />
            </button>
        </div>

        @fluxScripts
    </body>
</html>
