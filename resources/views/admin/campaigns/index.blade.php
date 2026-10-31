<x-admin.layouts.admin title="Campaigns" :breadcrumbs="[['label' => 'Campaigns']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Reward Campaigns</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $rows->total() }} campaign(s)</p>
        </div>
        <a href="{{ route('admin.campaigns.create') }}" class="btn-gold btn-sm">+ New Campaign</a>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="GET" action="{{ route('admin.campaigns.index') }}" class="mt-6 grid gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-royal-900/10 sm:grid-cols-2 lg:grid-cols-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search campaign..." class="input">
        <select name="status" class="input">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="btn-ghost btn-sm">Filter</button>
            <a href="{{ route('admin.campaigns.index') }}" class="btn-ghost btn-sm">Reset</a>
        </div>
    </form>

    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($rows as $campaign)
            <div class="card overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="font-display text-lg font-bold text-royal-950 hover:text-gold-700">{{ $campaign->name }}</a>
                        <x-admin.status-badge :status="$campaign->status" :label="$campaign->statusLabel()" />
                    </div>
                    @if ($campaign->description)
                        <p class="mt-2 line-clamp-2 text-sm text-royal-900/60">{{ $campaign->description }}</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between text-xs text-royal-900/60">
                        <span>{{ $campaign->campaign_vehicles_count }} vehicle(s)</span>
                        <span>{{ $campaign->start_date?->format('d M Y') ?? 'Open-ended' }}{{ $campaign->end_date ? ' → '.$campaign->end_date->format('d M Y') : '' }}</span>
                    </div>
                    @if ($campaign->is_featured)
                        <span class="badge-gold mt-3">★ Featured campaign</span>
                    @endif
                </div>
                <div class="flex items-center justify-between border-t border-royal-900/5 px-5 py-3">
                    <span class="text-xs text-royal-900/50">by {{ $campaign->createdBy?->fullName() ?? '—' }}</span>
                    <a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn-ghost btn-sm">Manage</a>
                </div>
            </div>
        @endforeach
    </div>
    @if ($rows->isEmpty())
        <p class="card mt-4 px-5 py-10 text-center text-sm text-royal-900/50">No campaigns found.</p>
    @endif

    @if ($rows->hasPages())
        <div class="mt-4">{{ $rows->links() }}</div>
    @endif
</x-admin.layouts.admin>