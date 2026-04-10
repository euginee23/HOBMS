@php
    $code        = 503;
    $title       = 'Down for Maintenance';
    $description = 'We\'re performing scheduled maintenance to improve your experience. We\'ll be back shortly.';
@endphp

<x-errors.layout :code="$code" :title="$title" :description="$description">
    <x-slot:actions>
        <button onclick="location.reload()"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Check Again
        </button>
    </x-slot:actions>
</x-errors.layout>
