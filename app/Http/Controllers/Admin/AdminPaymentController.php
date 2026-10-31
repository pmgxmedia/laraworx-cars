<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardPayout;
use App\Models\User;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    public function __construct(protected PayoutService $payouts)
    {
    }

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_FINANCE]), 403, 'You do not have permission to manage payouts.');

        $rows = RewardPayout::query()
            ->with(['user', 'buyerReward'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('provider'), fn ($q) => $q->where('provider', $request->input('provider')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('payout_reference', 'like', "%{$request->input('q')}%")
                ->orWhere('provider_transaction_id', 'like', "%{$request->input('q')}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$request->input('q')}%"))))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payouts.index', [
            'rows' => $rows,
            'providers' => RewardPayout::distinct()->orderBy('provider')->pluck('provider'),
        ]);
    }

    public function execute(RewardPayout $payout): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_FINANCE]), 403, 'You do not have permission to execute payouts.');

        try {
            $this->payouts->execute($payout, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $payout->fresh()->status === RewardPayout::STATUS_COMPLETED
            ? 'Payout completed successfully.'
            : 'Payout attempt failed. See the failure reason on the payout.');
    }
}