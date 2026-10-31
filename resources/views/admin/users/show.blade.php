<x-admin.layouts.admin title="{{ $user->fullName() }}" :breadcrumbs="[['label' => 'Users', 'url' => route('admin.users.index')], ['label' => $user->fullName()]]">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="grid h-14 w-14 place-items-center rounded-full bg-royal-950 text-xl font-bold text-gold-300">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</div>
            <div>
                <h1 class="font-display text-3xl font-black text-royal-950">{{ $user->fullName() }}</h1>
                <p class="mt-1 text-sm text-royal-900/60">{{ $user->email }} · {{ $user->phone ?? 'no phone' }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <x-admin.status-badge :status="$user->is_active ? 'active' : 'suspended'" :label="$user->is_active ? 'Active' : 'Inactive'" />
                    @foreach ($user->roles as $role)
                        <span class="badge-grey">{{ $role->label ?? ucwords(str_replace('_', ' ', $role->name)) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
            @csrf
            <button type="submit" class="btn-ghost btn-sm {{ $user->is_active ? '!text-rose-700 hover:!border-rose-300' : '' }}" onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this account?')">
                {{ $user->is_active ? 'Deactivate account' : 'Activate account' }}
            </button>
        </form>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Manage Roles</h2>
                <form method="POST" action="{{ route('admin.users.roles', $user) }}" class="mt-4">
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($roles as $role)
                            <label class="flex items-center gap-2 rounded-md bg-ivory-50 px-3 py-2 text-sm ring-1 ring-royal-900/10">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked($user->roles->contains(fn ($r) => $r->id === $role->id)) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                                {{ $role->label ?? ucwords(str_replace('_', ' ', $role->name)) }}
                                <span class="ml-auto text-xs text-royal-900/50">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="btn-gold btn-sm mt-4">Save roles</button>
                </form>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Purchase Claims ({{ $user->purchaseClaims->count() }})</h2>
                </div>
                @if ($user->purchaseClaims->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No claims submitted.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr><th>Reference</th><th>Vehicle</th><th>Reward</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($user->purchaseClaims as $claim)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-mono text-xs font-semibold"><a href="{{ route('admin.claims.show', $claim) }}" class="text-royal-950 hover:text-gold-700">{{ $claim->claim_reference }}</a></td>
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

            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Buyer Rewards ({{ $user->buyerRewards->count() }})</h2>
                </div>
                @if ($user->buyerRewards->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No rewards earned.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr><th>Reference</th><th>Vehicle</th><th class="text-right">Amount</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($user->buyerRewards as $reward)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-mono text-xs font-semibold"><a href="{{ route('admin.rewards.show', $reward) }}" class="text-royal-950 hover:text-gold-700">{{ $reward->reward_reference }}</a></td>
                                        <td class="max-w-[220px] truncate">{{ $reward->vehicle?->displayName() ?? '—' }}</td>
                                        <td class="text-right font-semibold text-gold-700">{{ money($reward->amount) }}</td>
                                        <td><x-admin.status-badge :status="$reward->status" :label="$reward->statusLabel()" /></td>
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
                <h3 class="section-title !text-xl">Account Details</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Email verified</dt><dd>{{ $user->email_verified_at ? 'Yes' : 'No' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Location</dt><dd class="text-right">{{ collect([$user->city, $user->province])->filter()->join(', ') ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Last login</dt><dd>{{ $user->last_login_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">ID tail</dt><dd>{{ $user->id_number_tail ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Registered</dt><dd>{{ $user->created_at?->format('d M Y') }}</dd></div>
                </dl>
            </div>

            <div class="p-0 card">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h3 class="section-title !text-xl">Permissions</h3>
                </div>
                <div class="px-5 py-4">
                    @php $perms = $user->permissions()->get()->groupBy('group'); @endphp
                    @forelse ($perms as $group => $permissions)
                        <div class="mb-3">
                            <p class="label !mb-1">{{ Str::headline($group ?: 'General') }}</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($permissions as $permission)
                                    <span class="badge-blue">{{ $permission->label ?? Str::headline($permission->name) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-royal-900/50">No explicit permissions (role-based access).</p>
                    @endforelse
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h3 class="section-title !text-xl">Enquiries ({{ $user->enquiries->count() }})</h3>
                </div>
                @if ($user->enquiries->isEmpty())
                    <p class="px-5 py-6 text-sm text-royal-900/50">No enquiries.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($user->enquiries as $enquiry)
                            <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="flex items-center justify-between gap-3 px-5 py-3 text-sm hover:bg-ivory-100/60">
                                <span class="truncate">{{ $enquiry->vehicle?->displayName() ?? 'General' }}</span>
                                <x-admin.status-badge :status="$enquiry->status" :label="$enquiry->statusLabel()" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.layouts.admin>