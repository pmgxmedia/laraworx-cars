<x-admin.layouts.admin title="New Vehicle" :breadcrumbs="[['label' => 'Vehicles', 'url' => route('admin.vehicles.index')], ['label' => 'New Vehicle']]">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">New Vehicle</h1>
            <p class="mt-1 text-sm text-royal-900/60">Create a vehicle listing directly from the admin panel.</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('admin.vehicles._form')
    </form>
</x-admin.layouts.admin>