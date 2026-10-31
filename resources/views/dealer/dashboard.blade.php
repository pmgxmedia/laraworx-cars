@extends('dealer.layouts.dealer')

@section('title', 'Dashboard')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Welcome back</p>
        <h2 class="mt-1 section-title">
            {{ $seller instanceof \App\Models\Dealership ? 'Manage your stock, '.$seller->business_name : 'Hello, '.$user->fullName() }}
        </h2>
        <p class="mt-2 max-w-2xl text-sm text-royal-900/60">
            Track your listings, respond to buyer enquiries, confirm sales and watch your commissions accumulate.
        </p>
    </div>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Published vehicles</p>
                <span class="grid h-9 w-9 place-items-center rounded-md bg-gold-500/15 text-gold-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                </span>
            </div>
            <p class="mt-3 font-display text-3xl font-black text-royal-950">{{ $kpis['published'] }}</p>
            <a href="{{ route('dealer.vehicles', ['status' => 'published']) }}" class="mt-2 inline-block text-xs font-semibold text-gold-700 hover:underline">View published</a>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Active listings</p>
                <span class="grid h-9 w-9 place-items-center rounded-md bg-sky-500/10 text-sky-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                </span>
            </div>
            <p class="mt-3 font-display text-3xl font-black text-royal-950">{{ $kpis['active'] }}</p>
            <a href="{{ route('dealer.vehicles') }}" class="mt-2 inline-block text-xs font-semibold text-gold-700 hover:underline">Manage listings</a>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">New enquiries</p>
                <span class="grid h-9 w-9 place-items-center rounded-md bg-emerald-500/10 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </span>
            </div>
            <p class="mt-3 font-display text-3xl font-black text-royal-950">{{ $kpis['newEnquiries'] }}</p>
            <a href="{{ route('dealer.enquiries') }}" class="mt-2 inline-block text-xs font-semibold text-gold-700 hover:underline">Respond to buyers</a>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Commissions earned</p>
                <span class="grid h-9 w-9 place-items-center rounded-md bg-gold-500/15 text-gold-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-3 font-display text-3xl font-black text-royal-950">{{ money($kpis['commission']) }}</p>
            <a href="{{ route('dealer.commissions') }}" class="mt-2 inline-block text-xs font-semibold text-gold-700 hover:underline">Commission details</a>
        </div>
    </div>

    @if ($kpis['pendingClaims'] > 0)
        <div class="mt-6 rounded-lg bg-gradient-to-r from-royal-950 to-royal-850 p-5 ring-1 ring-gold-500/30">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Action needed</p>
                    <p class="mt-1 font-display text-lg font-bold text-ivory-50">{{ $kpis['pendingClaims'] }} purchase claim{{ $kpis['pendingClaims'] > 1 ? 's' : '' }} awaiting your confirmation</p>
                </div>
                <a href="{{ route('dealer.claims') }}" class="btn-gold btn-sm">Confirm sales</a>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                <h3 class="font-display text-lg font-bold text-royal-950">Recent enquiries</h3>
                <a href="{{ route('dealer.enquiries') }}" class="text-xs font-semibold text-gold-700 hover:underline">View all</a>
            </div>
            @forelse ($recentEnquiries as $enquiry)
                <a href="{{ route('dealer.enquiries') }}" class="flex items-start gap-3 border-b border-royal-900/5 px-5 py-4 transition hover:bg-ivory-50">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-royal-950 text-xs font-black text-gold-400">{{ strtoupper(mb_substr($enquiry->name, 0, 1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-sm font-semibold text-royal-950">{{ $enquiry->name }}</p>
                            <span class="shrink-0 text-xs text-royal-900/40">{{ $enquiry->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="truncate text-xs text-royal-900/60">{{ $enquiry->vehicle?->displayName() ?? 'Vehicle' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-sky-500/10 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-sky-700 ring-1 ring-sky-500/30">{{ $enquiry->statusLabel() }}</span>
                </a>
            @empty
                <div class="px-5 py-10 text-center text-sm text-royal-900/50">No enquiries yet. New buyer messages will appear here.</div>
            @endforelse
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                <h3 class="font-display text-lg font-bold text-royal-950">Latest vehicles</h3>
                <a href="{{ route('dealer.vehicles') }}" class="text-xs font-semibold text-gold-700 hover:underline">View all</a>
            </div>
            @forelse ($recentVehicles as $vehicle)
                @php
                    $image = $vehicle->activeImages->firstWhere('is_main', true) ?? $vehicle->activeImages->first();
                @endphp
                <div class="flex items-center gap-3 border-b border-royal-900/5 px-5 py-4">
                    <div class="h-14 w-20 shrink-0 overflow-hidden rounded-md bg-royal-900">
                        @if ($image)
                            <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="{{ $image->alt_text }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-royal-950">{{ $vehicle->displayName() }}</p>
                        <p class="text-xs text-royal-900/50">Ref {{ $vehicle->vehicle_reference }} · {{ number_format($vehicle->views_count) }} views</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold text-royal-950">{{ money($vehicle->price) }}</p>
                        @include('dealer.components.status-badge', ['type' => 'vehicle', 'status' => $vehicle->status, 'label' => $vehicle->statusLabel()])
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-royal-900/50">You have no vehicles yet.</p>
                    <a href="{{ route('dealer.vehicles.create') }}" class="btn-gold btn-sm mt-4">Add your first vehicle</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection