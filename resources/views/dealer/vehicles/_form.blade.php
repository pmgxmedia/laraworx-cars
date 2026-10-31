@php
    $vehicle = $vehicle ?? null;
    $commissionPercent = (float) appSetting('worxcars.default_commission_percent', 2.5);
@endphp

<div class="space-y-8">
    <section>
        <div class="flex items-center gap-3">
            <h3 class="font-display text-lg font-bold text-royal-950">Basic information</h3>
            <div class="divider-gold"></div>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="label">Make</label>
                <input type="text" name="make" value="{{ old('make', $vehicle?->make) }}" placeholder="Toyota" required class="input">
            </div>
            <div>
                <label class="label">Model</label>
                <input type="text" name="model" value="{{ old('model', $vehicle?->model) }}" placeholder="Hilux" required class="input">
            </div>
            <div>
                <label class="label">Variant</label>
                <input type="text" name="variant" value="{{ old('variant', $vehicle?->variant) }}" placeholder="2.4 GD-6 Double Cab" class="input">
            </div>
            <div>
                <label class="label">Year</label>
                <input type="number" name="year" value="{{ old('year', $vehicle?->year) }}" min="1980" max="{{ now()->year + 1 }}" required class="input">
            </div>
            <div>
                <label class="label">Mileage (km)</label>
                <input type="number" name="mileage" value="{{ old('mileage', $vehicle?->mileage) }}" min="0" max="3000000" placeholder="45000" required class="input">
            </div>
            <div x-data="{ price: @js((float) old('price', $vehicle?->price ?? 0)) }">
                <label class="label">Price (ZAR)</label>
                <input type="number" name="price" x-model.number="price" value="{{ old('price', $vehicle?->price) }}" step="0.01" min="1000" required class="input">
                <p class="mt-1.5 text-xs text-royal-900/50">
                    Estimated commission: <span class="font-semibold text-gold-700" x-text="'R' + new Intl.NumberFormat('en-ZA').format(Math.round((@js($commissionPercent) / 100) * (price || 0)))"></span>
                    · exact commission & buyer rewards set by WorxCars
                </p>
            </div>
            <div>
                <label class="label">VIN (optional)</label>
                <input type="text" name="vin" value="{{ old('vin', $vehicle?->vin) }}" maxlength="60" class="input">
            </div>
            <div>
                <label class="label">Registration number (optional)</label>
                <input type="text" name="reg_number" value="{{ old('reg_number', $vehicle?->reg_number) }}" maxlength="30" class="input">
            </div>
            <div>
                <label class="label">Date first registered</label>
                <input type="date" name="date_first_registered" value="{{ old('date_first_registered', $vehicle?->date_first_registered?->format('Y-m-d')) }}" class="input">
            </div>
        </div>
    </section>

    <section>
        <div class="flex items-center gap-3">
            <h3 class="font-display text-lg font-bold text-royal-950">Specifications</h3>
            <div class="divider-gold"></div>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="label">Transmission</label>
                <select name="transmission" class="input">
                    <option value="">Select</option>
                    @foreach (['Automatic', 'Manual'] as $option)
                        <option value="{{ $option }}" @selected(old('transmission', $vehicle?->transmission) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Fuel type</label>
                <select name="fuel_type" class="input">
                    <option value="">Select</option>
                    @foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric'] as $option)
                        <option value="{{ $option }}" @selected(old('fuel_type', $vehicle?->fuel_type) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Body type</label>
                <select name="body_type" class="input">
                    <option value="">Select</option>
                    @foreach (['Sedan', 'Hatchback', 'SUV', 'Coupe', 'Convertible', 'Wagon', 'Bakkie', 'Van', 'Truck'] as $option)
                        <option value="{{ $option }}" @selected(old('body_type', $vehicle?->body_type) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Engine size</label>
                <input type="text" name="engine_size" value="{{ old('engine_size', $vehicle?->engine_size) }}" placeholder="2.4L Turbo" maxlength="40" class="input">
            </div>
            <div>
                <label class="label">Engine cc</label>
                <input type="number" name="engine_cc" value="{{ old('engine_cc', $vehicle?->engine_cc) }}" min="0" max="20000" class="input">
            </div>
            <div>
                <label class="label">Power (kW)</label>
                <input type="number" name="power_kw" value="{{ old('power_kw', $vehicle?->power_kw) }}" min="0" step="0.01" class="input">
            </div>
            <div>
                <label class="label">Torque (Nm)</label>
                <input type="number" name="torque_nm" value="{{ old('torque_nm', $vehicle?->torque_nm) }}" min="0" step="0.01" class="input">
            </div>
            <div>
                <label class="label">Colour</label>
                <input type="text" name="colour" value="{{ old('colour', $vehicle?->colour) }}" maxlength="60" class="input">
            </div>
            <div>
                <label class="label">Doors</label>
                <input type="number" name="doors" value="{{ old('doors', $vehicle?->doors) }}" min="1" max="24" class="input">
            </div>
            <div>
                <label class="label">Seats</label>
                <input type="number" name="seats" value="{{ old('seats', $vehicle?->seats) }}" min="1" max="100" class="input">
            </div>
            <div>
                <label class="label">Condition</label>
                <select name="condition" class="input">
                    <option value="">Select</option>
                    @foreach (['used' => 'Used', 'new' => 'New', 'demo' => 'Demo'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('condition', $vehicle?->condition) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="label">Registration status</label>
                <select name="registration_status" class="input">
                    <option value="">Select</option>
                    @foreach (['registered' => 'Registered', 'unregistered' => 'Unregistered'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('registration_status', $vehicle?->registration_status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label class="label">Condition details</label>
            <textarea name="vehicle_condition_detail" rows="3" class="input" placeholder="Any accident history, service records, tyres, or notable condition points">{{ old('vehicle_condition_detail', $vehicle?->vehicle_condition_detail) }}</textarea>
        </div>
    </section>

    <section>
        <div class="flex items-center gap-3">
            <h3 class="font-display text-lg font-bold text-royal-950">Location</h3>
            <div class="divider-gold"></div>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label">Province</label>
                <select name="location_province" class="input" required>
                    <option value="">Select province</option>
                    @foreach (['Eastern Cape', 'Free State', 'Gauteng', 'KwaZulu-Natal', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape', 'Western Cape'] as $province)
                        <option value="{{ $province }}" @selected(old('location_province', $vehicle?->location_province) === $province)>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">City</label>
                <input type="text" name="location_city" value="{{ old('location_city', $vehicle?->location_city) }}" placeholder="Johannesburg" required class="input">
            </div>
            <div>
                <label class="label">Suburb</label>
                <input type="text" name="location_suburb" value="{{ old('location_suburb', $vehicle?->location_suburb) }}" placeholder="Sandton" class="input">
            </div>
        </div>
    </section>

    <section>
        <div class="flex items-center gap-3">
            <h3 class="font-display text-lg font-bold text-royal-950">Description &amp; features</h3>
            <div class="divider-gold"></div>
        </div>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="6" class="input" placeholder="Give buyers a clear overview of the vehicle, its condition, service history and any extras included.">{{ old('description', $vehicle?->description) }}</textarea>
            </div>
            <div>
                <label class="label">Features (one per line)</label>
                <textarea name="features" rows="6" class="input" placeholder="Leather seats&#10;Sunroof&#10;Reverse camera&#10;Service history">{{ old('features', $vehicle?->features ? implode("\n", $vehicle->features) : '') }}</textarea>
            </div>
        </div>
    </section>
</div>