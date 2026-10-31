<x-admin.layouts.admin title="Dealerships" :breadcrumbs="[['label' => 'Dealerships']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Dealerships</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} registered dealership(s)</p>
        </div>
        <a href="{{ route('admin.dealerships.create') }}" class="btn-gold btn-sm">+ New Dealership</a>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.dealerships.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search business, email, city..." class="input">
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
            <a href="{{ route('admin.dealerships.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No dealerships match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Vehicles</th>
                            <th>Accounts</th>
                            <th>Claims</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $dealership)
                            <tr class="hover:bg-ivory-100/60">
                                <td>
                                    <a href="{{ route('admin.dealerships.show', $dealership) }}" class="block font-semibold text-royal-950 hover:text-gold-700">{{ $dealership->business_name }}</a>
                                    <p class="text-xs text-royal-900/55">{{ $dealership->trading_name ? 't/a '.$dealership->trading_name : $dealership->registration_number }}</p>
                                </td>
                                <td class="max-w-[220px]">
                                    <p class="truncate">{{ $dealership->email }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $dealership->contact_person_name }}</p>
                                </td>
                                <td class="max-w-[160px] truncate">{{ collect([$dealership->city, $dealership->province])->filter()->join(', ') ?: '—' }}</td>
                                <td><x-admin.status-badge :status="$dealership->status" :label="$dealership->statusLabel()" /></td>
                                <td class="text-center">{{ $dealership->vehicles_count }}</td>
                                <td class="text-center">{{ $dealership->user_accounts_count }}</td>
                                <td class="text-center">{{ $dealership->purchase_claims_count }}</td>
                                <td class="text-right"><a href="{{ route('admin.dealerships.show', $dealership) }}" class="btn-ghost btn-sm">View</a></td>
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