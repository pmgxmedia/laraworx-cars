@php
    $yieldedTitle = trim($__env->yieldContent('title'));
    $pageTitle = $yieldedTitle ? $yieldedTitle.' | '.siteName() : siteName();
    $user = auth()->user();
    $seller = $user->dealership() ?? $user->privateSellers->first();
    $ownedIds = $seller
        ? ($user->isDealership()
            ? \App\Models\Vehicle::where('dealership_id', $seller->id)->pluck('id')
            : \App\Models\Vehicle::where('private_seller_id', $seller->id)->pluck('id'))
        : collect();

    $newEnquiries = $ownedIds->count()
        ? \App\Models\BuyerEnquiry::whereIn('vehicle_id', $ownedIds)->where('status', \App\Models\BuyerEnquiry::STATUS_NEW)->count()
        : 0;

    $pendingClaims = $ownedIds->count()
        ? \App\Models\PurchaseClaim::whereIn('vehicle_id', $ownedIds)->whereIn('status', [
            \App\Models\PurchaseClaim::STATUS_SUBMITTED,
            \App\Models\PurchaseClaim::STATUS_UNDER_REVIEW,
            \App\Models\PurchaseClaim::STATUS_SELLER_VERIFICATION,
        ])->count()
        : 0;

    $notificationCount = $newEnquiries + $pendingClaims;

    $currentRoute = request()->route()?->getName();

    $nav = [
        ['label' => 'Dashboard', 'route' => 'dealer.dashboard', 'active' => ['dealer.dashboard'], 'icon' => 'dashboard'],
        ['label' => 'My Vehicles', 'route' => 'dealer.vehicles', 'active' => ['dealer.vehicles', 'dealer.vehicles.create', 'dealer.vehicles.edit', 'dealer.vehicles.store', 'dealer.vehicles.update', 'dealer.vehicles.submit'], 'icon' => 'car'],
        ['label' => 'Enquiries', 'route' => 'dealer.enquiries', 'active' => ['dealer.enquiries', 'dealer.enquiries.update'], 'icon' => 'chat'],
        ['label' => 'Claims', 'route' => 'dealer.claims', 'active' => ['dealer.claims', 'dealer.claims.confirm'], 'icon' => 'claim'],
        ['label' => 'Commissions', 'route' => 'dealer.commissions', 'active' => ['dealer.commissions'], 'icon' => 'coins'],
        ['label' => 'Reports', 'route' => 'dealer.reports', 'active' => ['dealer.reports'], 'icon' => 'chart'],
        ['label' => 'Profile', 'route' => 'dealer.profile', 'active' => ['dealer.profile', 'dealer.profile.update'], 'icon' => 'user'],
    ];

    $icons = [
        'dashboard' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>',
        'car' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>',
        'chat' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>',
        'claim' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'coins' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'chart' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>',
        'user' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    ];
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="WorxCars dealer dashboard for managing vehicle listings, enquiries, claims and commissions.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-ivory-50 text-royal-900 antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-royal-950/60 backdrop-blur-sm lg:hidden"></div>

        <aside class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-royal-950 transition-transform duration-300 lg:translate-x-0"
               :class="sidebarOpen && 'translate-x-0'">
            <div class="border-b border-white/10 px-6 py-5">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-royal-900 ring-1 ring-gold-500/60">
                        <span class="font-display text-lg font-black text-gold-400">W</span>
                    </span>
                    <div class="leading-tight">
                        <p class="font-display text-lg font-black tracking-tight text-ivory-50">WorxCars</p>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gold-500">Dealer Panel</p>
                    </div>
                </div>
                @if ($seller)
                    <div class="mt-4 truncate rounded-md bg-royal-900/70 px-3 py-2 text-xs text-ivory-100/70">
                        <span class="font-semibold text-gold-300">{{ $seller instanceof \App\Models\Dealership ? $seller->business_name : $user->fullName() }}</span>
                        @if ($seller->status)
                            <span class="ml-1 text-ivory-100/40">· {{ $seller->statusLabel() }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-md px-4 py-2.5 text-sm font-semibold transition
                              @if (in_array($currentRoute, $item['active'])) bg-gold-500/15 text-gold-300 ring-1 ring-gold-500/40
                              @else text-ivory-100/60 hover:bg-royal-900/70 hover:text-ivory-50 @endif">
                        {!! $icons[$item['icon']] !!}
                        <span>{{ $item['label'] }}</span>
                        @if ($item['route'] === 'dealer.enquiries' && $newEnquiries > 0)
                            <span class="ml-auto rounded-full bg-gold-500 px-2 py-0.5 text-[10px] font-black text-royal-950">{{ $newEnquiries }}</span>
                        @endif
                        @if ($item['route'] === 'dealer.claims' && $pendingClaims > 0)
                            <span class="ml-auto rounded-full bg-gold-500 px-2 py-0.5 text-[10px] font-black text-royal-950">{{ $pendingClaims }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-6 py-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-semibold text-ivory-100/50 transition hover:text-gold-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                    View public site
                </a>
            </div>
        </aside>

        <div class="flex min-h-screen flex-col lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-royal-900/10 bg-white/90 backdrop-blur">
                <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
                    <button type="button" @click="sidebarOpen = true"
                            class="grid h-10 w-10 shrink-0 place-items-center rounded-md text-royal-900/70 transition hover:bg-royal-900/5 lg:hidden" aria-label="Open menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[10px] font-bold uppercase tracking-widest text-gold-600">
                            <a href="{{ route('home') }}" class="hover:text-gold-700">WorxCars</a>
                            <span class="mx-1 text-royal-900/30">/</span>
                            Dealer dashboard
                        </p>
                        <h1 class="truncate font-display text-lg font-bold text-royal-950">{{ $yieldedTitle ?: 'Dashboard' }}</h1>
                    </div>

                    <a href="{{ route('dealer.enquiries') }}" class="relative grid h-10 w-10 place-items-center rounded-md text-royal-900/70 transition hover:bg-royal-900/5" title="Notifications">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        @if ($notificationCount > 0)
                            <span class="absolute right-0.5 top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-gold-500 px-1 text-[10px] font-black text-royal-950">{{ $notificationCount }}</span>
                        @endif
                    </a>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-semibold text-royal-950 transition hover:bg-royal-900/5">
                            <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-gold-400 to-gold-600 text-xs font-black text-royal-950">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <span class="hidden sm:inline">{{ $user->fullName() }}</span>
                            <svg class="h-3.5 w-3.5 text-royal-900/50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 overflow-hidden rounded-lg bg-white shadow-xl ring-1 ring-royal-900/10">
                            <a href="{{ route('dealer.dashboard') }}" class="block px-4 py-2.5 text-sm text-royal-900 hover:bg-royal-900/5">My Dashboard</a>
                            <a href="{{ route('dealer.profile') }}" class="block px-4 py-2.5 text-sm text-royal-900 hover:bg-royal-900/5">Profile</a>
                            <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2.5 text-sm text-royal-900 hover:bg-royal-900/5">My WorxCars</a>
                            <div class="my-1 border-t border-royal-900/10"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm text-rose-700 hover:bg-rose-50">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-md bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 ring-1 ring-rose-500/30">{{ session('error') }}</div>
                @endif

                @yield('header')

                <div class="mt-6">
                    @yield('content')
                </div>
            </main>

            <footer class="border-t border-royal-900/10 px-4 py-5 text-center text-xs text-royal-900/40 sm:px-6">
                &copy; {{ now()->year }} {{ siteName() }} · Dealer Portal
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>