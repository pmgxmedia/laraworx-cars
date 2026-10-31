<?php

namespace App\Http\Controllers;

use App\Models\PrivateSeller;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Notifications\VehicleSubmittedForApproval;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellCarController extends Controller
{
    public function create()
    {
        $seo = [
            'title' => 'Sell Your Car | WorxCars',
            'description' => 'List your vehicle on WorxCars and reach thousands of serious buyers across South Africa. Simple, secure and free to start.',
        ];

        return view('sell-car', compact('seo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:80'],
            'variant' => ['nullable', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
            'mileage' => ['required', 'integer', 'min:0', 'max:3000000'],
            'price' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'transmission' => ['required', 'in:Automatic,Manual'],
            'fuel_type' => ['required', 'in:Petrol,Diesel,Hybrid,Electric'],
            'body_type' => ['required', 'string', 'max:40'],
            'colour' => ['nullable', 'string', 'max:60'],
            'province' => ['required', 'string', 'max:60'],
            'city' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:30', 'max:3000'],
            'seller_type' => ['nullable', 'in:dealership,private'],
            'images' => ['required', 'array', 'min:1', 'max:12'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('status', 'Sign in or create an account to submit your vehicle listing.');
        }

        $wantsDealership = ($data['seller_type'] ?? null) === 'dealership';

        if ($wantsDealership && ! $user->isDealership()) {
            return back()->withInput()->withErrors([
                'seller_type' => 'Dealership listings require a verified dealership account. Apply to join as a dealership, or list as a private individual.',
            ]);
        }

        return DB::transaction(function () use ($request, $data, $user, $wantsDealership) {
            $privateSeller = null;
            $dealershipId = null;

            if ($wantsDealership) {
                $dealershipId = $user->dealership()?->id;
            } else {
                $privateSeller = $user->privateSellers()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => PrivateSeller::STATUS_VERIFIED, 'is_active' => true]
                );

                if (! $user->isPrivateSeller()) {
                    $role = Role::firstOrCreate(
                        ['name' => Role::PRIVATE_SELLER],
                        ['label' => 'Private Seller', 'description' => 'Approved private seller account.']
                    );

                    $user->roles()->syncWithoutDetaching($role->id);
                }
            }

            $vehicle = Vehicle::create([
                'vehicle_reference' => 'WC-'.strtoupper(Str::random(6)),
                'private_seller_id' => $privateSeller?->id,
                'dealership_id' => $dealershipId,
                'seller_type' => $privateSeller ? 'private' : 'dealership',
                'status' => Vehicle::STATUS_PENDING_APPROVAL,
                'make' => $data['make'],
                'model' => $data['model'],
                'variant' => $data['variant'] ?? null,
                'year' => $data['year'],
                'mileage' => $data['mileage'],
                'price' => $data['price'],
                'currency' => 'ZAR',
                'transmission' => $data['transmission'],
                'fuel_type' => $data['fuel_type'],
                'body_type' => $data['body_type'],
                'colour' => $data['colour'] ?? null,
                'condition' => 'Used',
                'description' => $data['description'],
                'location_province' => $data['province'],
                'location_city' => $data['city'],
                'slug' => Str::slug(implode(' ', [$data['year'], $data['make'], $data['model'], Str::random(4)])),
            ]);

            foreach (array_values($request->file('images')) as $i => $file) {
                $path = $file->store('vehicles/'.$vehicle->id, 'public');
                $name = "{$data['year']} {$data['make']} {$data['model']}";

                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'thumbnail_path' => $path,
                    'sort_order' => $i,
                    'is_main' => $i === 0,
                    'is_active' => true,
                    'alt_text' => $name,
                ]);
            }

            AuditLogger::log('vehicle_submitted', 'vehicle', $vehicle->id, 'Vehicle listing submitted for approval');

            User::whereHas('roles', fn ($q) => $q->whereIn('name', [User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS]))
                ->where('is_active', true)
                ->get()
                ->each(fn ($admin) => $admin->notify(new VehicleSubmittedForApproval($vehicle)));

            return redirect()->route('vehicles.show', $vehicle)
                ->with('status', 'Your vehicle has been submitted for approval. We\'ll review it shortly and publish it once approved.');
        });
    }
}
