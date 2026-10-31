<x-layouts.app :title="$seo_title" :description="$seo_description" :image="$vehicle->mainImage()?->thumbnail_path ? asset('storage/'.$vehicle->mainImage()->thumbnail_path) : asset('storage/brand/worxcars-logo.png')">
    <div
        class="bg-ivory-50"
        x-data="{
            current: 0,
            images: @js($vehicle->activeImages->map(fn ($img) => asset('storage/'.$img->image_path))->values()->all()),
            switch(i) { this.current = i; }
        }">
        <section class="border-b border-royal-900/10 bg-royal-950 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <nav class="text-xs text-ivory-100/50">
                    <a href="{{ route('home') }}" class="hover:text-gold-300">Home</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('vehicles.index') }}" class="hover:text-gold-300">Vehicles</a>
                    <span class="mx-2">/</span>
                    @if ($vehicle->make)
                        <a href="{{ route('vehicles.index', ['make' => $vehicle->make]) }}" class="hover:text-gold-300">{{ $vehicle->make }}</a>
                        <span class="mx-2">/</span>
                    @endif
                    <span class="text-ivory-100/80">{{ $vehicle->displayName() }}</span>
                </nav>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="font-display text-2xl font-black text-ivory-50 sm:text-3xl">{{ $vehicle->displayName() }}</h1>
                        <p class="mt-1 flex flex-wrap items-center gap-x-3 text-sm text-ivory-100/60">
                            <span>{{ $vehicle->year }}</span>
                            <span>·</span>
                            <span>{{ number_format($vehicle->mileage) }} km</span>
                            <span>·</span>
                            <span>{{ $vehicle->location_city }}{{ $vehicle->location_province ? ', '.$vehicle->location_province : '' }}</span>
                            <span>·</span>
                            <span>Ref {{ $vehicle->vehicle_reference }}</span>
                        </p>
                    </div>
                    @auth
                        <form method="POST" action="{{ route('vehicles.favorite', $vehicle) }}" x-data="{ fav: @js(auth()->user()->favorites()->where('vehicle_id', $vehicle->id)->exists()) }">
                            @csrf
                            <button type="submit" class="btn-ghost" :class="fav && '!border-gold-500 !text-gold-700'">
                                <svg x-show="!fav" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                <svg x-show="fav" class="h-4 w-4 text-gold-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                                <span x-text="fav ? 'Saved' : 'Save'"></span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost">Save vehicle</a>
                    @endauth
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
                <div>
                    <div class="card overflow-hidden">
                        <div class="relative aspect-[16/10] bg-royal-950">
                            <img x-bind:src="images[current]" :alt="'{{ $vehicle->displayName() }}'" class="h-full w-full object-contain">
                            @if ($vehicle->campaign)
                                <span class="badge absolute left-3 top-3 !bg-royal-950/80 text-gold-300 ring-1 ring-gold-500/50 backdrop-blur">Campaign: {{ $vehicle->campaign->name }}</span>
                            @endif
                            <button type="button" @click="current > 0 && current--" class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-royal-950/70 p-2 text-ivory-50 backdrop-blur hover:bg-royal-950" :class="current === 0 && 'opacity-30 pointer-events-none'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            </button>
                            <button type="button" @click="current < images.length - 1 && current++" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-royal-950/70 p-2 text-ivory-50 backdrop-blur hover:bg-royal-950" :class="current === images.length - 1 && 'opacity-30 pointer-events-none'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                            <span class="absolute bottom-3 right-3 rounded-full bg-royal-950/80 px-2.5 py-1 text-xs font-medium text-ivory-50 backdrop-blur" x-text="(current + 1) + ' / ' + images.length"></span>
                        </div>
                        @if ($vehicle->activeImages->count() > 1)
                            <div class="flex gap-2 overflow-x-auto border-t border-royal-900/10 p-3">
                                <template x-for="(img, i) in images" :key="i">
                                    <button type="button" @click="switch(i)" class="h-16 w-20 shrink-0 overflow-hidden rounded-md ring-2 transition" :class="i === current ? 'ring-gold-500' : 'ring-transparent opacity-60 hover:opacity-100'">
                                        <img :src="img" alt="" class="h-full w-full object-cover">
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>

                    <div class="card mt-6 p-6">
                        <h2 class="font-display text-xl font-bold text-royal-950">Overview & Stock Specs</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-royal-900/70">{{ $vehicle->description }}</p>

                        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach ([
                                'Body type' => $vehicle->body_type,
                                'Transmission' => $vehicle->transmission,
                                'Fuel type' => $vehicle->fuel_type,
                                'Engine' => $vehicle->engine_size ?: ($vehicle->engine_cc ? $vehicle->engine_cc.' cc' : null),
                                'Power' => $vehicle->power_kw ? $vehicle->power_kw.' kW' : null,
                                'Torque' => $vehicle->torque_nm ? $vehicle->torque_nm.' Nm' : null,
                                'Colour' => $vehicle->colour,
                                'Doors' => $vehicle->doors,
                                'Seats' => $vehicle->seats,
                                'Condition' => $vehicle->condition,
                                'Reg status' => $vehicle->registration_status,
                                'First registered' => $vehicle->date_first_registered?->format('M Y'),
                            ] as $label => $value)
                                @if ($value)
                                    <div class="rounded-md bg-ivory-100/70 p-3">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-royal-900/50">{{ $label }}</p>
                                        <p class="mt-0.5 text-sm font-semibold text-royal-950">{{ $value }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if ($vehicle->features)
                            <h3 class="mt-8 font-display text-lg font-bold text-royal-950">Features & Extras</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($vehicle->features as $feature)
                                    <span class="badge badge-grey !rounded-md !px-3 !py-1.5 !text-xs !font-medium">{{ $feature }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($similar->isNotEmpty())
                        <div class="mt-10">
                            <h2 class="font-display text-xl font-bold text-royal-950">Similar Vehicles</h2>
                            <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($similar as $vehicle)
                                    <x-vehicle-card :vehicle="$vehicle" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <aside class="space-y-6">
                    <div class="card p-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-royal-900/50">Asking price</p>
                        <p class="mt-1 font-display text-4xl font-black text-royal-950">{{ money($vehicle->price) }}</p>
                        @if ($estimates)
                            <p class="mt-2 text-sm text-royal-900/60">Estimate from <span class="font-semibold text-royal-950">{{ money($estimates['monthly']) }}/month</span> over {{ $estimates['term'] }} months ({{ $estimates['deposit_percent'] }}% deposit estimate).</p>
                        @endif

                        <div class="mt-5 rounded-md bg-gradient-to-r from-royal-950 to-royal-850 p-4 ring-1 ring-gold-500/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gold-400">WorxCars Buyer Reward</p>
                                    <p class="font-display text-2xl font-black text-gold-300">{{ money($breakdown['reward_amount']) }}</p>
                                </div>
                                <div class="h-10 w-px bg-gold-500/30"></div>
                                <div class="text-right">
                                    <p class="text-[10px] uppercase tracking-wider text-ivory-100/60">After verified purchase</p>
                                    <p class="text-xs font-semibold text-ivory-100/80">{{ money($breakdown['reward_amount']) }} back</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 space-y-2 text-xs text-royal-900/60" x-data="{ rewardOpen: false }">
                            <p class="font-semibold text-royal-950">How it works — in short:</p>
                            <p>1. Complete a qualifying purchase of this vehicle.</p>
                            <p>2. Seller confirms the transaction.</p>
                            <p>3. WorxCars verifies and approves.</p>
                            <p>4. Reward is paid to your nominated bank account.</p>
                            <button type="button" class="font-medium text-gold-700 hover:underline" @click="rewardOpen = !rewardOpen">Terms & conditions</button>
                            <div x-show="rewardOpen" x-collapse>
                                <p class="pt-1 leading-relaxed text-royal-900/55">Rewards are subject to WorxCars terms, eligibility criteria and fraud checks. No reward is payable for registering, viewing or enquiring alone. Reward amounts shown may change.</p>
                            </div>
                        </div>

                        @if ($vehicle->status === 'published')
                            <div class="mt-5">
                                <a href="{{ route('claims.create', $vehicle) }}" class="btn-gold w-full">Buy this vehicle &amp; claim reward</a>
                                <p class="mt-2 text-center text-[11px] text-royal-900/50">You'll submit purchase details after finalising your deal.</p>
                            </div>
                        @else
                            <div class="mt-5 rounded-md bg-amber-50 p-3 text-center text-xs font-medium text-amber-700 ring-1 ring-amber-500/30">This vehicle is currently reserved or sold.</div>
                        @endif
                    </div>

                    <div class="card p-6">
                        <h2 class="font-display text-lg font-bold text-royal-950">Contact Seller</h2>
                        <div class="mt-4 flex items-center gap-3">
                            @if ($vehicle->seller_type === 'dealership' && $vehicle->dealership)
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-royal-950 font-display text-xs font-black text-gold-400">
                                    {{ collect(explode(' ', $vehicle->dealership->business_name))->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
                                </div>
                                <div>
                                    <p class="font-semibold text-royal-950">{{ $vehicle->dealership->business_name }}</p>
                                    <p class="text-xs text-royal-900/55">{{ $vehicle->dealership->city }}{{ $vehicle->dealership->province ? ', '.$vehicle->dealership->province : '' }}</p>
                                </div>
                            @else
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-royal-950 font-display text-sm font-black text-gold-400">{{ strtoupper(mb_substr($vehicle->sellerName(), 0, 1)) }}</div>
                                <div>
                                    <p class="font-semibold text-royal-950">{{ $vehicle->sellerName() }}</p>
                                    <p class="text-xs text-royal-900/55">Verified private seller</p>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('vehicles.enquire', $vehicle) }}" class="mt-5 space-y-3">
                            @csrf
                            <div>
                                <label class="label">Your name</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()?->fullName() ?? '') }}" required class="input">
                            </div>
                            <div>
                                <label class="label">Email</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required class="input">
                            </div>
                            <div>
                                <label class="label">Phone</label>
                                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" class="input">
                            </div>
                            <div>
                                <label class="label">Preferred contact method</label>
                                <select name="preferred_contact_method" class="input" required>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone</option>
                                    <option value="whatsapp">WhatsApp</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Preferred test drive date</label>
                                <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" class="input">
                            </div>
                            <div>
                                <label class="label">Message</label>
                                <textarea name="message" rows="3" required class="input" placeholder="Hi, I'm interested in this vehicle. Please get in touch."></textarea>
                            </div>
                            <button type="submit" class="btn-royal w-full">Send enquiry</button>
                            <p class="text-center text-[11px] text-royal-900/50">The seller will contact you directly. Your details are shared only with this seller.</p>
                        </form>
                    </div>

                    @if ($vehicle->vehicle_condition_detail)
                        <div class="card p-6">
                            <h2 class="font-display text-lg font-bold text-royal-950">Vehicle Condition</h2>
                            <p class="mt-2 text-sm leading-relaxed text-royal-900/65">{{ $vehicle->vehicle_condition_detail }}</p>
                        </div>
                    @endif
                </aside>
            </div>
        </section>
    </div>
</x-layouts.app>