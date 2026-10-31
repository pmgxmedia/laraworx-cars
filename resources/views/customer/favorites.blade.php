<x-layouts.customer title="Favorites">
    <h1 class="font-display text-2xl font-bold text-royal-950">Saved Vehicles</h1>
    <p class="mt-1 text-sm text-royal-900/60">Vehicles you've saved for later.</p>

    @if (session('status'))
        <div class="mt-5 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
    @endif

    @if ($favorites->isEmpty())
        <div class="card mt-6 flex flex-col items-center justify-center p-14 text-center">
            <h2 class="font-display text-xl font-bold text-royal-950">Nothing saved yet</h2>
            <p class="mt-2 max-w-sm text-sm text-royal-900/60">Tap "Save" on any vehicle you like to find it here later.</p>
            <a href="{{ route('vehicles.index') }}" class="btn-gold btn-sm mt-5">Browse vehicles</a>
        </div>
    @else
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($favorites as $favorite)
                <x-vehicle-card :vehicle="$favorite->vehicle" />
            @endforeach
        </div>
        <div class="mt-10">{{ $favorites->links() }}</div>
    @endif
</x-layouts.customer>