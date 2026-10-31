<x-layouts.app title="Submit Reward Claim | WorxCars" description="Submit your WorxCars buyer reward claim for a qualifying purchase.">
    <section class="border-b border-royal-900/10 bg-royal-950 py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-2xl font-bold text-ivory-50">Submit Your Buyer Reward Claim</h1>
            <p class="mt-1 text-sm text-ivory-100/60">Tell us about your qualifying purchase. No reward is paid automatically — every claim is verified.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <x-auth.errors :errors="$errors" />
        @if (session('status'))
            <div class="mt-4 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
        @endif

        @if ($existing)
            <div class="rounded-md bg-amber-50 p-4 text-sm font-medium text-amber-800 ring-1 ring-amber-500/30">
                You already have a claim on this vehicle ({{ $existing->claim_reference }}, {{ $existing->statusLabel() }}). Submitting another duplicate claim may be flagged as fraud.
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <div class="card overflow-hidden">
                    <div class="flex gap-4 p-5">
                        <img src="{{ $vehicle->mainImage()?->image_path ? asset('storage/'.$vehicle->mainImage()->image_path) : asset('storage/brand/worxcars-logo.png') }}" alt="" class="h-24 w-36 rounded-md object-cover">
                        <div>
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="font-display text-lg font-bold text-royal-950 hover:text-gold-700">{{ $vehicle->displayName() }}</a>
                            <p class="text-sm text-royal-900/60">{{ $vehicle->year }} · {{ number_format($vehicle->mileage) }} km · {{ $vehicle->location_city }}</p>
                            <p class="mt-2 font-display text-xl font-black text-royal-950">{{ money($vehicle->price) }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('claims.store', $vehicle) }}" class="card p-6 sm:p-8" x-data="{ consent: false }">
                    @csrf
                    <h2 class="font-display text-lg font-bold text-royal-950">Purchase details</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">Purchase date *</label>
                            <input type="date" name="purchase_date" required value="{{ old('purchase_date') }}" max="{{ now()->toDateString() }}" min="{{ now()->subMonth()->toDateString() }}" class="input">
                        </div>
                        <div>
                            <label class="label">Purchase price (R) *</label>
                            <input type="number" name="purchase_price" required value="{{ old('purchase_price', $vehicle->price) }}" min="1000" class="input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">Transaction / invoice reference</label>
                            <input type="text" name="provider_reference" value="{{ old('provider_reference') }}" class="input" placeholder="Invoice or reference number from your purchase">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">How did the purchase happen? *</label>
                            <textarea name="description" rows="4" required class="input" placeholder="Describe the purchase — seller, date, how the deal was concluded...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <h2 class="mt-8 font-display text-lg font-bold text-royal-950">Where should your reward be paid?</h2>
                    <p class="mt-1 text-xs text-royal-900/50">Only for the reward payout. WorxCars verifies purchases before paying anything.</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label">Account holder name *</label>
                            <input type="text" name="bank_account_name" value="{{ old('bank_account_name', auth()->user()->fullName()) }}" required class="input">
                        </div>
                        <div>
                            <label class="label">Bank name *</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" required class="input" placeholder="e.g. Standard Bank">
                        </div>
                        <div>
                            <label class="label">Branch code *</label>
                            <input type="text" name="bank_branch_code" value="{{ old('bank_branch_code') }}" required class="input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">Account number *</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" required class="input" autocomplete="off">
                        </div>
                    </div>

                    <h2 class="mt-8 font-display text-lg font-bold text-royal-950">Declarations</h2>
                    <div class="mt-4 space-y-3 text-sm text-royal-900/75">
                        <label class="flex items-start gap-3 rounded-md bg-ivory-100/70 p-3">
                            <input type="checkbox" name="declarations[purchased]" value="1" required class="mt-0.5 rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
                            <span>I confirm I genuinely purchased this vehicle from the seller shown, at or near the listed price.</span>
                        </label>
                        <label class="flex items-start gap-3 rounded-md bg-ivory-100/70 p-3">
                            <input type="checkbox" name="declarations[no_fraud]" value="1" required class="mt-0.5 rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
                            <span>I confirm all information provided is true and accurate, and I understand that fraudulent claims are rejected and may be reported.</span>
                        </label>
                        <label class="flex items-start gap-3 rounded-md bg-ivory-100/70 p-3">
                            <input type="checkbox" name="declarations[reward_understanding]" value="1" required class="mt-0.5 rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
                            <span>I understand the reward is not guaranteed and is paid only after seller confirmation, WorxCars verification and approval.</span>
                        </label>
                    </div>

                    <label x-on:click="consent = !consent" class="mt-5 flex items-start gap-3 rounded-md border border-gold-500/50 bg-gold-500/5 p-3 text-sm text-royal-900/80" :class="consent && 'border-gold-600 bg-gold-500/10'">
                        <input type="checkbox" name="declaration_consent" value="1" required :checked="consent" class="mt-0.5 rounded border-gold-600 text-gold-600 focus:ring-gold-500">
                        <span>
                            <span class="font-semibold text-royal-950">Reward consent *</span><br>
                            I agree to the WorxCars reward terms and consent to WorxCars contacting the seller and conducting verification checks on this purchase. I understand my reward amount is capped and that WorxCars may refuse or cancel rewards where terms have been breached.
                        </span>
                    </label>

                    <button type="submit" class="btn-gold mt-8 w-full sm:w-auto" :disabled="!consent" :class="!consent && 'opacity-50 cursor-not-allowed'">
                        Submit claim for review
                    </button>
                </form>
            </div>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-md bg-gradient-to-r from-royal-950 to-royal-850 p-6 ring-1 ring-gold-500/30">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gold-400">Your potential reward</p>
                    <p class="mt-1 font-display text-3xl font-black text-gold-300">{{ money($vehicle->effective_reward_amount ?? $vehicle->activeRewardAmount()) }}</p>
                    <p class="mt-2 text-xs leading-relaxed text-ivory-100/60">Paid only after seller confirmation, WorxCars verification and administrative approval. Subject to the <a href="{{ route('page.show', 'reward-terms') }}" target="_blank" class="text-gold-300 underline">reward terms</a>.</p>
                </div>
                <div class="card p-6 text-sm text-royal-900/70">
                    <p class="font-semibold text-royal-950">What happens next?</p>
                    <ol class="mt-3 list-decimal space-y-2 pl-5">
                        <li>Your claim is recorded and the vehicle is marked reserved.</li>
                        <li>Our team reviews the claim for eligibility.</li>
                        <li>The seller confirms the transaction.</li>
                        <li>We verify and approve.</li>
                        <li>Your reward is paid to the account above.</li>
                    </ol>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>