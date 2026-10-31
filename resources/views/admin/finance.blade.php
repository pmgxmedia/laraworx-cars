<x-admin.layouts.admin title="Finance KPIs" :breadcrumbs="[['label' => 'Finance']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Financial Overview</h1>
            <p class="mt-1 text-sm text-royal-900/60">High-level health of the reward engine.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.transactions.index') }}" class="btn-ghost btn-sm">All transactions</a>
            <a href="{{ route('admin.reports.index') }}" class="btn-ghost btn-sm">Export reports</a>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="Commissions earned" :value="money($stats['commissions_earned'])" :sub="$stats['commission_count'].' commission transactions'" icon="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
        <x-admin.stat label="Rewards paid out" :value="money($stats['paid_out'])" :sub="$stats['completed_payouts'].' completed payouts'" icon="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
        <x-admin.stat label="Scheduled / pending" :value="money($stats['pending_payouts'])" :sub="'outstanding reward value '.money($stats['outstanding_rewards'])" icon="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-admin.stat label="WorxCars retains" :value="money($stats['retained'])" :sub="'avg payout '.money(round($stats['average_payout'], 2))" icon="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375" />
    </div>

    <div class="mt-7 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Recent Transactions</h2>
                </div>
                @if ($recent->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No transactions yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Type</th>
                                    <th>Vehicle</th>
                                    <th class="text-right">Amount</th>
                                    <th>Status</th>
                                    <th class="text-right">Occurred</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recent as $transaction)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-mono text-xs">{{ $transaction->transaction_reference }}</td>
                                        <td>{{ ucfirst($transaction->type) }}</td>
                                        <td class="max-w-[200px] truncate">{{ $transaction->vehicle?->displayName() ?? '—' }}</td>
                                        <td class="text-right font-semibold">{{ money($transaction->amount) }}</td>
                                        <td>{{ ucfirst($transaction->status) }}</td>
                                        <td class="text-right text-royal-900/60">{{ $transaction->occurred_at?->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h3 class="section-title !text-xl">Pipeline</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-royal-900/60">Claims paid</dt><dd class="font-semibold">{{ $stats['claims_paid'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-royal-900/60">Claims approved (incl. processing)</dt><dd class="font-semibold">{{ $stats['claims_approved'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-royal-900/60">Total payouts</dt><dd class="font-semibold">{{ $stats['payout_count'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-royal-900/60">Failed payouts</dt><dd class="font-semibold text-rose-700">{{ $stats['failed_payouts'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-royal-900/60">Payout transactions</dt><dd class="font-semibold">{{ $stats['payout_count_out'] }}</dd></div>
                </dl>
            </div>

            <div class="card-dark p-5">
                <h3 class="font-display text-lg font-bold text-gold-300">Net Position</h3>
                <p class="mt-3 text-3xl font-black text-ivory-50">{{ money($stats['retained']) }}</p>
                <p class="mt-1 text-sm text-ivory-100/50">Commission earned minus reward liabilities already paid and scheduled.</p>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>