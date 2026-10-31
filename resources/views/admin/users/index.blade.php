<x-admin.layouts.admin title="Users" :breadcrumbs="[['label' => 'Users']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">User Accounts</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} user(s)</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, surname, email..." class="input">
        <select name="role" class="input">
            <option value="">All roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->label ?? ucwords(str_replace('_', ' ', $role->name)) }}</option>
            @endforeach
        </select>
        <select name="is_active" class="input">
            <option value="">Active & inactive</option>
            <option value="1" @selected(request('is_active') === '1')>Active only</option>
            <option value="0" @selected(request('is_active') === '0')>Inactive only</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No users match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Roles</th>
                            <th>Claims</th>
                            <th>Rewards</th>
                            <th>Enquiries</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $member)
                            <tr class="hover:bg-ivory-100/60">
                                <td>
                                    <a href="{{ route('admin.users.show', $member) }}" class="block font-semibold text-royal-950 hover:text-gold-700">{{ $member->fullName() }}</a>
                                    <p class="text-xs text-royal-900/55">Joined {{ $member->created_at?->format('d M Y') }}</p>
                                </td>
                                <td class="max-w-[220px]">
                                    <p class="truncate">{{ $member->email }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $member->phone ?? '—' }}</p>
                                </td>
                                <td>
                                    <div class="flex max-w-[200px] flex-wrap gap-1">
                                        @forelse ($member->roles as $role)
                                            <span class="badge-grey">{{ $role->label ?? ucwords(str_replace('_', ' ', $role->name)) }}</span>
                                        @empty
                                            <span class="badge-grey">No role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-center">{{ $member->purchase_claims_count }}</td>
                                <td class="text-center">{{ $member->buyer_rewards_count }}</td>
                                <td class="text-center">{{ $member->enquiries_count }}</td>
                                <td>
                                    @if ($member->is_active)
                                        <span class="badge-green">Active</span>
                                    @else
                                        <span class="badge-red">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-right"><a href="{{ route('admin.users.show', $member) }}" class="btn-ghost btn-sm">View</a></td>
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