@extends('dealer.layouts.dealer')

@section('title', 'Reports')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Performance</p>
        <h2 class="mt-1 section-title">Monthly Reports</h2>
        <p class="mt-2 text-sm text-royal-900/60">Your activity over the last 12 months — listings, published vehicles, enquiries, confirmed sales and commission earned.</p>
    </div>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Listed</th>
                        <th>Published</th>
                        <th>Enquiries</th>
                        <th>Sales</th>
                        <th>Commission earned</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($months as $month)
                        <tr>
                            <td class="whitespace-nowrap font-semibold text-royal-950">{{ $month['label'] }}</td>
                            <td>{{ $month['listed'] }}</td>
                            <td>{{ $month['published'] }}</td>
                            <td>{{ $month['enquiries'] }}</td>
                            <td>{{ $month['sales'] }}</td>
                            <td class="whitespace-nowrap font-semibold text-gold-700">{{ money($month['commission']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-royal-950">
                        <td class="font-bold text-ivory-50">Total</td>
                        <td class="font-bold text-ivory-50">{{ $months->sum('listed') }}</td>
                        <td class="font-bold text-ivory-50">{{ $months->sum('published') }}</td>
                        <td class="font-bold text-ivory-50">{{ $months->sum('enquiries') }}</td>
                        <td class="font-bold text-ivory-50">{{ $months->sum('sales') }}</td>
                        <td class="whitespace-nowrap font-bold text-gold-300">{{ money($months->sum('commission')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6 rounded-lg bg-gradient-to-r from-royal-950 to-royal-850 p-5 ring-1 ring-gold-500/30">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Need the numbers elsewhere?</p>
                <p class="mt-1 text-sm text-ivory-100/70">The table above is CSV-friendly — select it in your browser and paste into a spreadsheet, or contact support for an export.</p>
            </div>
            <a href="{{ route('dealer.dashboard') }}" class="btn-ghost-dark btn-sm">Back to dashboard</a>
        </div>
    </div>
@endsection