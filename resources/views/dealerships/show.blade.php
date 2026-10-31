<x-layouts.app :title="$dealership->business_name.' | WorxCars'" :description="$dealership->description">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="text-xs text-ivory-100/50">
                <a href="{{ route('home') }}" class="hover:text-gold-300">Home</a><span class="mx-2">/</span>
                <a href="{{ route('dealerships.index') }}" class="hover:text-gold-300">Dealerships</a><span class="mx-2">/</span>
                <span class="text-ivory-100/80">{{ $dealership->business_name }}</span>
            </nav>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="grid h-16 w-16 place-items-center rounded-lg bg-royal-800 font-display text-lg font-black text-gold-400 ring-1 ring-gold-500/40">
                        {{ collect(explode(' ', $dealership->business_name))->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->join('') }}
                    </div>
                    <div>
                        <h1 class="font-display text-3xl font-black text-ivory-50">{{ $dealership->business_name }}</h1>
                        <p class="mt-1 text-sm text-ivory-100/60">
                            {{ $dealership->street_address }}, {{ $dealership->city }}, {{ $dealership->province }} {{ $dealership->postal_code }}
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-emerald-500/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Verified WorxCars partner
                </span>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($dealership->description)
            <div class="card mb-8 p-6">
                <p class="text-sm leading-relaxed text-royal-900/70">{{ $dealership->description }}</p>
                <div class="mt-4 flex flex-wrap gap-6 border-t border-royal-900/10 pt-4 text-sm text-royal-900/60">
                    @if ($dealership->website)
                        <a href="{{ $dealership->website }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 font-medium text-gold-700 hover:underline">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                            {{ $dealership->website }}
                        </a>
                    @endif
                    @if ($dealership->phone)
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            {{ $dealership->phone }}
                        </span>
                    @endif
                    <span>{{ $stats['vehicles'] }} vehicles sold</span>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl font-bold text-royal-950">Current Stock</h2>
            <a href="{{ route('vehicles.index', ['seller_type' => 'dealership']) }}" class="btn-ghost btn-sm">All dealer stock</a>
        </div>

        @if ($vehicles->isEmpty())
            <div class="card mt-4 p-14 text-center">
                <h3 class="font-display text-xl font-bold text-royal-950">No live stock right now</h3>
                <p class="mt-2 text-sm text-royal-900/60">This dealership hasn't listed any available vehicles at the moment.</p>
            </div>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($vehicles as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" />
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>