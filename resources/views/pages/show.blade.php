<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-black text-ivory-50">{{ $page->title }}</h1>
            @if ($page->updated_at)
                <p class="mt-2 text-xs text-ivory-100/40">Last updated {{ $page->updated_at->format('d F Y') }}</p>
            @endif
        </div>
    </section>
    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="prose prose-royal max-w-none"
                 x-data="{}">
            {!! $page->content !!}
        </article>
    </section>
</x-layouts.app>