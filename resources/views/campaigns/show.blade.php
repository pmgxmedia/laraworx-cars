<x-layouts.app :title="$campaign->name.' | WorxCars'" :description="$campaign->description">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="text-xs text-ivory-100/50">
                <a href="{{ route('home') }}" class="hover:text-gold-300">Home</a><span class="mx-2">/</span>
                <a href="{{ route('campaigns.index') }}" class="hover:text-gold-300">Campaigns</a><span class="mx-2">/</span>
                <span class="text-ivory-100/80">{{ $campaign->name }}</span>
            </nav>
            <div class="mt-4 flex flex-wrap items-start justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="badge-gold">Live campaign</span>
                    <h1 class="mt-3 font-display text-3xl font-black text-ivory-50">{{ $campaign->name }}</h1>
                    <p class="mt-3 text-sm leading-relaxed text-ivory-100/60">{{ $campaign->description }}</p>
                    @if ($campaign->terms)
                        <p class="mt-3 text-xs text-ivory-100/40">Offer terms apply. See campaign vehicle cards for reward details.</p>
                    @endif
                </div>
                <div class="rounded-lg bg-royal-900/70 px-5 py-4 text-sm ring-1 ring-gold-500/25">
                    @if ($campaign->start_date)
                        <p class="text-ivory-100/50">Starts <span class="font-semibold text-ivory-50">{{ $campaign->start_date->format('d M Y') }}</span></p>
                    @endif
                    @if ($campaign->end_date)
                        <p class="mt-1 text-ivory-100/50">Ends <span class="font-semibold text-gold-300">{{ $campaign->end_date->format('d M Y') }}</span></p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <p class="text-sm text-royal-900/60">{{ $vehicles->total() }} campaign vehicles</p>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($vehicles as $vehicle)
                <x-vehicle-card :vehicle="$vehicle" />
            @endforeach
        </div>
        @if ($vehicles->isEmpty())
            <div class="card mt-6 p-14 text-center">
                <h2 class="font-display text-xl font-bold text-royal-950">No vehicles in this campaign yet</h2>
                <p class="mt-2 text-sm text-royal-900/60">Reward-bearing vehicles will be added to this campaign soon.</p>
            </div>
        @endif
        <div class="mt-10">{{ $vehicles->links() }}</div>
    </section>
</x-layouts.app>