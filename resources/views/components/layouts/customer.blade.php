@props(['title' => 'Account | WorxCars'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ siteName() }}</title>
    <meta name="robots" content="noindex,nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ivory-50">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <aside class="flex flex-col bg-royal-950 text-ivory-100 lg:sticky lg:top-0 lg:h-screen">
            <div class="flex items-center justify-between px-6 py-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-royal-900 ring-1 ring-gold-500/60">
                        <span class="font-display text-base font-black text-gold-400">W</span>
                    </span>
                    <span class="font-display text-lg font-black text-ivory-50">WorxCars</span>
                </a>
                <button type="button" class="text-ivory-100/70 lg:hidden" x-data @click="$dispatch('toggle-sidebar')">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
            </div>

            <div class="border-b border-white/10 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-gold-500/20 font-bold text-gold-300">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-ivory-50">{{ auth()->user()->fullName() }}</p>
                        <p class="truncate text-xs text-ivory-100/50">Customer</p>
                    </div>
                </div>
            </div>

            <nav class="mt-4 flex-1 space-y-1 px-3 text-sm">
                @php
                    $items = [
                        'customer.dashboard' => ['Dashboard', route('customer.dashboard')],
                        'customer.favorites' => ['Saved vehicles', route('customer.favorites')],
                        'customer.enquiries' => ['Enquiries', route('customer.enquiries')],
                        'customer.claims' => ['My claims', route('customer.claims')],
                        'customer.rewards' => ['My rewards', route('customer.rewards')],
                        'customer.searches' => ['Saved searches', route('customer.searches')],
                        'customer.notifications' => ['Notifications', route('customer.notifications')],
                        'customer.profile' => ['Profile', route('customer.profile')],
                        'customer.security' => ['Security', route('customer.security')],
                    ];
                @endphp
                @foreach ($items as $name => [$label, $url])
                    <a href="{{ $url }}" class="flex items-center gap-3 rounded-md px-3 py-2 transition {{ request()->routeIs($name.'*') ? 'bg-gold-500/15 font-semibold text-gold-300' : 'text-ivory-100/70 hover:bg-white/5 hover:text-ivory-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-4">
                <a href="{{ route('vehicles.index') }}" class="flex w-full items-center justify-center gap-2 rounded-md bg-gold-500/15 px-4 py-2.5 text-sm font-semibold text-gold-300 hover:bg-gold-500/25">
                    Browse vehicles
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm text-ivory-100/60 hover:bg-white/5 hover:text-ivory-50">Sign out</button>
                </form>
            </div>
        </aside>

        <main class="min-w-0">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>