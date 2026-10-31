<x-layouts.customer title="My Rewards">
    <h1 class="font-display text-2xl font-bold text-royal-950">My Rewards</h1>
    <p class="mt-1 text-sm text-royal-900/60">Every reward on this page relates to a real, verified purchase. Rewards are paid to your nominated bank account after approval.</p>

    @if ($rewards->isEmpty())
        <div class="card mt-6 p-14 text-center">
            <h2 class="font-display text-xl font-bold text-royal-950">No rewards yet</h2>
            <p class="mt-2 max-w-sm text-sm text-royal-900/60">Complete a qualifying purchase, submit a claim, and once verified and approved, your reward appears here.</p>
            <a href="{{ route('vehicles.index') }}" class="btn-gold btn-sm mt-5">Browse vehicles</a>
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($rewards as $reward)
                <div class="card p-5">
                    <div class="flex items-start justify-between">
                        <span class="badge {{ match ($reward->status) { 'paid' => 'badge-green', 'approved' => 'badge-blue', 'rejected' => 'badge-red', default => 'badge-grey' } }}">{{ $reward->statusLabel() }}</span>
                        <p class="font-display text-xl font-black text-royal-950">{{ money($reward->amount) }}</p>
                    </div>
                    <p class="mt-3 font-semibold text-royal-950">{{ $reward->vehicle?->displayName() }}</p>
                    <p class="text-xs text-royal-900/55">{{ $reward->reward_reference }}</p>
                    @if ($reward->payout && $reward->payout->completed_at)
                        <p class="mt-3 text-xs text-royal-900/55">Paid {{ $reward->payout->completed_at->format('d M Y') }} ({{ $reward->payout->provider }})</p>
                    @endif
                    @if ($reward->purchaseClaim)
                        <a href="{{ route('customer.claims.show', $reward->purchaseClaim) }}" class="mt-3 inline-flex text-xs font-medium text-gold-700 hover:underline">View related claim</a>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-10">{{ $rewards->links() }}</div>
    @endif
</x-layouts.customer>