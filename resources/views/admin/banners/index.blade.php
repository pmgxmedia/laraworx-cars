<x-admin.layouts.admin title="Banners" :breadcrumbs="[['label' => 'Banners']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Homepage Banners</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $banners->count() }} banner(s).</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            @if ($banners->isEmpty())
                <div class="card p-8 text-center text-sm text-royal-900/50">No banners yet — upload your first one.</div>
            @endif
            @foreach ($banners as $banner)
                <div class="card overflow-hidden">
                    <div class="grid gap-4 p-4 sm:grid-cols-3">
                        <div class="sm:col-span-1">
                            <img src="{{ asset('storage/'.$banner->banner_path) }}" alt="{{ $banner->title }}" class="aspect-[16/9] w-full rounded object-cover">
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-display text-lg font-bold text-royal-950">{{ $banner->title ?: 'Untitled banner' }}</p>
                                    @if ($banner->subtitle)
                                        <p class="mt-1 text-sm text-royal-900/60">{{ $banner->subtitle }}</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="badge-grey">{{ Str::headline($banner->position ?? 'homepage') }} · #{{ $banner->sort_order }}</span>
                                    @if ($banner->is_active)
                                        <span class="badge-green">Active</span>
                                    @else
                                        <span class="badge-grey">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            @if ($banner->link_url)
                                <p class="mt-2 text-sm"><span class="text-royal-900/50">Links to </span><a href="{{ $banner->link_url }}" target="_blank" rel="noopener" class="text-gold-700 hover:underline">{{ $banner->link_url }}</a></p>
                            @endif
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="mt-3 flex justify-end">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300" onclick="return confirm('Delete this banner?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Upload Banner</h2>
                <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="label">Image * (jpeg, png, webp, gif)</label>
                        <input type="file" name="image" required accept="image/*" class="input">
                    </div>
                    <div>
                        <label class="label">Title</label>
                        <input type="text" name="title" class="input" value="{{ old('title') }}">
                    </div>
                    <div>
                        <label class="label">Subtitle</label>
                        <input type="text" name="subtitle" class="input" value="{{ old('subtitle') }}">
                    </div>
                    <div>
                        <label class="label">Link URL</label>
                        <input type="url" name="link_url" class="input" value="{{ old('link_url') }}">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Position</label>
                            <select name="position" class="input">
                                <option value="homepage">Homepage</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Sort Order</label>
                            <input type="number" name="sort_order" class="input" value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                        Active
                    </label>
                    <button type="submit" class="btn-gold btn-sm w-full">Upload banner</button>
                </form>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>