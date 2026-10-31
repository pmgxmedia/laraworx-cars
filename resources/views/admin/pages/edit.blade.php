<x-admin.layouts.admin title="Edit {{ $page->title }}" :breadcrumbs="[['label' => 'Pages', 'url' => route('admin.pages.index')], ['label' => $page->title]]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Edit Page</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $page->slug }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('page.show', $page) }}" target="_blank" class="btn-ghost btn-sm">View live</a>
            <a href="{{ route('admin.pages.index') }}" class="btn-ghost btn-sm">Back to pages</a>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    @if ($errors->any())
        <div class="mb-4 mt-6 rounded-md bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-500/30">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="mt-6 grid gap-6 xl:grid-cols-3">
        @csrf
        @method('PUT')
        <div class="space-y-6 xl:col-span-2">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Content</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="label">Title *</label>
                        <input type="text" name="title" required class="input" value="{{ old('title', $page->title) }}">
                    </div>
                    <div>
                        <label class="label">Slug</label>
                        <input type="text" name="slug" class="input" value="{{ old('slug', $page->slug) }}">
                        <p class="mt-1 text-xs text-royal-900/55">Leave blank to regenerate from the title.</p>
                    </div>
                    <div>
                        <label class="label">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="input">{{ old('excerpt', $page->excerpt) }}</textarea>
                    </div>
                    <div>
                        <label class="label">Body</label>
                        <textarea name="content" rows="18" class="input font-mono text-xs">{{ old('content', $page->content) }}</textarea>
                        <p class="mt-1 text-xs text-royal-900/55">Plain text / simple HTML allowed. Line breaks are preserved.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h3 class="section-title !text-xl">Publishing</h3>
                <div class="mt-4 space-y-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_published" value="1" @checked($page->is_published) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                        Published
                    </label>
                    @if ($page->published_at)
                        <p class="text-xs text-royal-900/55">Published {{ $page->published_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">SEO</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="label">Meta Title</label>
                        <input type="text" name="meta_title" class="input" value="{{ old('meta_title', $page->meta_title) }}">
                    </div>
                    <div>
                        <label class="label">Meta Description</label>
                        <textarea name="meta_description" rows="3" class="input">{{ old('meta_description', $page->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex justify-between gap-4">
                    <a href="{{ route('admin.pages.index') }}" class="btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn-gold btn-sm">Save page</button>
                </div>
            </div>
        </div>
    </form>
</x-admin.layouts.admin>