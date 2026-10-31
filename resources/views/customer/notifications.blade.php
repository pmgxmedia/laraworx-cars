<x-layouts.customer title="Notifications">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl font-bold text-royal-950">Notifications</h1>
        <a href="{{ route('customer.dashboard') }}" class="btn-ghost btn-sm">Back to dashboard</a>
    </div>

    @if ($notifications->isEmpty())
        <div class="card mt-6 p-14 text-center">
            <h2 class="font-display text-xl font-bold text-royal-950">You're all caught up</h2>
            <p class="mt-2 text-sm text-royal-900/60">Updates about your claims, rewards and enquiries will appear here.</p>
        </div>
    @else
        <div class="card mt-6 overflow-hidden divide-y divide-royal-900/5">
            @foreach ($notifications as $notification)
                <div class="flex items-start gap-3 px-5 py-4">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-royal-900/20' : 'bg-gold-500' }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold {{ $notification->read_at ? 'text-royal-900/60' : 'text-royal-950' }}">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <p class="mt-0.5 text-xs text-royal-900/55">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-royal-900/40">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if ($notification->data['url'] ?? null)
                        <a href="{{ $notification->data['url'] }}" class="shrink-0 text-xs font-medium text-gold-700 hover:underline">View</a>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-10">{{ $notifications->links() }}</div>
    @endif
</x-layouts.customer>