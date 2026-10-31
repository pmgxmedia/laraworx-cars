<x-admin.layouts.admin title="Enquiries" :breadcrumbs="[['label' => 'Enquiries']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Buyer Enquiries</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} enquiry(ies)</p>
        </div>
        <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="btn-ghost btn-sm">New enquiries</a>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.enquiries.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, vehicle..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="assigned_to" class="input">
            <option value="">Any assignee</option>
            @foreach ($admins as $admin)
                <option value="{{ $admin->id }}" @selected((string) request('assigned_to') === (string) $admin->id)>{{ $admin->fullName() }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.enquiries.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="card mt-4 overflow-hidden">
        @if ($rows->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No enquiries match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Enquiry</th>
                            <th>Vehicle</th>
                            <th>Contact</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Assignee</th>
                            <th class="text-right">Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $enquiry)
                            <tr class="hover:bg-ivory-100/60">
                                <td>
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="block font-semibold text-royal-950 hover:text-gold-700">{{ $enquiry->name }}</a>
                                    <p class="max-w-[200px] truncate text-xs text-royal-900/55">{{ $enquiry->message }}</p>
                                </td>
                                <td class="max-w-[200px] truncate">
                                    @if ($enquiry->vehicle)
                                        <a href="{{ route('admin.vehicles.show', $enquiry->vehicle) }}" class="text-gold-700 hover:underline">{{ $enquiry->vehicle->displayName() }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <p class="max-w-[180px] truncate">{{ $enquiry->email }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $enquiry->phone }}</p>
                                </td>
                                <td>{{ Str::headline($enquiry->source ?? 'website') }}</td>
                                <td><x-admin.status-badge :status="$enquiry->status" :label="$enquiry->statusLabel()" /></td>
                                <td class="max-w-[140px] truncate">{{ $enquiry->assignedTo?->fullName() ?? '—' }}</td>
                                <td class="text-right text-royal-900/60">{{ $enquiry->created_at?->format('d M Y') }}</td>
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