<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>{{ $code }} {{ $title }} — {{ config('app.name', 'HOBMS') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="flex h-full flex-col items-center justify-center bg-slate-50 font-sans antialiased dark:bg-zinc-900">

        {{-- Decorative code backdrop --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden select-none">
            <span class="text-[20rem] font-extrabold leading-none text-indigo-100 dark:text-indigo-950">{{ $code }}</span>
        </div>

        {{-- Content --}}
        <div class="relative z-10 mx-auto flex max-w-md flex-col items-center px-6 text-center">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2 text-zinc-900 dark:text-white">
                <x-app-logo-icon class="size-8 text-zinc-900 dark:text-white" />
                <span class="text-xl font-semibold">{{ config('app.name', 'HOBMS') }}</span>
            </a>

            {{-- Badge --}}
            <span class="mb-4 inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                Error {{ $code }}
            </span>

            {{-- Heading --}}
            <h1 class="mb-3 text-3xl font-bold text-zinc-900 dark:text-white">
                {{ $title }}
            </h1>

            {{-- Description --}}
            <p class="mb-8 text-base text-zinc-500 dark:text-zinc-400">
                {{ $description }}
            </p>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center justify-center gap-3">
                {{ $actions }}
            </div>
        </div>

    </body>
</html>
