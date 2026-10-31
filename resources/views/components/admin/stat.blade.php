@props(['label', 'value', 'sub' => null, 'icon' => null])

<div class="card p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] font-bold uppercase tracking-widest text-royal-900/50">{{ $label }}</p>
            <p class="mt-2 truncate font-display text-2xl font-black text-royal-950">{{ $value }}</p>
            @if ($sub)
                <p class="mt-1 truncate text-xs text-royal-900/55">{{ $sub }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-gold-500/15 text-gold-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
            </div>
        @endif
    </div>
</div>