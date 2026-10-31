<?php

namespace App\Http\Controllers;

use App\Models\BuyerReward;
use App\Models\PurchaseClaim;
use App\Models\Vehicle;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function __construct(protected ClaimService $claims)
    {
    }

    public function create(Vehicle $vehicle)
    {
        abort_if($vehicle->status === Vehicle::STATUS_SOLD, 403, 'This vehicle has already been sold.');
        abort_if($vehicle->status === Vehicle::STATUS_DRAFT || $vehicle->status === Vehicle::STATUS_PENDING_APPROVAL || $vehicle->status === Vehicle::STATUS_REJECTED || $vehicle->status === Vehicle::STATUS_ARCHIVED || $vehicle->status === Vehicle::STATUS_EXPIRED, 404);

        $vehicle->load(['dealership', 'privateSeller.user', 'campaign']);

        $existing = $vehicle->purchaseClaims()->withTrashed()->where('user_id', Auth::id())->first();

        $maxReward = (float) \App\Models\Setting::get('worxcars.max_reward', 25000);

        return view('claims.create', [
            'vehicle' => $vehicle,
            'existing' => $existing,
            'maxReward' => $maxReward,
        ]);
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'purchase_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subMonth()->toDateString()],
            'purchase_price' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'provider_reference' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'bank_account_name' => ['required', 'string', 'max:160'],
            'bank_name' => ['required', 'string', 'max:80'],
            'bank_account_number' => ['required', 'string', 'max:30'],
            'bank_branch_code' => ['required', 'string', 'max:30'],
            'declaration_consent' => ['accepted'],
            'declarations' => ['required', 'array'],
            'declarations.*' => ['required'],
        ]);

        $claim = $this->claims->submit(Auth::user(), $vehicle, $data, []);

        return redirect()->route('customer.claims.show', $claim)->with('status', 'Your reward claim has been submitted. We\'ll be in touch as verification progresses.');
    }

    public function mine()
    {
        $claims = Auth::user()->purchaseClaims()
            ->with(['vehicle', 'buyerReward'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.claims', compact('claims'));
    }

    public function show(PurchaseClaim $claim)
    {
        abort_unless($claim->user_id === Auth::id(), 403);

        $claim->load(['vehicle', 'buyerReward', 'documents', 'transactions']);

        $flow = PurchaseClaim::statuses();

        $order = array_flip(array_keys($flow));

        return view('customer.claim-show', compact('claim', 'flow', 'order'));
    }

    public function rewards()
    {
        $rewards = Auth::user()->buyerRewards()
            ->with(['vehicle', 'purchaseClaim', 'payout'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.rewards', compact('rewards'));
    }
}