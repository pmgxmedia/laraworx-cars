@props([])

<nav x-cloak x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-50 lg:hidden" x-data="{ mobileOpen: false }"
     x-init="window.worxcarsNav = { mobileOpen }; $watch('mobileOpen', v => document.body.classList.toggle('overflow-hidden', v))"
     @open-mobile-nav.window="mobileOpen = true">
    <div class="absolute inset-0 bg-royal-950/70 backdrop-blur-sm" @click="mobileOpen = false"></div>
    <div class="absolute inset-y-0 right-0 flex w-80 max-w-[85vw] flex-col overflow-y-auto bg-ivory-50 shadow-2xl">
        <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
            <span class="font-display text-lg font-black text-royal-950">Menu</span>
            <button @click="mobileOpen = false" class="grid h-9 w-9 place-items-center rounded-md hover:bg-royal-900/5" aria-label="Close menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex flex-1 flex-col gap-1 px-3 py-4">
            <a href="{{ route('vehicles.index') }}" @click="mobileOpen = false" class="rounded-md px-3 py-3 text-sm font-semibold text-royal-900 hover:bg-royal-900/5">Browse Vehicles</a>
            <a href="{{ route('campaigns.index') }}" @click="mobileOpen = false" class="rounded-md px-3 py-3 text-sm font-semibold text-royal-900 hover:bg-royal-900/5">Buy &amp; Get Paid</a>
            <a href="{{ route('dealerships.index') }}" @click="mobileOpen = false" class="rounded-md px-3 py-3 text-sm font-semibold text-royal-900 hover:bg-royal-900/5">Dealerships</a>
            <a href="{{ route('sell-car') }}" @click="mobileOpen = false" class="rounded-md px-3 py-3 text-sm font-semibold text-royal-900 hover:bg-royal-900/5">Sell Your Car</a>
            <a href="{{ route('how-it-works') }}" @click="mobileOpen = false" class="rounded-md px-3 py-3 text-sm font-semibold text-royal-900 hover:bg-royal-900/5">How It Works</a>
            <a href="{{ route('page.show', 'faq') }}" @click="mobileOpen = false" class="rounded-md px-3 py-3 text-sm font-semibold text-royal-900 hover:bg-royal-900/5">FAQs</a>

            <div class="my-3 border-t border-royal-900/10"></div>

            @auth
                <a href="{{ route('customer.dashboard') }}" @click="mobileOpen = false" class="btn-royal">My WorxCars</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn-ghost w-full">Sign out</button>
                </form>
            @else
                <a href="{{ route('login') }}" @click="mobileOpen = false" class="btn-ghost">Sign in</a>
                <a href="{{ route('register') }}" @click="mobileOpen = false" class="btn-gold mt-2">Create Account</a>
            @endauth
        </div>
    </div>
</nav>