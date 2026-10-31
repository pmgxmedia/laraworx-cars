@php
    $dealership = $dealership ?? new \App\Models\Dealership();
    $value = fn ($field, $default = '') => old($field, $dealership->$field ?? $default);
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="card p-5">
            <h2 class="section-title !text-xl">Business Information</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Business Name *</label>
                    <input type="text" name="business_name" required class="input" value="{{ $value('business_name') }}">
                </div>
                <div>
                    <label class="label">Trading Name</label>
                    <input type="text" name="trading_name" class="input" value="{{ $value('trading_name') }}">
                </div>
                <div>
                    <label class="label">Registration Number</label>
                    <input type="text" name="registration_number" class="input" value="{{ $value('registration_number') }}">
                </div>
                <div>
                    <label class="label">VAT Number</label>
                    <input type="text" name="vat_number" class="input" value="{{ $value('vat_number') }}">
                </div>
                <div>
                    <label class="label">Contact Person</label>
                    <input type="text" name="contact_person_name" class="input" value="{{ $value('contact_person_name') }}">
                </div>
                <div>
                    <label class="label">Email *</label>
                    <input type="email" name="email" required class="input" value="{{ $value('email') }}">
                </div>
                <div>
                    <label class="label">Phone</label>
                    <input type="text" name="phone" class="input" value="{{ $value('phone') }}">
                </div>
                <div>
                    <label class="label">Website</label>
                    <input type="url" name="website" class="input" value="{{ $value('website') }}">
                </div>
            </div>
            <div class="mt-4">
                <label class="label">Description</label>
                <textarea name="description" rows="4" class="input">{{ $value('description') }}</textarea>
            </div>
        </div>

        <div class="card p-5">
            <h2 class="section-title !text-xl">Location & Status</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="label">Street Address</label>
                    <input type="text" name="street_address" class="input" value="{{ $value('street_address') }}">
                </div>
                <div>
                    <label class="label">City</label>
                    <input type="text" name="city" class="input" value="{{ $value('city') }}">
                </div>
                <div>
                    <label class="label">Province</label>
                    <select name="province" class="input">
                        <option value="">Select</option>
                        @foreach (['Eastern Cape', 'Free State', 'Gauteng', 'KwaZulu-Natal', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape', 'Western Cape'] as $key)
                            <option value="{{ $key }}" @selected($value('province') === $key)>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Postal Code</label>
                    <input type="text" name="postal_code" maxlength="20" class="input" value="{{ $value('postal_code') }}">
                </div>
                <div>
                    <label class="label">Status *</label>
                    <select name="status" required class="input">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($value('status', \App\Models\Dealership::STATUS_PENDING) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked($value('is_active', true)) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                    Active account
                </label>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-5">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ isset($dealership->id) ? route('admin.dealerships.show', $dealership) : route('admin.dealerships.index') }}" class="btn-ghost btn-sm">Cancel</a>
                <button type="submit" class="btn-gold btn-sm">{{ isset($dealership->id) ? 'Save changes' : 'Create dealership' }}</button>
            </div>
        </div>
    </div>
</div>