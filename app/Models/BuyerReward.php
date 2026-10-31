<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerReward extends Model
{
    use HasFactory;
    use GeneratesUlid;
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ELIGIBLE = 'eligible';
    public const STATUS_UNDER_VERIFICATION = 'under_verification';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'ulid', 'reward_reference', 'purchase_claim_id', 'user_id', 'vehicle_id', 'campaign_id',
        'amount', 'currency', 'status', 'status_at', 'approved_by', 'approved_at',
        'eligibility_confirmed_at', 'paid_at', 'configuration_snapshot', 'payout_snapshot', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status_at' => 'datetime',
            'approved_at' => 'datetime',
            'eligibility_confirmed_at' => 'datetime',
            'paid_at' => 'datetime',
            'configuration_snapshot' => 'array',
            'payout_snapshot' => 'array',
        ];
    }

    public function purchaseClaim(): BelongsTo
    {
        return $this->belongsTo(PurchaseClaim::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(VehicleCampaign::class, 'campaign_id');
    }

    public function payout()
    {
        return $this->hasOne(RewardPayout::class);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ELIGIBLE => 'Eligible',
            self::STATUS_UNDER_VERIFICATION => 'Under Verification',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}