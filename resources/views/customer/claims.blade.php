<x-layouts.customer title="My Claims">
    <h1 class="font-display text-2xl font-bold text-royal-950">My Claims</h1>
    <p class="mt-1 text-sm text-royal-900/60">Track the status of your buyer reward claims. Remember: rewards are only paid after a verified purchase.</p>

    @if ($claims->isEmpty())
        <div class="card mt-6 flex flex-col items-center justify-center p-14 text-center">
            <h2 class="font-display text-xl font-bold text-royal-950">No claims yet</h2>
            <p class="mt-2 max-w-sm text-sm text-royal-900/60">When you've completed a qualifying purchase, submit a claim from the vehicle's page to start the verification process.</p>
            <a href="{{ route('vehicles.index') }}" class="btn-gold btn-sm mt-5">Browse vehicles</a>
        </div>
    @else
        <div class="card mt-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Vehicle</th>
                            <th>Purchase date</th>
                            <th>Reward</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($claims as $claim)
                            <tr>
                                <td class="font-medium text-royal-950">{{ $claim->claim_reference }}</td>
                                <td>
                                    <a href="{{ route('vehicles.show', $claim->vehicle) }}" class="font-medium text-gold-700 hover:underline">{{ $claim->vehicle?->displayName() }}</a>
                                </td>
                                <td>{{ $claim->purchase_date?->format('d M Y') }}</td>
                                <td class="font-semibold">{{ money($claim->reward_amount) }}</td>
                                <td>
                                    <span class="badge {{ match ($claim->status) { 'paid' => 'badge-green', 'rejected' => 'badge-red', 'disputed' => 'badge-amber', 'reward_approved', 'transaction_verified' => 'badge-blue', default => 'badge-grey' } }}">{{ $claim->statusLabel() }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('customer.claims.show', $claim) }}" class="btn-ghost btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-10">{{ $claims->links() }}</div>
    @endif
</x-layouts.customer>