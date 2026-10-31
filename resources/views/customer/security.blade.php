<x-layouts.customer title="Security">
    <h1 class="font-display text-2xl font-bold text-royal-950">Security</h1>
    <p class="mt-1 text-sm text-royal-900/60">Keep your account secure. Change your password regularly.</p>

    @if (session('status'))
        <div class="mt-5 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('customer.security.update') }}" class="card mt-6 max-w-2xl p-6 sm:p-8">
        @csrf
        @method('PUT')
        <x-auth.errors :errors="$errors" />
        <div class="mt-4 space-y-4">
            <div>
                <label class="label">Current password</label>
                <input type="password" name="current_password" required autocomplete="current-password" class="input">
            </div>
            <div>
                <label class="label">New password</label>
                <input type="password" name="password" required autocomplete="new-password" class="input">
            </div>
            <div>
                <label class="label">Confirm new password</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password" class="input">
            </div>
        </div>
        <button type="submit" class="btn-gold mt-6">Update password</button>
    </form>
</x-layouts.customer>