<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\BuyerEnquiry;
use App\Models\Vehicle;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class DealerEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = $this->ownedEnquiriesQuery()
            ->with(['vehicle.activeImages', 'user'])
            ->latest()
            ->paginate(20);

        return view('dealer.enquiries.index', [
            'enquiries' => $enquiries,
            'statuses' => BuyerEnquiry::statuses(),
        ]);
    }

    public function update(Request $request, BuyerEnquiry $enquiry)
    {
        if (! $this->ownsVehicle($enquiry->vehicle)) {
            abort(403, 'You do not have permission to manage this enquiry.');
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(BuyerEnquiry::statuses()))],
            'internal_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $enquiry->update($data);

        AuditLogger::log('enquiry_updated', 'buyer_enquiry', $enquiry->id, 'Enquiry status updated', null, $data);

        return back()->with('status', 'Enquiry updated.');
    }

    protected function ownedEnquiriesQuery()
    {
        $user = auth()->user();

        if ($user->isDealership()) {
            $dealership = $user->dealership();

            if ($dealership) {
                return BuyerEnquiry::whereHas('vehicle', fn ($q) => $q->where('dealership_id', $dealership->id));
            }
        } else {
            $seller = $user->privateSellers()->first();

            if ($seller) {
                return BuyerEnquiry::whereHas('vehicle', fn ($q) => $q->where('private_seller_id', $seller->id));
            }
        }

        return BuyerEnquiry::whereRaw('0 = 1');
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