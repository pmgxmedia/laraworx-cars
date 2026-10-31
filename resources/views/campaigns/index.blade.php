<x-layouts.app title="Reward Campaigns | WorxCars" description="Explore WorxCars reward campaigns and see which vehicles offer enhanced buyer rewards.">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Live now</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">Reward Campaigns</h1>
            <p class="mt-2 max-w-2xl text-sm text-ivory-100/60">Campaign vehicles carry enhanced WorxCars Buyer Rewards. Buy a qualifying vehicle during the campaign window, complete your purchase, and claim your reward.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($campaigns->isEmpty())
            <div class="card flex flex-col items-center justify-center p-14 text-center">
                <div class="grid h-16 w-16 place-items-center rounded-full bg-royal-950">
                    <svg class="h-8 w-8 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="mt-4 font-display text-xl font-bold text-royal-950">No active campaigns right now</h2>
                <p class="mt-2 max-w-sm text-sm text-royal-900/60">Every published vehicle still carries a standard WorxCars Buyer Reward. Check back soon.</p>
                <a href="{{ route('vehicles.index') }}" class="btn-gold mt-5 btn-sm">Browse vehicles</a>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($campaigns as $campaign)
                    <a href="{{ route('campaigns.show', $campaign) }}" class="card group overflow-hidden transition hover:shadow-xl hover:shadow-royal-900/10">
                        <div class="relative bg-royal-950 p-8 pb-6">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(201,162,39,0.25),transparent_55%)]"></div>
                            <div class="relative">
                                <span class="badge-gold">Live campaign</span>
                                <h2 class="mt-4 font-display text-xl font-bold text-ivory-50 group-hover:text-gold-300">{{ $campaign->name }}</h2>
                                @if ($campaign->end_date)
                                    <p class="mt-2 text-xs text-ivory-100/50">Ends {{ $campaign->end_date->format('d M Y') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="line-clamp-3 text-sm leading-relaxed text-royal-900/65">{{ $campaign->description }}</p>
                            <div class="mt-4 flex items-center justify-between border-t border-royal-900/10 pt-4 text-sm">
                                <span class="font-semibold text-royal-950">{{ $campaign->campaign_vehicles_count }} vehicles</span>
                                <span class="inline-flex items-center gap-1 font-medium text-gold-700">View rewards
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $campaigns->links() }}</div>
        @endif
    </section>
</x-layouts.app>