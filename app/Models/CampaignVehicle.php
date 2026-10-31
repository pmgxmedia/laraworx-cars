<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignVehicle extends Model
{
    protected $table = 'campaign_vehicles';

    protected $fillable = [
        'campaign_id', 'vehicle_id', 'commission_type', 'commission_value', 'reward_type',
        'reward_value', 'reward_percent', 'reward_min', 'reward_max', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(VehicleCampaign::class, 'campaign_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}