<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Partnerships</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">Partner With WorxCars</h1>
            <p class="mt-2 max-w-2xl text-sm text-ivory-100/60">List your stock to thousands of serious buyers. Verified dealerships build trust, close sales, and participate in the WorxCars buyer reward network.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-auth.errors :errors="$errors" />
        @if (session('status'))
            <div class="mt-4 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1fr_420px]">
            <form method="POST" action="{{ route('dealerships.join.store') }}" class="card p-6 sm:p-8">
                @csrf
                <h2 class="font-display text-xl font-bold text-royal-950">Dealership Application</h2>
                <p class="mt-1 text-sm text-royal-900/60">Share your dealership details and we'll be in touch to verify and onboard you.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label">Business name *</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Trading name</label>
                        <input type="text" name="trading_name" value="{{ old('trading_name') }}" class="input">
                    </div>
                    <div>
                        <label class="label">Registration number</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Contact person *</label>
                        <input type="text" name="contact_person_name" value="{{ old('contact_person_name') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Website</label>
                        <input type="url" name="website" value="{{ old('website') }}" class="input" placeholder="https://">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Street address</label>
                        <input type="text" name="street_address" value="{{ old('street_address') }}" class="input">
                    </div>
                    <div>
                        <label class="label">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="input">
                    </div>
                    <div>
                        <label class="label">Province</label>
                        <input type="text" name="province" value="{{ old('province') }}" class="input">
                    </div>
                    <div>
                        <label class="label">Postal code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="input">
                    </div>
                    <div>
                        <label class="label">VAT number</label>
                        <input type="text" name="vat_number" value="{{ old('vat_number') }}" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">About your dealership</label>
                        <textarea name="description" rows="4" class="input">{{ old('description') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-gold mt-6">Submit application</button>
            </form>

            <aside class="space-y-5">
                <div class="card-dark p-6">
                    <h3 class="font-display text-lg font-bold text-ivory-50">Why partner with WorxCars?</h3>
                    <ul class="mt-4 space-y-3 text-sm text-ivory-100/70">
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> National exposure for your stock</li>
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Verified-partner trust badge drives enquiries</li>
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Buyer rewards motivate faster purchase decisions</li>
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Dealer dashboard for stock, enquiries and claims</li>
                    </ul>
                </div>
                <div class="card p-6 text-sm text-royal-900/70">
                    If you'd prefer to sell as a private seller, <a href="{{ route('sell-car') }}" class="font-medium text-gold-700 hover:underline">list your vehicle here</a> instead.
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>