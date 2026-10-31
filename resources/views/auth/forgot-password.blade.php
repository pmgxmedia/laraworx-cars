<x-layouts.auth title="Reset password">
    <h1 class="font-display text-2xl font-bold text-royal-950">Forgot your password?</h1>
    <p class="mt-1 text-sm text-royal-900/60">No problem. Tell us your email address and we'll send you a secure reset link.</p>

    <x-auth.errors :errors="$errors" class="mt-6" />

    @if (session('status'))
        <div class="mt-6 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input" placeholder="you@example.com">
        </div>
        <button type="submit" class="btn-gold w-full">Email Password Reset Link</button>
    </form>

    <p class="mt-6 text-center text-sm text-royal-900/60">
        <a href="{{ route('login') }}" class="font-semibold text-gold-700 hover:underline">Back to sign in</a>
    </p>
</x-layouts.auth>