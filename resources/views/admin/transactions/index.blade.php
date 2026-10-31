<x-admin.layouts.admin title="Transactions" :breadcrumbs="[['label' => 'Transactions']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Ledger Transactions</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} transaction(s)</p>
        </div>
        <a href="{{ route('admin.finance') }}" class="btn-ghost btn-sm">Finance KPIs</a>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.transactions.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reference or description..." class="input">
        <select name="type" class="input">
            <option value="">All types</option>
            <option value="commission" @selected(request('type') === 'commission')>Commission</option>
            <option value="payout" @selected(request('type') === 'payout')>Payout</option>
            <option value="reward" @selected(request('type') === 'reward')>Reward</option>
            <option value="refund" @selected(request('type') === 'refund')>Refund</option>
            <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
        </select>
        <select name="direction" class="input">
            <option value="">Both directions</option>
            <option value="in" @selected(request('direction') === 'in')>In (money in)</option>
            <option value="out" @selected(request('direction') === 'out')>Out (money out)</option>
        </select>
        <select name="status" class="input">
            <option value="">All statuses</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            <option value="reversed" @selected(request('status') === 'reversed')>Reversed</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.transactions.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No transactions match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Vehicle / Claim</th>
                            <th class="text-right">Amount</th>
                            <th>Status</th>
                            <th class="text-right">Occurred</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $transaction)
                            <tr class="hover:bg-ivory-100/60">
                                <td class="font-mono text-xs font-semibold text-royal-950">{{ $transaction->transaction_reference }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>
                                    @if ($transaction->direction === 'in')
                                        <span class="badge-green">In</span>
                                    @else
                                        <span class="badge-red">Out</span>
                                    @endif
                                </td>
                                <td class="max-w-[240px]">
                                    <p class="truncate">{{ $transaction->vehicle?->displayName() ?? '—' }}</p>
                                    @if ($transaction->purchaseClaim)
                                        <a href="{{ route('admin.claims.show', $transaction->purchaseClaim) }}" class="font-mono text-xs text-gold-700 hover:underline">{{ $transaction->purchaseClaim->claim_reference }}</a>
                                    @endif
                                </td>
                                <td class="text-right font-semibold">{{ money($transaction->amount) }}</td>
                                <td>{{ ucfirst($transaction->status) }}</td>
                                <td class="text-right text-royal-900/60">{{ $transaction->occurred_at?->format('d M Y H:i') }}</td>
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