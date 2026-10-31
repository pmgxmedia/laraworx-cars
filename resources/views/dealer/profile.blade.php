@extends('dealer.layouts.dealer')

@section('title', 'Profile')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Your account</p>
        <h2 class="mt-1 section-title">Profile</h2>
    </div>
@endsection

@section('content')
    @if (! $seller)
        <div class="card p-8 text-center">
            <p class="font-display text-lg font-bold text-royal-950">No seller profile linked to your account</p>
            <p class="mt-2 text-sm text-royal-900/60">Please contact WorxCars support so we can link your dealership or verify your private seller profile.</p>
        </div>
    @else
        <div class="card mb-6 flex flex-wrap items-center justify-between gap-4 p-5">
            @if ($seller instanceof \App\Models\Dealership)
                <div class="flex items-center gap-4">
                    <div class="grid h-12 w-12 place-items-center rounded-md bg-royal-950 font-display text-sm font-black text-gold-400">
                        {{ collect(explode(' ', $seller->business_name))->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
                    </div>
                    <div>
                        <p class="font-semibold text-royal-950">{{ $seller->business_name }}</p>
                        <p class="text-xs text-royal-900/55">Dealership profile · {{ $seller->statusLabel() }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-4">
                    <div class="grid h-12 w-12 place-items-center rounded-full bg-royal-950 font-display text-sm font-black text-gold-400">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                    <div>
                        <p class="font-semibold text-royal-950">{{ $user->fullName() }}</p>
                        <p class="text-xs text-royal-900/55">Private seller profile · {{ $seller->statusLabel() }}</p>
                    </div>
                </div>
            @endif
            @include('dealer.components.status-badge', ['type' => $seller instanceof \App\Models\Dealership ? 'dealership' : 'seller', 'status' => $seller->status, 'label' => $seller->statusLabel()])
        </div>

        @if ($seller instanceof \App\Models\Dealership)
            <form method="POST" action="{{ route('dealer.profile.update') }}" class="card grid gap-6 p-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Business name</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $seller->business_name) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Trading name</label>
                        <input type="text" name="trading_name" value="{{ old('trading_name', $seller->trading_name) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Contact person</label>
                        <input type="text" name="contact_person_name" value="{{ old('contact_person_name', $seller->contact_person_name) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $seller->email) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $seller->phone) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Website</label>
                        <input type="url" name="website" value="{{ old('website', $seller->website) }}" class="input" placeholder="https://example.co.za">
                    </div>
                </div>

                <div>
                    <label class="label">Description</label>
                    <textarea name="description" rows="4" class="input">{{ old('description', $seller->description) }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label">Street address</label>
                        <input type="text" name="street_address" value="{{ old('street_address', $seller->street_address) }}" class="input">
                    </div>
                    <div>
                        <label class="label">City</label>
                        <input type="text" name="city" value="{{ old('city', $seller->city) }}" class="input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Province</label>
                            <select name="province" class="input">
                                <option value="">Select province</option>
                                @foreach (['Eastern Cape', 'Free State', 'Gauteng', 'KwaZulu-Natal', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape', 'Western Cape'] as $province)
                                    <option value="{{ $province }}" @selected(old('province', $seller->province) === $province)>{{ $province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Postal code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $seller->postal_code) }}" class="input">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-royal-900/10 pt-5">
                    <button type="submit" class="btn-gold">Save dealership profile</button>
                </div>
            </form>
        @else
            <form method="POST" action="{{ route('dealer.profile.update') }}" class="card grid gap-6 p-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Surname</label>
                        <input type="text" name="surname" value="{{ old('surname', $user->surname) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Contact email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $seller->contact_email) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $seller->phone ?? $user->phone) }}" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Street address</label>
                        <input type="text" name="street_address" value="{{ old('street_address', $seller->street_address) }}" class="input">
                    </div>
                    <div>
                        <label class="label">City</label>
                        <input type="text" name="city" value="{{ old('city', $seller->city) }}" class="input">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Province</label>
                            <select name="province" class="input">
                                <option value="">Select province</option>
                                @foreach (['Eastern Cape', 'Free State', 'Gauteng', 'KwaZulu-Natal', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape', 'Western Cape'] as $province)
                                    <option value="{{ $province }}" @selected(old('province', $seller->province) === $province)>{{ $province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Postal code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $seller->postal_code) }}" class="input">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-royal-900/10 pt-5">
                    <button type="submit" class="btn-gold">Save profile</button>
                </div>
            </form>
        @endif
    @endif
@endsection