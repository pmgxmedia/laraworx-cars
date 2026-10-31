<x-admin.layouts.admin title="Vehicles" :breadcrumbs="[['label' => 'Vehicles']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Vehicles</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} listing(s)</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}" class="btn-gold btn-sm">+ New Vehicle</a>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.vehicles.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search make, model, reference..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach ($filters['statuses'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="seller_type" class="input">
            <option value="">All sellers</option>
            <option value="dealership" @selected(request('seller_type') === 'dealership')>Dealership</option>
            <option value="private" @selected(request('seller_type') === 'private')>Private</option>
        </select>
        <select name="make" class="input">
            <option value="">All makes</option>
            @foreach ($filters['makes'] as $make)
                <option value="{{ $make }}" @selected(request('make') === $make)>{{ $make }}</option>
            @endforeach
        </select>
        <select name="campaign_id" class="input">
            <option value="">All campaigns</option>
            @foreach ($filters['campaigns'] as $campaign)
                <option value="{{ $campaign->id }}" @selected((string) request('campaign_id') === (string) $campaign->id)>{{ $campaign->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.vehicles.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No vehicles match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Reference</th>
                            <th>Seller</th>
                            <th>Price</th>
                            <th>Reward</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $vehicle)
                            <tr class="hover:bg-ivory-100/60">
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($image = $vehicle->activeImages->first())
                                            <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="{{ $vehicle->displayName() }}" class="h-10 w-14 rounded object-cover">
                                        @else
                                            <div class="grid h-10 w-14 place-items-center rounded bg-royal-900/5 text-xs text-royal-900/40">No img</div>
                                        @endif
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="block truncate font-semibold text-royal-950 hover:text-gold-700">{{ $vehicle->displayName() }}</a>
                                            <p class="text-xs text-royal-900/55">{{ $vehicle->year }} · {{ number_format($vehicle->mileage) }} km · <span class="uppercase">{{ $vehicle->transmission }}</span></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-mono text-xs">{{ $vehicle->vehicle_reference }}</td>
                                <td class="max-w-[180px] truncate">{{ $vehicle->sellerName() ?? '—' }}</td>
                                <td class="font-semibold">{{ money($vehicle->price) }}</td>
                                <td>{{ money($vehicle->effective_reward_amount ?? $vehicle->activeCommission()?->reward_amount) }}</td>
                                <td><x-admin.status-badge :status="$vehicle->status" :label="$vehicle->statusLabel()" /></td>
                                <td>{{ $vehicle->is_featured ? '★' : '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn-ghost btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-royal-900/5 px-4 py-3">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</x-admin.layouts.admin>