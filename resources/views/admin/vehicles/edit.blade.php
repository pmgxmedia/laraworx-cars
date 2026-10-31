<x-admin.layouts.admin title="Edit {{ $vehicle->displayName() }}" :breadcrumbs="[['label' => 'Vehicles', 'url' => route('admin.vehicles.index')], ['label' => $vehicle->displayName(), 'url' => route('admin.vehicles.show', $vehicle)], ['label' => 'Edit']]">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Edit Vehicle</h1>
            <p class="mt-1 text-sm text-royal-900/60">{{ $vehicle->displayName() }} · {{ $vehicle->vehicle_reference }}</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-500/30">
            <p class="font-semibold">There were errors with your submission:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.vehicles._form')
    </form>
</x-admin.layouts.admin>