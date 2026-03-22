<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 antialiased dark:bg-zinc-900">
        {{-- Navigation --}}
        <header class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/80 bg-white/80 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <x-app-logo-icon class="size-7 text-zinc-900 dark:text-white" />
                    <span class="text-lg font-semibold text-zinc-900 dark:text-white">HOBMS</span>
                </a>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="#rooms" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Rooms</a>
                    <a href="#track-booking" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Track Booking</a>
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

        {{-- Hero Section --}}
        <section class="relative flex min-h-screen items-center justify-center overflow-hidden pt-16">
            {{-- Abstract background --}}
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100 via-slate-50 to-indigo-100 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900"></div>

            {{-- Abstract decorative shapes (light mode) --}}
            <div class="absolute inset-0 overflow-hidden dark:opacity-0">
                <div class="absolute -top-40 -right-40 size-[600px] rounded-full bg-gradient-to-br from-blue-200/60 to-indigo-200/40 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-40 size-[500px] rounded-full bg-gradient-to-tr from-amber-200/50 to-orange-100/30 blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 size-[800px] rounded-full bg-blue-50/80 blur-3xl"></div>
                {{-- Grid pattern --}}
                <svg class="absolute inset-0 h-full w-full opacity-[0.03]" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                            <path d="M 60 0 L 0 0 0 60" fill="none" stroke="#1e3a5f" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
                {{-- Floating dots --}}
                <div class="absolute top-24 left-1/4 size-3 rounded-full bg-blue-400/40"></div>
                <div class="absolute top-40 right-1/3 size-2 rounded-full bg-indigo-400/30"></div>
                <div class="absolute bottom-32 left-1/3 size-4 rounded-full bg-amber-400/30"></div>
                <div class="absolute top-1/3 right-20 size-2 rounded-full bg-blue-500/20"></div>
            </div>

            {{-- Abstract decorative shapes (dark mode) --}}
            <div class="absolute inset-0 overflow-hidden opacity-0 dark:opacity-100">
                <div class="absolute -top-40 -right-40 size-[600px] rounded-full bg-gradient-to-br from-blue-900/30 to-indigo-900/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-40 size-[500px] rounded-full bg-gradient-to-tr from-zinc-800/50 to-zinc-700/30 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-6 py-24 text-center">
                {{-- Badge --}}
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50/80 px-4 py-1.5 text-xs font-medium text-blue-700 backdrop-blur dark:border-blue-800/50 dark:bg-blue-900/20 dark:text-blue-400">
                    <span class="size-1.5 rounded-full bg-blue-500"></span>
                    Hotel Online Booking Management System
                </div>
                <h1 class="text-5xl font-bold tracking-tight text-zinc-900 sm:text-6xl lg:text-7xl dark:text-white">
                    Welcome to <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-indigo-400">HOBMS</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                    Experience comfort, luxury, and exceptional service. Book your perfect room today with our seamless online reservation system.
                </p>
                <div class="mt-10 flex items-center justify-center gap-4">
                    <a href="#rooms" class="rounded-lg bg-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        Browse Rooms
                    </a>
                    <a href="#track-booking" class="rounded-lg border border-zinc-300 bg-white/80 px-8 py-3 text-sm font-semibold text-zinc-700 shadow-sm hover:bg-white transition-colors dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                        Track Booking
                    </a>
                </div>

                {{-- Stats --}}
                <div class="mx-auto mt-16 grid max-w-3xl grid-cols-3 gap-8">
                    <div class="rounded-2xl border border-slate-200/80 bg-white/60 p-6 shadow-sm backdrop-blur dark:border-zinc-700/50 dark:bg-zinc-800/50">
                        <div class="text-3xl font-bold text-zinc-900 dark:text-white">{{ \App\Models\RoomCategory::active()->count() }}</div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Room Types</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/60 p-6 shadow-sm backdrop-blur dark:border-zinc-700/50 dark:bg-zinc-800/50">
                        <div class="text-3xl font-bold text-zinc-900 dark:text-white">{{ \App\Models\Room::available()->count() }}</div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Rooms Available</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/60 p-6 shadow-sm backdrop-blur dark:border-zinc-700/50 dark:bg-zinc-800/50">
                        <div class="text-3xl font-bold text-zinc-900 dark:text-white">24/7</div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Front Desk</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Room Categories Section --}}
        <section id="rooms" class="border-t border-slate-200 bg-white py-24 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center">
                    <div class="mb-3 inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Accommodations</div>
                    <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Our Rooms</h2>
                    <p class="mt-4 text-zinc-500 dark:text-zinc-400">Choose from our carefully curated selection of rooms</p>
                </div>

                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach(\App\Models\RoomCategory::active()->get() as $category)
                        <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg hover:-translate-y-0.5 dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="relative flex h-48 items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-zinc-800">
                                {{-- Abstract room card pattern --}}
                                <div class="absolute inset-0 opacity-30">
                                    <div class="absolute -top-6 -right-6 size-32 rounded-full bg-blue-200/60 dark:bg-blue-800/30"></div>
                                    <div class="absolute -bottom-4 -left-4 size-24 rounded-full bg-indigo-200/60 dark:bg-indigo-800/20"></div>
                                </div>
                                <svg class="relative size-16 text-blue-400 dark:text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21"/>
                                </svg>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $category->name }}</h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($category->description, 100) }}</p>
                                <div class="mt-4 flex items-center justify-between">
                                    <div>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱{{ number_format($category->price_per_night) }}</span>
                                        <span class="text-sm text-zinc-400 dark:text-zinc-500">/night</span>
                                    </div>
                                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-slate-100 dark:bg-zinc-800 px-2 py-1 rounded-full">Up to {{ $category->max_capacity }} guests</span>
                                </div>
                                <a href="{{ route('rooms.show', $category->slug) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-500 transition-colors" wire:navigate>
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Booking Lookup Section --}}
        <section id="track-booking" class="relative overflow-hidden border-t border-slate-200 bg-slate-50 py-24 dark:border-zinc-700 dark:bg-zinc-900">
            {{-- Abstract bg --}}
            <div class="absolute inset-0 dark:opacity-0">
                <div class="absolute right-0 top-0 size-96 rounded-full bg-blue-100/60 blur-3xl"></div>
                <div class="absolute left-0 bottom-0 size-72 rounded-full bg-indigo-100/50 blur-3xl"></div>
            </div>
            <div class="relative mx-auto max-w-md px-6">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="size-7 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </div>
                    <div class="mb-2 inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Already Booked?</div>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Track Your Reservation</h2>
                    <p class="mt-3 text-zinc-500 dark:text-zinc-400">Enter your booking reference number to view your booking details</p>
                </div>
                @livewire('pages::portal.lookup')
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-slate-200 bg-white py-12 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="mx-auto max-w-7xl px-6 text-center">
                <div class="flex items-center justify-center gap-2">
                    <x-app-logo-icon class="size-5 text-zinc-400" />
                    <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">HOBMS</span>
                </div>
                <p class="mt-3 text-sm text-zinc-400 dark:text-zinc-500">
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
