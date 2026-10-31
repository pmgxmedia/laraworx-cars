<x-layouts.auth title="Set a new password">
    <h1 class="font-display text-2xl font-bold text-royal-950">Set a new password</h1>
    <p class="mt-1 text-sm text-royal-900/60">Choose a strong password you haven't used on another site.</p>

    <x-auth.errors :errors="$errors" class="mt-6" />

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required class="input" autocomplete="username">
        </div>
        <div>
            <label for="password" class="label">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="input">
        </div>
        <div>
            <label for="password_confirmation" class="label">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
        </div>
        <button type="submit" class="btn-gold w-full">Reset Password</button>
    </form>
</x-layouts.auth>