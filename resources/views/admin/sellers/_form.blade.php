@php
    $seller = $seller ?? new \App\Models\PrivateSeller();
    $value = fn ($field, $default = '') => old($field, $seller->$field ?? $default);
    $memberUsers = \App\Models\User::orderBy('name')->get();
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="card p-5">
            <h2 class="section-title !text-xl">Seller Information</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @if (empty($seller->id))
                    <div>
                        <label class="label">User Account *</label>
                        <select name="user_id" required class="input">
                            <option value="">Select user</option>
                            @foreach ($memberUsers as $member)
                                <option value="{{ $member->id }}" @selected((string) $value('user_id') === (string) $member->id)>{{ $member->fullName() }} — {{ $member->email }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="label">Contact Email</label>
                    <input type="email" name="contact_email" class="input" value="{{ $value('contact_email') }}" placeholder="Defaults to the user account email">
                </div>
                <div>
                    <label class="label">Phone</label>
                    <input type="text" name="phone" class="input" value="{{ $value('phone') }}">
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
                    <label class="label">Street Address</label>
                    <input type="text" name="street_address" class="input" value="{{ $value('street_address') }}">
                </div>
                <div>
                    <label class="label">Postal Code</label>
                    <input type="text" name="postal_code" maxlength="20" class="input" value="{{ $value('postal_code') }}">
                </div>
                <div>
                    <label class="label">Status *</label>
                    <select name="status" required class="input">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($value('status', \App\Models\PrivateSeller::STATUS_PENDING) === $key)>{{ $label }}</option>
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
                <a href="{{ isset($seller->id) ? route('admin.sellers.show', $seller) : route('admin.sellers.index') }}" class="btn-ghost btn-sm">Cancel</a>
                <button type="submit" class="btn-gold btn-sm">{{ isset($seller->id) ? 'Save changes' : 'Create seller' }}</button>
            </div>
        </div>
    </div>
</div>