<x-layouts.customer title="Claim {{ $claim->claim_reference }}">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-royal-950">Claim {{ $claim->claim_reference }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">Submitted {{ $claim->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="badge {{ match ($claim->status) { 'paid' => 'badge-green', 'rejected' => 'badge-red', 'disputed' => 'badge-amber', 'reward_approved', 'transaction_verified' => 'badge-blue', default => 'badge-grey' } }} text-sm">{{ $claim->statusLabel() }}</span>
    </div>

    <div class="card mt-6 p-6">
        <h2 class="font-display text-lg font-bold text-royal-950">Status tracker</h2>
        <div class="mt-5 flex flex-wrap items-center gap-2">
            @foreach ($flow as $status => $label)
                <div class="flex items-center gap-2">
                    <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $order[$status] <= $order[$claim->status] ? 'bg-royal-950 text-gold-300' : 'bg-royal-900/5 text-royal-900/40' }}">
                        {{ $order[$status] <= $order[$claim->status] ? '✓' : $loop->iteration }} {{ $label }}
                    </span>
                    @if (! $loop->last)
                        <span class="h-px w-4 bg-royal-900/15"></span>
                    @endif
                </div>
            @endforeach
        </div>
        @if (in_array($claim->status, ['rejected', 'disputed']) && $claim->rejection_reason)
            <div class="mt-5 rounded-md bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-500/30">
                <p class="font-semibold">Reason:</p>
                <p class="mt-1">{{ $claim->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h2 class="font-display text-lg font-bold text-royal-950">Purchase details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Vehicle</dt><dd class="font-medium text-royal-950">{{ $claim->vehicle?->displayName() }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Purchase date</dt><dd class="font-medium text-royal-950">{{ $claim->purchase_date?->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Purchase price</dt><dd class="font-medium text-royal-950">{{ money($claim->purchase_price) }}</dd></div>
                @if ($claim->provider_reference)
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Reference</dt><dd class="font-medium text-royal-950">{{ $claim->provider_reference }}</dd></div>
                @endif
                <div class="border-t border-royal-900/10 pt-3"><dt class="text-royal-900/55">Notes</dt><dd class="mt-1 text-royal-900/75">{{ $claim->description }}</dd></div>
            </dl>
        </div>

        <div class="card p-6">
            <h2 class="font-display text-lg font-bold text-royal-950">Reward</h2>
            @if ($claim->buyerReward)
                <p class="mt-4 rounded-md bg-gradient-to-r from-royal-950 to-royal-850 p-4 text-center font-display text-3xl font-black text-gold-300 ring-1 ring-gold-500/30">{{ money($claim->buyerReward->amount) }}</p>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Reference</dt><dd class="font-medium text-royal-950">{{ $claim->buyerReward->reward_reference }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Status</dt><dd class="font-medium text-royal-950">{{ $claim->buyerReward->statusLabel() }}</dd></div>
                    @if ($claim->buyerReward->payout)
                        <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Payout</dt><dd class="font-medium text-royal-950">{{ $claim->buyerReward->payout->payout_reference }}</dd></div>
                        @if ($claim->buyerReward->payout->completed_at)
                            <div class="flex justify-between gap-4"><dt class="text-royal-900/55">Paid on</dt><dd class="font-medium text-royal-950">{{ $claim->buyerReward->payout->completed_at->format('d M Y H:i') }}</dd></div>
                        @endif
                    @endif
                </dl>
            @else
                <p class="mt-4 text-sm text-royal-900/60">No reward record attached.</p>
            @endif
        </div>
    </div>

    @if ($claim->fraud_flags)
        <div class="card mt-6 p-6">
            <h2 class="font-display text-lg font-bold text-royal-950">Verification checks</h2>
            <p class="mt-2 text-xs text-royal-900/55">Our automated fraud and eligibility checks run on every claim. The presence of a flag doesn't mean your claim is rejected — our team reviews thoroughly.</p>
            @if (count($claim->fraud_flags))
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($claim->fraud_flags as $flag)
                        <span class="badge badge-amber">{{ ucwords(str_replace('_', ' ', $flag)) }}</span>
                    @endforeach
                </div>
            @else
                <span class="badge badge-green mt-3">No flags detected</span>
            @endif
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('customer.claims') }}" class="btn-ghost btn-sm">← Back to claims</a>
    </div>
</x-layouts.customer>