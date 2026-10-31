@extends('dealer.layouts.dealer')

@section('title', 'Edit '.$vehicle->displayName())

@section('header')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gold-600">{{ $vehicle->vehicle_reference }}</p>
            <h2 class="mt-1 section-title">{{ $vehicle->displayName() }}</h2>
            <p class="mt-2 flex flex-wrap items-center gap-2 text-sm text-royal-900/60">
                <span>{{ $vehicle->year }} · {{ number_format($vehicle->mileage) }} km</span>
                <span class="h-1 w-1 rounded-full bg-royal-900/30"></span>
                <span>{{ money($vehicle->price) }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            @include('dealer.components.status-badge', ['type' => 'vehicle', 'status' => $vehicle->status, 'label' => $vehicle->statusLabel()])
            @if ($vehicle->status === \App\Models\Vehicle::STATUS_PUBLISHED)
                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-ghost btn-sm">View listing</a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('dealer.vehicles.update', $vehicle) }}" enctype="multipart/form-data" class="card p-6">
        @csrf
        @method('PUT')

        @include('dealer.vehicles._form', ['vehicle' => $vehicle])

        <div class="mt-8 flex flex-wrap items-center justify-end gap-3 border-t border-royal-900/10 pt-5">
            @if (in_array($vehicle->status, [\App\Models\Vehicle::STATUS_DRAFT, \App\Models\Vehicle::STATUS_REJECTED]))
                <button type="submit" name="action" value="submit" class="btn-gold">Save &amp; submit for approval</button>
            @endif
            <button type="submit" name="action" value="save" class="btn-royal">Save changes</button>
        </div>
    </form>

    <div class="mt-6">
        @include('dealer.vehicles.images-manager')
    </div>

    <div class="mt-6 card p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-royal-950">Delete this listing</p>
                <p class="text-xs text-royal-900/55">Removes the vehicle and all its images. This cannot be undone.</p>
            </div>
            <form method="POST" action="{{ route('dealer.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle listing permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-ghost btn-sm !text-rose-600 hover:!border-rose-300">Delete vehicle</button>
            </form>
        </div>
    </div>
@endsection