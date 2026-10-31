<x-layouts.customer title="Profile">
    <h1 class="font-display text-2xl font-bold text-royal-950">Your Profile</h1>
    <p class="mt-1 text-sm text-royal-900/60">Manage the personal details used across WorxCars.</p>

    @if (session('status'))
        <div class="mt-5 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('customer.profile.update') }}" class="card mt-6 max-w-2xl p-6 sm:p-8">
        @csrf
        @method('PUT')
        <x-auth.errors :errors="$errors" />
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">First name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input">
            </div>
            <div>
                <label class="label">Surname</label>
                <input type="text" name="surname" value="{{ old('surname', $user->surname) }}" required class="input">
            </div>
            <div>
                <label class="label">Mobile number</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
            </div>
            <div>
                <label class="label">City</label>
                <input type="text" name="city" value="{{ old('city', $user->city) }}" class="input">
            </div>
            <div>
                <label class="label">Province</label>
                <select name="province" class="input">
                    <option value="">Select</option>
                    @foreach (['Gauteng', 'Western Cape', 'KwaZulu-Natal', 'Eastern Cape', 'Free State', 'Limpopo', 'Mpumalanga', 'North West', 'Northern Cape'] as $p)
                        <option value="{{ $p }}" @selected(old('province', $user->province) === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Email (locked)</label>
                <input type="email" value="{{ $user->email }}" disabled class="input opacity-60">
            </div>
        </div>
        <button type="submit" class="btn-gold mt-6">Save changes</button>
    </form>
</x-layouts.customer>