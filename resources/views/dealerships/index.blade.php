<x-layouts.app title="Verified Dealerships | WorxCars" description="Browse South Africa's verified WorxCars dealership partners and their stock.">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Buy with confidence</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">Verified Dealerships</h1>
            <p class="mt-2 max-w-2xl text-sm text-ivory-100/60">Every WorxCars dealership has passed our verification process. Buy from a trusted partner and your qualifying purchase can earn you a buyer reward.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('dealerships.index') }}" class="card mb-8 flex flex-wrap items-end gap-4 p-5">
            <div class="min-w-[220px] flex-1">
                <label class="label">Search dealerships</label>
                <input type="text" name="q" value="{{ request('q') }}" class="input" placeholder="Name or trading name">
            </div>
            <div class="w-full sm:w-56">
                <label class="label">Province</label>
                <select name="province" class="input">
                    <option value="">All provinces</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province }}" @selected(request('province') === $province)>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-royal">Filter</button>
        </form>

        @if ($dealerships->isEmpty())
            <div class="card p-14 text-center">
                <h2 class="font-display text-xl font-bold text-royal-950">No dealerships found</h2>
                <p class="mt-2 text-sm text-royal-900/60">Try a different search or browse all vehicles instead.</p>
                <a href="{{ route('vehicles.index') }}" class="btn-gold mt-5 btn-sm">Browse vehicles</a>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($dealerships as $dealership)
                    <a href="{{ route('dealerships.show', $dealership) }}" class="card group flex flex-col p-6 transition hover:shadow-lg hover:shadow-royal-900/10">
                        <div class="flex items-center gap-4">
                            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-royal-950 font-display text-sm font-black text-gold-400">
                                {{ collect(explode(' ', $dealership->business_name))->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
                            </div>
                            <div>
                                <h2 class="font-display text-lg font-bold text-royal-950 group-hover:text-gold-700">{{ $dealership->business_name }}</h2>
                                <p class="text-xs text-royal-900/55">{{ $dealership->city }}{{ $dealership->province ? ', '.$dealership->province : '' }}</p>
                            </div>
                        </div>
                        <p class="mt-4 line-clamp-2 flex-1 text-sm text-royal-900/60">{{ $dealership->description }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-royal-900/10 pt-4 text-sm">
                            <span class="inline-flex items-center gap-1 text-green-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Verified partner
                            </span>
                            <span class="font-semibold text-royal-950">{{ $dealership->vehicles_count }} vehicles</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $dealerships->links() }}</div>
        @endif

        <div class="card mt-12 flex flex-col items-center justify-between gap-4 bg-gradient-to-r from-royal-950 to-royal-850 p-8 text-center sm:flex-row sm:text-left">
            <div>
                <h2 class="font-display text-xl font-bold text-ivory-50">Own or represent a dealership?</h2>
                <p class="mt-1 text-sm text-ivory-100/60">Partner with WorxCars and reach thousands of serious buyers.</p>
            </div>
            <a href="{{ route('dealerships.join') }}" class="btn-gold shrink-0">Apply to partner</a>
        </div>
    </section>
</x-layouts.app>