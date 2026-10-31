<x-admin.layouts.admin title="Edit {{ $campaign->name }}" :breadcrumbs="[['label' => 'Campaigns', 'url' => route('admin.campaigns.index')], ['label' => $campaign->name, 'url' => route('admin.campaigns.show', $campaign)], ['label' => 'Edit']]">
    <div class="mb-6">
        <h1 class="font-display text-3xl font-black text-royal-950">Edit Campaign</h1>
        <p class="mt-1 text-sm text-royal-900/60">{{ $campaign->name }}</p>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    @if ($errors->any())
        <div class="mb-4 mt-6 rounded-md bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-500/30">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.campaigns._form')
    </form>
</x-admin.layouts.admin>