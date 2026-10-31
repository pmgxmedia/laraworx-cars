<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleCampaign;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = VehicleCampaign::query()
            ->withCount('campaignVehicles')
            ->where('status', VehicleCampaign::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('start_date', 'desc')
            ->paginate(9)
            ->withQueryString();

        return view('campaigns.index', compact('campaigns'));
    }

    public function show(VehicleCampaign $campaign)
    {
        if (! $campaign->isActive()) {
            abort(404);
        }

        $vehicles = $campaign->vehicles()
            ->where('vehicles.status', Vehicle::STATUS_PUBLISHED)
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->orderBy('vehicles.is_featured', 'desc')
            ->orderBy('vehicles.published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('campaigns.show', compact('campaign', 'vehicles'));
    }
}