<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BuyerEnquiry;
use App\Models\BuyerReward;
use App\Models\Dealership;
use App\Models\PurchaseClaim;
use App\Models\RewardPayout;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'vehicles' => Vehicle::count(),
            'vehicles_pending' => Vehicle::where('status', Vehicle::STATUS_PENDING_APPROVAL)->count(),
            'rewards_paid' => round((float) BuyerReward::where('status', BuyerReward::STATUS_PAID)->sum('amount'), 2),
            'outstanding_payouts' => round((float) RewardPayout::whereIn('status', [RewardPayout::STATUS_SCHEDULED, RewardPayout::STATUS_PROCESSING])->sum('amount'), 2),
            'claims_pending' => PurchaseClaim::whereIn('status', [
                PurchaseClaim::STATUS_SUBMITTED,
                PurchaseClaim::STATUS_UNDER_REVIEW,
                PurchaseClaim::STATUS_SELLER_VERIFICATION,
                PurchaseClaim::STATUS_TRANSACTION_VERIFIED,
            ])->count(),
            'enquiries_new' => BuyerEnquiry::where('status', BuyerEnquiry::STATUS_NEW)->count(),
            'dealerships_pending' => Dealership::where('status', Dealership::STATUS_PENDING)->count(),
            'commission_earned' => round((float) Transaction::where('type', 'commission')->where('direction', 'in')->sum('amount'), 2),
        ];

        $recentClaims = PurchaseClaim::with(['user', 'vehicle'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $pendingVehicles = Vehicle::with(['dealership', 'privateSeller.user'])
            ->where('status', Vehicle::STATUS_PENDING_APPROVAL)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $lowActivity = [
            'vehicles' => Vehicle::where('status', Vehicle::STATUS_PENDING_APPROVAL)
                ->where('created_at', '<=', now()->subDays(1))->count(),
            'claims' => PurchaseClaim::whereIn('status', [PurchaseClaim::STATUS_SUBMITTED, PurchaseClaim::STATUS_SELLER_VERIFICATION])
                ->where('created_at', '<=', now()->subDays(2))->count(),
            'enquiries' => BuyerEnquiry::where('status', BuyerEnquiry::STATUS_NEW)
                ->where('created_at', '<=', now()->subDays(2))->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentClaims', 'pendingVehicles', 'lowActivity'));
    }

    public function auditLogs(Request $request): View
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('action'), fn ($q) => $q->where('action', 'like', "%{$request->input('action')}%"))
            ->when($request->filled('entity'), fn ($q) => $q->where('entity_type', $request->input('entity')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('description', 'like', "%{$request->input('q')}%")
                ->orWhere('action', 'like', "%{$request->input('q')}%")))
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        $actions = AuditLog::distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs', compact('logs', 'actions'));
    }
}