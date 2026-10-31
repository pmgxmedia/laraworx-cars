<x-layouts.auth title="Sign in">
    <h1 class="font-display text-2xl font-bold text-royal-950">Welcome back</h1>
    <p class="mt-1 text-sm text-royal-900/60">Sign in to browse vehicles, save favourites, enquire and claim your buyer reward.</p>

    <x-auth.errors :errors="$errors" class="mt-6" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input" placeholder="you@example.com">
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <div x-data="{ show: false }">
                <div class="relative">
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" class="input pr-12" placeholder="••••••••">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 text-royal-900/40 hover:text-royal-900" aria-label="Toggle password visibility">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 text-royal-900/70">
                <input type="checkbox" name="remember" class="rounded border-royal-900/30 text-gold-600 focus:ring-gold-500">
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="font-medium text-gold-700 hover:text-gold-600">Forgot password?</a>
        </div>
        <button type="submit" class="btn-gold w-full">Sign in</button>
    </form>

    <p class="mt-6 text-center text-sm text-royal-900/60">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-gold-700 hover:underline">Create one now</a>
    </p>
</x-layouts.auth>