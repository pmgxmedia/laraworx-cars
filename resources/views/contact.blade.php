<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">We're here to help</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">Contact WorxCars</h1>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-auth.errors :errors="$errors" />
        @if (session('status'))
            <div class="mt-4 rounded-md bg-emerald-50 p-4 text-sm font-medium text-emerald-800 ring-1 ring-emerald-500/30">{{ session('status') }}</div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
            <form method="POST" action="{{ route('contact.store') }}" class="card p-6 sm:p-8">
                @csrf
                <h2 class="font-display text-xl font-bold text-royal-950">Send us a message</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Your name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Subject *</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required class="input" placeholder="Buyer reward, partnership, support...">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Message *</label>
                        <textarea name="message" rows="6" required class="input"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-gold mt-6">Send message</button>
            </form>

            <aside class="space-y-5">
                <div class="card p-6">
                    <h3 class="font-display text-lg font-bold text-royal-950">Contact details</h3>
                    <div class="mt-4 space-y-4 text-sm text-royal-900/70">
                        <p class="flex items-start gap-3"><span class="text-gold-600">Email:</span><span>{{ $contact['email'] }}</span></p>
                        <p class="flex items-start gap-3"><span class="text-gold-600">Phone:</span><span>{{ $contact['phone'] }}</span></p>
                        <p class="flex items-start gap-3"><span class="text-gold-600">Address:</span><span>{{ $contact['address'] }}</span></p>
                        <p class="flex items-start gap-3"><span class="text-gold-600">Hours:</span><span>{{ $contact['hours'] }}</span></p>
                    </div>
                </div>
                <div class="card p-6 text-sm text-royal-900/70">
                    Looking for reward help? Start in your <a href="{{ route('customer.dashboard') }}" class="font-medium text-gold-700 hover:underline">account dashboard</a> where your claims and rewards live.
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>