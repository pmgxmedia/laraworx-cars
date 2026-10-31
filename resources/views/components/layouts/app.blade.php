@props(['title' => null, 'description' => null, 'metaDescription' => null, 'image' => null])

@php
    $siteTitle = $title ? $title.' | '.siteName() : siteName();
    $metaDescription = $metaDescription ?? $description ?? appSetting('worxcars.seo_default_description');
    $currency = appSetting('worxcars.currency', 'ZAR');
    $pages = \App\Models\Page::where('is_published', true)->pluck('slug')->all();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ siteName() }}">
    @if ($image)
        <meta property="og:image" content="{{ $image }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.worxcars = { csrfToken: '{{ csrf_token() }}' }
    </script>
    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    <div x-data="window.worxcarsNav ?? {}">
        <x-layouts.partials.nav :currency="$currency" />
    </div>

    <main class="flex-1">
        @if (session('status'))
            <div class="mx-auto mt-6 max-w-6xl px-4">
                <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-auto mt-6 max-w-6xl px-4">
                <div class="rounded-md bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 ring-1 ring-rose-500/30">
                    {{ session('error') }}
                </div>
            </div>
        @endif
        {{ $slot }}
    </main>

    <x-layouts.partials.footer :pages="$pages" :currency="$currency" />

    <div x-data class="fixed inset-x-0 bottom-0 z-40 lg:hidden">
        <x-layouts.partials.mobile-nav />
    </div>

    @stack('scripts')
</body>
</html>