<x-layouts.app title="Search Vehicles | WorxCars" description="Browse quality vehicles from trusted South African dealerships and private sellers.">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-ivory-50">Search Vehicles</h1>
            <p class="mt-1 text-sm text-ivory-100/60">{{ $total }} vehicles available. Every qualifying purchase can earn you a WorxCars Buyer Reward.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[280px_1fr]">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <form action="{{ route('vehicles.index') }}" method="GET" x-data="{ open: false }">
                    <div class="card p-5">
                        <div class="flex items-center justify-between">
                            <h2 class="font-display text-lg font-bold text-royal-950">Filters</h2>
                            <button type="button" class="text-xs font-medium text-gold-700 hover:underline lg:hidden" @click="open = !open" x-text="open ? 'Hide' : 'Show filters'"></button>
                        </div>

                        <div class="mt-4 space-y-4" :class="open || 'hidden lg:block'">
                            <div>
                                <label class="label">Search</label>
                                <input type="text" name="q" value="{{ $activeFilters['q'] ?? '' }}" placeholder="Make, model, keyword..." class="input">
                            </div>

                            <div>
                                <label class="label">Make</label>
                                <select name="make" class="input">
                                    <option value="">All makes</option>
                                    @foreach ($facets['makes'] as $make)
                                        <option value="{{ $make }}" @selected(($activeFilters['make'] ?? '') === $make)>{{ $make }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Max price</label>
                                <select name="price_max" class="input">
                                    <option value="">Any</option>
                                    @foreach ($facets['price_max_options'] as $option)
                                        <option value="{{ $option }}" @selected(($activeFilters['price_max'] ?? '') == $option)>{{ money($option) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Min year</label>
                                    <select name="year_min" class="input">
                                        <option value="">Any</option>
                                        @foreach (range(now()->year, now()->year - 15) as $year)
                                            <option value="{{ $year }}" @selected(($activeFilters['year_min'] ?? '') == $year)>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Max km</label>
                                    <select name="mileage_max" class="input">
                                        <option value="">Any</option>
                                        @foreach ([50000, 100000, 150000, 200000, 250000] as $km)
                                            <option value="{{ $km }}" @selected(($activeFilters['mileage_max'] ?? '') == $km)>{{ number_format($km) }} km</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="label">Body type</label>
                                <select name="body_type" class="input">
                                    <option value="">All types</option>
                                    @foreach ($facets['bodies'] as $body)
                                        <option value="{{ $body }}" @selected(($activeFilters['body_type'] ?? '') === $body)>{{ $body }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Transmission</label>
                                <select name="transmission" class="input">
                                    <option value="">All</option>
                                    <option value="Automatic" @selected(($activeFilters['transmission'] ?? '') === 'Automatic')>Automatic</option>
                                    <option value="Manual" @selected(($activeFilters['transmission'] ?? '') === 'Manual')>Manual</option>
                                </select>
                            </div>

                            <div>
                                <label class="label">Fuel type</label>
                                <select name="fuel_type" class="input">
                                    <option value="">All</option>
                                    @foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric'] as $fuel)
                                        <option value="{{ $fuel }}" @selected(($activeFilters['fuel_type'] ?? '') === $fuel)>{{ $fuel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="label">Seller type</label>
                                <select name="seller_type" class="input">
                                    <option value="">All sellers</option>
                                    <option value="dealership" @selected(($activeFilters['seller_type'] ?? '') === 'dealership')>Dealerships</option>
                                    <option value="private" @selected(($activeFilters['seller_type'] ?? '') === 'private')>Private sellers</option>
                                </select>
                            </div>

                            <div>
                                <label class="label">Province</label>
                                <select name="province" class="input">
                                    <option value="">All provinces</option>
                                    @foreach ($facets['provinces'] as $province)
                                        <option value="{{ $province }}" @selected(($activeFilters['province'] ?? '') === $province)>{{ $province }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-royal-900/75">
                                <input type="checkbox" name="reward" value="yes" @checked(($activeFilters['reward'] ?? '') === 'yes') class="rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
                                Vehicles with rewards
                            </label>

                            <label class="flex items-center gap-2 text-sm text-royal-900/75">
                                <input type="checkbox" name="featured" value="1" @checked(($activeFilters['featured'] ?? '') === '1') class="rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
                                Featured only
                            </label>

                            <div class="flex gap-2 pt-1">
                                <button type="submit" class="btn-gold flex-1">Apply filters</button>
                                @if (count($activeFilters))
                                    <a href="{{ route('vehicles.index') }}" class="btn-ghost">Reset</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </aside>

            <div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-royal-900/60">Showing <span class="font-semibold text-royal-950">{{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}</span> of <span class="font-semibold text-royal-950">{{ $vehicles->total() }}</span></p>
                    <form id="sort-form" method="GET" action="{{ route('vehicles.index') }}">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium uppercase tracking-wider text-royal-900/50">Sort</span>
                            <select name="sort" class="input !w-48 !py-2">
                                @foreach ($sorts as $key => $sort)
                                    <option value="{{ $key }}" @selected($activeSort === $key)>{{ $sort['label'] }}</option>
                                @endforeach
                            </select>
                            <noscript><button class="btn-ghost btn-sm">Apply</button></noscript>
                        </div>
                        @foreach ($activeFilters as $k => $v)
                            @if ($k !== 'sort' && $v !== '')
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                    </form>
                </div>

                @if ($vehicles->isEmpty())
                    <div class="card mt-6 flex flex-col items-center justify-center p-14 text-center">
                        <div class="grid h-16 w-16 place-items-center rounded-full bg-royal-950">
                            <svg class="h-8 w-8 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <h3 class="mt-4 font-display text-xl font-bold text-royal-950">No vehicles match your filters</h3>
                        <p class="mt-2 max-w-sm text-sm text-royal-900/60">Try widening your search — remove some filters or clear them entirely to see everything available.</p>
                        <div class="mt-5 flex gap-3">
                            <a href="{{ route('vehicles.index') }}" class="btn-gold btn-sm">Clear all filters</a>
                            <a href="{{ route('sell-car') }}" class="btn-ghost btn-sm">Sell your car instead</a>
                        </div>
                    </div>
                @else
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($vehicles as $vehicle)
                            <x-vehicle-card :vehicle="$vehicle" />
                        @endforeach
                    </div>
                    <div class="mt-10">
                        {{ $vehicles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>