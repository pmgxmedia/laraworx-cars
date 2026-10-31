<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerVehicleActionController extends Controller
{
    public function favorites()
    {
        $vehicles = auth()->user()->favorites()
            ->with('vehicle.dealership', 'vehicle.privateSeller.user', 'vehicle.activeImages', 'vehicle.campaign', 'vehicle.activeCommission')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('customer.favorites', ['favorites' => $vehicles]);
    }

    public function toggleFavorite(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('vehicle_id', $vehicle->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('status', 'Vehicle removed from your saved list.');
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $vehicle->id,
        ]);

        return back()->with('status', 'Vehicle saved to your list.');
    }
}