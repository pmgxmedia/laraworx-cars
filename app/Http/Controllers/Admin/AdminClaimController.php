<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PurchaseClaim;
use App\Services\ClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminClaimController extends Controller
{
    public function __construct(protected ClaimService $claims)
    {
    }

    public function index(Request $request): View
    {
        $rows = PurchaseClaim::query()
            ->with(['user', 'vehicle', 'buyerReward'])
            ->withCount('documents')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('claim_reference', 'like', "%{$request->input('q')}%")
                ->orWhere('provider_reference', 'like', "%{$request->input('q')}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$request->input('q')}%")
                    ->orWhere('name', 'like', "%{$request->input('q')}%")
                    ->orWhere('surname', 'like', "%{$request->input('q')}%"))))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.claims.index', [
            'rows' => $rows,
            'statuses' => PurchaseClaim::statuses(),
        ]);
    }

    public function show(PurchaseClaim $claim): View
    {
        $claim->load(['user', 'vehicle.dealership', 'vehicle.privateSeller.user', 'documents', 'buyerReward.payout', 'dealership', 'transactions']);

        $timeline = $this->buildTimeline($claim);

        return view('admin.claims.show', [
            'claim' => $claim,
            'timeline' => $timeline,
            'bank' => [
                'account_name' => $claim->bank_account_name,
                'bank_name' => $claim->bank_name,
                'account_number' => $this->maskBank($claim->bank_account_number),
                'branch_code' => $this->maskBank($claim->bank_branch_code, 2),
            ],
            'statuses' => PurchaseClaim::statuses(),
        ]);
    }

    public function verify(PurchaseClaim $claim): RedirectResponse
    {
        try {
            $this->claims->verifyTransaction($claim, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Transaction verified. The reward is now eligible for approval.');
    }

    public function approve(PurchaseClaim $claim): RedirectResponse
    {
        try {
            $this->claims->approveReward($claim, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Reward approved and a payout has been scheduled.');
    }

    public function reject(Request $request, PurchaseClaim $claim): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        try {
            $this->claims->reject($claim, $data['reason'], auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Claim rejected and the buyer reward marked as rejected.');
    }

    public function dispute(PurchaseClaim $claim): RedirectResponse
    {
        try {
            $this->claims->markDisputed($claim, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Claim marked as disputed.');
    }

    protected function buildTimeline(PurchaseClaim $claim): array
    {
        $events = [];

        $events[] = ['at' => $claim->created_at, 'label' => 'Claim submitted', 'detail' => $claim->claim_reference];

        if ($claim->seller_verified_at) {
            $events[] = ['at' => $claim->seller_verified_at, 'label' => 'Seller confirmed sale', 'detail' => $claim->seller_verification_note];
        }

        if ($claim->verified_at) {
            $events[] = ['at' => $claim->verified_at, 'label' => 'Transaction verified by WorxCars', 'detail' => null];
        }

        if ($claim->approved_at) {
            $events[] = ['at' => $claim->approved_at, 'label' => 'Reward approved', 'detail' => 'Payout scheduled'];
        }

        if ($claim->rejected_at) {
            $events[] = ['at' => $claim->rejected_at, 'label' => 'Claim rejected', 'detail' => $claim->rejection_reason];
        }

        $rewardId = $claim->buyerReward?->id;

        $logs = AuditLog::where(function ($q) use ($claim, $rewardId) {
            $q->where('entity_type', 'purchase_claim')->where('entity_id', $claim->id);
            if ($rewardId) {
                $q->orWhere(fn ($w) => $w->where('entity_type', 'buyer_reward')->where('entity_id', $rewardId));
            }
        })->get();

        foreach ($logs as $log) {
            $events[] = [
                'at' => $log->occurred_at,
                'label' => ucfirst(str_replace('_', ' ', $log->action)),
                'detail' => $log->description,
            ];
        }

        usort($events, fn ($a, $b) => $a['at']->timestamp <=> $b['at']->timestamp);

        return $events;
    }

    protected function maskBank(?string $value, int $visible = 4): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return str_repeat('•', max(0, strlen($value) - $visible)).substr($value, -$visible);
    }
}