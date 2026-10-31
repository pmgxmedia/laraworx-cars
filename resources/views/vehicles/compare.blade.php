<x-layouts.app title="Compare Vehicles | WorxCars" description="Compare WorxCars vehicles side by side — price, specs and buyer rewards.">
    <section class="border-b border-royal-900/10 bg-royal-950 py-8">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-2xl font-bold text-ivory-50">Compare Vehicles</h1>
            <p class="mt-1 text-sm text-ivory-100/60">Select up to 4 vehicles and compare specs, price and buyer rewards side by side.</p>
        </div>
    </section>

    <section class="mx-auto max-w-[1500px] px-4 py-8 sm:px-6 lg:px-8">
        @if ($vehicles->isEmpty())
            <div class="card mt-6 flex flex-col items-center justify-center p-14 text-center">
                <div class="grid h-16 w-16 place-items-center rounded-full bg-royal-950">
                    <svg class="h-8 w-8 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6M3 17v-4M7 7v10M7 17v-2M11 7v6M11 17v-4M15 7v10M15 17v-2M19 7v6M19 17v-4"/></svg>
                </div>
                <h2 class="mt-4 font-display text-xl font-bold text-royal-950">Nothing to compare yet</h2>
                <p class="mt-2 max-w-sm text-sm text-royal-900/60">Add vehicles to your comparison from the vehicle pages or the search results — look for the compare icon on each card.</p>
                <a href="{{ route('vehicles.index') }}" class="btn-gold mt-5">Browse vehicles</a>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg ring-1 ring-royal-900/10">
                <table class="min-w-full border-collapse bg-white text-sm text-royal-900">
                    <thead>
                        <tr class="border-b border-royal-900/10">
                            <th class="w-40 bg-royal-950 px-4 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-gold-400"></th>
                            @foreach ($vehicles as $vehicle)
                                <th class="min-w-[220px] px-4 py-4 text-left align-top">
                                    <div class="aspect-[3/2] overflow-hidden rounded-md bg-royal-950">
                                        <img src="{{ $vehicle->mainImage()?->image_path ? asset('storage/'.$vehicle->mainImage()->image_path) : asset('storage/brand/worxcars-logo.png') }}" alt="" class="h-full w-full object-cover">
                                    </div>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="mt-3 block font-display text-base font-bold text-royal-950 hover:text-gold-700">{{ $vehicle->displayName() }}</a>
                                    <p class="text-xs text-royal-900/50">{{ $vehicle->year }} · {{ $vehicle->sellerName() }}</p>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-royal-900/5 bg-ivory-100/50">
                            <td class="bg-royal-950 px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-gold-400">Price</td>
                            @foreach ($vehicles as $vehicle)
                                <td class="px-4 py-3 font-display text-lg font-black text-royal-950">{{ money($vehicle->price) }}</td>
                            @endforeach
                        </tr>
                        <tr class="border-b border-royal-900/5">
                            <td class="bg-royal-950 px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-gold-400">Buyer reward</td>
                            @foreach ($vehicles as $vehicle)
                                <td class="px-4 py-3">
                                    <span class="badge-gold !text-sm !font-black">{{ money($vehicle->activeRewardAmount()) }}</span>
                                </td>
                            @endforeach
                        </tr>
                        @foreach ($attributes as $attribute)
                            <tr class="border-b border-royal-900/5">
                                <td class="bg-royal-950 px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-gold-400">{{ $attribute['label'] }}</td>
                                @foreach ($vehicles as $vehicle)
                                    <td class="px-4 py-3">{{ $vehicle->{$attribute['key']} ?? '—' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td class="bg-royal-950 px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-gold-400">Action</td>
                            @foreach ($vehicles as $vehicle)
                                <td class="px-4 py-4">
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-royal btn-sm">View & enquire</a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.app>