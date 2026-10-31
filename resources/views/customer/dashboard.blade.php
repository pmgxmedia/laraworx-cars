<x-layouts.customer title="Dashboard">
    <h1 class="font-display text-2xl font-bold text-royal-950">Welcome back, {{ auth()->user()->name }}</h1>
    <p class="mt-1 text-sm text-royal-900/60">Here's how your WorxCars journey is going.</p>

    @if (session('status'))
        <div class="mt-5 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
    @endif

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Saved vehicles</p>
            <p class="mt-2 font-display text-3xl font-black text-royal-950">{{ $stats['favorites'] }}</p>
            <a href="{{ route('customer.favorites') }}" class="mt-2 inline-flex text-xs font-medium text-gold-700 hover:underline">View saved</a>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Enquiries</p>
            <p class="mt-2 font-display text-3xl font-black text-royal-950">{{ $stats['enquiries'] }}</p>
            <a href="{{ route('customer.enquiries') }}" class="mt-2 inline-flex text-xs font-medium text-gold-700 hover:underline">View enquiries</a>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Claims</p>
            <p class="mt-2 font-display text-3xl font-black text-royal-950">{{ $stats['claims'] }}</p>
            <a href="{{ route('customer.claims') }}" class="mt-2 inline-flex text-xs font-medium text-gold-700 hover:underline">View claims</a>
        </div>
        <div class="rounded-lg bg-gradient-to-b from-royal-900 to-royal-950 p-5 ring-1 ring-gold-500/30">
            <p class="text-xs font-bold uppercase tracking-wider text-gold-400">Rewards value</p>
            <p class="mt-2 font-display text-3xl font-black text-gold-300">{{ money($stats['rewards_total']) }}</p>
            <a href="{{ route('customer.rewards') }}" class="mt-2 inline-flex text-xs font-medium text-gold-300 hover:underline">View rewards</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                <h2 class="font-display text-lg font-bold text-royal-950">Recent claims</h2>
                <a href="{{ route('customer.claims') }}" class="text-xs font-medium text-gold-700 hover:underline">All claims</a>
            </div>
            @if ($recentClaims->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-royal-900/60">No claims yet.</p>
                    <p class="mt-1 text-xs text-royal-900/45">Once you buy a vehicle, submit your claim for a buyer reward.</p>
                    <a href="{{ route('vehicles.index') }}" class="btn-gold btn-sm mt-4">Find a vehicle</a>
                </div>
            @else
                <div class="divide-y divide-royal-900/5">
                    @foreach ($recentClaims as $claim)
                        <a href="{{ route('customer.claims.show', $claim) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-ivory-100/60">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-royal-950">{{ $claim->vehicle?->displayName() }}</p>
                                <p class="text-xs text-royal-900/55">{{ $claim->claim_reference }} · {{ $claim->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-royal-950">{{ money($claim->reward_amount) }}</p>
                                <span class="badge {{ match ($claim->status) { 'paid' => 'badge-green', 'rejected' => 'badge-red', 'disputed' => 'badge-amber', 'reward_approved', 'transaction_verified' => 'badge-blue', default => 'badge-grey' } }}">{{ $claim->statusLabel() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                <h2 class="font-display text-lg font-bold text-royal-950">Recent rewards</h2>
                <a href="{{ route('customer.rewards') }}" class="text-xs font-medium text-gold-700 hover:underline">All rewards</a>
            </div>
            @if ($recentRewards->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-royal-900/60">No rewards yet.</p>
                    <p class="mt-1 text-xs text-royal-900/45">Rewards are only paid for completed, verified purchases.</p>
                </div>
            @else
                <div class="divide-y divide-royal-900/5">
                    @foreach ($recentRewards as $reward)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-royal-950">{{ $reward->vehicle?->displayName() }}</p>
                                <p class="text-xs text-royal-900/55">{{ $reward->reward_reference }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-royal-950">{{ money($reward->amount) }}</p>
                                <span class="badge {{ match ($reward->status) { 'paid' => 'badge-green', 'approved' => 'badge-blue', 'rejected' => 'badge-red', default => 'badge-grey' } }}">{{ $reward->statusLabel() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        @if ($recentNotifications->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                    <h2 class="font-display text-lg font-bold text-royal-950">Notifications</h2>
                    <a href="{{ route('customer.notifications') }}" class="text-xs font-medium text-gold-700 hover:underline">View all</a>
                </div>
                <div class="divide-y divide-royal-900/5">
                    @foreach ($recentNotifications as $notification)
                        <div class="flex items-start gap-3 px-5 py-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-royal-900/20' : 'bg-gold-500' }}"></span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium {{ $notification->read_at ? 'text-royal-900/60' : 'text-royal-950' }}">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                <p class="mt-0.5 line-clamp-1 text-xs text-royal-900/50">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($recommended->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                    <h2 class="font-display text-lg font-bold text-royal-950">Featured for you</h2>
                    <a href="{{ route('vehicles.index') }}" class="text-xs font-medium text-gold-700 hover:underline">Browse all</a>
                </div>
                <div class="divide-y divide-royal-900/5">
                    @foreach ($recommended as $vehicle)
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="flex items-center gap-4 px-5 py-4 transition hover:bg-ivory-100/60">
                            <img src="{{ $vehicle->mainImage()?->image_path ? asset('storage/'.$vehicle->mainImage()->image_path) : asset('storage/brand/worxcars-logo.png') }}" alt="" class="h-14 w-20 rounded-md object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-royal-950">{{ $vehicle->displayName() }}</p>
                                <p class="text-xs text-royal-900/55">{{ $vehicle->year }} · {{ number_format($vehicle->mileage) }} km</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-royal-950">{{ money($vehicle->price) }}</p>
                                <p class="text-xs font-medium text-gold-700">+ {{ money($vehicle->activeRewardAmount()) }} reward</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.customer>