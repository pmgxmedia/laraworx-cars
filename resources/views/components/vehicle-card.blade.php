@props(['vehicle'])

@php
    $breakdown = \App\Services\CommissionService::class ? null : null;
    $image = $vehicle->mainImage();
    $imageUrl = $image ? asset('storage/'.$image->thumbnail_path) : asset('storage/brand/worxcars-logo.png');
    $reward = $vehicle->effective_reward_amount ?? $vehicle->activeRewardAmount();
    $sold = $vehicle->status === \App\Models\Vehicle::STATUS_SOLD;
    $reserved = $vehicle->status === \App\Models\Vehicle::STATUS_RESERVED;
@endphp

<article class="group card overflow-hidden transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-royal-900/10">
    <div class="relative aspect-[3/2] overflow-hidden bg-royal-900">
        <img src="{{ $imageUrl }}"
             alt="{{ $image?->alt_text ?? $vehicle->displayName() }}"
             loading="lazy"
             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($vehicle->is_featured)
                <span class="badge-gold !bg-gold-500 text-white shadow">Featured</span>
            @endif
            @if ($vehicle->campaign)
                <span class="badge !bg-royal-950/80 text-gold-300 ring-1 ring-gold-500/50 backdrop-blur">Campaign</span>
            @endif
            @if ($sold)
                <span class="badge !bg-royal-950/90 text-ivory-50">Sold</span>
            @elseif ($reserved)
                <span class="badge !bg-amber-500/90 text-royal-950">Reserved</span>
            @endif
        </div>
        @if ($vehicle->activeImages->count())
            <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-royal-950/70 px-2 py-0.5 text-[11px] font-semibold text-ivory-100 backdrop-blur">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 21H6a2.25 2.25 0 01-2.25-2.25V5.25A2.25 2.25 0 016 3h12a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0118 21z"/></svg>
                {{ $vehicle->activeImages->count() }}
            </span>
        @endif
    </div>

    <div class="relative p-4">
        <div class="pr-2">
            <div class="flex items-start justify-between gap-2">
                <h3 class="font-display text-base font-bold leading-snug text-royal-950">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-gold-700">{{ $vehicle->displayName() }}</a>
                </h3>
                <a href="{{ route('vehicles.show', $vehicle) }}" class="shrink-0 rounded-md p-1.5 text-royal-900/40 transition hover:bg-gold-500/10 hover:text-gold-600" title="Compare"
                   x-on:click.prevent="Alpine.store('compare').toggle({{ $vehicle->id }}); $el.classList.toggle('!text-gold-600')">
                    <svg class="h-4.5 w-4.5 h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6M3 17v-4M7 7v10M7 17v-2M11 7v6M11 17v-4M15 7v10M15 17v-2M19 7v6M19 17v-4"/></svg>
                </a>
            </div>
            <p class="mt-1 flex items-center gap-2 text-xs text-royal-900/60">
                <span>{{ $vehicle->year }}</span>
                <span class="h-0.5 w-0.5 rounded-full bg-royal-900/30"></span>
                <span>{{ number_format($vehicle->mileage) }} km</span>
                <span class="h-0.5 w-0.5 rounded-full bg-royal-900/30"></span>
                <span>{{ $vehicle->transmission }}</span>
                <span class="h-0.5 w-0.5 rounded-full bg-royal-900/30"></span>
                <span>{{ $vehicle->fuel_type }}</span>
            </p>
        </div>

        <div class="mt-3 flex items-baseline justify-between border-t border-royal-900/10 pt-3">
            <div>
                <p class="font-display text-xl font-black text-royal-950">{{ money($vehicle->price) }}</p>
                @if ($estimates = \App\Support\FinanceEstimator::estimate($vehicle))
                    <p class="text-xs text-royal-900/55">from {{ money($estimates['monthly']) }}/mo*</p>
                @endif
            </div>
        </div>

        <div class="mt-2 flex items-center justify-between text-xs text-royal-900/60">
            <span class="inline-flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                {{ $vehicle->location_city ?? '—' }}
            </span>
            <span class="font-medium text-royal-900/70">Ref {{ $vehicle->vehicle_reference }}</span>
        </div>

        <div class="mt-3 rounded-md bg-gradient-to-r from-royal-950 to-royal-850 p-3 ring-1 ring-gold-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gold-400">Buyer Reward</p>
                    <p class="font-display text-lg font-black text-gold-300">{{ money($reward) }}</p>
                </div>
                <span class="h-8 w-px bg-gold-500/30"></span>
                <p class="max-w-[130px] text-right text-[11px] leading-tight text-ivory-100/70">Get up to {{ money($reward) }} back after a successful qualifying purchase.</p>
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between gap-2">
            <div class="text-xs">
                <p class="font-semibold text-royal-900/80">{{ $vehicle->sellerName() }}</p>
                <p class="text-royal-900/50">{{ $vehicle->seller_type === 'private' ? 'Private Seller' : 'Verified Dealership' }}</p>
            </div>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-ghost btn-sm">View Vehicle</a>
        </div>
    </div>
</article>