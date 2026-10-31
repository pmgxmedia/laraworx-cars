<x-admin.layouts.admin title="Purchase Claims" :breadcrumbs="[['label' => 'Claims']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Purchase Claims</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} claim(s)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.claims.index', ['status' => 'submitted']) }}" class="btn-ghost btn-sm">Submitted</a>
            <a href="{{ route('admin.claims.index', ['status' => 'transaction_verified']) }}" class="btn-ghost btn-sm">Awaiting approval</a>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.claims.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reference, provider ref, buyer..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.claims.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No claims match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Buyer</th>
                            <th>Vehicle</th>
                            <th>Purchase price</th>
                            <th>Reward</th>
                            <th>Docs</th>
                            <th>Status</th>
                            <th class="text-right">Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $claim)
                            <tr class="hover:bg-ivory-100/60">
                                <td class="font-semibold">
                                    <a href="{{ route('admin.claims.show', $claim) }}" class="hover:text-gold-700">{{ $claim->claim_reference }}</a>
                                    <p class="text-xs font-normal text-royal-900/55">{{ $claim->provider_reference }}</p>
                                </td>
                                <td>
                                    <p class="max-w-[180px] truncate">{{ $claim->user?->fullName() ?? '—' }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $claim->user?->email }}</p>
                                </td>
                                <td class="max-w-[220px] truncate">{{ $claim->vehicle?->displayName() ?? '—' }}</td>
                                <td>{{ money($claim->purchase_price) }}</td>
                                <td class="font-semibold text-gold-700">{{ money($claim->reward_amount) }}</td>
                                <td class="text-center">{{ $claim->documents_count }}</td>
                                <td><x-admin.status-badge :status="$claim->status" :label="$claim->statusLabel()" /></td>
                                <td class="text-right text-royal-900/60">{{ $claim->created_at?->format('d M Y') }}</td>
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