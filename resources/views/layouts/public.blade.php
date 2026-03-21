<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">
        {{-- Navigation --}}
        <header class="fixed inset-x-0 top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                    <x-app-logo-icon class="size-7 text-zinc-900 dark:text-white" />
                    <span class="text-lg font-semibold text-zinc-900 dark:text-white">HOBMS</span>
                </a>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('rooms.index') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" wire:navigate>Rooms</a>
                    <a href="{{ route('portal.lookup') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" wire:navigate>Track Booking</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-zinc-900 px-4 py-2 text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" wire:navigate>Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Staff Login</a>
                    @endauth
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
