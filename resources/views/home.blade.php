<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="relative overflow-hidden bg-royal-950">
        <div class="absolute inset-0">
            <img src="{{ $heroUrl }}" alt="WorxCars premium vehicles" class="h-full w-full object-cover opacity-90">
            <div class="absolute inset-0 bg-gradient-to-t from-royal-950 via-royal-950/70 to-royal-950/30"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-royal-950/90 via-royal-950/40 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pb-16 pt-24 sm:px-6 lg:px-8 lg:pb-24 lg:pt-32">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-gold-500/15 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-gold-300 ring-1 ring-gold-500/40">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span>
                    South Africa's premium vehicle marketplace
                </span>
                <h1 class="mt-5 font-display text-4xl font-black leading-[1.1] text-ivory-50 sm:text-5xl lg:text-6xl">
                    BUY YOUR CAR.<br>
                    <span class="text-gold-400">GET PAID.</span>
                </h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-ivory-100/70 sm:text-lg">
                    Discover quality vehicles from trusted dealerships and verified private sellers.
                    Complete a successful qualifying purchase and receive a real WorxCars Buyer Reward.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('vehicles.index') }}" class="btn-gold">Browse Vehicles</a>
                    <a href="{{ route('sell-car') }}" class="btn-ghost-dark">Sell Your Car</a>
                </div>

                <div class="mt-10 grid max-w-lg grid-cols-3 gap-6 border-t border-white/10 pt-8">
                    <div>
                        <p class="font-display text-3xl font-black text-gold-400">{{ number_format($stats['vehicles']) }}+</p>
                        <p class="mt-1 text-xs uppercase tracking-wider text-ivory-100/50">Live Vehicles</p>
                    </div>
                    <div>
                        <p class="font-display text-3xl font-black text-gold-400">{{ $stats['dealerships'] }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wider text-ivory-100/50">Trusted Dealers</p>
                    </div>
                    <div>
                        <p class="font-display text-3xl font-black text-gold-400">R{{ number_format($stats['rewards']) }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wider text-ivory-100/50">Rewards Paid</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto -mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        <form action="{{ route('vehicles.index') }}" method="GET" class="card p-4 shadow-2xl shadow-royal-900/15 sm:p-5">
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-12 lg:gap-4">
                <div class="col-span-2 lg:col-span-4">
                    <label class="label">Make or model</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search Toyota, Polo, SUV..." class="input" list="make-list">
                    <datalist id="make-list">
                        @foreach (\App\Models\Vehicle::query()->published()->distinct()->orderBy('make')->pluck('make') as $make)
                            <option value="{{ $make }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="lg:col-span-2">
                    <label class="label">Max price</label>
                    <select name="price_max" class="input">
                        <option value="">Any price</option>
                        @foreach ([200000, 300000, 400000, 500000, 700000, 1000000, 1500000, 3000000] as $p)
                            <option value="{{ $p }}" @selected(request('price_max') == $p)>{{ money($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="label">Body type</label>
                    <select name="body_type" class="input">
                        <option value="">Any type</option>
                        @foreach (['Sedan', 'Hatchback', 'SUV', 'Coupe', 'Convertible', 'Wagon', 'Bakkie', 'Van', 'Truck'] as $t)
                            <option value="{{ $t }}" @selected(request('body_type') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="label">Seller type</label>
                    <select name="seller_type" class="input">
                        <option value="">All sellers</option>
                        <option value="dealership" @selected(request('seller_type') === 'dealership')>Dealerships</option>
                        <option value="private" @selected(request('seller_type') === 'private')>Private sellers</option>
                    </select>
                </div>
                <div class="col-span-2 flex items-end lg:col-span-2">
                    <button type="submit" class="btn-gold w-full">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        Search
                    </button>
                </div>
            </div>
        </form>
    </section>

    @if ($featured->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Handpicked for you</p>
                    <h2 class="mt-2 section-title">Featured Vehicles</h2>
                </div>
                <a href="{{ route('vehicles.index', ['featured' => 1]) }}" class="btn-ghost btn-sm">View all</a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($campaigns->isNotEmpty())
        <section class="bg-gradient-to-b from-royal-950 to-royal-900 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Live now</p>
                        <h2 class="mt-2 font-display text-2xl font-bold text-ivory-50 sm:text-3xl">Active Campaigns</h2>
                    </div>
                    <a href="{{ route('campaigns.index') }}" class="btn-ghost-dark btn-sm">All campaigns</a>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($campaigns as $campaign)
                        <a href="{{ route('campaigns.show', $campaign) }}" class="group rounded-lg bg-royal-800/60 p-6 ring-1 ring-gold-500/25 transition hover:ring-gold-400/60">
                            <p class="font-display text-lg font-bold text-ivory-50 group-hover:text-gold-300">{{ $campaign->name }}</p>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-ivory-100/60">{{ $campaign->description }}</p>
                            <div class="mt-4 flex items-center justify-between text-xs">
                                <span class="inline-flex items-center gap-1 text-gold-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6M3 17v-4M7 7v10M7 17v-2M11 7v6M11 17v-4M15 7v10M15 17v-2M19 7v6M19 17v-4"/></svg>
                                    Explore reward offers
                                </span>
                                @if ($campaign->end_date)
                                    <span class="text-ivory-100/50">Ends {{ $campaign->end_date->format('d M Y') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Simple and transparent</p>
            <h2 class="mt-2 section-title">How WorxCars Works</h2>
            <div class="divider-gold mx-auto mt-4"></div>
        </div>
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $steps = [
                    ['01', 'Find your vehicle', 'Search our marketplace of quality vehicles from verified dealerships and private sellers.'],
                    ['02', 'Complete your purchase', 'Buy the vehicle directly from the seller through your preferred financing, dealer or private arrangement.'],
                    ['03', 'Claim your reward', 'Submit your qualifying purchase details in your WorxCars account. Escrow-like verification keeps the process trustworthy.'],
                    ['04', 'Get paid', 'Once verified and approved, your WorxCars Buyer Reward is paid securely to your nominated bank account.'],
                ];
            @endphp
            @foreach ($steps as [$num, $title, $desc])
                <div class="relative rounded-lg bg-white p-6 shadow-sm ring-1 ring-royal-900/10">
                    <span class="font-display text-4xl font-black text-gold-500/30">{{ $num }}</span>
                    <h3 class="mt-3 font-display text-lg font-bold text-royal-950">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-royal-900/60">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('how-it-works') }}" class="btn-royal">Read the full details</a>
        </div>
    </section>

    @if ($dealerships->isNotEmpty())
        <section class="bg-ivory-100/60 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Buy with confidence</p>
                        <h2 class="mt-2 section-title">Trusted Verified Dealerships</h2>
                    </div>
                    <a href="{{ route('dealerships.index') }}" class="btn-ghost btn-sm">All dealerships</a>
                </div>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($dealerships as $dealership)
                        <a href="{{ route('dealerships.show', $dealership) }}" class="card flex items-center gap-4 p-5 transition hover:shadow-md">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-royal-950 font-display text-sm font-black text-gold-400">
                                {{ collect(explode(' ', $dealership->business_name))->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-royal-950">{{ $dealership->business_name }}</p>
                                <p class="text-xs text-royal-900/55">{{ $dealership->city }}{{ $dealership->province ? ', '.$dealership->province : '' }} · {{ $dealership->vehicles_count }} vehicles</p>
                            </div>
                            <span class="ml-auto inline-flex h-6 w-6 items-center justify-center rounded-full bg-gold-500/15 text-gold-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($latest->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Fresh on the market</p>
                    <h2 class="mt-2 section-title">Newest Arrivals</h2>
                </div>
                <a href="{{ route('vehicles.index') }}" class="btn-ghost btn-sm">Browse all vehicles</a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latest as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        <section class="bg-royal-950 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Real rewards, real buyers</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-ivory-50">Buyers Who Got Paid</h2>
                    <div class="divider-gold mx-auto mt-4"></div>
                </div>
                <div class="mt-12 grid gap-6 lg:grid-cols-3">
                    @foreach ($testimonials as $reward)
                        <div class="rounded-lg bg-royal-900/70 p-6 ring-1 ring-gold-500/20">
                            <div class="flex items-center gap-1 text-gold-400">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.286 3.958c.3.921-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.175 0l-3.366 2.446c-.784.57-1.838-.197-1.539-1.118l1.285-3.958a1 1 0 00-.363-1.118L2.174 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z"/></svg>
                                @endfor
                            </div>
                            <p class="mt-4 text-sm leading-relaxed text-ivory-100/80">
                                "I bought the {{ $reward->vehicle?->displayName() ?? 'vehicle' }} I'd been eyeing and the team verified my purchase quickly. The reward of {{ money($reward->amount) }} landed in my account — a brilliant experience."
                            </p>
                            <div class="mt-5 flex items-center gap-3">
                                <div class="grid h-9 w-9 place-items-center rounded-full bg-gold-500/20 font-bold text-gold-300">{{ strtoupper(mb_substr($reward->user?->name ?? 'W', 0, 1)) }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-ivory-50">{{ $reward->user?->fullName() ?? 'Verified Buyer' }}</p>
                                    <p class="text-xs text-ivory-100/50">{{ $reward->vehicle?->location_city ?? 'South Africa' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($faqs->isNotEmpty())
        <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Good to know</p>
                <h2 class="mt-2 section-title">Frequently Asked Questions</h2>
            </div>
            <div class="mt-8 space-y-3">
                @foreach ($faqs as $faq)
                    <div class="card overflow-hidden" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                            <span class="font-semibold text-royal-950">{{ $faq->question }}</span>
                            <svg class="h-5 w-5 shrink-0 text-gold-600 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="border-t border-royal-900/5 px-5 py-4 text-sm leading-relaxed text-royal-900/70">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="relative overflow-hidden bg-gradient-to-br from-gold-500 via-gold-400 to-gold-600 py-16">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6">
            <h2 class="font-display text-3xl font-black text-royal-950">Ready to get paid for buying your next car?</h2>
            <p class="mt-3 text-royal-950/80">Join WorxCars free. Browse, buy from trusted sellers, and claim your reward after your qualifying purchase.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="btn bg-royal-950 text-ivory-50 shadow-xl hover:bg-royal-900 focus:ring-royal-950">Create free account</a>
                <a href="{{ route('vehicles.index') }}" class="btn bg-white/90 text-royal-950 hover:bg-white">Browse vehicles</a>
            </div>
        </div>
    </section>
</x-layouts.app>