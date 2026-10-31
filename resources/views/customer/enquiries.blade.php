<x-layouts.customer title="Enquiries">
    <h1 class="font-display text-2xl font-bold text-royal-950">My Enquiries</h1>
    <p class="mt-1 text-sm text-royal-900/60">Messages you've sent to sellers about vehicles.</p>

    @if ($enquiries->isEmpty())
        <div class="card mt-6 p-14 text-center">
            <h2 class="font-display text-xl font-bold text-royal-950">No enquiries yet</h2>
            <p class="mt-2 text-sm text-royal-900/60">Send a seller a message from any vehicle page.</p>
            <a href="{{ route('vehicles.index') }}" class="btn-gold btn-sm mt-5">Browse vehicles</a>
        </div>
    @else
        <div class="card mt-6 overflow-hidden">
            <div class="divide-y divide-royal-900/5">
                @foreach ($enquiries as $enquiry)
                    <a href="{{ route('vehicles.show', $enquiry->vehicle) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-ivory-100/60">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-royal-950">{{ $enquiry->vehicle?->displayName() }}</p>
                            <p class="mt-0.5 line-clamp-1 text-xs text-royal-900/55">{{ $enquiry->message }}</p>
                            <p class="mt-1 text-xs text-royal-900/45">{{ $enquiry->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <span class="badge {{ match ($enquiry->status) { 'new' => 'badge-blue', 'contacted' => 'badge-amber', 'qualified' => 'badge-gold', 'appointment' => 'badge-grey', 'converted' => 'badge-green', 'closed' => 'badge-grey', 'spam' => 'badge-red', default => 'badge-grey' } }}">{{ $enquiry->statusLabel() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="mt-10">{{ $enquiries->links() }}</div>
    @endif
</x-layouts.customer>