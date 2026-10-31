<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use GeneratesUlid;

    protected $fillable = [
        'ulid', 'vehicle_id', 'dealership_id', 'seller_type', 'campaign_id', 'commission_type',
        'commission_value', 'commission_amount', 'reward_type', 'reward_value', 'reward_percent',
        'reward_min', 'reward_max', 'reward_amount', 'retained_amount', 'recorded_by',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(VehicleCampaign::class, 'campaign_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}