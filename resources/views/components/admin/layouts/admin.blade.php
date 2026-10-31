@props([
    'title' => 'Admin',
    'breadcrumbs' => [],
])

@php
    $user = auth()->user();
    $nav = [
        'Dashboard' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
        ],
        'Listings' => [
            ['label' => 'Vehicles', 'route' => 'admin.vehicles.index', 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
            ['label' => 'Dealerships', 'route' => 'admin.dealerships.index', 'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z'],
            ['label' => 'Private Sellers', 'route' => 'admin.sellers.index', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
            ['label' => 'Campaigns', 'route' => 'admin.campaigns.index', 'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z'],
        ],
        'Rewards' => [
            ['label' => 'Claims', 'route' => 'admin.claims.index', 'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
            ['label' => 'Rewards', 'route' => 'admin.rewards.index', 'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z'],
            ['label' => 'Payouts', 'route' => 'admin.payouts.index', 'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z'],
        ],
        'Leads' => [
            ['label' => 'Enquiries', 'route' => 'admin.enquiries.index', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
        ],
        'Finance' => [
            ['label' => 'Transactions', 'route' => 'admin.transactions.index', 'icon' => 'M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5'],
            ['label' => 'Finance KPIs', 'route' => 'admin.finance', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
        ],
        'Content' => [
            ['label' => 'Pages', 'route' => 'admin.pages.index', 'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
            ['label' => 'FAQs', 'route' => 'admin.faqs.index', 'icon' => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z'],
            ['label' => 'Banners', 'route' => 'admin.banners.index', 'icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z'],
        ],
        'Administration' => [
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
            ['label' => 'Roles', 'route' => 'admin.roles.index', 'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z'],
            ['label' => 'Audit Logs', 'route' => 'admin.audit-logs', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
            ['label' => 'Reports', 'route' => 'admin.reports.index', 'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25'],
        ],
    ];

    $siteTitle = $title.' | '.siteName().' Admin';
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteTitle }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="min-h-screen bg-ivory-100 text-royal-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-royal-950 lg:flex">
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
                <div class="grid h-9 w-9 place-items-center rounded-md bg-gold-500 font-display text-sm font-black text-royal-950">WC</div>
                <div>
                    <p class="font-display text-sm font-bold text-ivory-50">{{ siteName() }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gold-400">Admin Panel</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                @foreach ($nav as $group => $links)
                    <p class="mt-4 px-2 text-[10px] font-bold uppercase tracking-widest text-ivory-100/40 first:mt-0">{{ $group }}</p>
                    <div class="mt-1.5 space-y-0.5">
                        @foreach ($links as $link)
                            @php $active = request()->routeIs($link['route'].'*'); @endphp
                            <a href="{{ route($link['route']) }}"
                               class="flex items-center gap-3 rounded-md px-2.5 py-2 text-sm font-medium transition {{ $active ? 'bg-gold-500/15 text-gold-300 ring-1 ring-gold-500/40' : 'text-ivory-100/70 hover:bg-royal-800 hover:text-ivory-50' }}">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-royal-800 font-bold text-gold-300">{{ strtoupper(mb_substr($user->name ?? 'A', 0, 1)) }}</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-ivory-50">{{ $user->fullName() }}</p>
                        <p class="truncate text-xs text-ivory-100/50">{{ $user->roles->pluck('name')->map(fn ($r) => ucwords(str_replace('_', ' ', $r)))->join(', ') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-ghost-dark btn-sm w-full">Sign out</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-30 border-b border-royal-900/10 bg-ivory-100/90 backdrop-blur">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
                    <nav class="flex min-w-0 items-center gap-2 text-sm text-royal-900/60">
                        <a href="{{ route('home') }}" class="hover:text-gold-700">Site</a>
                        <span class="text-royal-900/30">/</span>
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-gold-700">Admin</a>
                        @foreach ($breadcrumbs as $crumb)
                            <span class="text-royal-900/30">/</span>
                            @if ($crumb['url'] ?? null)
                                <a href="{{ $crumb['url'] }}" class="hover:text-gold-700">{{ $crumb['label'] }}</a>
                            @else
                                <span class="truncate font-semibold text-royal-900">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.vehicles.index') }}" method="GET" class="hidden sm:block">
                            <input type="text" name="q" placeholder="Search vehicles..." class="input !w-56 !py-2" value="{{ request('q') }}">
                        </form>
                        <a href="{{ route('admin.vehicles.create') }}" class="btn-gold btn-sm hidden sm:inline-flex">+ New Vehicle</a>
                        <details class="relative">
                            <summary class="grid h-9 w-9 cursor-pointer list-none place-items-center rounded-full bg-royal-950 font-bold text-gold-300">{{ strtoupper(mb_substr($user->name ?? 'A', 0, 1)) }}</summary>
                            <div class="absolute right-0 mt-2 w-56 rounded-lg bg-white p-2 shadow-xl ring-1 ring-royal-900/10">
                                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-ivory-100">Return to dashboard</a>
                                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-royal-900/10 pt-1">
                                    @csrf
                                    <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm text-rose-700 hover:bg-rose-50">Sign out</button>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1500px] flex-1 px-6 py-6">
                <div class="mb-6 space-y-2">
                    @if (session('success'))
                        <x-admin.alert type="success" :message="session('success')" />
                    @endif
                    @if (session('error'))
                        <x-admin.alert type="error" :message="session('error')" />
                    @endif
                    @if (session('info'))
                        <x-admin.alert type="info" :message="session('info')" />
                    @endif
                    @if (session('status'))
                        <x-admin.alert type="success" :message="session('status')" />
                    @endif
                </div>
                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>