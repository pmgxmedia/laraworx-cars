<x-admin.layouts.admin title="Claim {{ $claim->claim_reference }}" :breadcrumbs="[['label' => 'Claims', 'url' => route('admin.claims.index')], ['label' => $claim->claim_reference]]">
    @php
        $verified = in_array($claim->status, [App\Models\PurchaseClaim::STATUS_TRANSACTION_VERIFIED, App\Models\PurchaseClaim::STATUS_REWARD_APPROVED, App\Models\PurchaseClaim::STATUS_PAYMENT_PROCESSING, App\Models\PurchaseClaim::STATUS_PAID]);
        $canVerify = in_array($claim->status, [App\Models\PurchaseClaim::STATUS_SUBMITTED, App\Models\PurchaseClaim::STATUS_UNDER_REVIEW, App\Models\PurchaseClaim::STATUS_SELLER_VERIFICATION, App\Models\PurchaseClaim::STATUS_TRANSACTION_VERIFIED]);
        $canApprove = $claim->status === App\Models\PurchaseClaim::STATUS_TRANSACTION_VERIFIED;
        $canReject = ! in_array($claim->status, [App\Models\PurchaseClaim::STATUS_REJECTED, App\Models\PurchaseClaim::STATUS_PAID]);
        $canDispute = ! in_array($claim->status, [App\Models\PurchaseClaim::STATUS_REJECTED, App\Models\PurchaseClaim::STATUS_PAID, App\Models\PurchaseClaim::STATUS_DISPUTED]);
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">{{ $claim->claim_reference }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">
                Buyer {{ $claim->user?->fullName() ?? '—' }}
                · Vehicle {{ $claim->vehicle?->displayName() ?? '—' }}
                · Purchased {{ $claim->purchase_date?->format('d M Y') }}
            </p>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-admin.status-badge :status="$claim->status" :label="$claim->statusLabel()" />
                @if (! empty($claim->fraud_flags))
                    @foreach ($claim->fraud_flags as $flag)
                        <span class="badge-red">{{ ucwords(str_replace('_', ' ', $flag)) }}</span>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($canVerify)
                <form method="POST" action="{{ route('admin.claims.verify', $claim) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Confirm that the purchase transaction is legitimate?')">✔ Verify transaction</button>
                </form>
            @endif
            @if ($canApprove)
                <form method="POST" action="{{ route('admin.claims.approve', $claim) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Approve the buyer reward and schedule the payout?')">✔ Approve reward</button>
                </form>
            @endif
            @if ($canReject)
                <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" x-data="{ reason: '' }">
                    @csrf
                    <input type="hidden" name="reason" :value="reason">
                    <button type="submit" class="btn-ghost btn-sm" @click.prevent="reason = prompt('Reason for rejection:'); if (reason) { $el.closest('form').submit(); }">✕ Reject</button>
                </form>
            @endif
            @if ($canDispute)
                <form method="POST" action="{{ route('admin.claims.dispute', $claim) }}">
                    @csrf
                    <button type="submit" class="btn-ghost btn-sm" onclick="return confirm('Mark this claim as disputed?')">⚠ Dispute</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Purchase Details</h2>
                </div>
                <dl class="grid gap-x-6 gap-y-3 px-5 py-4 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Provider reference</dt><dd class="font-mono text-xs">{{ $claim->provider_reference ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Purchase date</dt><dd>{{ $claim->purchase_date?->format('d M Y') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Purchase price</dt><dd class="font-semibold">{{ money($claim->purchase_price) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Commission</dt><dd>{{ money($claim->commission_amount) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/40">Buyer reward</dt><dd class="font-semibold text-gold-700">{{ money($claim->reward_amount) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">WorxCars retains</dt><dd>{{ money($claim->retained_amount) }}</dd></div>
                </dl>
                @if ($claim->description)
                    <div class="border-t border-royal-900/10 px-5 py-4">
                        <h3 class="label">Claim notes</h3>
                        <p class="whitespace-pre-line text-sm text-royal-900/75">{{ $claim->description }}</p>
                    </div>
                @endif
                @if ($verified)
                    <div class="grid gap-2 border-t border-royal-900/10 px-5 py-4 text-sm sm:grid-cols-3">
                        <div><p class="text-xs text-royal-900/50">Verified by</p><p class="font-medium">{{ $claim->verified_by ? \App\Models\User::find($claim->verified_by)?->fullName() : '—' }}</p><p class="text-xs text-royal-900/50">{{ $claim->verified_at?->format('d M Y H:i') }}</p></div>
                        <div><p class="text-xs text-royal-900/50">Approved by</p><p class="font-medium">{{ $claim->approved_by ? \App\Models\User::find($claim->approved_by)?->fullName() : '—' }}</p><p class="text-xs text-royal-900/50">{{ $claim->approved_at?->format('d M Y H:i') }}</p></div>
                        <div><p class="text-xs text-royal-900/50">Seller confirmed</p><p class="font-medium">{{ $claim->seller_verified_at?->format('d M Y H:i') ?? '—' }}</p></div>
                    </div>
                @endif
                @if ($claim->rejection_reason)
                    <div class="border-t border-royal-900/10 px-5 py-4">
                        <h3 class="label !text-rose-700">Rejection reason</h3>
                        <p class="text-sm text-rose-800">{{ $claim->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Documents ({{ $claim->documents->count() }})</h2>
                </div>
                @if ($claim->documents->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No supporting documents uploaded.</p>
                @else
                    <div class="grid gap-3 px-5 py-4 sm:grid-cols-2">
                        @foreach ($claim->documents as $document)
                            <div class="flex items-center justify-between gap-3 rounded-md bg-ivory-50 px-3 py-2.5 text-sm ring-1 ring-royal-900/10">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold">{{ ucwords(str_replace('_', ' ', $document->type)) }}</p>
                                    <p class="truncate text-xs text-royal-900/55">{{ $document->file_name ?? 'File' }} · {{ number_format($document->size / 1024, 1) }} KB</p>
                                </div>
                                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="shrink-0 btn-ghost btn-sm">View</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Transactions</h2>
                </div>
                @if ($claim->transactions->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No ledger transactions recorded for this claim.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr><th>Reference</th><th>Type</th><th>Direction</th><th class="text-right">Amount</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($claim->transactions as $transaction)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-mono text-xs">{{ $transaction->transaction_reference }}</td>
                                        <td>{{ ucfirst($transaction->type) }}</td>
                                        <td>{{ ucfirst($transaction->direction) }}</td>
                                        <td class="text-right font-semibold">{{ money($transaction->amount) }}</td>
                                        <td>{{ ucfirst($transaction->status) }}</td>
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
                <h3 class="section-title !text-xl">Payout Bank Details</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Account name</dt><dd class="text-right">{{ $bank['account_name'] ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Bank</dt><dd class="text-right">{{ $bank['bank_name'] ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Account number</dt><dd class="font-mono text-right">{{ $bank['account_number'] ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Branch code</dt><dd class="font-mono text-right">{{ $bank['branch_code'] ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Reward</h3>
                @if ($claim->buyerReward)
                    <div class="mt-4">
                        <a href="{{ route('admin.rewards.show', $claim->buyerReward) }}" class="font-mono text-sm font-semibold text-gold-700 hover:underline">{{ $claim->buyerReward->reward_reference }}</a>
                        <div class="mt-2 flex items-center gap-2">
                            <x-admin.status-badge :status="$claim->buyerReward->status" :label="$claim->buyerReward->statusLabel()" />
                        </div>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-royal-900/60">Amount</dt><dd class="font-semibold">{{ money($claim->buyerReward->amount) }}</dd></div>
                            @if ($claim->buyerReward->payout)
                                <div class="flex justify-between"><dt class="text-royal-900/60">Payout</dt><dd class="font-mono text-xs"><a href="{{ route('admin.payouts.index', ['q' => $claim->buyerReward->payout->payout_reference]) }}" class="text-gold-700 hover:underline">{{ $claim->buyerReward->payout->payout_reference }}</a></dd></div>
                            @endif
                        </dl>
                    </div>
                @else
                    <p class="mt-3 text-sm text-royal-900/50">No reward attached.</p>
                @endif
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Seller</h3>
                <div class="mt-3 text-sm">
                    @if ($claim->dealership)
                        <p class="font-semibold">{{ $claim->dealership->business_name }}</p>
                        <a href="{{ route('admin.dealerships.show', $claim->dealership) }}" class="mt-1 inline-block text-gold-700 hover:underline">View dealership</a>
                    @elseif ($claim->privateSeller)
                        <p class="font-semibold">{{ $claim->privateSeller?->user?->fullName() }}</p>
                        <a href="{{ route('admin.sellers.show', $claim->privateSeller) }}" class="mt-1 inline-block text-gold-700 hover:underline">View seller</a>
                        @if ($claim->seller_verified_at)
                            <p class="mt-2 text-xs text-royal-900/50">Confirmed sale {{ $claim->seller_verified_at->format('d M Y H:i') }}</p>
                        @endif
                    @else
                        <p class="text-royal-900/50">Vehicle was head-office listed.</p>
                    @endif
                </div>
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Activity Timeline</h3>
                <ol class="mt-4 space-y-0">
                    @foreach ($timeline as $event)
                        <li class="relative border-l-2 border-royal-900/10 pb-4 pl-4 last:pb-0">
                            <span class="absolute -left-[5px] top-1 h-2 w-2 rounded-full bg-gold-500"></span>
                            <p class="text-sm font-semibold text-royal-950">{{ $event['label'] }}</p>
                            <p class="text-xs text-royal-900/55">{{ $event['at']?->format('d M Y H:i') }}</p>
                            @if ($event['detail'])
                                <p class="mt-1 text-xs text-royal-900/60">{{ $event['detail'] }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>