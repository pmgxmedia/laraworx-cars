<x-layouts.customer title="Saved Searches">
    <h1 class="font-display text-2xl font-bold text-royal-950">Saved Searches</h1>
    <p class="mt-1 text-sm text-royal-900/60">Re-run your favourite vehicle searches in one click.</p>

    <form method="POST" action="{{ route('customer.searches.store') }}" class="card mt-6 p-6">
        @csrf
        <h2 class="font-display text-lg font-bold text-royal-950">Save current filters</h2>
        <p class="mt-1 text-xs text-royal-900/55">Save the filters you were just using on the search page. We'll remember them here.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label">Search name</label>
                <input type="text" name="name" value="{{ request('name') }}" class="input" placeholder="e.g. My Polo budget">
            </div>
            <div>
                <label class="label">Make</label>
                <select name="make" class="input">
                    <option value="">Any</option>
                    @foreach (\App\Models\Vehicle::published()->distinct()->orderBy('make')->pluck('make') as $make)
                        <option value="{{ $make }}">{{ $make }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Max price</label>
                <select name="price_max" class="input">
                    <option value="">Any</option>
                    @foreach ([200000, 300000, 400000, 500000, 700000, 1000000] as $p)
                        <option value="{{ $p }}">{{ money($p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn-gold btn-sm mt-5">Save search</button>
    </form>

    @if ($searches->isEmpty())
        <div class="card mt-6 p-14 text-center">
            <h2 class="font-display text-xl font-bold text-royal-950">No saved searches</h2>
            <p class="mt-2 text-sm text-royal-900/60">Save a search above to quickly revisit it later.</p>
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($searches as $search)
                <div class="card flex flex-col p-5">
                    <p class="font-semibold text-royal-950">{{ $search->name ?: 'Saved search' }}</p>
                    <p class="mt-1 text-xs text-royal-900/55">{{ $search->created_at->format('d M Y') }}</p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach (array_filter($search->filters ?? []) as $k => $v)
                            <span class="badge badge-grey">{{ ucwords(str_replace('_', ' ', $k)) }}: {{ $v }}</span>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center gap-2 border-t border-royal-900/10 pt-3">
                        <a href="{{ route('vehicles.index', array_filter($search->filters ?? [])) }}" class="btn-royal btn-sm">Run search</a>
                        <form method="POST" action="{{ route('customer.searches.destroy', $search) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost btn-sm text-rose-700">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-10">{{ $searches->links() }}</div>
    @endif
</x-layouts.customer>