@extends('dealer.layouts.dealer')

@section('title', 'Enquiries')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Buyer interest</p>
        <h2 class="mt-1 section-title">Enquiries</h2>
        <p class="mt-2 text-sm text-royal-900/60">Every enquiry is from a genuine buyer viewing your vehicles. Update the status as you respond and qualify them.</p>
    </div>
@endsection

@section('content')
    <div class="space-y-4">
        @forelse ($enquiries as $enquiry)
            @php
                $image = $enquiry->vehicle?->activeImages->firstWhere('is_main', true) ?? $enquiry->vehicle?->activeImages->first();
            @endphp
            <form method="POST" action="{{ route('dealer.enquiries.update', $enquiry) }}" class="card p-5">
                @csrf
                @method('PUT')

                <div class="flex flex-wrap items-start gap-3">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-royal-950 text-xs font-black text-gold-400">{{ strtoupper(mb_substr($enquiry->name, 0, 1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <p class="font-semibold text-royal-950">{{ $enquiry->name }}</p>
                            <a href="mailto:{{ $enquiry->email }}" class="text-xs text-gold-700 hover:underline">{{ $enquiry->email }}</a>
                            @if ($enquiry->phone)
                                <a href="tel:{{ $enquiry->phone }}" class="text-xs text-royal-900/55">{{ $enquiry->phone }}</a>
                            @endif
                            <span class="text-xs text-royal-900/40">· {{ $enquiry->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm font-medium text-royal-900/80">
                            Re: {{ $enquiry->vehicle?->displayName() ?? 'Vehicle' }}
                            @if ($enquiry->vehicle)
                                <span class="text-royal-900/40">({{ $enquiry->vehicle->vehicle_reference }})</span>
                            @endif
                        </p>
                        @if ($enquiry->preferred_date)
                            <p class="mt-1 text-xs text-royal-900/55">Preferred date: {{ $enquiry->preferred_date->format('d M Y') }} · Contact via {{ ucfirst($enquiry->preferred_contact_method) }}</p>
                        @endif
                        @if ($enquiry->message)
                            <p class="mt-2 whitespace-pre-line rounded-md bg-ivory-100/70 px-3 py-2 text-sm leading-relaxed text-royal-900/70">{{ $enquiry->message }}</p>
                        @endif
                    </div>
                    @include('dealer.components.status-badge', ['type' => 'enquiry', 'status' => $enquiry->status, 'label' => $enquiry->statusLabel()])
                </div>

                <div class="mt-4 grid gap-3 border-t border-royal-900/10 pt-4 sm:grid-cols-[220px_1fr_auto]">
                    <div>
                        <label class="label">Status</label>
                        <select name="status" class="input">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($enquiry->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Internal note</label>
                        <input type="text" name="internal_note" value="{{ old('internal_note', $enquiry->internal_note) }}" placeholder="e.g. called back, test drive booked" maxlength="2000" class="input">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-royal btn-sm w-full sm:w-auto">Update enquiry</button>
                    </div>
                </div>
            </form>
        @empty
            <div class="card flex flex-col items-center justify-center px-6 py-14 text-center">
                <div class="grid h-16 w-16 place-items-center rounded-full bg-royal-950">
                    <svg class="h-8 w-8 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                </div>
                <h3 class="mt-4 font-display text-xl font-bold text-royal-950">No enquiries yet</h3>
                <p class="mt-2 max-w-sm text-sm text-royal-900/60">Buyer enquiries on your vehicles will appear here as they come in.</p>
            </div>
        @endforelse
    </div>

    @if ($enquiries->hasPages())
        <div class="mt-6">
            {{ $enquiries->links() }}
        </div>
    @endif
@endsection