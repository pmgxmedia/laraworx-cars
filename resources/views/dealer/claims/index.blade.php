@extends('dealer.layouts.dealer')

@section('title', 'Claims')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Purchase rewards</p>
        <h2 class="mt-1 section-title">Purchase Claims</h2>
        <p class="mt-2 text-sm text-royal-900/60">When a buyer confirms a qualifying purchase of one of your vehicles, confirm the sale so WorxCars can verify and pay the buyer reward.</p>
    </div>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Claim</th>
                        <th>Vehicle</th>
                        <th>Buyer</th>
                        <th>Purchase date</th>
                        <th>Price</th>
                        <th>Reward</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($claims as $claim)
                        @php
                            $image = $claim->vehicle?->activeImages->firstWhere('is_main', true) ?? $claim->vehicle?->activeImages->first();
                            $canConfirm = in_array($claim->status, $confirmable);
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap text-xs">
                                <p class="font-semibold text-royal-950">{{ $claim->claim_reference }}</p>
                                <p class="text-royal-900/50">{{ $claim->created_at->format('d M Y') }}</p>
                                @if ($claim->provider_reference)
                                    <p class="text-royal-900/50">Ref: {{ $claim->provider_reference }}</p>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-14 shrink-0 overflow-hidden rounded-md bg-royal-900">
                                        @if ($image)
                                            <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-royal-950">{{ $claim->vehicle?->displayName() ?? 'Vehicle unavailable' }}</p>
                                        <p class="text-xs text-royal-900/50">{{ $claim->vehicle?->vehicle_reference }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="font-medium text-royal-950">{{ $claim->user->fullName() }}</p>
                                <p class="text-xs text-royal-900/50">{{ $claim->user->email }}</p>
                            </td>
                            <td class="whitespace-nowrap">{{ $claim->purchase_date?->format('d M Y') ?? '—' }}</td>
                            <td class="whitespace-nowrap">{{ $claim->purchase_price ? money($claim->purchase_price) : '—' }}</td>
                            <td class="whitespace-nowrap font-semibold text-gold-700">{{ $claim->reward_amount ? money($claim->reward_amount) : '—' }}</td>
                            <td>
                                @include('dealer.components.status-badge', ['type' => 'claim', 'status' => $claim->status, 'label' => $claim->statusLabel()])
                            </td>
                            <td>
                                @if ($canConfirm)
                                    <form method="POST" action="{{ route('dealer.claims.confirm', $claim) }}" class="flex items-center justify-end gap-2" x-data="{ open: false }">
                                        @csrf
                                        <input type="text" name="note" x-show="open" x-collapse placeholder="Confirmation note (optional)" maxlength="2000" class="input !w-48 !py-2">
                                        <button type="button" @click="open = !open" x-text="open ? 'Hide note' : 'Add note'" class="btn-ghost btn-sm">Add note</button>
                                        <button type="submit" class="btn-gold btn-sm" onclick="return confirm('Confirm this sale happened? The buyer will progress to reward verification.')">Confirm sale</button>
                                    </form>
                                @else
                                    <span class="block text-right text-xs text-royal-900/40">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                                    <div class="grid h-16 w-16 place-items-center rounded-full bg-royal-950">
                                        <svg class="h-8 w-8 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="mt-4 font-display text-xl font-bold text-royal-950">No purchase claims</h3>
                                    <p class="mt-2 max-w-sm text-sm text-royal-900/60">When buyers submit reward claims on your vehicles, they'll appear here for you to confirm.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($claims->hasPages())
            <div class="border-t border-royal-900/10 px-5 py-4">
                {{ $claims->links() }}
            </div>
        @endif
    </div>
@endsection