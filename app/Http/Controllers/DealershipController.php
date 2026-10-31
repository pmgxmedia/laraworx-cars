<?php

namespace App\Http\Controllers;

use App\Models\Dealership;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DealershipController extends Controller
{
    public function index(Request $request)
    {
        $query = Dealership::where('status', Dealership::STATUS_VERIFIED)
            ->where('is_active', true)
            ->withCount(['vehicles' => fn ($q) => $q->where('status', Vehicle::STATUS_PUBLISHED)]);

        if ($request->filled('province')) {
            $query->where('province', $request->input('province'));
        }

        if ($request->filled('q')) {
            $query->where('business_name', 'like', '%'.$request->input('q').'%');
        }

        $dealerships = $query->orderBy('business_name')->paginate(12)->withQueryString();
        $provinces = Dealership::where('status', Dealership::STATUS_VERIFIED)->whereNotNull('province')->distinct()->orderBy('province')->pluck('province');

        return view('dealerships.index', compact('dealerships', 'provinces'));
    }

    public function show(Dealership $dealership)
    {
        if (! $dealership->isVerified()) {
            abort(404);
        }

        $vehicles = $dealership->vehicles()
            ->where('status', Vehicle::STATUS_PUBLISHED)
            ->with(['dealership', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(12)
            ->get();

        $stats = [
            'vehicles' => $dealership->vehicles->where('status', Vehicle::STATUS_SOLD)->count(),
            'rewards' => $dealership->purchaseClaims()->where('status', 'paid')->count(),
        ];

        return view('dealerships.show', compact('dealership', 'vehicles', 'stats'));
    }

    public function join()
    {
        $seo = [
            'title' => 'Partner With Us | WorxCars',
            'description' => 'Join South Africa\'s premium vehicle marketplace. List your stock to thousands of buyers and get paid.',
        ];

        return view('dealerships.join', compact('seo'));
    }

    public function storeApplication(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:180'],
            'trading_name' => ['nullable', 'string', 'max:180'],
            'registration_number' => ['nullable', 'string', 'max:60'],
            'vat_number' => ['nullable', 'string', 'max:30'],
            'contact_person_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:60'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $dealership = Dealership::updateOrCreate(
            ['email' => $data['email']],
            array_merge($data, ['status' => Dealership::STATUS_PENDING, 'is_active' => true])
        );

        \App\Support\AuditLogger::log('dealership_application', 'dealership', $dealership->id, 'Dealership partnership application submitted');

        return redirect()->route('dealerships.join')->with('status', 'Thank you — your dealership application has been received. Our partnerships team will be in touch.');
    }
}