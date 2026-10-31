<x-admin.layouts.admin title="Audit Logs" :breadcrumbs="[['label' => 'Audit Logs']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Audit Logs</h1>
            <p class="mt-1 text-sm text-royal-900/60">Immutable trail of administrative actions across the platform.</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.audit-logs') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search action, description..." class="input">
        <select name="action" class="input">
            <option value="">All actions</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
            @endforeach
        </select>
        <select name="entity" class="input">
            <option value="">All entities</option>
            @foreach (['vehicle', 'dealership', 'private_seller', 'vehicle_campaign', 'buyer_reward', 'purchase_claim', 'reward_payout', 'page', 'faq', 'banner', 'user', 'role', 'setting'] as $entity)
                <option value="{{ $entity }}" @selected(request('entity') === $entity)>{{ ucwords(str_replace('_', ' ', $entity)) }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.audit-logs') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($logs->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No log entries match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>IP</th>
                            <th class="text-right">Occurred</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr class="hover:bg-ivory-100/60">
                                <td><span class="badge-blue">{{ $log->action }}</span></td>
                                <td class="font-mono text-xs">{{ $log->entity_type }} · {{ $log->entity_id }}</td>
                                <td class="max-w-[320px]">
                                    <p class="truncate">{{ $log->description }}</p>
                                    @if ($log->before || $log->after)
                                        <details class="mt-1">
                                            <summary class="cursor-pointer text-xs text-gold-700 hover:underline">view data diff</summary>
                                            <div class="mt-1 rounded-md bg-royal-950 p-2 font-mono text-[11px] text-ivory-100/80">
                                                <p class="text-rose-300">before: {{ json_encode($log->before) }}</p>
                                                <p class="text-emerald-300">after: {{ json_encode($log->after) }}</p>
                                            </div>
                                        </details>
                                    @endif
                                </td>
                                <td class="max-w-[160px] truncate">{{ $log->user?->fullName() ?? 'System' }}</td>
                                <td class="font-mono text-xs">{{ $log->ip_address }}</td>
                                <td class="text-right text-royal-900/60">{{ $log->occurred_at?->format('d M Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-royal-900/5 px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-admin.layouts.admin>