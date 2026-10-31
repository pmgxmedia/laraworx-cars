@extends('dealer.layouts.dealer')

@section('title', 'My Vehicles')

@section('header')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Your stock</p>
            <h2 class="mt-1 section-title">My Vehicles</h2>
        </div>
        <a href="{{ route('dealer.vehicles.create') }}" class="btn-gold btn-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add vehicle
        </a>
    </div>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-royal-900/10 px-5 py-4">
            <p class="text-sm text-royal-900/60">{{ $vehicles->total() }} vehicle{{ $vehicles->total() !== 1 ? 's' : '' }}</p>
            <form method="GET" action="{{ route('dealer.vehicles') }}" class="flex items-center gap-2">
                <select name="status" class="input !w-48 !py-2" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($filterStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($filterStatus)
                    <a href="{{ route('dealer.vehicles') }}" class="btn-ghost btn-sm">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Reward</th>
                        <th>Views</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $vehicle)
                        @php
                            $image = $vehicle->activeImages->firstWhere('is_main', true) ?? $vehicle->activeImages->first();
                            $reward = $vehicle->effective_reward_amount ?? $vehicle->activeRewardAmount();
                        @endphp
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-16 shrink-0 overflow-hidden rounded-md bg-royal-900">
                                        @if ($image)
                                            <img src="{{ asset('storage/'.$image->thumbnail_path) }}" alt="{{ $image->alt_text }}" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-royal-950">{{ $vehicle->displayName() }}</p>
                                        <p class="text-xs text-royal-900/50">{{ $vehicle->year }} · {{ number_format($vehicle->mileage) }} km · {{ $vehicle->location_city }}</p>
                                        @if ($vehicle->campaign)
                                            <p class="text-xs font-semibold text-gold-700">Campaign: {{ $vehicle->campaign->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap text-xs">{{ $vehicle->vehicle_reference }}</td>
                            <td>
                                @include('dealer.components.status-badge', ['type' => 'vehicle', 'status' => $vehicle->status, 'label' => $vehicle->statusLabel()])
                            </td>
                            <td class="whitespace-nowrap font-semibold text-royal-950">{{ money($vehicle->price) }}</td>
                            <td class="whitespace-nowrap">{{ $reward > 0 ? money($reward) : '—' }}</td>
                            <td>{{ number_format($vehicle->views_count) }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @if ($vehicle->status === \App\Models\Vehicle::STATUS_PUBLISHED)
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-ghost btn-sm" title="View on site">View</a>
                                    @endif
                                    @if (in_array($vehicle->status, [\App\Models\Vehicle::STATUS_DRAFT, \App\Models\Vehicle::STATUS_REJECTED]))
                                        <form method="POST" action="{{ route('dealer.vehicles.submit', $vehicle) }}">
                                            @csrf
                                            <button type="submit" class="btn-gold btn-sm">Submit</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('dealer.vehicles.edit', $vehicle) }}" class="btn-royal btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('dealer.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle listing? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost btn-sm !text-rose-600 hover:!border-rose-300" title="Delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                                    <div class="grid h-16 w-16 place-items-center rounded-full bg-royal-950">
                                        <svg class="h-8 w-8 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                    </div>
                                    <h3 class="mt-4 font-display text-xl font-bold text-royal-950">No vehicles found</h3>
                                    <p class="mt-2 max-w-sm text-sm text-royal-900/60">List your first vehicle and start earning buyer rewards on every qualifying sale.</p>
                                    <a href="{{ route('dealer.vehicles.create') }}" class="btn-gold btn-sm mt-5">Add your first vehicle</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($vehicles->hasPages())
            <div class="border-t border-royal-900/10 px-5 py-4">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
@endsection