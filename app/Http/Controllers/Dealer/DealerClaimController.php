<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\PurchaseClaim;
use App\Models\Vehicle;
use App\Services\ClaimService;
use Illuminate\Http\Request;

class DealerClaimController extends Controller
{
    public function __construct(protected ClaimService $claims)
    {
    }

    public function index()
    {
        $claims = $this->ownedClaimsQuery()
            ->with(['vehicle.activeImages', 'user', 'buyerReward'])
            ->latest()
            ->paginate(20);

        $confirmable = [
            PurchaseClaim::STATUS_SUBMITTED,
            PurchaseClaim::STATUS_UNDER_REVIEW,
            PurchaseClaim::STATUS_SELLER_VERIFICATION,
        ];

        return view('dealer.claims.index', [
            'claims' => $claims,
            'confirmable' => $confirmable,
        ]);
    }

    public function confirm(Request $request, PurchaseClaim $claim)
    {
        if (! $this->ownsVehicle($claim->vehicle)) {
            abort(403, 'You do not have permission to confirm this claim.');
        }

        $note = $request->validate(['note' => ['nullable', 'string', 'max:2000']])['note'] ?? null;

        try {
            $this->claims->sellerConfirm($claim, $note, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Sale confirmed. WorxCars will now verify the transaction and approve the buyer reward.');
    }

    protected function ownedClaimsQuery()
    {
        $user = auth()->user();

        if ($user->isDealership()) {
            $dealership = $user->dealership();

            if ($dealership) {
                return PurchaseClaim::whereHas('vehicle', fn ($q) => $q->where('dealership_id', $dealership->id));
            }
        } else {
            $seller = $user->privateSellers()->first();

            if ($seller) {
                return PurchaseClaim::whereHas('vehicle', fn ($q) => $q->where('private_seller_id', $seller->id));
            }
        }

        return PurchaseClaim::whereRaw('0 = 1');
    }

    protected function ownsVehicle(?Vehicle $vehicle): bool
    {
        if (! $vehicle) {
            return false;
        }

        $user = auth()->user();

        if ($user->isDealership()) {
            $dealership = $user->dealership();

            return (bool) ($dealership && $vehicle->dealership_id === $dealership->id);
        }

        $seller = $user->privateSellers()->first();

        return (bool) ($seller && $vehicle->private_seller_id === $seller->id);
    }
}