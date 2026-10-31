@php
    $campaign = $campaign ?? new \App\Models\VehicleCampaign();
    $value = fn ($field, $default = '') => old($field, $campaign->$field ?? $default);
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="card p-5">
            <h2 class="section-title !text-xl">Campaign Details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="label">Name *</label>
                    <input type="text" name="name" required class="input" value="{{ $value('name') }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Description</label>
                    <textarea name="description" rows="4" class="input">{{ $value('description') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Terms & Conditions</label>
                    <textarea name="terms" rows="4" class="input">{{ $value('terms') }}</textarea>
                </div>
                <div>
                    <label class="label">Start Date</label>
                    <input type="datetime-local" name="start_date" class="input" value="{{ $value('start_date') ? \Illuminate\Support\Carbon::parse($value('start_date'))->format('Y-m-d\TH:i') : '' }}">
                </div>
                <div>
                    <label class="label">End Date</label>
                    <input type="datetime-local" name="end_date" class="input" value="{{ $value('end_date') ? \Illuminate\Support\Carbon::parse($value('end_date'))->format('Y-m-d\TH:i') : '' }}">
                </div>
                <div>
                    <label class="label">Status *</label>
                    <select name="status" required class="input">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($value('status', \App\Models\VehicleCampaign::STATUS_DRAFT) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_featured" value="1" @checked($value('is_featured')) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                    Featured campaign
                </label>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-5">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ isset($campaign->id) ? route('admin.campaigns.show', $campaign) : route('admin.campaigns.index') }}" class="btn-ghost btn-sm">Cancel</a>
                <button type="submit" class="btn-gold btn-sm">{{ isset($campaign->id) ? 'Save changes' : 'Create campaign' }}</button>
            </div>
        </div>
    </div>
</div>