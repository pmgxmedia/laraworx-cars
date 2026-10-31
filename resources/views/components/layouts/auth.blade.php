@props(['title' => 'WorxCars'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ siteName() }}</title>
    <meta name="description" content="Sign in to your WorxCars account.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-royal-950">
    <div class="flex min-h-screen">
        <div class="relative hidden w-1/2 lg:block">
            <div class="absolute inset-0 bg-gradient-to-br from-royal-900 via-royal-950 to-black"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(201,162,39,0.28),transparent_45%)]"></div>
            <div class="relative flex h-full flex-col justify-between p-12">
                <a href="/" class="flex items-center gap-3">
                    <span class="grid h-12 w-12 place-items-center rounded-lg bg-royal-900 ring-1 ring-gold-500/60">
                        <span class="font-display text-2xl font-black text-gold-400">W</span>
                    </span>
                    <span class="font-display text-2xl font-black tracking-tight text-ivory-50">WorxCars</span>
                </a>
                <div>
                    <h1 class="font-display text-4xl font-black leading-tight text-ivory-50">BUY YOUR CAR.<br><span class="text-gold-400">GET PAID.</span></h1>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-ivory-100/60">Find your next vehicle from trusted sellers and receive a WorxCars Buyer Reward when your qualifying purchase is successfully completed and verified.</p>
                </div>
                <p class="text-xs text-ivory-100/40">Every vehicle has a story. Every qualifying purchase has a reward.</p>
            </div>
        </div>

        <div class="flex w-full items-center justify-center bg-ivory-50 lg:w-1/2">
            <div class="w-full max-w-md px-6 py-12">
                <a href="/" class="mb-8 flex items-center gap-2 lg:hidden">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-royal-950 ring-1 ring-gold-500/60">
                        <span class="font-display text-lg font-black text-gold-400">W</span>
                    </span>
                    <span class="font-display text-xl font-black text-royal-950">WorxCars</span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>