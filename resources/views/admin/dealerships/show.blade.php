<x-admin.layouts.admin title="{{ $dealership->business_name }}" :breadcrumbs="[['label' => 'Dealerships', 'url' => route('admin.dealerships.index')], ['label' => $dealership->business_name]]">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">{{ $dealership->business_name }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $dealership->trading_name ? 't/a '.$dealership->trading_name.' · ' : '' }}{{ $dealership->registration_number ?: '' }}</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-admin.status-badge :status="$dealership->status" :label="$dealership->statusLabel()" />
                @if ($dealership->is_active)
                    <span class="badge-green">Active</span>
                @else
                    <span class="badge-red">Inactive</span>
                @endif
                @if ($dealership->verified_at)
                    <span class="badge-grey">Verified {{ $dealership->verified_at->format('d M Y') }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($dealership->status !== App\Models\Dealership::STATUS_VERIFIED)
                <form method="POST" action="{{ route('admin.dealerships.verify', $dealership) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Verify this dealership and activate its accounts?')">✔ Verify</button>
                </form>
            @endif
            @if ($dealership->is_active)
                <form method="POST" action="{{ route('admin.dealerships.suspend', $dealership) }}">
                    @csrf
                    <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300" onclick="return confirm('Suspend this dealership?')">Suspend</button>
                </form>
            @endif
            <a href="{{ route('admin.dealerships.edit', $dealership) }}" class="btn-ghost btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.dealerships.destroy', $dealership) }}" onsubmit="return confirm('Delete this dealership?')">
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
                    <h2 class="section-title !text-xl">Vehicles</h2>
                </div>
                @if ($dealership->vehicles->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No vehicles listed by this dealership.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($dealership->vehicles as $vehicle)
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
                    <h2 class="section-title !text-xl">User Accounts ({{ $dealership->userAccounts->count() }})</h2>
                </div>
                @if ($dealership->userAccounts->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No user accounts linked to this dealership.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($dealership->userAccounts as $account)
                            <a href="{{ route('admin.users.show', $account) }}" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-ivory-100/60">
                                <div>
                                    <p class="font-semibold text-royal-950">{{ $account->fullName() }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $account->email }}</p>
                                </div>
                                @if ($account->pivot->is_primary)
                                    <span class="badge-gold">Primary</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h3 class="section-title !text-xl">Details</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Contact person</dt><dd class="text-right">{{ $dealership->contact_person_name ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Email</dt><dd class="text-right">{{ $dealership->email }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Phone</dt><dd class="text-right">{{ $dealership->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Website</dt><dd class="text-right"><a href="{{ $dealership->website }}" target="_blank" rel="noopener" class="text-gold-700 hover:underline">{{ $dealership->website ?? '—' }}</a></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">VAT number</dt><dd class="text-right">{{ $dealership->vat_number ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Address</dt><dd class="text-right max-w-[200px]">{{ collect([$dealership->street_address, $dealership->city, $dealership->postal_code, $dealership->province])->filter()->join(', ') ?: '—' }}</dd></div>
                </dl>
                @if ($dealership->description)
                    <p class="mt-4 border-t border-royal-900/10 pt-4 text-sm text-royal-900/75">{{ $dealership->description }}</p>
                @endif
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Verification Documents</h3>
                @if ($documents->isEmpty())
                    <p class="mt-3 text-sm text-royal-900/50">No documents on file.</p>
                @else
                    <div class="mt-3 space-y-2">
                        @foreach ($documents as $document)
                            <div class="flex items-center justify-between gap-3 rounded-md bg-ivory-50 px-3 py-2 text-sm">
                                <span class="truncate">{{ $document->category ?? $document->file_name }}</span>
                                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="text-gold-700 hover:underline">View</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.layouts.admin>