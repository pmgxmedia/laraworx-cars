<x-admin.layouts.admin title="Dashboard">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Dashboard</h1>
            <p class="mt-1 text-sm text-royal-900/60">Overview of the WorxCars marketplace and buyer-reward engine.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.claims.index') }}" class="btn-ghost btn-sm">Claims</a>
            <a href="{{ route('admin.reports.index') }}" class="btn-ghost btn-sm">Reports</a>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="Total vehicles" :value="number_format($stats['vehicles'])" :sub="$stats['vehicles_pending'].' awaiting approval'" icon="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
        <x-admin.stat label="Rewards paid" :value="money($stats['rewards_paid'])" :sub="money($stats['outstanding_payouts']).' outstanding'" icon="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
        <x-admin.stat label="Pending claims" :value="number_format($stats['claims_pending'])" :sub="$stats['enquiries_new'].' new enquiries'" icon="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        <x-admin.stat label="Commission earned" :value="money($stats['commission_earned'])" :sub="$stats['dealerships_pending'].' dealerships pending'" icon="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
    </div>

    <div class="mt-7 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                    <h2 class="font-display text-lg font-bold text-royal-950">Recent Claims</h2>
                    <a href="{{ route('admin.claims.index') }}" class="btn-ghost btn-sm">All claims</a>
                </div>
                @if ($recentClaims->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No claims yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Buyer</th>
                                    <th>Vehicle</th>
                                    <th>Reward</th>
                                    <th>Status</th>
                                    <th class="text-right">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentClaims as $claim)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-semibold">
                                            <a href="{{ route('admin.claims.show', $claim) }}" class="hover:text-gold-700">{{ $claim->claim_reference }}</a>
                                        </td>
                                        <td class="max-w-[180px] truncate">{{ $claim->user?->fullName() ?? '—' }}</td>
                                        <td class="max-w-[200px] truncate">{{ $claim->vehicle?->displayName() ?? '—' }}</td>
                                        <td>{{ money($claim->reward_amount) }}</td>
                                        <td><x-admin.status-badge :status="$claim->status" :label="$claim->statusLabel()" /></td>
                                        <td class="text-right text-royal-900/60">{{ $claim->created_at?->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                    <h2 class="font-display text-lg font-bold text-royal-950">Vehicles Awaiting Approval</h2>
                    <a href="{{ route('admin.vehicles.index', ['status' => 'pending_approval']) }}" class="btn-ghost btn-sm">Review pending</a>
                </div>
                @if ($pendingVehicles->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No vehicles awaiting approval.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($pendingVehicles as $vehicle)
                            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-ivory-100/60">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-royal-950">{{ $vehicle->displayName() }}</p>
                                    <p class="truncate text-xs text-royal-900/55">{{ $vehicle->sellerName() ?? '—' }} · {{ $vehicle->vehicle_reference }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-3">
                                    <span class="text-sm font-semibold text-royal-950">{{ money($vehicle->price) }}</span>
                                    <x-admin.status-badge :status="$vehicle->status" :label="$vehicle->statusLabel()" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="font-display text-lg font-bold text-royal-950">Quick Links</h2>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.vehicles.create') }}" class="btn-ghost btn-sm">Add vehicle</a>
                    <a href="{{ route('admin.campaigns.create') }}" class="btn-ghost btn-sm">New campaign</a>
                    <a href="{{ route('admin.dealerships.index', ['status' => 'pending']) }}" class="btn-ghost btn-sm">Dealerships</a>
                    <a href="{{ route('admin.payouts.index', ['status' => 'scheduled']) }}" class="btn-ghost btn-sm">Scheduled payouts</a>
                    <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="btn-ghost btn-sm">New enquiries</a>
                    <a href="{{ route('admin.reports.index') }}" class="btn-ghost btn-sm">Export reports</a>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="font-display text-lg font-bold text-royal-950">Needs Attention</h2>
                <div class="mt-4 space-y-3">
                    @if ($lowActivity['vehicles'])
                        <a href="{{ route('admin.vehicles.index', ['status' => 'pending_approval']) }}" class="flex items-center justify-between gap-3 rounded-md bg-amber-50 px-3 py-2.5 text-sm ring-1 ring-amber-500/30">
                            <span class="text-amber-800">{{ $lowActivity['vehicles'] }} vehicle(s) still waiting for approval</span>
                            <span class="badge-red">Review</span>
                        </a>
                    @endif
                    @if ($lowActivity['claims'])
                        <a href="{{ route('admin.claims.index') }}" class="flex items-center justify-between gap-3 rounded-md bg-amber-50 px-3 py-2.5 text-sm ring-1 ring-amber-500/30">
                            <span class="text-amber-800">{{ $lowActivity['claims'] }} claim(s) unresolved for 2+ days</span>
                            <span class="badge-red">Action</span>
                        </a>
                    @endif
                    @if ($lowActivity['enquiries'])
                        <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="flex items-center justify-between gap-3 rounded-md bg-amber-50 px-3 py-2.5 text-sm ring-1 ring-amber-500/30">
                            <span class="text-amber-800">{{ $lowActivity['enquiries'] }} new enquiry(s) untouched for 2+ days</span>
                            <span class="badge-red">Contact</span>
                        </a>
                    @endif
                    @if (! array_sum($lowActivity))
                        <p class="text-sm text-royal-900/55">All quiet — nothing requires immediate attention.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>