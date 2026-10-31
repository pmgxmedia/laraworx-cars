@props(['type' => 'success', 'message'])

@php
    $styles = [
        'success' => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-500/30',
        'error' => 'bg-rose-50 text-rose-800 ring-1 ring-rose-500/30',
        'info' => 'bg-sky-50 text-sky-800 ring-1 ring-sky-500/30',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition class="mb-4">
    <div class="flex items-start justify-between gap-3 rounded-md px-4 py-3 text-sm font-medium {{ $styles[$type] ?? $styles['success'] }}">
        <p>{{ $message }}</p>
        <button type="button" @click="show = false" class="shrink-0 opacity-60 hover:opacity-100" aria-label="Dismiss">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>