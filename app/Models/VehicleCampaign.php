<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleCampaign extends Model
{
    use HasFactory;
    use GeneratesUlid;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'ulid', 'name', 'slug', 'description', 'terms', 'start_date', 'end_date',
        'status', 'banner_path', 'is_featured', 'reward_rules', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_featured' => 'boolean',
            'reward_rules' => 'array',
        ];
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'campaign_vehicles', 'campaign_id', 'vehicle_id')
            ->withPivot(['commission_type', 'commission_value', 'reward_type', 'reward_value', 'reward_percent', 'reward_min', 'reward_max', 'is_active'])
            ->withTimestamps();
    }

    public function campaignVehicles(): HasMany
    {
        return $this->hasMany(CampaignVehicle::class, 'campaign_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $now = now();

        return ($this->start_date === null || $this->start_date <= $now)
            && ($this->end_date === null || $this->end_date >= $now);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function resolveRouteKeyName(): string
    {
        return 'slug';
    }
}