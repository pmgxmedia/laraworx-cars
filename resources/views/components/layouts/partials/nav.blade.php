@props(['currency'])

<header class="border-b border-royal-900/10 bg-white/90 backdrop-blur sticky top-0 z-50">
    <div class="hidden bg-royal-950 text-ivory-100/80 sm:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-1.5 text-xs">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1"><svg class="h-3.5 w-3.5 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z"/></svg>{{ appSetting('worxcars.contact_phone') }}</span>
                <span class="hidden md:inline">{{ appSetting('worxcars.contact_email') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="font-semibold text-gold-400">BUY YOUR CAR. GET PAID.</span>
                <span class="rounded-full bg-gold-500/20 px-2 py-0.5 font-bold text-gold-300">{{ $currency }}</span>
            </div>
        </div>
    </div>

    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3">
        <a href="/" class="flex items-center gap-2">
            <span class="grid h-10 w-10 place-items-center rounded-md bg-royal-950 ring-1 ring-gold-500/60">
                <span class="font-display text-lg font-black text-gold-400">W</span>
            </span>
            <span class="leading-none">
                <span class="block font-display text-xl font-black tracking-tight text-royal-950">WorxCars</span>
                <span class="block text-[9px] font-bold uppercase tracking-[0.25em] text-gold-600">Buy Your Car. Get Paid.</span>
            </span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            <a href="{{ route('vehicles.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-royal-900/80 transition hover:bg-royal-900/5 hover:text-royal-950">Vehicles</a>
            <a href="{{ route('campaigns.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-royal-900/80 transition hover:bg-royal-900/5 hover:text-royal-950">Buy &amp; Get Paid</a>
            <a href="{{ route('dealerships.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-royal-900/80 transition hover:bg-royal-900/5 hover:text-royal-950">Dealerships</a>
            <a href="{{ route('sell-car') }}" class="rounded-md px-3 py-2 text-sm font-medium text-royal-900/80 transition hover:bg-royal-900/5 hover:text-royal-950">Sell Your Car</a>
            <a href="{{ route('how-it-works') }}" class="rounded-md px-3 py-2 text-sm font-medium text-royal-900/80 transition hover:bg-royal-900/5 hover:text-royal-950">How It Works</a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('vehicles.index') }}" class="grid h-10 w-10 place-items-center rounded-md text-royal-900/70 transition hover:bg-royal-900/5" aria-label="Search vehicles">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            </a>

            @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-semibold text-royal-950 transition hover:bg-royal-900/5">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-gold-400 to-gold-600 text-xs font-black text-royal-950">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden sm:inline">{{ auth()->user()->fullName() }}</span>
                        <svg class="h-3.5 w-3.5 text-royal-900/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 overflow-hidden rounded-lg bg-white shadow-xl ring-1 ring-royal-900/10">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-royal-900 hover:bg-royal-900/5">Admin Panel</a>
                        @endif
                        @if(auth()->user()->isDealership() || auth()->user()->isPrivateSeller())
                            <a href="{{ route('dealer.dashboard') }}" class="block px-4 py-2.5 text-sm text-royal-900 hover:bg-royal-900/5">My Dashboard</a>
                        @endif
                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2.5 text-sm text-royal-900 hover:bg-royal-900/5">My WorxCars</a>
                        <div class="my-1 border-t border-royal-900/10"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm text-rose-700 hover:bg-rose-50">Sign out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-md px-3 py-2 text-sm font-medium text-royal-900/80 transition hover:bg-royal-900/5 sm:block">Sign in</a>
                <a href="{{ route('register') }}" class="btn-gold btn-sm !py-2">Find My Car</a>
            @endauth

            <button class="grid h-10 w-10 place-items-center rounded-md text-royal-900/70 transition hover:bg-royal-900/5 lg:hidden"
                    x-data @click="$dispatch('open-mobile-nav')" aria-label="Open menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </nav>
</header>