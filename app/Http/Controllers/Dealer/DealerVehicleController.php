<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealership;
use App\Models\PrivateSeller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealerVehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = $this->ownedVehiclesQuery()
            ->with(['activeImages', 'campaign'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dealer.vehicles.index', [
            'vehicles' => $vehicles,
            'statuses' => Vehicle::statuses(),
            'filterStatus' => $request->input('status'),
        ]);
    }

    public function create()
    {
        return view('dealer.vehicles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->vehicleRules());

        if ($request->hasFile('images')) {
            $data['images'] = $request->validate([
                'images' => ['required', 'array', 'max:'.config('worxcars.images_per_vehicle_max', 20)],
                'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
            ])['images'];
        }

        $user = auth()->user();
        $submit = $request->input('action') === 'submit';

        $vehicle = DB::transaction(function () use ($data, $user, $submit) {
            $dealershipId = null;
            $privateSellerId = null;
            $sellerType = 'dealership';

            if ($user->isDealership()) {
                $dealership = $user->dealership();
                $dealershipId = $dealership?->id;
                $sellerType = 'dealership';
            } elseif ($user->isPrivateSeller()) {
                $privateSeller = $user->privateSellers()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => PrivateSeller::STATUS_VERIFIED, 'is_active' => true]
                );
                $privateSellerId = $privateSeller->id;
                $sellerType = 'private';
            }

            $vehicle = Vehicle::create(array_merge($this->fillablePayload($data), [
                'vehicle_reference' => 'WC-'.strtoupper(Str::random(6)),
                'dealership_id' => $dealershipId,
                'private_seller_id' => $privateSellerId,
                'seller_type' => $sellerType,
                'status' => $submit ? Vehicle::STATUS_PENDING_APPROVAL : Vehicle::STATUS_DRAFT,
                'slug' => $this->uniqueSlug($data['make'], $data['model'], $data['variant'] ?? null, $data['year']),
                'views_count' => 0,
                'is_featured' => false,
                'is_promoted' => false,
            ]));

            $this->storeImages($vehicle, $data['images'] ?? []);

            AuditLogger::log('vehicle_created', 'vehicle', $vehicle->id, $submit ? 'Vehicle listing created and submitted for approval' : 'Vehicle listing draft created');

            return $vehicle;
        });

        if ($submit) {
            $this->notifyAdmins($vehicle);
        }

        return $submit
            ? redirect()->route('dealer.vehicles')->with('status', 'Your vehicle has been submitted for approval.')
            : redirect()->route('dealer.vehicles.edit', $vehicle)->with('status', 'Draft saved. Add details and images, then submit for approval when ready.');
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        return view('dealer.vehicles.edit', ['vehicle' => $vehicle]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $data = $request->validate($this->vehicleRules());
        $submit = $request->input('action') === 'submit';
        $before = $vehicle->only(['status', 'price', 'mileage', 'year']);

        $vehicle = DB::transaction(function () use ($vehicle, $data, $submit, $before) {
            $payload = $this->fillablePayload($data);

            if ($submit) {
                $payload['status'] = Vehicle::STATUS_PENDING_APPROVAL;
            }

            $vehicle->update($payload);

            AuditLogger::log('vehicle_updated', 'vehicle', $vehicle->id, 'Vehicle listing updated', $before, $vehicle->only(['status', 'price', 'mileage', 'year']));

            return $vehicle;
        });

        if ($submit) {
            $this->notifyAdmins($vehicle);
        }

        return redirect()->route('dealer.vehicles.edit', $vehicle)
            ->with('status', $submit ? 'Vehicle updated and submitted for approval.' : 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        DB::transaction(function () use ($vehicle) {
            foreach ($vehicle->images()->get() as $image) {
                Storage::disk('public')->delete([$image->image_path, $image->thumbnail_path]);
            }

            AuditLogger::log('vehicle_deleted', 'vehicle', $vehicle->id, 'Vehicle listing deleted');

            $vehicle->delete();
        });

        return redirect()->route('dealer.vehicles')->with('status', 'Vehicle listing deleted.');
    }

    public function uploadImages(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $newImages = $request->file('images', []);
        $existing = $vehicle->images()->count();
        $max = (int) config('worxcars.images_per_vehicle_max', 20);

        if ($existing + count($newImages) > $max) {
            return back()->with('error', "You can upload a maximum of {$max} images per vehicle ({$existing} currently attached).");
        }

        $data = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        DB::transaction(function () use ($vehicle, $data) {
            $this->storeImages($vehicle, $data['images']);
            AuditLogger::log('vehicle_images_uploaded', 'vehicle', $vehicle->id, 'Images uploaded for vehicle listing');
        });

        return back()->with('status', 'Images uploaded.');
    }

    public function deleteImage(VehicleImage $image)
    {
        $this->authorizeVehicle($image->vehicle);

        DB::transaction(function () use ($image) {
            $wasMain = $image->is_main;

            Storage::disk('public')->delete([$image->image_path, $image->thumbnail_path]);

            $image->delete();

            if ($wasMain) {
                $next = $image->vehicle->images()->orderBy('sort_order')->first();
                $next?->update(['is_main' => true]);
            }

            AuditLogger::log('vehicle_image_deleted', 'vehicle_image', $image->id, 'Vehicle image deleted');
        });

        return back()->with('status', 'Image removed.');
    }

    public function reorderImages(Request $request)
    {
        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map('intval')
            ->values()
            ->all();

        if (! $ids) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'No images provided.'], 422)
                : back()->with('error', 'No images provided to reorder.');
        }

        $images = VehicleImage::whereIn('id', $ids)->with('vehicle')->get()->keyBy('id');

        if ($images->count() !== count($ids)) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'One or more images could not be found.'], 422)
                : back()->with('error', 'One or more images could not be found.');
        }

        foreach ($images as $image) {
            $this->authorizeVehicle($image->vehicle);
        }

        DB::transaction(function () use ($ids, $images) {
            foreach (array_values($ids) as $index => $id) {
                $images[$id]->update(['sort_order' => $index, 'is_main' => $index === 0]);
            }

            AuditLogger::log('vehicle_images_reordered', 'vehicle', $images->first()->vehicle_id, 'Vehicle images reordered');
        });

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back()->with('status', 'Image order updated.');
    }

    public function submit(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        if (! in_array($vehicle->status, [Vehicle::STATUS_DRAFT, Vehicle::STATUS_REJECTED, Vehicle::STATUS_ARCHIVED])) {
            return back()->with('error', 'Only draft or rejected listings can be submitted for approval.');
        }

        DB::transaction(function () use ($vehicle) {
            $vehicle->update(['status' => Vehicle::STATUS_PENDING_APPROVAL]);
            AuditLogger::log('vehicle_submitted', 'vehicle', $vehicle->id, 'Vehicle listing submitted for approval');
        });

        $this->notifyAdmins($vehicle);

        return redirect()->route('dealer.vehicles')->with('status', 'Your vehicle has been submitted for approval.');
    }

    protected function vehicleRules(): array
    {
        return [
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:80'],
            'variant' => ['nullable', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
            'mileage' => ['required', 'integer', 'min:0', 'max:3000000'],
            'price' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'currency' => ['nullable', 'string', 'size:3'],
            'transmission' => ['nullable', 'string', 'in:Automatic,Manual'],
            'fuel_type' => ['nullable', 'string', 'in:Petrol,Diesel,Hybrid,Electric'],
            'body_type' => ['nullable', 'string', 'max:40'],
            'engine_size' => ['nullable', 'string', 'max:40'],
            'engine_cc' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'power_kw' => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'torque_nm' => ['nullable', 'numeric', 'min:0', 'max:20000'],
            'colour' => ['nullable', 'string', 'max:60'],
            'doors' => ['nullable', 'integer', 'min:1', 'max:24'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:100'],
            'condition' => ['nullable', 'string', 'in:new,demo,used'],
            'vehicle_condition_detail' => ['nullable', 'string', 'max:2000'],
            'registration_status' => ['nullable', 'string', 'in:registered,unregistered'],
            'location_province' => ['required', 'string', 'max:60'],
            'location_city' => ['required', 'string', 'max:120'],
            'location_suburb' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:3000'],
            'features' => ['nullable', 'string'],
            'vin' => ['nullable', 'string', 'max:60'],
            'reg_number' => ['nullable', 'string', 'max:30'],
            'date_first_registered' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function fillablePayload(array $data): array
    {
        $keys = [
            'make', 'model', 'variant', 'year', 'mileage', 'price', 'currency', 'transmission',
            'fuel_type', 'body_type', 'engine_size', 'engine_cc', 'power_kw', 'torque_nm',
            'colour', 'doors', 'seats', 'condition', 'vehicle_condition_detail', 'registration_status',
            'description', 'location_province', 'location_city', 'location_suburb', 'vin', 'reg_number',
            'date_first_registered', 'seo_title', 'seo_description',
        ];

        $payload = array_intersect_key($data, array_flip($keys));
        $payload['features'] = $this->featureList($data['features'] ?? null);

        return $payload;
    }

    protected function featureList(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    protected function uniqueSlug(string $make, string $model, ?string $variant, int $year): string
    {
        $base = Str::slug(trim(implode(' ', array_filter([$make, $model, $variant, $year])))) ?: 'vehicle';

        do {
            $slug = $base.'-'.Str::random(4);
        } while (Vehicle::withTrashed()->where('slug', $slug)->exists());

        return $slug;
    }

    protected function storeImages(Vehicle $vehicle, array $files): void
    {
        $existing = $vehicle->images()->count();

        foreach (array_values($files) as $i => $file) {
            $path = $file->store('vehicles/'.$vehicle->id, 'public');

            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image_path' => $path,
                'thumbnail_path' => $path,
                'alt_text' => $vehicle->displayName(),
                'sort_order' => $existing + $i,
                'is_main' => $existing === 0 && $i === 0,
                'is_active' => true,
            ]);
        }
    }

    protected function authorizeVehicle(Vehicle $vehicle): void
    {
        $user = auth()->user();

        if ($user->isDealership()) {
            $dealership = $user->dealership();

            if ($dealership && $vehicle->dealership_id === $dealership->id) {
                return;
            }
        } elseif ($user->isPrivateSeller()) {
            $seller = $user->privateSellers()->first();

            if ($seller && $vehicle->private_seller_id === $seller->id) {
                return;
            }
        }

        abort(403, 'You do not have permission to manage this vehicle.');
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

    protected function notifyAdmins(Vehicle $vehicle): void
    {
        try {
            User::whereHas('roles', fn ($q) => $q->whereIn('name', [User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS]))
                ->where('is_active', true)
                ->get()
                ->each(fn ($admin) => $admin->notify(new \App\Notifications\VehicleSubmittedForApproval($vehicle)));
        } catch (\Throwable $e) {
        }
    }
}