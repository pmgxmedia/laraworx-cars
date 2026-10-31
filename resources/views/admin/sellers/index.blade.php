<x-admin.layouts.admin title="Private Sellers" :breadcrumbs="[['label' => 'Private Sellers']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Private Sellers</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} private seller profile(s)</p>
        </div>
        <a href="{{ route('admin.sellers.create') }}" class="btn-gold btn-sm">+ New Seller</a>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.sellers.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, city..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach ($filters['statuses'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="province" class="input">
            <option value="">All provinces</option>
            @foreach ($filters['provinces'] as $province)
                <option value="{{ $province }}" @selected(request('province') === $province)>{{ $province }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.sellers.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No private sellers match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Vehicles</th>
                            <th>Claims</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $seller)
                            <tr class="hover:bg-ivory-100/60">
                                <td>
                                    <a href="{{ route('admin.sellers.show', $seller) }}" class="block font-semibold text-royal-950 hover:text-gold-700">{{ $seller->user?->fullName() ?? '—' }}</a>
                                    <p class="text-xs text-royal-900/55">Account #{{ $seller->user_id }}</p>
                                </td>
                                <td class="max-w-[220px]">
                                    <p class="truncate">{{ $seller->contact_email }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $seller->phone }}</p>
                                </td>
                                <td class="max-w-[160px] truncate">{{ collect([$seller->city, $seller->province])->filter()->join(', ') ?: '—' }}</td>
                                <td><x-admin.status-badge :status="$seller->status" :label="$seller->statusLabel()" /></td>
                                <td class="text-center">{{ $seller->vehicles_count }}</td>
                                <td class="text-center">{{ $seller->purchase_claims_count }}</td>
                                <td class="text-right"><a href="{{ route('admin.sellers.show', $seller) }}" class="btn-ghost btn-sm">View</a></td>
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