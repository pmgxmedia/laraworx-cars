<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyerReward;
use App\Services\ClaimService;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRewardController extends Controller
{
    public function __construct(protected ClaimService $claims, protected PayoutService $payouts)
    {
    }

    public function index(Request $request): View
    {
        $rows = BuyerReward::query()
            ->with(['user', 'vehicle', 'purchaseClaim', 'campaign'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('reward_reference', 'like', "%{$request->input('q')}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$request->input('q')}%")
                    ->orWhere('name', 'like', "%{$request->input('q')}%"))
                ->orWhereHas('vehicle', fn ($v) => $v->where('make', 'like', "%{$request->input('q')}%")
                    ->orWhere('model', 'like', "%{$request->input('q')}%"))))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.rewards.index', [
            'rows' => $rows,
            'statuses' => BuyerReward::statuses(),
        ]);
    }

    public function show(BuyerReward $reward): View
    {
        $reward->load(['user', 'vehicle', 'purchaseClaim.documents', 'campaign', 'payout']);

        return view('admin.rewards.show', [
            'reward' => $reward,
            'statuses' => BuyerReward::statuses(),
        ]);
    }

    public function approve(BuyerReward $reward): RedirectResponse
    {
        $claim = $reward->purchaseClaim;

        if (! $claim) {
            return back()->with('error', 'This reward has no attached purchase claim.');
        }

        try {
            $this->claims->approveReward($claim, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Reward approved and payout scheduled.');
    }

    public function reject(Request $request, BuyerReward $reward): RedirectResponse
    {
        $claim = $reward->purchaseClaim;

        if (! $claim) {
            return back()->with('error', 'This reward has no attached purchase claim.');
        }

        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        try {
            $this->claims->reject($claim, $data['reason'], auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Reward rejected.');
    }

    public function pay(BuyerReward $reward): RedirectResponse
    {
        $payout = $reward->payout;

        if (! $payout) {
            return back()->with('error', 'No payout has been scheduled for this reward yet. Approve the claim to schedule one.');
        }

        try {
            $this->payouts->execute($payout, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payout executed successfully.');
    }
}