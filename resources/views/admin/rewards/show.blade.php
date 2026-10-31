<x-admin.layouts.admin title="Reward {{ $reward->reward_reference }}" :breadcrumbs="[['label' => 'Rewards', 'url' => route('admin.rewards.index')], ['label' => $reward->reward_reference]]">
    @php
        $canApprove = in_array($reward->status, [App\Models\BuyerReward::STATUS_PENDING, App\Models\BuyerReward::STATUS_ELIGIBLE, App\Models\BuyerReward::STATUS_UNDER_VERIFICATION]);
        $canPay = $reward->status === App\Models\BuyerReward::STATUS_APPROVED;
        $canReject = ! in_array($reward->status, [App\Models\BuyerReward::STATUS_REJECTED, App\Models\BuyerReward::STATUS_PAID]);
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">{{ $reward->reward_reference }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">Reward of {{ money($reward->amount) }} for {{ $reward->user?->fullName() ?? 'a buyer' }} on the {{ $reward->vehicle?->displayName() ?? 'vehicle' }}.</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-admin.status-badge :status="$reward->status" :label="$reward->statusLabel()" />
                @if ($reward->campaign)
                    <a href="{{ route('admin.campaigns.show', $reward->campaign) }}" class="badge-blue">{{ $reward->campaign->name }}</a>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($canApprove)
                <form method="POST" action="{{ route('admin.rewards.approve', $reward) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Approve this reward and schedule the payout?')">✔ Approve & schedule</button>
                </form>
            @endif
            @if ($canPay)
                <form method="POST" action="{{ route('admin.rewards.pay', $reward) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Execute the payout for this reward now?')">✔ Execute payout</button>
                </form>
            @endif
            @if ($canReject)
                <form method="POST" action="{{ route('admin.rewards.reject', $reward) }}" x-data="{ reason: '' }">
                    @csrf
                    <input type="hidden" name="reason" :value="reason">
                    <button type="submit" class="btn-ghost btn-sm" @click.prevent="reason = prompt('Reason for rejection:'); if (reason) { $el.closest('form').submit(); }">✕ Reject</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Reward Details</h2>
                <dl class="mt-4 grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Buyer</dt><dd class="text-right"><a href="{{ route('admin.users.show', $reward->user) }}" class="text-gold-700 hover:underline">{{ $reward->user?->fullName() ?? '—' }}</a></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Email</dt><dd class="text-right">{{ $reward->user?->email ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Vehicle</dt><dd class="text-right"><a href="{{ route('admin.vehicles.show', $reward->vehicle) }}" class="text-gold-700 hover:underline">{{ $reward->vehicle?->displayName() ?? '—' }}</a></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Campaign</dt><dd class="text-right">{{ $reward->campaign?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Reference</dt><dd class="font-mono text-xs">{{ $reward->reward_reference }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Claim</dt><dd class="text-right">@if ($reward->purchaseClaim)<a href="{{ route('admin.claims.show', $reward->purchaseClaim) }}" class="font-mono text-xs text-gold-700 hover:underline">{{ $reward->purchaseClaim->claim_reference }}</a>@else — @endif</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Status at</dt><dd>{{ $reward->status_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Eligibility confirmed</dt><dd>{{ $reward->eligibility_confirmed_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                </dl>
                @if ($reward->notes)
                    <div class="mt-4 border-t border-royal-900/10 pt-4">
                        <h3 class="label">Notes</h3>
                        <p class="text-sm text-royal-900/70">{{ $reward->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="card p-5">
                <h2 class="section-title !text-xl">Configuration Snapshot</h2>
                @if (is_array($reward->configuration_snapshot))
                    <dl class="mt-4 grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                        @foreach ($reward->configuration_snapshot as $key => $item)
                            @if (! is_array($item))
                                <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-1">
                                    <dt class="text-royal-900/55">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                                    <dd class="text-right">{{ $item }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                @else
                    <p class="mt-3 text-sm text-royal-900/50">No configuration recorded.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card-dark p-5">
                <h3 class="font-display text-lg font-bold text-gold-300">Payout</h3>
                @if ($reward->payout)
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-ivory-100/60">Reference</dt><dd class="font-mono text-ivory-50">{{ $reward->payout->payout_reference }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ivory-100/60">Provider</dt><dd class="text-ivory-50">{{ $reward->payout->provider }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ivory-100/60">Status</dt><dd><x-admin.status-badge :status="$reward->payout->status" /></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ivory-100/60">Provider txn</dt><dd class="font-mono text-xs text-ivory-50">{{ $reward->payout->provider_transaction_id ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ivory-100/60">Requested</dt><dd class="text-ivory-50">{{ $reward->payout->requested_at?->format('d M Y H:i') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ivory-100/60">Completed</dt><dd class="text-ivory-50">{{ $reward->payout->completed_at?->format('d M Y H:i') }}</dd></div>
                        @if ($reward->payout->failure_reason)
                            <div class="rounded-md bg-rose-500/10 px-3 py-2 text-xs text-rose-300"><span class="font-semibold">Failure:</span> {{ $reward->payout->failure_reason }}</div>
                        @endif
                    </dl>
                    @if ($reward->payout->status === App\Models\RewardPayout::STATUS_SCHEDULED)
                        <form method="POST" action="{{ route('admin.rewards.pay', $reward) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full bg-gold-500 px-4 py-2 text-sm font-bold text-royal-950 transition hover:bg-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400">Execute payout</button>
                        </form>
                    @endif
                @else
                    <p class="mt-3 text-sm text-ivory-100/50">No payout scheduled yet. Approve the reward to schedule one.</p>
                @endif
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Claim Documents</h3>
                @if ($reward->purchaseClaim?->documents->isEmpty())
                    <p class="mt-3 text-sm text-royal-900/50">No documents attached.</p>
                @elseif ($reward->purchaseClaim)
                    <div class="mt-3 space-y-2">
                        @foreach ($reward->purchaseClaim->documents as $document)
                            <div class="flex items-center justify-between gap-3 rounded-md bg-ivory-50 px-3 py-2 text-sm ring-1 ring-royal-900/10">
                                <span class="truncate">{{ ucwords(str_replace('_', ' ', $document->type)) }}</span>
                                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="shrink-0 text-gold-700 hover:underline">View</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-royal-900/50">No claim attached.</p>
                @endif
            </div>
        </div>
    </div>
</x-admin.layouts.admin>