<?php

namespace App\Http\Controllers;

use App\Models\BuyerEnquiry;
use App\Models\Commission;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCampaign;
use App\Notifications\VehicleEnquiryReceived;
use App\Support\FinanceEstimator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public const SORTS = [
        'newest' => ['label' => 'Newest first', 'clause' => ['published_at' => 'desc']],
        'price_asc' => ['label' => 'Price: low to high', 'clause' => ['price' => 'asc']],
        'price_desc' => ['label' => 'Price: high to low', 'clause' => ['price' => 'desc']],
        'year_desc' => ['label' => 'Year: newest', 'clause' => ['year' => 'desc']],
        'mileage_asc' => ['label' => 'Mileage: lowest', 'clause' => ['mileage' => 'asc']],
        'reward_desc' => ['label' => 'Biggest reward', 'clause' => ['effective_reward_amount' => 'desc']],
    ];

    public function index(Request $request): View
    {
        $query = Vehicle::published()
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->with('specifications');

        $query->searchQuery($request->input('q'))
            ->byMaker($request->input('make'))
            ->byModel($request->input('model'))
            ->withinPrice($request->filled('price_min') ? (float) $request->input('price_min') : null, $request->filled('price_max') ? (float) $request->input('price_max') : null)
            ->withinYear($request->filled('year_min') ? (int) $request->input('year_min') : null, $request->filled('year_max') ? (int) $request->input('year_max') : null)
            ->withinMileage($request->filled('mileage_max') ? (int) $request->input('mileage_max') : null)
            ->withTransmission($request->input('transmission'))
            ->withFuelType($request->input('fuel_type'))
            ->withBodyType($request->input('body_type'))
            ->withinLocation($request->input('province'), $request->input('city'))
            ->withSellerType($request->input('seller_type'))
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->when($request->input('reward') === 'yes', fn ($q) => $q->withReward(1))
            ->when($request->filled('reward_min'), fn ($q) => $q->withReward((float) $request->input('reward_min')));

        $sort = $request->input('sort', 'newest');
        $sortClause = self::SORTS[$sort]['clause'] ?? self::SORTS['newest']['clause'];
        $sortColumn = array_key_first($sortClause);
        $sortDirection = $sortClause[$sortColumn];

        $vehicles = $query->orderBy($sortColumn, $sortDirection)
            ->paginate(12)
            ->withQueryString();

        $facets = [
            'makes' => Vehicle::published()->distinct()->orderBy('make')->pluck('make'),
            'cities' => Vehicle::published()->whereNotNull('location_city')->distinct()->orderBy('location_city')->pluck('location_city'),
            'provinces' => Vehicle::published()->whereNotNull('location_province')->distinct()->orderBy('location_province')->pluck('location_province'),
            'bodies' => Vehicle::published()->whereNotNull('body_type')->distinct()->orderBy('body_type')->pluck('body_type'),
            'price_max_options' => [200000, 300000, 400000, 500000, 700000, 1000000, 1500000, 3000000],
        ];

        $sortUrl = fn (string $key) => null;

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'facets' => $facets,
            'sorts' => self::SORTS,
            'activeSort' => $sort,
            'activeFilters' => $request->query(),
            'total' => $vehicles->total(),
        ]);
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        if ($vehicle->status !== Vehicle::STATUS_PUBLISHED && $vehicle->status !== Vehicle::STATUS_RESERVED) {
            abort(404);
        }

        $vehicle->load(['dealership', 'privateSeller.user', 'activeImages', 'specifications', 'campaign']);
        $vehicle->setAttribute('effective_reward_amount', $vehicle->activeRewardAmount());

        $breakdown = app(\App\Services\CommissionService::class)->breakdown($vehicle);
        $estimates = FinanceEstimator::estimate($vehicle);
        $similar = Vehicle::published()
            ->where('id', '!=', $vehicle->id)
            ->where('make', $vehicle->make)
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        if ($similar->isEmpty()) {
            $similar = Vehicle::published()
                ->where('id', '!=', $vehicle->id)
                ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
                ->withEffectiveReward()
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get();
        }

        return view('vehicles.show', [
            'vehicle' => $vehicle,
            'breakdown' => $breakdown,
            'estimates' => $estimates,
            'similar' => $similar,
            'seo_title' => $vehicle->seo_title ?: $vehicle->displayName().' for sale | WorxCars',
            'seo_description' => $vehicle->seo_description ?: sprintf('%s %s with %s km for %s in %s. Get a buyer reward on your qualifying WorxCars purchase.', $vehicle->year, $vehicle->displayName(), number_format($vehicle->mileage), money($vehicle->price), $vehicle->location_city),
        ]);
    }

    public function enquire(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_contact_method' => ['required', 'in:email,phone,whatsapp'],
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = BuyerEnquiry::STATUS_NEW;
        $data['source'] = 'website';

        $enquiry = $vehicle->enquiries()->create($data);

        $recipients = User::whereHas('roles', fn ($q) => $q->whereIn('name', [User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS, User::ROLE_SUPPORT]))
            ->where('is_active', true)
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new VehicleEnquiryReceived($enquiry));
        }

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Thank you. The seller has been notified of your enquiry.');
    }

    public function respond(Request $request, BuyerEnquiry $enquiry): RedirectResponse
    {
        abort_unless($enquiry->vehicle->seller_type === 'private' && $enquiry->vehicle->private_seller_id !== null, 403);
        abort_unless(auth()->user()->id === $enquiry->vehicle->privateSeller?->user_id, 403);

        return back()->with('status', 'Message sent.');
    }

    public function recordView(Vehicle $vehicle): RedirectResponse
    {
        Vehicle::withoutTimestamps(fn () => $vehicle->increment('views_count'));

        return response()->noContent();
    }
}