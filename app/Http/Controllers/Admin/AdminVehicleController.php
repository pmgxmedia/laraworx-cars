<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignVehicle;
use App\Models\Commission;
use App\Models\Dealership;
use App\Models\PrivateSeller;
use App\Models\Vehicle;
use App\Models\VehicleCampaign;
use App\Models\VehicleImage;
use App\Services\CommissionService;
use App\Support\AuditLogger;
use App\Support\FinancialRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminVehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()
            ->with(['activeImages', 'campaign', 'dealership', 'privateSeller.user', 'commissions'])
            ->withEffectiveReward();

        if ($q = trim((string) $request->input('q'))) {
            $needle = "%{$q}%";

            $query->where(function ($query) use ($needle) {
                $query->where('make', 'like', $needle)
                    ->orWhere('model', 'like', $needle)
                    ->orWhere('variant', 'like', $needle)
                    ->orWhere('vehicle_reference', 'like', $needle);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('seller_type') && in_array($request->input('seller_type'), ['dealership', 'private'], true)) {
            $query->where('seller_type', $request->input('seller_type'));
        }

        if ($request->filled('make')) {
            $query->where('make', $request->input('make'));
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', (int) $request->input('campaign_id'));
        }

        $rows = $query->latest('id')->paginate(20)->withQueryString();

        $filters = [
            'statuses' => Vehicle::statuses(),
            'makes' => Vehicle::query()->distinct()->orderBy('make')->pluck('make'),
            'campaigns' => VehicleCampaign::orderBy('name')->get(),
        ];

        return view('admin.vehicles.index', compact('rows', 'filters'));
    }

    public function create()
    {
        return view('admin.vehicles.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $vehicle = DB::transaction(function () use ($data) {
            $status = $data['status'] ?? Vehicle::STATUS_DRAFT;

            $vehicle = Vehicle::create(array_merge($this->payload($data), [
                'status' => $status,
                'vehicle_reference' => $this->reference($data['vehicle_reference'] ?? null),
                'slug' => $this->uniqueSlug($data['make'], $data['model'], $data['variant'] ?? null, $data['year']),
                'views_count' => 0,
                'is_promoted' => false,
                'published_at' => $status === Vehicle::STATUS_PUBLISHED ? now() : null,
                'expires_at' => $status === Vehicle::STATUS_PUBLISHED ? now()->addDays(90) : null,
            ]));

            $this->storeImages($vehicle, $data['images'] ?? []);
            $this->applyCampaignConfig($vehicle, $data);

            AuditLogger::log('admin_vehicle_created', 'vehicle', $vehicle->id, 'Vehicle listing created by administrator');

            return $vehicle;
        });

        return redirect()->route('admin.vehicles.show', $vehicle)->with('status', 'Vehicle created.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load([
            'dealership',
            'privateSeller.user',
            'activeImages',
            'specifications',
            'campaign',
            'commissions.campaign',
            'commissions.recordedBy',
        ]);

        $breakdown = app(CommissionService::class)->breakdown($vehicle);
        $claims = $vehicle->purchaseClaims()->with(['user', 'buyerReward'])->latest()->get();
        $commissions = $vehicle->commissions()->with(['campaign', 'recordedBy'])->latest()->get();

        return view('admin.vehicles.show', [
            'vehicle' => $vehicle,
            'statuses' => Vehicle::statuses(),
            'breakdown' => $breakdown,
            'campaigns' => VehicleCampaign::orderBy('name')->get(),
            'claims' => $claims,
            'commissions' => $commissions,
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', array_merge(['vehicle' => $vehicle], $this->formData()));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($vehicle, $data) {
            $status = $data['status'] ?? $vehicle->status;

            $payload = array_merge($this->payload($data), [
                'status' => $status,
                'vehicle_reference' => $this->reference($data['vehicle_reference'] ?? null),
            ]);

            if ($status === Vehicle::STATUS_PUBLISHED && $vehicle->published_at === null) {
                $payload['published_at'] = now();
                $payload['expires_at'] = $vehicle->expires_at ?? now()->addDays(90);
            }

            $displayName = trim(implode(' ', array_filter([$data['make'] ?? '', $data['model'] ?? '', $data['variant'] ?? ''])));

            if ($displayName !== $vehicle->displayName()) {
                $payload['slug'] = $this->uniqueSlug($data['make'], $data['model'], $data['variant'] ?? null, $data['year']);
            }

            $vehicle->update($payload);

            if (! empty($data['images'])) {
                $this->storeImages($vehicle, $data['images']);
            }

            $this->applyCampaignConfig($vehicle, $data);

            AuditLogger::log('admin_vehicle_updated', 'vehicle', $vehicle->id, 'Vehicle listing updated by administrator');
        });

        return redirect()->route('admin.vehicles.show', $vehicle)->with('status', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        DB::transaction(function () use ($vehicle) {
            foreach ($vehicle->images()->get() as $image) {
                Storage::disk('public')->delete([$image->image_path, $image->thumbnail_path]);
            }

            AuditLogger::log('admin_vehicle_deleted', 'vehicle', $vehicle->id, 'Vehicle listing deleted by administrator');

            $vehicle->delete();
        });

        return redirect()->route('admin.vehicles.index')->with('status', 'Vehicle listing deleted.');
    }

    public function approve(Vehicle $vehicle)
    {
        DB::transaction(function () use ($vehicle) {
            $rules = FinancialRules::instance();

            $config = [
                'commission_type' => 'percent',
                'commission_value' => $rules->defaultCommissionPercent(),
                'reward_type' => 'commission_based',
                'reward_value' => 0,
                'reward_percent' => $rules->rewardPercentOfCommission(),
                'reward_min' => $rules->minReward(),
                'reward_max' => $rules->maxReward(),
            ];

            $vehicle->update([
                'status' => Vehicle::STATUS_PUBLISHED,
                'published_at' => $vehicle->published_at ?? now(),
                'expires_at' => $vehicle->expires_at ?? now()->addDays(90),
            ]);

            if (! $vehicle->campaignRule()) {
                app(CommissionService::class)->syncForVehicle($vehicle, $config, null, auth()->id());
            }

            AuditLogger::log('admin_vehicle_approved', 'vehicle', $vehicle->id, 'Vehicle approved and published with default commission/reward configuration');
        });

        return redirect()->route('admin.vehicles.show', $vehicle)->with('status', 'Vehicle approved and published.');
    }

    public function reject(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($vehicle, $data) {
            $notes = $vehicle->admin_notes ?? [];
            $notes['rejection_reason'] = $data['rejection_reason'] ?? null;

            $vehicle->update(['status' => Vehicle::STATUS_REJECTED, 'admin_notes' => $notes]);

            AuditLogger::log('admin_vehicle_rejected', 'vehicle', $vehicle->id, 'Vehicle listing rejected by administrator');
        });

        return redirect()->route('admin.vehicles.show', $vehicle)->with('status', 'Vehicle rejected.');
    }

    public function toggleFeature(Vehicle $vehicle)
    {
        DB::transaction(function () use ($vehicle) {
            $vehicle->update(['is_featured' => ! $vehicle->is_featured]);

            AuditLogger::log('admin_vehicle_featured', 'vehicle', $vehicle->id, $vehicle->is_featured ? 'Vehicle marked as featured' : 'Vehicle unfeatured');
        });

        return back()->with('status', $vehicle->is_featured ? 'Vehicle featured.' : 'Vehicle unfeatured.');
    }

    public function changeStatus(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Vehicle::statuses()))],
        ]);

        DB::transaction(function () use ($vehicle, $data) {
            $payload = ['status' => $data['status']];

            if ($data['status'] === Vehicle::STATUS_PUBLISHED && $vehicle->published_at === null) {
                $payload['published_at'] = now();
                $payload['expires_at'] = $vehicle->expires_at ?? now()->addDays(90);
            }

            $vehicle->update($payload);

            AuditLogger::log('admin_vehicle_status', 'vehicle', $vehicle->id, "Vehicle status changed to {$data['status']}");
        });

        return redirect()->route('admin.vehicles.show', $vehicle)->with('status', 'Vehicle status updated.');
    }

    public function setCampaign(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'campaign_id' => ['nullable', 'integer', 'exists:vehicle_campaigns,id'],
            'commission_type' => ['nullable', 'string', 'in:percent,fixed'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
            'reward_type' => ['nullable', 'string', 'in:commission_based,fixed,percent'],
            'reward_value' => ['nullable', 'numeric', 'min:0'],
            'reward_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reward_min' => ['nullable', 'numeric', 'min:0'],
            'reward_max' => ['nullable', 'numeric', 'min:0'],
            'is_inactive' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($vehicle, $data) {
            $config = $this->normalizedConfig($data);
            $inactive = $request->boolean('is_inactive');
            $campaignId = $data['campaign_id'] ?? null;

            if ($campaignId) {
                CampaignVehicle::updateOrCreate(
                    ['campaign_id' => $campaignId, 'vehicle_id' => $vehicle->id],
                    $config + ['is_active' => ! $inactive]
                );

                $vehicle->update(['campaign_id' => $campaignId]);

                if ($config['commission_type']) {
                    app(CommissionService::class)->syncForVehicle($vehicle, $config, (int) $campaignId, auth()->id());
                }
            } else {
                CampaignVehicle::where('vehicle_id', $vehicle->id)->update(['is_active' => false]);
                $vehicle->update(['campaign_id' => null]);

                if ($this->hasUserConfig($data)) {
                    app(CommissionService::class)->syncForVehicle($vehicle, $config, null, auth()->id());
                }
            }

            AuditLogger::log('admin_vehicle_configuration', 'vehicle', $vehicle->id, 'Campaign and commission configuration updated');
        });

        return redirect()->route('admin.vehicles.show', $vehicle)->with('status', 'Configuration saved.');
    }

    protected function rules(): array
    {
        return [
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:80'],
            'variant' => ['nullable', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:1950', 'max:'.(now()->year + 1)],
            'mileage' => ['required', 'integer', 'min:0', 'max:3000000'],
            'price' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'currency' => ['nullable', 'string', 'size:3'],
            'vehicle_reference' => ['nullable', 'string', 'max:30'],
            'transmission' => ['nullable', 'string', 'in:Automatic,Manual,CVT,Tiptronic,Dual-Clutch'],
            'fuel_type' => ['nullable', 'string', 'in:Petrol,Diesel,Hybrid,Plug-in Hybrid,Electric'],
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
            'seller_type' => ['required', 'string', 'in:dealership,private'],
            'status' => ['required', 'string', Rule::in(array_keys(Vehicle::statuses()))],
            'dealership_id' => ['nullable', 'integer', 'exists:dealerships,id'],
            'private_seller_id' => ['nullable', 'integer', 'exists:private_sellers,id'],
            'is_featured' => ['nullable', 'boolean'],
            'campaign_id' => ['nullable', 'integer', 'exists:vehicle_campaigns,id'],
            'commission_type' => ['nullable', 'string', 'in:percent,fixed'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
            'reward_type' => ['nullable', 'string', 'in:commission_based,fixed,percent'],
            'reward_value' => ['nullable', 'numeric', 'min:0'],
            'reward_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reward_min' => ['nullable', 'numeric', 'min:0'],
            'reward_max' => ['nullable', 'numeric', 'min:0'],
            'images' => ['nullable', 'array', 'max:'.config('worxcars.images_per_vehicle_max', 20)],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function payload(array $data): array
    {
        $keys = [
            'make', 'model', 'variant', 'year', 'mileage', 'price', 'currency',
            'transmission', 'fuel_type', 'body_type', 'engine_size', 'engine_cc',
            'power_kw', 'torque_nm', 'colour', 'doors', 'seats', 'condition',
            'vehicle_condition_detail', 'registration_status', 'description',
            'location_province', 'location_city', 'location_suburb', 'vin', 'reg_number',
            'date_first_registered', 'seo_title', 'seo_description',
            'seller_type', 'dealership_id', 'private_seller_id',
        ];

        $payload = array_merge(array_intersect_key($data, array_flip($keys)), [
            'features' => $this->featureList($data['features'] ?? null),
            'is_featured' => isset($data['is_featured']) ? (bool) $data['is_featured'] : false,
        ]);

        if (! $data['dealership_id'] ?? true) {
            $payload['dealership_id'] = null;
        }

        if (! $data['private_seller_id'] ?? true) {
            $payload['private_seller_id'] = null;
        }

        if (($data['seller_type'] ?? null) === 'dealership') {
            $payload['private_seller_id'] = null;
        } elseif (($data['seller_type'] ?? null) === 'private') {
            $payload['dealership_id'] = null;
        }

        return $payload;
    }

    protected function featureList(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return collect(preg_split('/[\r\n,]+/', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    protected function reference(?string $reference): string
    {
        if ($reference !== null && trim($reference) !== '') {
            return strtoupper(trim($reference));
        }

        do {
            $ref = 'WC-'.strtoupper(Str::random(6));
        } while (Vehicle::withTrashed()->where('vehicle_reference', $ref)->exists());

        return $ref;
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

    protected function applyCampaignConfig(Vehicle $vehicle, array $data): void
    {
        $config = $this->normalizedConfig($data);
        $campaignId = $data['campaign_id'] ?? null;

        if ($campaignId) {
            CampaignVehicle::updateOrCreate(
                ['campaign_id' => $campaignId, 'vehicle_id' => $vehicle->id],
                $config + ['is_active' => true]
            );

            $vehicle->update(['campaign_id' => $campaignId]);

            if ($config['commission_type']) {
                app(CommissionService::class)->syncForVehicle($vehicle, $config, (int) $campaignId, auth()->id());
            }
        } else {
            CampaignVehicle::where('vehicle_id', $vehicle->id)->update(['is_active' => false]);
            $vehicle->update(['campaign_id' => null]);

            if ($this->hasUserConfig($data)) {
                app(CommissionService::class)->syncForVehicle($vehicle, $config, null, auth()->id());
            }
        }
    }

    protected function normalizedConfig(array $data): array
    {
        $rules = FinancialRules::instance();

        return [
            'commission_type' => $data['commission_type'] ?? ($data['commission_value'] !== null ? 'percent' : 'percent'),
            'commission_value' => $data['commission_value'] ?? $rules->defaultCommissionPercent(),
            'reward_type' => $data['reward_type'] ?? 'commission_based',
            'reward_value' => $data['reward_value'] ?? 0,
            'reward_percent' => $data['reward_percent'] ?? $rules->rewardPercentOfCommission(),
            'reward_min' => $data['reward_min'] ?? $rules->minReward(),
            'reward_max' => $data['reward_max'] ?? $rules->maxReward(),
        ];
    }

    protected function hasUserConfig(array $data): bool
    {
        return ! empty($data['commission_type'])
            || ! empty($data['reward_type'])
            || $data['commission_value'] !== null
            || $data['reward_value'] !== null
            || $data['reward_percent'] !== null
            || $data['reward_min'] !== null
            || $data['reward_max'] !== null;
    }

    protected function formData(): array
    {
        return [
            'statuses' => Vehicle::statuses(),
            'dealerships' => Dealership::orderBy('business_name')->get(),
            'sellers' => PrivateSeller::with('user')->orderBy('id')->get(),
            'campaigns' => VehicleCampaign::orderBy('name')->get(),
        ];
    }
}