<x-admin.layouts.admin title="Reports" :breadcrumbs="[['label' => 'Reports']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Data Exports</h1>
            <p class="mt-1 text-sm text-royal-900/60">Download CSV snapshots of the platform data for analysis and reconciliation.</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($reports as $key => $report)
            <div class="card flex items-center justify-between gap-4 p-5">
                <div>
                    <p class="font-display text-lg font-bold text-royal-950">{{ $report['label'] }}</p>
                    <p class="mt-1 text-xs text-royal-900/55">{{ number_format($report['count']) }} record(s)</p>
                </div>
                <a href="{{ route('admin.reports.export', $key) }}" class="btn-gold btn-sm shrink-0">Download CSV</a>
            </div>
        @endforeach
    </div>
</x-admin.layouts.admin>