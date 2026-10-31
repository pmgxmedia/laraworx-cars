<?php

namespace App\Http\Controllers;

use App\Models\Dealership;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Setting;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('pages.show', [
            'page' => $page,
            'seo' => [
                'title' => $page->seo_title ?: $page->title.' | WorxCars',
                'description' => $page->seo_description,
            ],
        ]);
    }

    public function howItWorks()
    {
        $steps = [
            ['01', 'Browse & find', 'Search WorxCars for your next vehicle from verified dealerships and trusted private sellers. Save favourites and compare options side by side.'],
            ['02', 'Contact the seller', 'Enquire directly with the seller, arrange a viewing or test drive, and negotiate your deal. WorxCars never handles the vehicle or payment between buyer and seller.'],
            ['03', 'Complete your purchase', 'Buy the vehicle under your own finance, cash or trade-in arrangement. Keep your proof of purchase — you\'ll need it for your claim.'],
            ['04', 'Submit your claim', 'From your WorxCars account, submit a reward claim with your purchase details, the qualifying vehicle, and your bank details for payout.'],
            ['05', 'Seller confirms', 'The seller confirms the transaction was genuine. WorxCars runs eligibility and fraud checks on every claim.'],
            ['06', 'Verification & approval', 'WorxCars verifies the transaction. Approved rewards are scheduled for payout.'],
            ['07', 'You get paid', 'Your WorxCars Buyer Reward is paid into your nominated South African bank account. Simple.'],
        ];

        $faqs = Faq::where('is_published', true)->orderBy('sort_order')->get();

        $seo = [
            'title' => 'How WorxCars Works | Buy Your Car, Get Paid',
            'description' => 'Understand exactly how to earn a WorxCars Buyer Reward: find a vehicle, complete a qualifying purchase, submit your claim, and get paid after verification.',
        ];

        $minReward = money((float) Setting::get('worxcars.min_reward', 1500));
        $maxReward = money((float) Setting::get('worxcars.max_reward', 25000));

        return view('pages.how-it-works', compact('steps', 'faqs', 'seo', 'minReward', 'maxReward'));
    }

    public function about()
    {
        $dealerships = Dealership::where('status', Dealership::STATUS_VERIFIED)->where('is_active', true)->count();

        $seo = [
            'title' => 'About WorxCars | Premium Vehicle Marketplace',
            'description' => 'WorxCars is a South African vehicle marketplace where buyers earn real rewards for completing qualifying vehicle purchases.',
        ];

        return view('pages.about', compact('dealerships', 'seo'));
    }
}