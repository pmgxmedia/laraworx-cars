<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BuyerReward;
use App\Models\PurchaseClaim;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'favorites' => $user->favorites()->count(),
            'enquiries' => $user->enquiries()->count(),
            'claims' => $user->purchaseClaims()->count(),
            'rewards_total' => round($user->buyerRewards()->sum('amount'), 2),
        ];

        $recentClaims = $user->purchaseClaims()->with(['vehicle'])->orderBy('created_at', 'desc')->limit(5)->get();
        $recentRewards = $user->buyerRewards()->with(['vehicle', 'payout'])->orderBy('created_at', 'desc')->limit(5)->get();
        $recentNotifications = $user->notifications()->limit(6)->get();
        $recommended = Vehicle::published()
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->featured(true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('customer.dashboard', compact('stats', 'recentClaims', 'recentRewards', 'recentNotifications', 'recommended'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate(15);

        return view('customer.notifications', compact('notifications'));
    }

    public function markRead(Request $request, $notification)
    {
        auth()->user()->notifications()->where('id', $notification)->first()?->markAsRead();

        return back();
    }
}