<x-admin.layouts.admin title="Buyer Rewards" :breadcrumbs="[['label' => 'Rewards']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Buyer Rewards</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} reward record(s)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.rewards.index', ['status' => 'approved']) }}" class="btn-ghost btn-sm">Approved</a>
            <a href="{{ route('admin.rewards.index', ['status' => 'paid']) }}" class="btn-ghost btn-sm">Paid</a>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.rewards.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reward ref, buyer, vehicle..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.rewards.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No rewards match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Reward</th>
                            <th>Buyer</th>
                            <th>Vehicle</th>
                            <th>Campaign</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-right">Approved / Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $reward)
                            <tr class="hover:bg-ivory-100/60">
                                <td class="font-mono text-xs font-semibold">
                                    <a href="{{ route('admin.rewards.show', $reward) }}" class="text-royal-950 hover:text-gold-700">{{ $reward->reward_reference }}</a>
                                </td>
                                <td class="max-w-[180px] truncate">{{ $reward->user?->fullName() ?? '—' }}</td>
                                <td class="max-w-[220px] truncate">{{ $reward->vehicle?->displayName() ?? '—' }}</td>
                                <td class="max-w-[160px] truncate">{{ $reward->campaign?->name ?? '—' }}</td>
                                <td class="font-semibold text-gold-700">{{ money($reward->amount) }}</td>
                                <td><x-admin.status-badge :status="$reward->status" :label="$reward->statusLabel()" /></td>
                                <td class="text-right text-royal-900/60">{{ $reward->approved_at?->format('d M Y') }}<br>{{ $reward->paid_at?->format('d M Y') }}</td>
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