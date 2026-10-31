<x-admin.layouts.admin title="Roles & Permissions" :breadcrumbs="[['label' => 'Roles']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Roles & Permissions</h1>
            <p class="mt-1 text-sm text-royal-900/60">Manage what each role can do across the platform.</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            @foreach ($roles as $role)
                <div class="card overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-royal-900/10 px-5 py-4">
                        <div>
                            <h2 class="font-display text-lg font-bold text-royal-950">{{ $role->label ?? ucwords(str_replace('_', ' ', $role->name)) }}</h2>
                            <p class="text-xs text-royal-900/55">{{ $role->name }} · {{ $role->users_count }} user(s) · {{ $role->permissions_count }} permission(s)</p>
                        </div>
                        @if ($managed->contains(fn ($m) => $m->id === $role->id))
                            <span class="badge-gold">Managed</span>
                        @else
                            <span class="badge-grey">System</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="px-5 py-4">
                        @csrf
                        @method('PUT')
                        @if ($managed->contains(fn ($m) => $m->id === $role->id))
                            @foreach ($permissions as $group => $groupPermissions)
                                <div class="mb-4 last:mb-0">
                                    <p class="label !mb-1">{{ Str::headline($group ?: 'General') }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($groupPermissions as $permission)
                                            <label class="flex items-center gap-1.5 rounded-full bg-ivory-50 px-3 py-1.5 text-xs ring-1 ring-royal-900/10">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                    @checked($role->permissions->contains(fn ($p) => $p->id === $permission->id))
                                                    class="h-3.5 w-3.5 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                                                {{ $permission->label ?? Str::headline($permission->name) }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <button type="submit" class="btn-gold btn-sm mt-4">Save permissions</button>
                        @else
                            <p class="text-sm text-royal-900/50">This role's permissions are fixed to keep the platform secure.</p>
                            @if ($role->permissions->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1">
                                    @foreach ($role->permissions as $permission)
                                        <span class="badge-blue">{{ $permission->label ?? Str::headline($permission->name) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </form>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h3 class="section-title !text-xl">Permission Groups</h3>
                <p class="mt-2 text-sm text-royal-900/55">Permissions are grouped by feature area. The super admin always bypasses permission checks.</p>
                <div class="mt-4">
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="mb-3 flex items-center justify-between rounded-md bg-ivory-50 px-3 py-2 text-sm ring-1 ring-royal-900/10">
                            <span class="font-medium">{{ Str::headline($group ?: 'General') }}</span>
                            <span class="badge-grey">{{ $groupPermissions->count() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>