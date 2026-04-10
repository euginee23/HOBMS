@php
    $code        = 404;
    $title       = 'Page Not Found';
    $description = 'The page you\'re looking for doesn\'t exist or may have been moved.';
@endphp

<x-errors.layout :code="$code" :title="$title" :description="$description">
    <x-slot:actions>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75 12 3l9 6.75V21H3V9.75Z" />
            </svg>
            Go Home
        </a>
        <button onclick="history.back()"
                class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Go Back
        </button>
    </x-slot:actions>
</x-errors.layout>
