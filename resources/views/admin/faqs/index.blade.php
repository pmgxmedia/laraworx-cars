<x-admin.layouts.admin title="FAQs" :breadcrumbs="[['label' => 'FAQs']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Frequently Asked Questions</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $faqs->count() }} question(s).</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            @if ($faqs->isEmpty())
                <div class="card p-8 text-center text-sm text-royal-900/50">No FAQs yet — add your first one.</div>
            @else
                @foreach ($faqs->groupBy('category') as $category => $items)
                    <div class="card overflow-hidden">
                        <div class="border-b border-royal-900/10 px-5 py-3">
                            <h2 class="font-display text-lg font-bold text-royal-950">{{ Str::headline($category) }} <span class="badge-grey align-middle">{{ $items->count() }}</span></h2>
                        </div>
                        @foreach ($items as $faq)
                            <div class="border-b border-royal-900/5 px-5 py-4 last:border-b-0">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="font-semibold text-royal-950">{{ $faq->question }}</p>
                                    <div class="flex shrink-0 items-center gap-2">
                                        @if ($faq->is_published)
                                            <span class="badge-green">Live</span>
                                        @else
                                            <span class="badge-grey">Hidden</span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="mt-2 grid gap-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="question" value="{{ $faq->question }}" class="input" required>
                                    <textarea name="answer" rows="2" class="input" required>{{ $faq->answer }}</textarea>
                                    <div class="grid grid-cols-3 gap-3">
                                        <input type="text" name="category" value="{{ $faq->category }}" class="input" placeholder="Category">
                                        <input type="number" name="sort_order" value="{{ $faq->sort_order }}" class="input" placeholder="Order">
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="is_published" value="1" @checked($faq->is_published) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                                            Publish
                                        </label>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="submit" class="btn-gold btn-sm">Save</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="mt-2 flex justify-end">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm !text-rose-700 hover:!border-rose-300" onclick="return confirm('Delete this FAQ?')">Delete</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Add New FAQ</h2>
                <form method="POST" action="{{ route('admin.faqs.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="label">Question *</label>
                        <input type="text" name="question" required class="input" value="{{ old('question') }}">
                    </div>
                    <div>
                        <label class="label">Answer *</label>
                        <textarea name="answer" rows="4" required class="input">{{ old('answer') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Category</label>
                            <input type="text" name="category" class="input" value="{{ old('category', 'general') }}">
                        </div>
                        <div>
                            <label class="label">Sort Order</label>
                            <input type="number" name="sort_order" class="input" value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', true)) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                        Publish immediately
                    </label>
                    <button type="submit" class="btn-gold btn-sm w-full">Add question</button>
                </form>
            </div>
        </div>
    </div>
</x-admin.layouts.admin>