<x-admin.layouts.admin title="{{ $campaign->name }}" :breadcrumbs="[['label' => 'Campaigns', 'url' => route('admin.campaigns.index')], ['label' => $campaign->name]]">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">{{ $campaign->name }}</h1>
            <p class="mt-1 text-sm text-royal-900/60">
                {{ $campaign->start_date?->format('d M Y') ?? 'Open-ended' }}{{ $campaign->end_date ? ' → '.$campaign->end_date->format('d M Y') : '' }}
                · by {{ $campaign->createdBy?->fullName() ?? '—' }}
            </p>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-admin.status-badge :status="$campaign->status" :label="$campaign->statusLabel()" />
                @if ($campaign->is_featured)
                    <span class="badge-gold">★ Featured</span>
                @endif
                @if ($campaign->isActive())
                    <span class="badge-green">Live now</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('admin.campaigns.status', $campaign) }}" class="flex items-center gap-2">
                @csrf
                <select name="status" class="input !w-44 !py-2">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($campaign->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-ghost btn-sm">Set</button>
            </form>
            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn-ghost btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300">Delete</button>
            </form>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Description</h2>
                @if ($campaign->description)
                    <p class="mt-3 whitespace-pre-line text-sm text-royal-900/75">{{ $campaign->description }}</p>
                @else
                    <p class="mt-3 text-sm text-royal-900/50">No description provided.</p>
                @endif
                @if ($campaign->terms)
                    <h3 class="label mt-4">Terms & conditions</h3>
                    <p class="whitespace-pre-line text-sm text-royal-900/70">{{ $campaign->terms }}</p>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Participating Vehicles ({{ $campaign->campaignVehicles->count() }})</h2>
                </div>
                @if ($campaign->campaignVehicles->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No vehicles attached to this campaign yet.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($campaign->campaignVehicles as $entry)
                            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.vehicles.show', $entry->vehicle) }}" class="font-semibold text-royal-950 hover:text-gold-700">{{ $entry->vehicle->displayName() }}</a>
                                    <p class="text-xs text-royal-900/55">{{ $entry->vehicle->vehicle_reference }} · {{ money($entry->vehicle->price) }}</p>
                                    <p class="mt-1 text-xs text-royal-900/60">
                                        Commission: {{ $entry->commission_type === 'percent' ? $entry->commission_value.'%' : money($entry->commission_value) }}
                                        @if ($entry->reward_type === 'commission_based')
                                            · Reward: {{ $entry->reward_percent }}% of commission
                                        @elseif ($entry->reward_type === 'fixed')
                                            · Reward: {{ money($entry->reward_value) }}
                                        @elseif ($entry->reward_type === 'percent')
                                            · Reward: {{ $entry->reward_value }}% of price
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if (!$entry->is_active)
                                        <span class="badge-grey">Inactive</span>
                                    @endif
                                    <x-admin.status-badge :status="$entry->vehicle->status" :label="$entry->vehicle->statusLabel()" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Attach Vehicle</h2>
                @if ($availableVehicles->isEmpty())
                    <p class="mt-3 rounded-md bg-ivory-100 px-3 py-2 text-sm text-royal-900/50">No published vehicles left to attach to this campaign.</p>
                @else
                    <form method="POST" action="{{ route('admin.campaigns.add-vehicle', $campaign) }}" class="mt-4 grid gap-4">
                        @csrf
                        <div>
                            <label class="label">Vehicle *</label>
                            <select name="vehicle_id" required class="input">
                                <option value="">Select published vehicle</option>
                                @foreach ($availableVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->displayName() }} — {{ money($vehicle->price) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Commission Type</label>
                                <select name="commission_type" class="input">
                                    <option value="percent">Percent (%)</option>
                                    <option value="fixed">Fixed (R)</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Commission Value</label>
                                <input type="number" name="commission_value" min="0" step="0.01" class="input" value="{{ App\Support\FinancialRules::instance()->defaultCommissionPercent() }}">
                            </div>
                        </div>
                        <div>
                            <label class="label">Reward Type</label>
                            <select name="reward_type" class="input">
                                <option value="commission_based">Percent of commission</option>
                                <option value="fixed">Fixed (R)</option>
                                <option value="percent">Percent of price (%)</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Reward Value</label>
                                <input type="number" name="reward_value" min="0" step="0.01" class="input" value="0">
                            </div>
                            <div>
                                <label class="label">Reward Percent (%)</label>
                                <input type="number" name="reward_percent" min="0" max="100" step="0.01" class="input" value="{{ App\Support\FinancialRules::instance()->rewardPercentOfCommission() }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Reward Min (R)</label>
                                <input type="number" name="reward_min" min="0" step="0.01" class="input" value="{{ App\Support\FinancialRules::instance()->minReward() }}">
                            </div>
                            <div>
                                <label class="label">Reward Max (R)</label>
                                <input type="number" name="reward_max" min="0" step="0.01" class="input" value="{{ App\Support\FinancialRules::instance()->maxReward() }}">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_inactive" value="1" class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                            Rule inactive (exclude from payout)
                        </label>
                        <button type="submit" class="btn-gold btn-sm">Attach vehicle</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-admin.layouts.admin>