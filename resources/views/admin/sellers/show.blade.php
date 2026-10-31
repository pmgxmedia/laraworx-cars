<x-admin.layouts.admin title="{{ $seller->user?->fullName() ?? 'Private Seller' }}" :breadcrumbs="[['label' => 'Private Sellers', 'url' => route('admin.sellers.index')], ['label' => $seller->user?->fullName() ?? 'Seller']]">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">{{ $seller->user?->fullName() ?? 'Private Seller' }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $seller->contact_email }} · {{ $seller->phone ?? 'no phone' }}</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-admin.status-badge :status="$seller->status" :label="$seller->statusLabel()" />
                @if ($seller->is_active)
                    <span class="badge-green">Active</span>
                @else
                    <span class="badge-red">Inactive</span>
                @endif
                @if ($seller->verified_at)
                    <span class="badge-grey">Verified {{ $seller->verified_at->format('d M Y') }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($seller->status !== App\Models\PrivateSeller::STATUS_VERIFIED)
                <form method="POST" action="{{ route('admin.sellers.verify', $seller) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Verify and activate this private seller?')">✔ Verify</button>
                </form>
            @endif
            @if ($seller->is_active)
                <form method="POST" action="{{ route('admin.sellers.suspend', $seller) }}">
                    @csrf
                    <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300" onclick="return confirm('Suspend this seller?')">Suspend</button>
                </form>
            @endif
            <a href="{{ route('admin.sellers.edit', $seller) }}" class="btn-ghost btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.sellers.destroy', $seller) }}" onsubmit="return confirm('Delete this seller?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300">Delete</button>
            </form>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Vehicles ({{ $seller->vehicles->count() }})</h2>
                </div>
                @if ($seller->vehicles->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No vehicles listed by this seller.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($seller->vehicles as $vehicle)
                            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-ivory-100/60">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-royal-950">{{ $vehicle->displayName() }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $vehicle->vehicle_reference }} · {{ money($vehicle->price) }}</p>
                                </div>
                                <x-admin.status-badge :status="$vehicle->status" :label="$vehicle->statusLabel()" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Purchase Claims ({{ $seller->purchaseClaims->count() }})</h2>
                </div>
                @if ($seller->purchaseClaims->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No purchase claims tied to this seller.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr><th>Reference</th><th>Vehicle</th><th>Reward</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($seller->purchaseClaims as $claim)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-semibold"><a href="{{ route('admin.claims.show', $claim) }}" class="hover:text-gold-700">{{ $claim->claim_reference }}</a></td>
                                        <td class="max-w-[220px] truncate">{{ $claim->vehicle?->displayName() ?? '—' }}</td>
                                        <td>{{ money($claim->reward_amount) }}</td>
                                        <td><x-admin.status-badge :status="$claim->status" :label="$claim->statusLabel()" /></td>
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
                <h3 class="section-title !text-xl">Details</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Account</dt><dd class="text-right"><a href="{{ route('admin.users.show', $seller->user) }}" class="text-gold-700 hover:underline">{{ $seller->user?->email ?? '—' }}</a></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Contact email</dt><dd class="text-right">{{ $seller->contact_email ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Phone</dt><dd class="text-right">{{ $seller->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Address</dt><dd class="text-right max-w-[200px]">{{ collect([$seller->street_address, $seller->city, $seller->postal_code, $seller->province])->filter()->join(', ') ?: '—' }}</dd></div>
                </dl>
                @if ($seller->id_document_path)
                    <div class="mt-4 border-t border-royal-900/10 pt-4">
                        <a href="{{ asset('storage/'.$seller->id_document_path) }}" target="_blank" class="btn-ghost btn-sm">View ID document</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.layouts.admin>