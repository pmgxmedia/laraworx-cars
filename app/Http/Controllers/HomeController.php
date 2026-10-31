<?php

namespace App\Http\Controllers;

use App\Models\BuyerReward;
use App\Models\Faq;
use App\Models\Vehicle;
use App\Models\VehicleCampaign;
use App\Support\PlaceholderImageService;
use Illuminate\Support\Facades\Storage;
use App\Models\Dealership;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $imageService = app(PlaceholderImageService::class);
        $imageService->generateLogo();
        $imageService->generateHero();
        $heroUrl = Storage::disk('public')->url('brand/hero.webp');

        $featured = Vehicle::published()
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->featured(true)
            ->orderBy('is_promoted', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        $latest = Vehicle::published()
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->newListings(true)
            ->orderBy('published_at', 'desc')
            ->limit(8)
            ->get();

        if ($latest->isEmpty()) {
            $latest = Vehicle::published()
                ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
                ->withEffectiveReward()
                ->orderBy('published_at', 'desc')
                ->limit(8)
                ->get();
        }

        $campaigns = VehicleCampaign::where('status', VehicleCampaign::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('start_date', 'desc')
            ->limit(3)
            ->get();

        $dealerships = Dealership::where('status', Dealership::STATUS_VERIFIED)
            ->where('is_active', true)
            ->withCount(['vehicles' => fn ($q) => $q->where('status', Vehicle::STATUS_PUBLISHED)])
            ->orderBy('business_name')
            ->limit(6)
            ->get();

        $testimonials = BuyerReward::with(['user', 'vehicle'])
            ->where('status', BuyerReward::STATUS_PAID)
            ->orderBy('paid_at', 'desc')
            ->limit(3)
            ->get();

        if ($testimonials->isEmpty()) {
            $testimonials = BuyerReward::with(['user', 'vehicle'])
                ->where('status', BuyerReward::STATUS_PAID)
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
        }

        $faqs = Faq::where('is_published', true)->orderBy('sort_order')->limit(6)->get();

        $stats = [
            'vehicles' => Vehicle::published()->count(),
            'dealerships' => Dealership::where('status', Dealership::STATUS_VERIFIED)->where('is_active', true)->count(),
            'rewards' => round(BuyerReward::where('status', BuyerReward::STATUS_PAID)->sum('amount'), 0),
            'claim_rate' => (float) Setting::get('worxcars.claim_approval_rate', 96.4),
        ];

        $seo = [
            'title' => Setting::get('seo.home_title', 'Buy Your Car, Get Paid | WorxCars'),
            'description' => Setting::get('seo.home_description', 'South Africa\'s premium vehicle marketplace. Buy from trusted dealerships and private sellers and earn a WorxCars Buyer Reward on your qualifying purchase.'),
        ];

        return view('home', compact('heroUrl', 'featured', 'latest', 'campaigns', 'dealerships', 'testimonials', 'faqs', 'stats', 'seo'));
    }
}