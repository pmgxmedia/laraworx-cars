@extends('dealer.layouts.dealer')

@section('title', 'Commissions')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Earnings</p>
        <h2 class="mt-1 section-title">Commissions &amp; Rewards</h2>
        <p class="mt-2 text-sm text-royal-900/60">WorxCars charges a commission on qualifying sales and rewards the buyer out of it. Below is your commission configuration and confirmed earnings.</p>
    </div>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Configured commission</p>
            <p class="mt-2 font-display text-3xl font-black text-royal-950">{{ money($totals['configured']) }}</p>
            <p class="mt-1 text-xs text-royal-900/50">Expected across current stock</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Confirmed sales</p>
            <p class="mt-2 font-display text-3xl font-black text-royal-950">{{ money($totals['confirmed']) }}</p>
            <p class="mt-1 text-xs text-royal-900/50">Commission on verified purchases</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider text-royal-900/50">Rewards paid out</p>
            <p class="mt-2 font-display text-3xl font-black text-royal-950">{{ money($totals['paid_out']) }}</p>
            <p class="mt-1 text-xs text-royal-900/50">Buyer rewards settled on your vehicles</p>
        </div>
    </div>

    <div class="mt-8 card overflow-hidden">
        <div class="border-b border-royal-900/10 px-5 py-4">
            <h3 class="font-display text-lg font-bold text-royal-950">Commission records</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Type</th>
                        <th>Commission</th>
                        <th>Buyer reward</th>
                        <th>Retained</th>
                        <th>Campaign</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($commissions as $commission)
                        <tr>
                            <td>
                                <p class="font-semibold text-royal-950">{{ $commission->vehicle?->displayName() ?? 'Vehicle' }}</p>
                                <p class="text-xs text-royal-900/50">{{ $commission->vehicle?->vehicle_reference }}</p>
                            </td>
                            <td>
                                <span class="text-sm capitalize">{{ $commission->commission_type }}</span>
                                <p class="text-xs text-royal-900/50">{{ $commission->commission_value ? rtrim(rtrim(number_format($commission->commission_value, 2), '0'), '.').($commission->commission_type === 'percent' ? '%' : '') : '—' }}</p>
                            </td>
                            <td class="whitespace-nowrap font-semibold text-royal-950">{{ $commission->commission_amount ? money($commission->commission_amount) : '—' }}</td>
                            <td class="whitespace-nowrap font-semibold text-gold-700">{{ $commission->reward_amount ? money($commission->reward_amount) : '—' }}</td>
                            <td class="whitespace-nowrap">{{ $commission->retained_amount ? money($commission->retained_amount) : '—' }}</td>
                            <td>{{ $commission->campaign?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="px-6 py-12 text-center text-sm text-royal-900/50">No commission records yet. Commission is configured by the WorxCars team when your listings are approved.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 card overflow-hidden">
        <div class="border-b border-royal-900/10 px-5 py-4">
            <h3 class="font-display text-lg font-bold text-royal-950">Buyer reward payouts on your vehicles</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Payout</th>
                        <th>Buyer</th>
                        <th>Vehicle</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payouts as $payout)
                        <tr>
                            <td class="whitespace-nowrap text-xs font-semibold text-royal-950">{{ $payout->payout_reference }}</td>
                            <td>{{ $payout->user?->fullName() }}</td>
                            <td>{{ $payout->buyerReward?->vehicle?->displayName() ?? '—' }}</td>
                            <td class="whitespace-nowrap font-semibold text-gold-700">{{ money($payout->amount) }}</td>
                            <td>
                                @include('dealer.components.status-badge', ['type' => 'payout', 'status' => $payout->status, 'label' => ucwords(str_replace('_', ' ', $payout->status))])
                            </td>
                            <td class="whitespace-nowrap">{{ $payout->completed_at?->format('d M Y') ?? $payout->requested_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="px-6 py-12 text-center text-sm text-royal-900/50">No payouts yet. Once buyer rewards are approved and paid, they will be listed here.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection