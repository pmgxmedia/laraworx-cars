<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignVehicle;
use App\Models\Vehicle;
use App\Models\VehicleCampaign;
use App\Services\CommissionService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCampaignController extends Controller
{
    public function __construct(protected CommissionService $commissions)
    {
    }

    public function index(Request $request): View
    {
        $rows = VehicleCampaign::query()
            ->withCount('campaignVehicles')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$request->input('q')}%")
                ->orWhere('slug', 'like', "%{$request->input('q')}%")))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.campaigns.index', [
            'rows' => $rows,
            'statuses' => VehicleCampaign::statuses(),
        ]);
    }

    public function create(): View
    {
        return view('admin.campaigns.create', ['statuses' => VehicleCampaign::statuses()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $campaign = VehicleCampaign::create($data + [
            'slug' => $this->uniqueSlug(Str::slug($data['name'])),
            'created_by' => auth()->id(),
        ]);

        AuditLogger::log('campaign_created', 'vehicle_campaign', $campaign->id, 'Campaign created from admin panel');

        return redirect()->route('admin.campaigns.show', $campaign)->with('success', 'Campaign created successfully.');
    }

    public function show(VehicleCampaign $campaign): View
    {
        $campaign->load(['createdBy', 'campaignVehicles.vehicle.dealership', 'campaignVehicles.vehicle.privateSeller.user']);

        $attachedIds = $campaign->campaignVehicles->pluck('vehicle_id')->all();

        $availableVehicles = Vehicle::where('status', Vehicle::STATUS_PUBLISHED)
            ->when(count($attachedIds), fn ($q) => $q->whereNotIn('id', $attachedIds))
            ->orderByDesc('created_at')
            ->get();

        return view('admin.campaigns.show', [
            'campaign' => $campaign,
            'availableVehicles' => $availableVehicles,
            'statuses' => VehicleCampaign::statuses(),
        ]);
    }

    public function edit(VehicleCampaign $campaign): View
    {
        return view('admin.campaigns.edit', [
            'campaign' => $campaign,
            'statuses' => VehicleCampaign::statuses(),
        ]);
    }

    public function update(Request $request, VehicleCampaign $campaign): RedirectResponse
    {
        $data = $this->validated($request);

        if ($data['name'] !== $campaign->name) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $campaign->id);
        }

        $campaign->update($data);

        AuditLogger::log('campaign_updated', 'vehicle_campaign', $campaign->id, 'Campaign updated from admin panel');

        return redirect()->route('admin.campaigns.show', $campaign)->with('success', 'Campaign updated successfully.');
    }

    public function destroy(VehicleCampaign $campaign): RedirectResponse
    {
        $campaign->delete();

        AuditLogger::log('campaign_deleted', 'vehicle_campaign', $campaign->id, 'Campaign removed');

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign removed.');
    }

    public function changeStatus(Request $request, VehicleCampaign $campaign): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(VehicleCampaign::statuses()))]]);

        $campaign->update(['status' => $data['status']]);

        AuditLogger::log('campaign_status_changed', 'vehicle_campaign', $campaign->id, 'Campaign status changed', null, ['status' => $data['status']]);

        return back()->with('success', 'Campaign status updated to '.$campaign->statusLabel().'.');
    }

    public function addVehicle(Request $request, VehicleCampaign $campaign): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'commission_type' => ['required', Rule::in(['fixed', 'percent'])],
            'commission_value' => ['required', 'numeric', 'min:0'],
            'reward_type' => ['required', Rule::in(['fixed', 'percent', 'commission_based'])],
            'reward_value' => ['nullable', 'numeric', 'min:0'],
            'reward_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reward_min' => ['nullable', 'numeric', 'min:0'],
            'reward_max' => ['nullable', 'numeric', 'min:0'],
        ]);

        $config = [
            'commission_type' => $data['commission_type'],
            'commission_value' => (float) $data['commission_value'],
            'reward_type' => $data['reward_type'],
            'reward_value' => (float) ($data['reward_value'] ?? 0),
            'reward_percent' => (float) ($data['reward_percent'] ?? 0),
            'reward_min' => isset($data['reward_min']) && $data['reward_min'] !== null && $data['reward_min'] !== '' ? (float) $data['reward_min'] : null,
            'reward_max' => isset($data['reward_max']) && $data['reward_max'] !== null && $data['reward_max'] !== '' ? (float) $data['reward_max'] : null,
        ];

        CampaignVehicle::updateOrCreate(
            ['campaign_id' => $campaign->id, 'vehicle_id' => $data['vehicle_id']],
            $config + ['is_active' => ! $request->boolean('is_inactive')]
        );

        $vehicle = Vehicle::find($data['vehicle_id']);
        $vehicle->update(['campaign_id' => $campaign->id]);

        $this->commissions->syncForVehicle($vehicle, $config, $campaign->id, auth()->id());

        AuditLogger::log('campaign_vehicle_added', 'vehicle_campaign', $campaign->id, 'Vehicle attached to campaign with commission configuration', null, ['vehicle_id' => $data['vehicle_id']]);

        return back()->with('success', 'Vehicle attached to the campaign with its commission/reward configuration.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(array_keys(VehicleCampaign::statuses()))],
            'is_featured' => ['nullable', 'boolean'],
        ]);
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $candidate = $slug;
        $suffix = 1;

        while (VehicleCampaign::where('slug', $candidate)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $candidate = $slug.'-'.(++$suffix);
        }

        return $candidate;
    }
}