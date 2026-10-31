<x-layouts.auth title="Verify your email">
    <h1 class="font-display text-2xl font-bold text-royal-950">Verify your email</h1>
    <p class="mt-1 text-sm text-royal-900/60">Before you can make a claim or use buyer features, please confirm your email address.</p>

    @if (session('status'))
        <div class="mt-6 rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 rounded-lg bg-ivory-100 p-4 text-sm text-royal-900/75 ring-1 ring-royal-900/10">
        <p class="font-semibold text-royal-950">Check your inbox</p>
        <p class="mt-1">A verification link was sent to <span class="font-semibold">{{ auth()->user()?->email }}</span>. Click the link in the email to activate your account. If you can't find it, check your spam folder or resend the link below.</p>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6 space-y-4">
        @csrf
        <button type="submit" class="btn-gold w-full">Resend verification link</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full rounded-md px-5 py-2.5 text-center text-sm font-semibold text-royal-900/60 transition hover:text-royal-900">Sign out</button>
    </form>
</x-layouts.auth>