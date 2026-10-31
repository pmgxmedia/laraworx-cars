@props(['pages', 'currency'])

<footer class="mt-auto bg-royal-950 text-ivory-100/70">
    <div class="mx-auto max-w-7xl px-4 py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="/" class="flex items-center gap-2">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-royal-900 ring-1 ring-gold-500/60">
                        <span class="font-display text-lg font-black text-gold-400">W</span>
                    </span>
                    <span class="font-display text-xl font-black tracking-tight text-ivory-50">WorxCars</span>
                </a>
                <p class="mt-4 text-sm leading-relaxed">South Africa's premium vehicle marketplace. Find your next vehicle from trusted sellers and receive a WorxCars Buyer Reward on qualifying purchases.</p>
                <p class="mt-4 font-display text-lg font-bold text-gold-400">BUY YOUR CAR. GET PAID.</p>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-gold-400">Marketplace</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('vehicles.index') }}" class="transition hover:text-gold-300">Browse Vehicles</a></li>
                    <li><a href="{{ route('campaigns.index') }}" class="transition hover:text-gold-300">Buy &amp; Get Paid Campaigns</a></li>
                    <li><a href="{{ route('dealerships.index') }}" class="transition hover:text-gold-300">Trusted Dealerships</a></li>
                    <li><a href="{{ route('sell-car') }}" class="transition hover:text-gold-300">Sell Your Car</a></li>
                    <li><a href="{{ route('dealerships.join') }}" class="transition hover:text-gold-300">Dealership Partnership</a></li>
                    <li><a href="{{ route('vehicles.compare') }}" x-on:click.prevent="Alpine.store('compare').items.length && (window.location='{{ route('vehicles.compare') }}?ids='+Alpine.store('compare').items.join(','))" class="transition hover:text-gold-300">Compare Vehicles</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-gold-400">Company</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('how-it-works') }}" class="transition hover:text-gold-300">How WorxCars Works</a></li>
                    @foreach ($pages as $slug)
                        <li><a href="{{ route('page.show', $slug) }}" class="transition hover:text-gold-300">{{ ucwords(str_replace('-', ' ', $slug)) }}</a></li>
                    @endforeach
                    <li><a href="{{ route('contact') }}" class="transition hover:text-gold-300">Contact Us</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-gold-400">Legal</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('page.show', 'terms') }}" class="transition hover:text-gold-300">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('page.show', 'privacy') }}" class="transition hover:text-gold-300">Privacy Policy</a></li>
                    <li><a href="{{ route('page.show', 'reward-terms') }}" class="transition hover:text-gold-300">Buyer Reward Terms</a></li>
                    <li><a href="{{ route('page.show', 'disclaimer') }}" class="transition hover:text-gold-300">Disclaimer</a></li>
                    <li><a href="{{ route('page.show', 'faq') }}" class="transition hover:text-gold-300">FAQs</a></li>
                </ul>
                <div class="mt-6 rounded-md bg-royal-900/70 p-4 ring-1 ring-gold-500/20">
                    <p class="text-xs leading-relaxed text-ivory-100/60">Reward availability is subject to eligibility and verification of a successful qualifying purchase. Rewards are not payable for mere enquiries.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-ivory-100/10">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-ivory-100/50 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ siteName() }}. All rights reserved. Proudly South African.</p>
            <p class="font-semibold text-gold-500/80">BUY YOUR CAR. GET PAID.</p>
        </div>
    </div>
</footer>