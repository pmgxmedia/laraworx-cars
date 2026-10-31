<x-layouts.auth title="Create your account">
    <h1 class="font-display text-2xl font-bold text-royal-950">Create your account</h1>
    <p class="mt-1 text-sm text-royal-900/60">Register for free. No reward is ever paid for registering alone &mdash; a completed, verified purchase is required.</p>

    <x-auth.errors :errors="$errors" class="mt-6" />

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="name" class="label">First name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="given-name" class="input">
            </div>
            <div>
                <label for="surname" class="label">Surname</label>
                <input id="surname" type="text" name="surname" value="{{ old('surname') }}" required autocomplete="family-name" class="input">
            </div>
        </div>

        <div class="pt-2">
            <h2 class="font-display text-sm font-bold uppercase tracking-wider text-royal-900/50">Contact details</h2>
        </div>
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input" placeholder="you@example.com">
        </div>
        <div>
            <label for="phone" class="label">Mobile number</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" class="input" placeholder="082 000 0000" autocomplete="tel">
        </div>

        <div class="pt-2">
            <h2 class="font-display text-sm font-bold uppercase tracking-wider text-royal-900/50">Physical address</h2>
            <p class="mt-1 text-xs text-royal-900/50">Enter your address manually, or use your current location to fill it in.</p>
        </div>

        <div x-data="sellerLocation()" class="space-y-4">
            <div>
                <button type="button" @click="locate()" :disabled="loading" class="btn-ghost w-full text-sm disabled:opacity-60">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    <span x-text="loading ? 'Getting your location...' : 'Use my current location'"></span>
                </button>
                <p x-show="message" x-text="message" class="mt-2 text-xs" :class="error ? 'text-rose-700' : 'text-emerald-700'"></p>
                <input type="hidden" name="latitude" :value="latitude">
                <input type="hidden" name="longitude" :value="longitude">
            </div>

            <div>
                <label for="street_address" class="label">Street address</label>
                <input id="street_address" x-ref="street" type="text" name="street_address" value="{{ old('street_address') }}" class="input" placeholder="12 Main Road, Unit 4" autocomplete="street-address">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="city" class="label">City / suburb</label>
                    <input id="city" x-ref="city" type="text" name="city" value="{{ old('city') }}" class="input" autocomplete="address-level2">
                </div>
                <div>
                    <label for="postal_code" class="label">Postal code</label>
                    <input id="postal_code" x-ref="postal" type="text" name="postal_code" value="{{ old('postal_code') }}" class="input" autocomplete="postal-code">
                </div>
            </div>
            <div>
                <label for="province" class="label">Province</label>
                <select id="province" x-ref="province" name="province" class="input" autocomplete="address-level1">
                    <option value="">Select</option>
                    @foreach (['Gauteng', 'Western Cape', 'KwaZulu-Natal', 'Eastern Cape', 'Free State', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape'] as $p)
                        <option value="{{ $p }}" @selected(old('province') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-2">
            <h2 class="font-display text-sm font-bold uppercase tracking-wider text-royal-900/50">Security</h2>
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="input" placeholder="At least 8 characters">
        </div>
        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
        </div>
        <label class="flex items-start gap-2 text-xs leading-relaxed text-royal-900/60">
            <input type="checkbox" name="terms" required class="mt-0.5 rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
            <span>I agree to the <a href="{{ route('page.show', 'terms') }}" class="font-medium text-gold-700 hover:underline" target="_blank">Terms &amp; Conditions</a> and <a href="{{ route('page.show', 'privacy') }}" class="font-medium text-gold-700 hover:underline" target="_blank">Privacy Policy</a>. I understand that a WorxCars Buyer Reward is payable only after a successful qualifying purchase is verified.</span>
        </label>
        <button type="submit" class="btn-gold w-full">Create Account</button>
    </form>

    <p class="mt-6 text-center text-sm text-royal-900/60">
        Already registered?
        <a href="{{ route('login') }}" class="font-semibold text-gold-700 hover:underline">Sign in</a>
    </p>

    @push('scripts')
        <script>
            function sellerLocation() {
                return {
                    loading: false,
                    error: false,
                    message: '',
                    latitude: @json(old('latitude')),
                    longitude: @json(old('longitude')),

                    locate() {
                        if (! navigator.geolocation) {
                            this.error = true;
                            this.message = 'Geolocation is not supported by your browser. Please enter your address manually.';
                            return;
                        }

                        this.loading = true;
                        this.error = false;
                        this.message = '';

                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                this.latitude = position.coords.latitude.toFixed(7);
                                this.longitude = position.coords.longitude.toFixed(7);
                                this.message = 'Location captured. Review the address below and edit it if needed.';
                                await this.reverseGeocode();
                                this.loading = false;
                            },
                            () => {
                                this.loading = false;
                                this.error = true;
                                this.message = 'We could not get your location. Please enter your address manually.';
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                        );
                    },

                    async reverseGeocode() {
                        try {
                            const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat='
                                + encodeURIComponent(this.latitude)
                                + '&lon=' + encodeURIComponent(this.longitude);
                            const response = await fetch(url, { headers: { Accept: 'application/json' } });

                            if (! response.ok) {
                                return;
                            }

                            const data = await response.json();
                            const address = data.address || {};
                            const street = [address.house_number, address.road].filter(Boolean).join(' ');
                            const city = address.city || address.town || address.village || address.suburb || address.county;

                            if (street && this.$refs.street && ! this.$refs.street.value) {
                                this.$refs.street.value = street;
                            }

                            if (city && this.$refs.city && ! this.$refs.city.value) {
                                this.$refs.city.value = city;
                            }

                            if (address.postcode && this.$refs.postal && ! this.$refs.postal.value) {
                                this.$refs.postal.value = address.postcode;
                            }

                            if (address.state && this.$refs.province && ! this.$refs.province.value) {
                                const match = Array.from(this.$refs.province.options)
                                    .find((option) => option.value.toLowerCase() === address.state.toLowerCase());

                                if (match) {
                                    this.$refs.province.value = match.value;
                                }
                            }
                        } catch (error) {
                            // Coordinates are still saved even if reverse geocoding fails.
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.auth>
