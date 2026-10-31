<x-admin.layouts.admin title="Enquiry from {{ $enquiry->name }}" :breadcrumbs="[['label' => 'Enquiries', 'url' => route('admin.enquiries.index')], ['label' => $enquiry->name]]">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Enquiry — {{ $enquiry->name }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">Received {{ $enquiry->created_at?->format('d M Y H:i') }} · {{ Str::headline($enquiry->source ?? 'website') }}</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-admin.status-badge :status="$enquiry->status" :label="$enquiry->statusLabel()" />
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Message</h2>
                @if ($enquiry->message)
                    <p class="mt-3 whitespace-pre-line rounded-md bg-ivory-50 px-4 py-3 text-sm text-royal-900/80 ring-1 ring-royal-900/10">{{ $enquiry->message }}</p>
                @else
                    <p class="mt-3 text-sm text-royal-900/50">No message provided.</p>
                @endif
                <dl class="mt-4 grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Name</dt><dd>{{ $enquiry->name }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Email</dt><dd><a href="mailto:{{ $enquiry->email }}" class="text-gold-700 hover:underline">{{ $enquiry->email }}</a></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Phone</dt><dd>{{ $enquiry->phone ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Preferred contact</dt><dd>{{ ucfirst($enquiry->preferred_contact_method ?? '—') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Preferred date</dt><dd>{{ $enquiry->preferred_date?->format('d M Y') ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Linked account</dt><dd class="text-right">@if ($enquiry->user)<a href="{{ route('admin.users.show', $enquiry->user) }}" class="text-gold-700 hover:underline">{{ $enquiry->user->email }}</a>@else N/A @endif</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Finance interest</dt><dd>{{ $enquiry->finance_interest ? 'Yes' : 'No' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-royal-900/60">Trade-in interest</dt><dd>{{ $enquiry->trade_in_interest ? 'Yes' : 'No' }}</dd></div>
                </dl>
            </div>

            @if ($enquiry->vehicle)
                <div class="card overflow-hidden">
                    <div class="border-b border-royal-900/10 px-5 py-4">
                        <h2 class="section-title !text-xl">Vehicle</h2>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4">
                        <div class="min-w-0">
                            <a href="{{ route('admin.vehicles.show', $enquiry->vehicle) }}" class="font-semibold text-royal-950 hover:text-gold-700">{{ $enquiry->vehicle->displayName() }}</a>
                            <p class="text-xs text-royal-900/55">{{ $enquiry->vehicle->vehicle_reference }} · {{ money($enquiry->vehicle->price) }}</p>
                        </div>
                        <x-admin.status-badge :status="$enquiry->vehicle->status" :label="$enquiry->vehicle->statusLabel()" />
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Manage Enquiry</h2>
                <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="label">Status</label>
                        <select name="status" class="input" required>
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected($enquiry->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Assignee</label>
                        <select name="assigned_to" class="input">
                            <option value="">Unassigned</option>
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}" @selected($enquiry->assigned_to === $admin->id)>{{ $admin->fullName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Internal Note</label>
                        <textarea name="internal_note" rows="4" class="input">{{ old('internal_note', $enquiry->internal_note) }}</textarea>
                    </div>
                    <button type="submit" class="btn-gold btn-sm w-full">Save changes</button>
                </form>
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Quick Contact</h3>
                <div class="mt-3 flex flex-col gap-2">
                    <a href="mailto:{{ $enquiry->email }}" class="btn-ghost btn-sm">Email {{ $enquiry->name }}</a>
                    @if ($enquiry->phone)
                        <a href="tel:{{ $enquiry->phone }}" class="btn-ghost btn-sm">Call {{ $enquiry->phone }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>