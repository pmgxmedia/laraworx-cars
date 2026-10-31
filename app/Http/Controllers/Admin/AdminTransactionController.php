<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseClaim;
use App\Models\RewardPayout;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTransactionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_FINANCE]), 403, 'You do not have permission to view transactions.');

        $rows = Transaction::query()
            ->with(['vehicle', 'user', 'dealership', 'purchaseClaim'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('direction'), fn ($q) => $q->where('direction', $request->input('direction')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('transaction_reference', 'like', "%{$request->input('q')}%")
                ->orWhere('description', 'like', "%{$request->input('q')}%")))
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.transactions.index', compact('rows'));
    }

    public function finance(): View
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_FINANCE]), 403, 'You do not have permission to view the financial dashboard.');

        $paidOut = (float) RewardPayout::where('status', RewardPayout::STATUS_COMPLETED)->sum('amount');
        $pendingPayouts = (float) RewardPayout::whereIn('status', [RewardPayout::STATUS_SCHEDULED, RewardPayout::STATUS_PROCESSING])->sum('amount');
        $commissionsEarned = (float) Transaction::where('type', 'commission')->where('direction', 'in')->sum('amount');
        $retained = (float) Transaction::where('type', 'commission')->sum('retained_amount');
        $outstandingRewards = (float) PurchaseClaim::whereIn('status', [PurchaseClaim::STATUS_REWARD_APPROVED, PurchaseClaim::STATUS_PAYMENT_PROCESSING])->sum('reward_amount');

        $stats = [
            'paid_out' => $paidOut,
            'pending_payouts' => $pendingPayouts,
            'outstanding_rewards' => $outstandingRewards,
            'commissions_earned' => $commissionsEarned,
            'retained' => $retained,
            'payout_count' => RewardPayout::count(),
            'completed_payouts' => RewardPayout::where('status', RewardPayout::STATUS_COMPLETED)->count(),
            'failed_payouts' => RewardPayout::where('status', RewardPayout::STATUS_FAILED)->count(),
            'commission_count' => Transaction::where('type', 'commission')->count(),
            'payout_count_out' => Transaction::where('type', 'payout')->count(),
            'average_payout' => RewardPayout::where('status', RewardPayout::STATUS_COMPLETED)->avg('amount') ?: 0,
            'claims_paid' => PurchaseClaim::where('status', PurchaseClaim::STATUS_PAID)->count(),
            'claims_approved' => PurchaseClaim::whereIn('status', [PurchaseClaim::STATUS_REWARD_APPROVED, PurchaseClaim::STATUS_PAYMENT_PROCESSING, PurchaseClaim::STATUS_PAID])->count(),
        ];

        $recent = Transaction::with(['vehicle', 'user'])
            ->orderByDesc('occurred_at')
            ->limit(10)
            ->get();

        return view('admin.finance', compact('stats', 'recent'));
    }
}