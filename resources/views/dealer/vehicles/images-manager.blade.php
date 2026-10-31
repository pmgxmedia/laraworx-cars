@php
    $images = $vehicle->images()->orderBy('sort_order')->get();
    $ids = $images->pluck('id')->values()->all();
    $last = count($ids) - 1;
@endphp

<section>
    <div class="flex items-center gap-3">
        <h3 class="font-display text-lg font-bold text-royal-950">Images ({{ $images->count() }})</h3>
        <div class="divider-gold"></div>
    </div>
    <p class="mt-2 text-xs text-royal-900/50">Maximum {{ config('worxcars.images_per_vehicle_max', 20) }} photos. The first image is the main photo — use the arrows to reorder.</p>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($images as $i => $image)
            @php
                $upOrder = $ids;
                if ($i > 0) {
                    $tmp = $upOrder[$i - 1];
                    $upOrder[$i - 1] = $upOrder[$i];
                    $upOrder[$i] = $tmp;
                }

                $downOrder = $ids;
                if ($i < $last) {
                    $tmp = $downOrder[$i + 1];
                    $downOrder[$i + 1] = $downOrder[$i];
                    $downOrder[$i] = $tmp;
                }
            @endphp
            <div class="card overflow-hidden">
                <div class="relative aspect-[4/3] bg-royal-900">
                    <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="{{ $image->alt_text }}" class="h-full w-full object-cover">
                    @if ($image->is_main)
                        <span class="badge-gold absolute left-2 top-2 !bg-gold-500 text-white">Main</span>
                    @endif
                    <span class="absolute right-2 top-2 rounded-full bg-royal-950/80 px-2 py-0.5 text-[10px] font-bold text-ivory-50">#{{ $i + 1 }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 p-3">
                    <div class="flex items-center gap-1">
                        <form method="POST" action="{{ route('dealer.vehicles.images.reorder') }}">
                            @csrf
                            @foreach ($upOrder as $oid)
                                <input type="hidden" name="ids[]" value="{{ $oid }}">
                            @endforeach
                            <button type="submit" @disabled($i === 0) title="Move up"
                                    class="grid h-8 w-8 place-items-center rounded-md transition disabled:opacity-30
                                           {{ $i > 0 ? 'bg-royal-900 text-ivory-50 hover:bg-royal-700' : 'bg-royal-900/5 text-royal-900/30' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dealer.vehicles.images.reorder') }}">
                            @csrf
                            @foreach ($downOrder as $oid)
                                <input type="hidden" name="ids[]" value="{{ $oid }}">
                            @endforeach
                            <button type="submit" @disabled($i === $last) title="Move down"
                                    class="grid h-8 w-8 place-items-center rounded-md transition disabled:opacity-30
                                           {{ $i < $last ? 'bg-royal-900 text-ivory-50 hover:bg-royal-700' : 'bg-royal-900/5 text-royal-900/30' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('dealer.vehicles.image.delete', $image) }}" onsubmit="return confirm('Remove this image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost btn-sm !text-rose-600 hover:!border-rose-300">Remove</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    @if ($images->isEmpty())
        <div class="mt-4 rounded-lg border-2 border-dashed border-royal-900/15 px-6 py-10 text-center">
            <p class="text-sm text-royal-900/50">No images yet. Upload photos below to make your listing stand out.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('dealer.vehicles.images', $vehicle) }}" enctype="multipart/form-data" class="mt-4 card p-5">
        @csrf
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1">
                <label class="label">Add more photos</label>
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="input file:text-sm file:text-royal-900">
            </div>
            <button type="submit" class="btn-royal btn-sm">Upload images</button>
        </div>
    </form>
</section>