<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">The WorxCars promise</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">About WorxCars</h1>
            <p class="mt-2 max-w-2xl text-sm text-ivory-100/60">BUY YOUR CAR. GET PAID. Trust, transparency and real rewards — that's WorxCars.</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="prose prose-royal max-w-none text-sm leading-relaxed text-royal-900/75">
            <p>WorxCars is a South African vehicle marketplace with a difference. We connect verified dealerships and trusted private sellers with buyers across the country — and we reward buyers for completing real, qualifying purchases.</p>
            <h2 class="font-display text-xl font-bold text-royal-950">Why rewards?</h2>
            <p>Buying a car is one of life's biggest purchases. We believe that when a buyer completes a genuine purchase through our marketplace, everyone benefits — the seller, the marketplace, and especially the buyer. That's why qualifying purchases earn a WorxCars Buyer Reward.</p>
            <h2 class="font-display text-xl font-bold text-royal-950">Trust first</h2>
            <p>Every dealership is verified and every reward claim goes through eligibility and fraud checks before any money is paid. We don't pay rewards for browsing, registering or enquiring. We pay for proven, completed purchases.</p>
            <h2 class="font-display text-xl font-bold text-royal-950">{{ $dealerships }}+ verified partners</h2>
            <p>Our network of verified dealerships across the country lists quality stock that buyers can trust — and every qualifying purchase from a WorxCars partner carries the potential for a buyer reward.</p>
        </div>
    </section>
</x-layouts.app>