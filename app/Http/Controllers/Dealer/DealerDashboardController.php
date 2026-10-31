<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\BuyerEnquiry;
use App\Models\Commission;
use App\Models\Dealership;
use App\Models\PrivateSeller;
use App\Models\PurchaseClaim;
use App\Models\RewardPayout;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $seller = $this->seller();
        $ownedIds = $this->ownedVehiclesQuery()->pluck('id');

        $kpis = [
            'total' => 0,
            'published' => 0,
            'active' => 0,
            'newEnquiries' => 0,
            'pendingClaims' => 0,
            'commission' => 0.0,
            'views' => 0,
        ];

        $kpis['total'] = Vehicle::whereIn('id', $ownedIds)->count();
        $kpis['published'] = Vehicle::whereIn('id', $ownedIds)->where('status', Vehicle::STATUS_PUBLISHED)->count();
        $kpis['active'] = Vehicle::whereIn('id', $ownedIds)->whereIn('status', [Vehicle::STATUS_PUBLISHED, Vehicle::STATUS_RESERVED])->count();
        $kpis['views'] = (int) Vehicle::whereIn('id', $ownedIds)->sum('views_count');
        $kpis['newEnquiries'] = BuyerEnquiry::whereIn('vehicle_id', $ownedIds)->where('status', BuyerEnquiry::STATUS_NEW)->count();
        $kpis['pendingClaims'] = PurchaseClaim::whereIn('vehicle_id', $ownedIds)
            ->whereIn('status', [PurchaseClaim::STATUS_SUBMITTED, PurchaseClaim::STATUS_UNDER_REVIEW, PurchaseClaim::STATUS_SELLER_VERIFICATION])
            ->count();

        if ($seller instanceof Dealership) {
            $kpis['commission'] = (float) Commission::where('dealership_id', $seller->id)->sum('commission_amount');
        } elseif ($seller instanceof PrivateSeller) {
            $kpis['commission'] = (float) Commission::whereIn('vehicle_id', $ownedIds)->sum('commission_amount');
        }

        $recentEnquiries = BuyerEnquiry::whereIn('vehicle_id', $ownedIds)
            ->with(['vehicle.activeImages'])
            ->latest()
            ->limit(6)
            ->get();

        $recentVehicles = Vehicle::whereIn('id', $ownedIds)
            ->with(['activeImages', 'campaign'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dealer.dashboard', compact('user', 'seller', 'kpis', 'recentEnquiries', 'recentVehicles'));
    }

    public function profile()
    {
        $user = auth()->user();
        $seller = $this->seller();

        return view('dealer.profile', compact('user', 'seller'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $seller = $this->seller();

        if ($seller instanceof Dealership) {
            $data = $request->validate([
                'business_name' => ['required', 'string', 'max:180'],
                'trading_name' => ['nullable', 'string', 'max:180'],
                'contact_person_name' => ['nullable', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'website' => ['nullable', 'url', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
                'street_address' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:120'],
                'province' => ['nullable', 'string', 'max:60'],
                'postal_code' => ['nullable', 'string', 'max:20'],
            ]);

            DB::transaction(function () use ($seller, $data) {
                $seller->update(collect($data)->except(['email'])->all());
                AuditLogger::log('dealership_profile_updated', 'dealership', $seller->id, 'Dealership profile updated');
            });

            return back()->with('status', 'Dealership profile updated.');
        }

        if ($seller instanceof PrivateSeller) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'surname' => ['nullable', 'string', 'max:120'],
                'contact_email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'street_address' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:120'],
                'province' => ['nullable', 'string', 'max:60'],
                'postal_code' => ['nullable', 'string', 'max:20'],
            ]);

            DB::transaction(function () use ($user, $seller, $data) {
                $user->update([
                    'name' => $data['name'],
                    'surname' => $data['surname'] ?? null,
                    'phone' => $data['phone'] ?? null,
                ]);

                $seller->update(collect($data)->except(['name', 'surname'])->all());
                AuditLogger::log('seller_profile_updated', 'private_seller', $seller->id, 'Private seller profile updated');
            });

            return back()->with('status', 'Profile updated.');
        }

        return redirect()->route('dealer.dashboard')->with('error', 'No dealership or seller profile is linked to your account. Contact support.');
    }

    public function commissions()
    {
        $user = auth()->user();
        $seller = $this->seller();
        $ownedIds = $this->ownedVehiclesQuery()->pluck('id');

        if (! $seller) {
            return view('dealer.commissions.index', [
                'user' => $user,
                'seller' => null,
                'commissions' => collect(),
                'payouts' => collect(),
                'totals' => ['configured' => 0.0, 'confirmed' => 0.0, 'paid_out' => 0.0],
            ]);
        }

        $commissions = Commission::query()
            ->when($seller instanceof Dealership, fn ($q) => $q->where('dealership_id', $seller->id))
            ->when($seller instanceof PrivateSeller, fn ($q) => $q->whereIn('vehicle_id', $ownedIds))
            ->with(['vehicle', 'campaign'])
            ->latest()
            ->get();

        $payouts = RewardPayout::whereHas('buyerReward', fn ($q) => $q->whereIn('vehicle_id', $ownedIds))
            ->with(['buyerReward.vehicle', 'user'])
            ->latest()
            ->get();

        $totals = [
            'configured' => (float) $commissions->sum('commission_amount'),
            'confirmed' => (float) PurchaseClaim::whereIn('vehicle_id', $ownedIds)
                ->whereNotNull('seller_verified_at')
                ->sum('commission_amount'),
            'paid_out' => (float) $payouts->where('status', RewardPayout::STATUS_COMPLETED)->sum('amount'),
        ];

        return view('dealer.commissions.index', compact('user', 'seller', 'commissions', 'payouts', 'totals'));
    }

    public function reports()
    {
        $user = auth()->user();
        $seller = $this->seller();
        $ownedIds = $this->ownedVehiclesQuery()->pluck('id');

        $months = collect();

        for ($i = 11; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end = (clone $start)->addMonth();

            $vehicles = Vehicle::whereIn('id', $ownedIds);
            $claims = PurchaseClaim::whereIn('vehicle_id', $ownedIds);
            $enquiries = BuyerEnquiry::whereHas('vehicle', fn ($q) => $q->whereIn('id', $ownedIds));

            $confirmedThisMonth = (clone $claims)
                ->whereBetween('seller_verified_at', [$start, $end]);

            $months->push([
                'label' => $start->format('M Y'),
                'listed' => (clone $vehicles)->whereBetween('created_at', [$start, $end])->count(),
                'published' => (clone $vehicles)->whereBetween('published_at', [$start, $end])->count(),
                'enquiries' => (clone $enquiries)->whereBetween('created_at', [$start, $end])->count(),
                'sales' => (clone $confirmedThisMonth)->whereIn('status', [
                    PurchaseClaim::STATUS_TRANSACTION_VERIFIED,
                    PurchaseClaim::STATUS_REWARD_APPROVED,
                    PurchaseClaim::STATUS_PAYMENT_PROCESSING,
                    PurchaseClaim::STATUS_PAID,
                ])->count(),
                'commission' => (float) (clone $confirmedThisMonth)->sum('commission_amount'),
            ]);
        }

        return view('dealer.reports.index', compact('months', 'user', 'seller'));
    }

    protected function seller(): Dealership|PrivateSeller|null
    {
        $user = auth()->user();

        if ($user->isDealership()) {
            return $user->dealership();
        }

        if ($user->isPrivateSeller()) {
            return $user->privateSellers()->firstOrCreate(
                ['user_id' => $user->id],
                ['status' => PrivateSeller::STATUS_VERIFIED, 'is_active' => true]
            );
        }

        return null;
    }

    protected function ownedVehiclesQuery()
    {
        $user = auth()->user();

        if ($user->isDealership()) {
            $dealership = $user->dealership();

            return $dealership ? Vehicle::where('dealership_id', $dealership->id) : Vehicle::whereRaw('0 = 1');
        }

        $seller = $user->privateSellers()->first();

        return Vehicle::where('private_seller_id', $seller?->id ?? 0);
    }
}