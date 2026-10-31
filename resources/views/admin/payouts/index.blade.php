<x-admin.layouts.admin title="Payouts" :breadcrumbs="[['label' => 'Payouts']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Reward Payouts</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} payout(s)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.payouts.index', ['status' => 'scheduled']) }}" class="btn-ghost btn-sm">Scheduled</a>
            <a href="{{ route('admin.payouts.index', ['status' => 'failed']) }}" class="btn-ghost btn-sm">Failed</a>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.payouts.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search payout ref, provider txn, email..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach (['scheduled' => 'Scheduled', 'processing' => 'Processing', 'completed' => 'Completed', 'failed' => 'Failed', 'reversed' => 'Reversed'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="provider" class="input">
            <option value="">All providers</option>
            @foreach ($providers as $provider)
                <option value="{{ $provider }}" @selected(request('provider') === $provider)>{{ ucfirst($provider) }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.payouts.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No payouts match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Payout</th>
                            <th>Buyer</th>
                            <th>Reward</th>
                            <th>Provider</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Provider txn</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $payout)
                            <tr class="hover:bg-ivory-100/60">
                                <td class="font-mono text-xs font-semibold text-royal-950">{{ $payout->payout_reference }}</td>
                                <td class="max-w-[180px] truncate">{{ $payout->user?->fullName() ?? '—' }}</td>
                                <td class="font-mono text-xs">{{ $payout->buyerReward?->reward_reference ?? '—' }}</td>
                                <td>{{ ucfirst($payout->provider) }}</td>
                                <td class="font-semibold text-gold-700">{{ money($payout->amount) }}</td>
                                <td><x-admin.status-badge :status="$payout->status" /></td>
                                <td class="max-w-[160px] truncate font-mono text-xs">{{ $payout->provider_transaction_id ?? '—' }}</td>
                                <td class="text-right">
                                    @if (in_array($payout->status, ['scheduled', 'failed']))
                                        <form method="POST" action="{{ route('admin.payouts.execute', $payout) }}">
                                            @csrf
                                            <button type="submit" class="btn-ghost btn-sm" onclick="return confirm('Execute this payout?')">Execute</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-royal-900/40">{{ $payout->completed_at?->format('d M Y') }}</span>
                                    @endif
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