@php
    $vehicle = $vehicle ?? new \App\Models\Vehicle();
    $rules = \App\Support\FinancialRules::instance();
    $value = fn ($field, $default = '') => old($field, $vehicle->$field ?? $default);
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="card p-5">
            <h2 class="section-title !text-xl">Listing Details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Make *</label>
                    <input type="text" name="make" required class="input" value="{{ $value('make') }}">
                </div>
                <div>
                    <label class="label">Model *</label>
                    <input type="text" name="model" required class="input" value="{{ $value('model') }}">
                </div>
                <div>
                    <label class="label">Variant</label>
                    <input type="text" name="variant" class="input" value="{{ $value('variant') }}">
                </div>
                <div>
                    <label class="label">Year *</label>
                    <input type="number" name="year" required min="1950" max="{{ date('Y') + 1 }}" class="input" value="{{ $value('year') }}">
                </div>
                <div>
                    <label class="label">Mileage (km) *</label>
                    <input type="number" name="mileage" required min="0" class="input" value="{{ $value('mileage') }}">
                </div>
                <div>
                    <label class="label">Price (ZAR) *</label>
                    <input type="number" name="price" required min="0" step="0.01" class="input" value="{{ $value('price') }}">
                </div>
                <div>
                    <label class="label">Currency</label>
                    <input type="text" name="currency" maxlength="3" class="input" value="{{ $value('currency', 'ZAR') }}">
                </div>
                <div>
                    <label class="label">Vehicle Reference</label>
                    <input type="text" name="vehicle_reference" maxlength="30" class="input" value="{{ $value('vehicle_reference') }}" placeholder="Auto-generated if left blank">
                </div>
                <div>
                    <label class="label">Condition</label>
                    <select name="condition" class="input">
                        <option value="">Select</option>
                        @foreach (['new' => 'New', 'demo' => 'Demo', 'used' => 'Used'] as $key => $label)
                            <option value="{{ $key }}" @selected($value('condition') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Registration Status</label>
                    <input type="text" name="registration_status" class="input" value="{{ $value('registration_status') }}">
                </div>
                <div>
                    <label class="label">Transmission</label>
                    <select name="transmission" class="input">
                        <option value="">Select</option>
                        @foreach (['Automatic', 'Manual', 'CVT', 'Tiptronic', 'Dual-Clutch'] as $key)
                            <option value="{{ $key }}" @selected($value('transmission') === $key)>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Fuel Type</label>
                    <select name="fuel_type" class="input">
                        <option value="">Select</option>
                        @foreach (['Petrol', 'Diesel', 'Hybrid', 'Plug-in Hybrid', 'Electric'] as $key)
                            <option value="{{ $key }}" @selected($value('fuel_type') === $key)>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Body Type</label>
                    <input type="text" name="body_type" class="input" value="{{ $value('body_type') }}">
                </div>
                <div>
                    <label class="label">Colour</label>
                    <input type="text" name="colour" class="input" value="{{ $value('colour') }}">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">Doors</label>
                        <input type="number" name="doors" min="1" max="9" class="input" value="{{ $value('doors') }}">
                    </div>
                    <div>
                        <label class="label">Seats</label>
                        <input type="number" name="seats" min="1" max="50" class="input" value="{{ $value('seats') }}">
                    </div>
                    <div>
                        <label class="label">Engine CC</label>
                        <input type="number" name="engine_cc" class="input" value="{{ $value('engine_cc') }}">
                    </div>
                </div>
                <div>
                    <label class="label">Engine Size</label>
                    <input type="text" name="engine_size" class="input" value="{{ $value('engine_size') }}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Power (kW)</label>
                        <input type="number" name="power_kw" class="input" value="{{ $value('power_kw') }}">
                    </div>
                    <div>
                        <label class="label">Torque (Nm)</label>
                        <input type="number" name="torque_nm" class="input" value="{{ $value('torque_nm') }}">
                    </div>
                </div>
                <div>
                    <label class="label">VIN</label>
                    <input type="text" name="vin" maxlength="60" class="input" value="{{ $value('vin') }}">
                </div>
                <div>
                    <label class="label">Registration Number</label>
                    <input type="text" name="reg_number" maxlength="60" class="input" value="{{ $value('reg_number') }}">
                </div>
                <div>
                    <label class="label">Province</label>
                    <select name="location_province" class="input">
                        <option value="">Select</option>
                        @foreach (['Eastern Cape', 'Free State', 'Gauteng', 'KwaZulu-Natal', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape', 'Western Cape'] as $key)
                            <option value="{{ $key }}" @selected($value('location_province') === $key)>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">City</label>
                    <input type="text" name="location_city" class="input" value="{{ $value('location_city') }}">
                </div>
                <div>
                    <label class="label">Suburb</label>
                    <input type="text" name="location_suburb" class="input" value="{{ $value('location_suburb') }}">
                </div>
            </div>
            <div class="mt-4">
                <label class="label">Vehicle Condition Detail</label>
                <textarea name="vehicle_condition_detail" rows="3" class="input">{{ $value('vehicle_condition_detail') }}</textarea>
            </div>
            <div class="mt-4">
                <label class="label">Description</label>
                <textarea name="description" rows="4" class="input">{{ $value('description') }}</textarea>
            </div>
            <div class="mt-4">
                <label class="label">Features (one per line or comma-separated)</label>
                <textarea name="features" rows="4" class="input">{{ is_array($vehicle->features) ? implode("\n", $vehicle->features) : '' }}</textarea>
            </div>
        </div>

        <div class="card p-5">
            <h2 class="section-title !text-xl">Seller & Status</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Seller Type *</label>
                    <select name="seller_type" id="seller_type" required class="input">
                        <option value="dealership" @selected($value('seller_type', 'dealership') === 'dealership')>Dealership</option>
                        <option value="private" @selected($value('seller_type') === 'private')>Private seller</option>
                    </select>
                </div>
                <div>
                    <label class="label">Status *</label>
                    <select name="status" required class="input">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($value('status', \App\Models\Vehicle::STATUS_PENDING_APPROVAL) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="dealership_field">
                    <label class="label">Dealership</label>
                    <select name="dealership_id" class="input">
                        <option value="">None</option>
                        @foreach ($dealerships as $dealership)
                            <option value="{{ $dealership->id }}" @selected((string) $value('dealership_id') === (string) $dealership->id)>{{ $dealership->business_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="seller_field">
                    <label class="label">Private Seller</label>
                    <select name="private_seller_id" class="input">
                        <option value="">None</option>
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->id }}" @selected((string) $value('private_seller_id') === (string) $seller->id)>{{ $seller->user?->fullName() ?? $seller->contact_email }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_featured" value="1" @checked($value('is_featured')) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                    Featured listing
                </label>
            </div>
        </div>

        <div class="card p-5">
            <h2 class="section-title !text-xl">Commission & Reward</h2>
            <p class="mt-1 text-sm text-royal-900/55">Configure how WorxCars earns commission and the buyer reward attached to this vehicle. Leave blank to apply platform defaults on approval.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Commission Type</label>
                    <select name="commission_type" class="input">
                        <option value="">Platform default</option>
                        <option value="fixed" @selected($value('commission_type') === 'fixed')>Fixed (R)</option>
                        <option value="percent" @selected($value('commission_type') === 'percent')>Percent (%)</option>
                    </select>
                </div>
                <div>
                    <label class="label">Commission Value</label>
                    <input type="number" name="commission_value" min="0" step="0.01" class="input" value="{{ $value('commission_value', $rules->defaultCommissionPercent()) }}" placeholder="e.g. 2.5">
                </div>
                <div>
                    <label class="label">Reward Type</label>
                    <select name="reward_type" class="input">
                        <option value="">Platform default</option>
                        <option value="fixed" @selected($value('reward_type') === 'fixed')>Fixed (R)</option>
                        <option value="percent" @selected($value('reward_type') === 'percent')>Percent of price (%)</option>
                        <option value="commission_based" @selected($value('reward_type') === 'commission_based')>Percent of commission</option>
                    </select>
                </div>
                <div>
                    <label class="label">Reward Value</label>
                    <input type="number" name="reward_value" min="0" step="0.01" class="input" value="{{ $value('reward_value') }}">
                </div>
                <div>
                    <label class="label">Reward Percent (%)</label>
                    <input type="number" name="reward_percent" min="0" max="100" step="0.01" class="input" value="{{ $value('reward_percent', $rules->rewardPercentOfCommission()) }}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Reward Min (R)</label>
                        <input type="number" name="reward_min" min="0" step="0.01" class="input" value="{{ $value('reward_min', $rules->minReward()) }}">
                    </div>
                    <div>
                        <label class="label">Reward Max (R)</label>
                        <input type="number" name="reward_max" min="0" step="0.01" class="input" value="{{ $value('reward_max', $rules->maxReward()) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-5">
            <h2 class="section-title !text-xl">Images</h2>
            <div class="mt-4">
                <label class="label">Upload Images (jpeg, png, webp)</label>
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="input">
                <p class="mt-2 text-xs text-royal-900/55">The first image uploaded becomes the main image.</p>
            </div>
            @if (isset($vehicle->id) && $vehicle->images()->exists())
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach ($vehicle->images as $image)
                        <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="{{ $image->alt_text }}" class="aspect-[4/3] w-full rounded object-cover">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card p-5">
            <h2 class="section-title !text-xl">Campaign</h2>
            <div class="mt-4">
                <label class="label">Attach to Campaign</label>
                <select name="campaign_id" class="input">
                    <option value="">No campaign (platform default)</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}" @selected((string) $value('campaign_id') === (string) $campaign->id)>{{ $campaign->name }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-royal-900/55">Attaching a campaign uses the campaign commission/reward rules for this vehicle.</p>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ isset($vehicle->id) ? route('admin.vehicles.show', $vehicle) : route('admin.vehicles.index') }}" class="btn-ghost btn-sm">Cancel</a>
                <button type="submit" class="btn-gold btn-sm">{{ isset($vehicle->id) ? 'Save changes' : 'Create vehicle' }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const type = document.querySelector('#seller_type');
        const toggle = () => {
            document.querySelector('#dealership_field').classList.toggle('hidden', type.value !== 'dealership');
            document.querySelector('#seller_field').classList.toggle('hidden', type.value !== 'private');
        };
        type.addEventListener('change', toggle);
        toggle();
    });
</script>
@endpush