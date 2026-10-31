<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">For sellers</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">Sell Your Car</h1>
            <p class="mt-2 max-w-2xl text-sm text-ivory-100/60">Reach thousands of serious buyers. Create an account, list your vehicle, and manage it from your dashboard once approved.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-auth.errors :errors="$errors" />
        @if (session('status'))
            <div class="mt-4 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1fr_340px]">
            <form method="POST" action="{{ route('sell-car.store') }}" enctype="multipart/form-data" class="card p-6 sm:p-8">
                @csrf
                <div x-data="{ status: '{{ old('seller_type', '') }}' }" class="mb-6">
                    <label class="label">Who are you selling as?</label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @if (auth()->check() && auth()->user()->isDealership())
                            <label class="input flex items-center gap-2 !rounded-md !py-3 cursor-pointer">
                                <input type="radio" name="seller_type" value="dealership" x-model="status" checked class="text-gold-600 focus:ring-gold-500">
                                <span class="text-sm font-semibold text-royal-950">My dealership</span>
                            </label>
                        @else
                            <label class="input flex items-center gap-2 !rounded-md !py-3 cursor-pointer" :class="status === 'private' && '!ring-gold-500'">
                                <input type="radio" name="seller_type" value="private" x-model="status" @checked(!auth()->user()?->isDealership()) class="text-gold-600 focus:ring-gold-500">
                                <span class="text-sm font-semibold text-royal-950">Private individual</span>
                            </label>
                        @endif
                        <label class="input flex items-center gap-2 !rounded-md !py-3 cursor-pointer" :class="status === 'dealership' && '!ring-gold-500'">
                            <input type="radio" name="seller_type" value="dealership" x-model="status" @checked(auth()->user()?->isDealership()) class="text-gold-600 focus:ring-gold-500">
                            <span class="text-sm font-semibold text-royal-950">Registered dealership</span>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-royal-900/50">Dealership listings require a verified dealership account. Private listings are reviewed before publishing.</p>
                </div>

                <h2 class="font-display text-lg font-bold text-royal-950">Vehicle details</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Make *</label>
                        <input type="text" name="make" value="{{ old('make') }}" required class="input" placeholder="Toyota">
                    </div>
                    <div>
                        <label class="label">Model *</label>
                        <input type="text" name="model" value="{{ old('model') }}" required class="input" placeholder="Corolla">
                    </div>
                    <div>
                        <label class="label">Variant</label>
                        <input type="text" name="variant" value="{{ old('variant') }}" class="input" placeholder="1.8 Hybrid">
                    </div>
                    <div>
                        <label class="label">Year *</label>
                        <input type="number" name="year" value="{{ old('year') }}" required min="1980" max="{{ now()->year + 1 }}" class="input">
                    </div>
                    <div>
                        <label class="label">Mileage (km) *</label>
                        <input type="number" name="mileage" value="{{ old('mileage') }}" required min="0" class="input">
                    </div>
                    <div>
                        <label class="label">Asking price (R) *</label>
                        <input type="number" name="price" value="{{ old('price') }}" required min="1000" class="input" step="1">
                    </div>
                    <div>
                        <label class="label">Transmission *</label>
                        <select name="transmission" required class="input">
                            <option value="">Select</option>
                            <option value="Automatic" @selected(old('transmission') === 'Automatic')>Automatic</option>
                            <option value="Manual" @selected(old('transmission') === 'Manual')>Manual</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Fuel *</label>
                        <select name="fuel_type" required class="input">
                            <option value="">Select</option>
                            @foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric'] as $fuel)
                                <option value="{{ $fuel }}" @selected(old('fuel_type') === $fuel)>{{ $fuel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Body type *</label>
                        <input type="text" name="body_type" value="{{ old('body_type') }}" required class="input" placeholder="Sedan">
                    </div>
                    <div>
                        <label class="label">Colour</label>
                        <input type="text" name="colour" value="{{ old('colour') }}" class="input" placeholder="White">
                    </div>
                </div>

                <h2 class="mt-8 font-display text-lg font-bold text-royal-950">Location</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Province *</label>
                        <select name="province" required class="input">
                            <option value="">Select</option>
                            @foreach (['Gauteng', 'Western Cape', 'KwaZulu-Natal', 'Eastern Cape', 'Free State', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape'] as $p)
                                <option value="{{ $p }}" @selected(old('province') === $p)>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">City *</label>
                        <input type="text" name="city" value="{{ old('city') }}" required class="input" placeholder="Johannesburg">
                    </div>
                </div>

                <h2 class="mt-8 font-display text-lg font-bold text-royal-950">Description</h2>
                <div class="mt-4">
                    <label class="label">About this vehicle *</label>
                    <textarea name="description" rows="6" required class="input" placeholder="Service history, condition, extras, reason for selling...">{{ old('description') }}</textarea>
                </div>

                <h2 class="mt-8 font-display text-lg font-bold text-royal-950">Photos</h2>
                <div class="mt-4">
                    <label class="label">Upload up to 12 photos *</label>
                    <div class="grid place-items-center rounded-lg border-2 border-dashed border-royal-900/15 bg-ivory-100/50 p-8 text-center">
                        <div>
                            <svg class="mx-auto h-10 w-10 text-royal-900/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            <input type="file" name="images[]" accept="image/*" multiple required class="mt-4 text-sm text-royal-900/60 file:mr-3 file:rounded-md file:border-0 file:bg-royal-950 file:px-4 file:py-2 file:text-xs file:font-bold file:text-gold-300 hover:file:bg-royal-900">
                            <p class="mt-2 text-xs text-royal-900/50">JPEG, PNG or WebP · up to 5 MB each</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-royal-900/10 pt-6">
                    @auth
                        <button type="submit" class="btn-gold">Submit for approval</button>
                    @else
                        <a href="{{ route('login') }}" class="btn-gold">Sign in to submit</a>
                        <a href="{{ route('register') }}" class="btn-ghost">Create an account</a>
                        <p class="text-xs text-royal-900/50">You need an account to submit a listing. Create one in under a minute.</p>
                    @endauth
                </div>
            </form>

            <aside class="space-y-5">
                <div class="card-dark p-6">
                    <h3 class="font-display text-lg font-bold text-ivory-50">Listing checklist</h3>
                    <ul class="mt-4 space-y-3 text-sm text-ivory-100/70">
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Accurate mileage and condition</li>
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Clear photos from multiple angles</li>
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Complete service history if available</li>
                        <li class="flex gap-3"><span class="text-gold-400">✓</span> Honest description — transparency sells</li>
                    </ul>
                </div>
                <div class="card p-6 text-sm text-royal-900/70">
                    Listings are reviewed by our team before publishing. Approved listings appear in search and can earn buyers a WorxCars reward on their qualifying purchase.
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>