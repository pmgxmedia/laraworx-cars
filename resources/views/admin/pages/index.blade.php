<x-admin.layouts.admin title="Pages" :breadcrumbs="[['label' => 'Pages']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Content Pages</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $pages->count() }} page(s) in total.</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="card mt-6 overflow-hidden">
        @if ($pages->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-royal-900/50">No pages found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr class="hover:bg-ivory-100/60">
                                <td>
                                    <p class="font-semibold text-royal-950">{{ $page->title }}</p>
                                    @if ($page->excerpt)
                                        <p class="max-w-[300px] truncate text-xs text-royal-900/55">{{ $page->excerpt }}</p>
                                    @endif
                                </td>
                                <td class="font-mono text-xs">{{ $page->slug }}</td>
                                <td>
                                    @if ($page->is_published)
                                        <span class="badge-green">Published</span>
                                    @else
                                        <span class="badge-grey">Draft</span>
                                    @endif
                                </td>
                                <td class="text-royal-900/60">{{ $page->updated_at?->format('d M Y H:i') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('page.show', $page) }}" target="_blank" class="btn-ghost btn-sm">View site</a>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn-ghost btn-sm">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin.layouts.admin>