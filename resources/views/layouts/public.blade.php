<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">
        {{-- Navigation --}}
        <header x-data="{ mobileOpen: false }" class="fixed inset-x-0 top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                    <x-app-logo-icon class="size-7 text-zinc-900 dark:text-white" />
                    <span class="text-lg font-semibold text-zinc-900 dark:text-white">HOBMS</span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden items-center gap-4 text-sm sm:flex">
                    <a href="{{ route('home') }}#rooms" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Rooms</a>
                    <a href="{{ route('home') }}#track-booking" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Track Booking</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-zinc-900 px-4 py-2 text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" wire:navigate>Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Staff Login</a>
                    @endauth
                    <button
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
                </nav>

                {{-- Mobile: theme toggle + hamburger --}}
                <div class="flex items-center gap-1 sm:hidden">
                    <button
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
                        class="flex size-9 items-center justify-center rounded-md text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
                        title="Toggle theme"
                    >
                        <flux:icon.sun class="size-4 hidden dark:block" />
                        <flux:icon.moon class="size-4 dark:hidden" />
                    </button>
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="flex size-9 items-center justify-center rounded-md text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
                        :aria-expanded="mobileOpen"
                        aria-label="Toggle menu"
                    >
                        <svg x-show="!mobileOpen" class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg x-show="mobileOpen" class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu Dropdown --}}
            <div
                x-show="mobileOpen"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="border-t border-zinc-200/80 bg-white/95 backdrop-blur sm:hidden dark:border-zinc-700 dark:bg-zinc-900/95"
            >
                <nav class="flex flex-col gap-1 px-4 py-3">
                    <a href="{{ route('home') }}#rooms" @click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-slate-100 dark:text-zinc-300 dark:hover:bg-zinc-800">Rooms</a>
                    <a href="{{ route('home') }}#track-booking" @click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-slate-100 dark:text-zinc-300 dark:hover:bg-zinc-800">Track Booking</a>
                    <div class="my-1 border-t border-slate-100 dark:border-zinc-800"></div>
                    @auth
                        <a href="{{ route('dashboard') }}" @click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-slate-100 dark:text-zinc-300 dark:hover:bg-zinc-800" wire:navigate>Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" @click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-slate-100 dark:text-zinc-300 dark:hover:bg-zinc-800">Staff Login</a>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="pt-20">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-zinc-200 bg-zinc-50 py-12 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="mx-auto max-w-7xl px-6 text-center">
                <div class="flex items-center justify-center gap-2">
                    <x-app-logo-icon class="size-5 text-zinc-400" />
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">HOBMS</span>
                </div>
                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-500">
                    CodeHub.Site | Hotel Online Booking Management System &copy; {{ date('Y') }}
                </p>
            </div>
        </footer>

        @fluxScripts

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
    </body>
</html>
