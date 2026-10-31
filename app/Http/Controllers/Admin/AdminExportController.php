<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyerEnquiry;
use App\Models\BuyerReward;
use App\Models\Commission;
use App\Models\Dealership;
use App\Models\PrivateSeller;
use App\Models\PurchaseClaim;
use App\Models\RewardPayout;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    protected const REPORTS = [
        'vehicles' => ['label' => 'Vehicles', 'model' => Vehicle::class],
        'dealerships' => ['label' => 'Dealerships', 'model' => Dealership::class],
        'sellers' => ['label' => 'Private Sellers', 'model' => PrivateSeller::class],
        'claims' => ['label' => 'Purchase Claims', 'model' => PurchaseClaim::class],
        'rewards' => ['label' => 'Buyer Rewards', 'model' => BuyerReward::class],
        'payouts' => ['label' => 'Reward Payouts', 'model' => RewardPayout::class],
        'enquiries' => ['label' => 'Enquiries', 'model' => BuyerEnquiry::class],
        'transactions' => ['label' => 'Transactions', 'model' => Transaction::class],
        'users' => ['label' => 'Users', 'model' => User::class],
        'commissions' => ['label' => 'Commissions', 'model' => Commission::class],
    ];

    public function index(): View
    {
        $reports = [];

        foreach (self::REPORTS as $key => $config) {
            $reports[$key] = [
                'label' => $config['label'],
                'count' => $config['model']::count(),
            ];
        }

        return view('admin.reports.index', compact('reports'));
    }

    public function export(string $report): StreamedResponse
    {
        if (! isset(self::REPORTS[$report])) {
            abort(404, 'Unknown report type.');
        }

        [$headings, $rows] = $this->definition($report);

        return response()->streamDownload(function () use ($headings, $rows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $headings);

            foreach ($rows() as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $report.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function definition(string $report): array
    {
        $headings = [
            'vehicles' => ['ID', 'Reference', 'Maker', 'Model', 'Variant', 'Year', 'Mileage', 'Price', 'Status', 'Seller Type', 'Featured', 'Published At', 'Created At'],
            'dealerships' => ['ID', 'Business Name', 'Email', 'Phone', 'City', 'Province', 'Status', 'Verified At', 'Active'],
            'sellers' => ['ID', 'User Email', 'Contact Email', 'Phone', 'City', 'Province', 'Status', 'Verified At', 'Active'],
            'claims' => ['ID', 'Claim Reference', 'Status', 'User Email', 'Vehicle Reference', 'Purchase Date', 'Purchase Price', 'Commission', 'Reward', 'Retained', 'Created At'],
            'rewards' => ['ID', 'Reward Reference', 'Status', 'User Email', 'Vehicle Reference', 'Amount', 'Approved At', 'Paid At', 'Created At'],
            'payouts' => ['ID', 'Payout Reference', 'Status', 'Provider', 'User Email', 'Amount', 'Provider Transaction ID', 'Requested At', 'Completed At'],
            'enquiries' => ['ID', 'Name', 'Email', 'Phone', 'Vehicle Reference', 'Status', 'Source', 'Created At'],
            'transactions' => ['ID', 'Reference', 'Type', 'Direction', 'Status', 'Amount', 'Commission', 'Reward', 'Retained', 'Claim Reference', 'Occurred At'],
            'users' => ['ID', 'Name', 'Surname', 'Email', 'Phone', 'City', 'Province', 'Active', 'Roles', 'Created At'],
            'commissions' => ['ID', 'Vehicle Reference', 'Campaign ID', 'Commission Type', 'Commission Value', 'Reward Type', 'Reward Value', 'Reward Percent', 'Commission Amount', 'Reward Amount', 'Retained', 'Recorded By'],
        ];

        $rows = match ($report) {
            'vehicles' => fn () => Vehicle::query()->lazy()->map(fn ($v) => [$v->id, $v->vehicle_reference, $v->make, $v->model, $v->variant, $v->year, $v->mileage, $v->price, $v->status, $v->seller_type, $v->is_featured ? 'Yes' : 'No', $v->published_at?->toDateTimeString(), $v->created_at?->toDateTimeString()]),
            'dealerships' => fn () => Dealership::query()->lazy()->map(fn ($d) => [$d->id, $d->business_name, $d->email, $d->phone, $d->city, $d->province, $d->status, $d->verified_at?->toDateTimeString(), $d->is_active ? 'Yes' : 'No']),
            'sellers' => fn () => PrivateSeller::with('user')->lazy()->map(fn ($s) => [$s->id, $s->user?->email, $s->contact_email, $s->phone, $s->city, $s->province, $s->status, $s->verified_at?->toDateTimeString(), $s->is_active ? 'Yes' : 'No']),
            'claims' => fn () => PurchaseClaim::with(['user', 'vehicle'])->lazy()->map(fn ($c) => [$c->id, $c->claim_reference, $c->status, $c->user?->email, $c->vehicle?->vehicle_reference, $c->purchase_date?->toDateString(), $c->purchase_price, $c->commission_amount, $c->reward_amount, $c->retained_amount, $c->created_at?->toDateTimeString()]),
            'rewards' => fn () => BuyerReward::with(['user', 'vehicle'])->lazy()->map(fn ($r) => [$r->id, $r->reward_reference, $r->status, $r->user?->email, $r->vehicle?->vehicle_reference, $r->amount, $r->approved_at?->toDateTimeString(), $r->paid_at?->toDateTimeString(), $r->created_at?->toDateTimeString()]),
            'payouts' => fn () => RewardPayout::with('user')->lazy()->map(fn ($p) => [$p->id, $p->payout_reference, $p->status, $p->provider, $p->user?->email, $p->amount, $p->provider_transaction_id, $p->requested_at?->toDateTimeString(), $p->completed_at?->toDateTimeString()]),
            'enquiries' => fn () => BuyerEnquiry::with('vehicle')->lazy()->map(fn ($e) => [$e->id, $e->name, $e->email, $e->phone, $e->vehicle?->vehicle_reference, $e->status, $e->source, $e->created_at?->toDateTimeString()]),
            'transactions' => fn () => Transaction::with('purchaseClaim')->lazy()->map(fn ($t) => [$t->id, $t->transaction_reference, $t->type, $t->direction, $t->status, $t->amount, $t->commission_amount, $t->reward_amount, $t->retained_amount, $t->purchaseClaim?->claim_reference, $t->occurred_at?->toDateTimeString()]),
            'users' => fn () => User::with('roles')->lazy()->map(fn ($u) => [$u->id, $u->name, $u->surname, $u->email, $u->phone, $u->city, $u->province, $u->is_active ? 'Yes' : 'No', $u->roles->pluck('name')->join(', '), $u->created_at?->toDateTimeString()]),
            'commissions' => fn () => Commission::with('vehicle')->lazy()->map(fn ($c) => [$c->id, $c->vehicle?->vehicle_reference, $c->campaign_id, $c->commission_type, $c->commission_value, $c->reward_type, $c->reward_value, $c->reward_percent, $c->commission_amount, $c->reward_amount, $c->retained_amount, $c->recorded_by]),
        };

        return [$headings[$report], $rows];
    }
}