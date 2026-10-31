<x-admin.layouts.admin title="{{ $vehicle->displayName() }}" :breadcrumbs="[['label' => 'Vehicles', 'url' => route('admin.vehicles.index')], ['label' => $vehicle->displayName()]]">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            @if ($image = $vehicle->mainImage())
                <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="{{ $vehicle->displayName() }}" class="h-16 w-24 rounded-lg object-cover">
            @endif
            <div>
                <h1 class="font-display text-3xl font-black text-royal-950">{{ $vehicle->displayName() }}</h1>
                <p class="mt-1 text-sm text-royal-900/60">{{ $vehicle->vehicle_reference }} · {{ $vehicle->year }} · {{ number_format($vehicle->mileage) }} km</p>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <x-admin.status-badge :status="$vehicle->status" :label="$vehicle->statusLabel()" />
                    @if ($vehicle->is_featured)
                        <span class="badge-gold">★ Featured</span>
                    @endif
                    <span class="badge-grey">{{ ucfirst($vehicle->seller_type) }} seller</span>
                    @if ($vehicle->campaign)
                        <a href="{{ route('admin.campaigns.show', $vehicle->campaign) }}" class="badge-blue">{{ $vehicle->campaign->name }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($vehicle->status !== App\Models\Vehicle::STATUS_PUBLISHED)
                <form method="POST" action="{{ route('admin.vehicles.approve', $vehicle) }}">
                    @csrf
                    <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Approve and publish this vehicle with default commission/reward configuration?')">✔ Approve & Publish</button>
                </form>
            @endif
            @if ($vehicle->status !== App\Models\Vehicle::STATUS_REJECTED)
                <form method="POST" action="{{ route('admin.vehicles.reject', $vehicle) }}" x-data="{ reason: '' }">
                    @csrf
                    <input type="hidden" name="rejection_reason" :value="reason">
                    <button type="submit" class="btn-ghost btn-sm" @click.prevent="reason = prompt('Reason for rejection:'); if (reason) { $el.closest('form').submit(); }">✕ Reject</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.vehicles.feature', $vehicle) }}">
                @csrf
                <button type="submit" class="btn-ghost btn-sm">{{ $vehicle->is_featured ? 'Unfeature' : 'Feature' }}</button>
            </form>
            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn-ghost btn-sm">Edit</a>
            <a href="{{ route('vehicles.show', $vehicle) }}" target="_blank" class="btn-ghost btn-sm">View site</a>
            <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle listing?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300">Delete</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.vehicles.status', $vehicle) }}" class="mt-4 flex items-center gap-3">
        @csrf
        <label class="label mb-0 !mt-0">Quick status:</label>
        <select name="status" class="input !w-56 !py-2">
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected($vehicle->status === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-ghost btn-sm">Update status</button>
    </form>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                @if ($vehicle->activeImages->isNotEmpty())
                    <div class="grid grid-cols-2 gap-2 p-3 sm:grid-cols-3">
                        @foreach ($vehicle->activeImages as $image)
                            <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $image->alt_text }}" class="aspect-[4/3] w-full rounded-md object-cover">
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-10 text-center text-sm text-royal-900/50">No images uploaded.</p>
                @endif
            </div>

            <div class="card p-5">
                <h2 class="section-title !text-xl">Specifications</h2>
                <dl class="mt-4 grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Price</dt><dd class="font-semibold">{{ money($vehicle->price) }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Transmission</dt><dd>{{ $vehicle->transmission ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Fuel</dt><dd>{{ $vehicle->fuel_type ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Body type</dt><dd>{{ $vehicle->body_type ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Colour</dt><dd>{{ $vehicle->colour ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Doors / Seats</dt><dd>{{ $vehicle->doors ?? '—' }} / {{ $vehicle->seats ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Engine</dt><dd>{{ $vehicle->engine_size ?? ($vehicle->engine_cc ? $vehicle->engine_cc.' cc' : '—') }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Power / Torque</dt><dd>{{ $vehicle->power_kw ?? '—' }} kW / {{ $vehicle->torque_nm ?? '—' }} Nm</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Condition</dt><dd>{{ ucfirst($vehicle->condition ?? '—') }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-royal-900/5 pb-2"><dt class="text-royal-900/60">Location</dt><dd>{{ collect([$vehicle->location_city, $vehicle->location_province])->filter()->join(', ') ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-4 pb-2"><dt class="text-royal-900/60">Published</dt><dd>{{ $vehicle->published_at?->format('d M Y H:i') ?? 'Not published' }}</dd></div>
                    <div class="flex justify-between gap-4 pb-2"><dt class="text-royal-900/60">Expires</dt><dd>{{ $vehicle->expires_at?->format('d M Y') ?? '—' }}</dd></div>
                </dl>
                @if ($vehicle->specifications->isNotEmpty())
                    <dl class="mt-4 grid gap-x-6 gap-y-2 border-t border-royal-900/10 pt-4 text-sm sm:grid-cols-2">
                        @foreach ($vehicle->specifications as $spec)
                            <div class="flex justify-between gap-4"><dt class="text-royal-900/60">{{ $spec->label }}</dt><dd class="text-right">{{ $spec->value }}</dd></div>
                        @endforeach
                    </dl>
                @endif
                @if ($vehicle->description)
                    <div class="mt-4 border-t border-royal-900/10 pt-4">
                        <h3 class="label !mb-2">Description</h3>
                        <p class="whitespace-pre-line text-sm text-royal-900/80">{{ $vehicle->description }}</p>
                    </div>
                @endif
                @if ($vehicle->features)
                    <div class="mt-4 border-t border-royal-900/10 pt-4">
                        <h3 class="label !mb-2">Features</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($vehicle->features as $feature)
                                <span class="badge-grey">{{ $feature }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="card p-5">
                <h2 class="section-title !text-xl">Campaign & Commission Configuration</h2>
                <form method="POST" action="{{ route('admin.vehicles.set-campaign', $vehicle) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div>
                        <label class="label">Campaign</label>
                        <select name="campaign_id" class="input">
                            <option value="">No campaign</option>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" @selected($vehicle->campaign_id === $campaign->id)>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Commission Type</label>
                        <select name="commission_type" class="input">
                            <option value="percent" @selected(($breakdown['config']['commission_type'] ?? 'percent') === 'percent')>Percent (%)</option>
                            <option value="fixed" @selected(($breakdown['config']['commission_type'] ?? '') === 'fixed')>Fixed (R)</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Commission Value</label>
                        <input type="number" name="commission_value" min="0" step="0.01" class="input" value="{{ $breakdown['config']['commission_value'] ?? App\Support\FinancialRules::instance()->defaultCommissionPercent() }}">
                    </div>
                    <div>
                        <label class="label">Reward Type</label>
                        <select name="reward_type" class="input">
                            <option value="commission_based" @selected(($breakdown['config']['reward_type'] ?? '') === 'commission_based')>Percent of commission</option>
                            <option value="fixed" @selected(($breakdown['config']['reward_type'] ?? '') === 'fixed')>Fixed (R)</option>
                            <option value="percent" @selected(($breakdown['config']['reward_type'] ?? '') === 'percent')>Percent of price</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Reward Value</label>
                        <input type="number" name="reward_value" min="0" step="0.01" class="input" value="{{ $breakdown['config']['reward_value'] ?? 0 }}">
                    </div>
                    <div>
                        <label class="label">Reward Percent (%)</label>
                        <input type="number" name="reward_percent" min="0" max="100" step="0.01" class="input" value="{{ $breakdown['config']['reward_percent'] ?? App\Support\FinancialRules::instance()->rewardPercentOfCommission() }}">
                    </div>
                    <div>
                        <label class="label">Reward Min (R)</label>
                        <input type="number" name="reward_min" min="0" step="0.01" class="input" value="{{ $breakdown['config']['reward_min'] ?? App\Support\FinancialRules::instance()->minReward() }}">
                    </div>
                    <div>
                        <label class="label">Reward Max (R)</label>
                        <input type="number" name="reward_max" min="0" step="0.01" class="input" value="{{ $breakdown['config']['reward_max'] ?? App\Support\FinancialRules::instance()->maxReward() }}">
                    </div>
                    <div class="flex items-center gap-4 sm:col-span-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_inactive" value="1" class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                            Mark campaign rule inactive (where applicable)
                        </label>
                        <button type="submit" class="btn-gold btn-sm ml-auto">Save configuration</button>
                    </div>
                </form>
            </div>

            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-royal-900/10 px-5 py-4">
                    <h2 class="section-title !text-xl">Purchase Claims</h2>
                </div>
                @if ($claims->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-royal-900/50">No claims made against this vehicle.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr><th>Reference</th><th>Buyer</th><th>Purchase price</th><th>Reward</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($claims as $claim)
                                    <tr class="hover:bg-ivory-100/60">
                                        <td class="font-semibold"><a href="{{ route('admin.claims.show', $claim) }}" class="hover:text-gold-700">{{ $claim->claim_reference }}</a></td>
                                        <td>{{ $claim->user?->fullName() ?? '—' }}</td>
                                        <td>{{ money($claim->purchase_price) }}</td>
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
            <div class="card-dark p-5">
                <h3 class="font-display text-lg font-bold text-gold-300">Financial Breakdown</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-ivory-100/60">Sale price</span>
                        <span class="font-semibold text-ivory-50">{{ money($vehicle->price) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ivory-100/60">Commission</span>
                        <span class="font-semibold text-ivory-50">{{ money($breakdown['commission_amount']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ivory-100/60">Buyer reward</span>
                        <span class="font-semibold text-gold-300">{{ money($breakdown['reward_amount']) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-white/10 pt-3">
                        <span class="text-ivory-100/80">WorxCars retains</span>
                        <span class="font-black text-ivory-50">{{ money($breakdown['retained_amount']) }}</span>
                    </div>
                    <p class="pt-2 text-xs text-ivory-100/40">Source: {{ $breakdown['source'] }}</p>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Seller</h3>
                <div class="mt-4 space-y-2 text-sm">
                    <p class="font-semibold text-royal-950">{{ $vehicle->sellerName() ?? '—' }}</p>
                    @if ($vehicle->seller_type === 'dealership' && $vehicle->dealership)
                        <p class="text-royal-900/60">{{ $vehicle->dealership->email ?? '' }} · {{ $vehicle->dealership->phone ?? '' }}</p>
                        <p class="text-royal-900/60">{{ collect([$vehicle->dealership->city, $vehicle->dealership->province])->filter()->join(', ') }}</p>
                        <a href="{{ route('admin.dealerships.show', $vehicle->dealership) }}" class="mt-2 inline-block btn-ghost btn-sm">View dealership</a>
                    @elseif ($vehicle->seller_type === 'private' && $vehicle->privateSeller)
                        <p class="text-royal-900/60">{{ $vehicle->privateSeller->contact_email ?? '' }} · {{ $vehicle->privateSeller->phone ?? '' }}</p>
                        <a href="{{ route('admin.sellers.show', $vehicle->privateSeller) }}" class="mt-2 inline-block btn-ghost btn-sm">View seller</a>
                    @endif
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="px-5 py-4">
                    <h3 class="section-title !text-xl">Commission History</h3>
                </div>
                @if ($commissions->isEmpty())
                    <p class="px-5 pb-6 text-sm text-royal-900/50">No commission configuration recorded yet. Approve or save configuration to create one.</p>
                @else
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($commissions as $commission)
                            <div class="px-5 py-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold">{{ $commission->campaign?->name ?? 'Base' }}</span>
                                    <span class="text-xs text-royal-900/50">{{ $commission->created_at?->format('d M Y') }}</span>
                                </div>
                                <p class="mt-1 text-xs text-royal-900/60">
                                    {{ ucfirst($commission->commission_type) }} @ {{ $commission->commission_type === 'percent' ? $commission->commission_value.'%' : money($commission->commission_value) }}
                                    · Reward: {{ money($commission->reward_amount) }}
                                    @if ($commission->recordedBy)
                                        · by {{ $commission->recordedBy->fullName() }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.layouts.admin>