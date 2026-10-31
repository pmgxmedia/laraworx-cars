<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseClaim extends Model
{
    use HasFactory;
    use GeneratesUlid;
    use SoftDeletes;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_SELLER_VERIFICATION = 'seller_verification';
    public const STATUS_TRANSACTION_VERIFIED = 'transaction_verified';
    public const STATUS_REWARD_APPROVED = 'reward_approved';
    public const STATUS_PAYMENT_PROCESSING = 'payment_processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_DISPUTED = 'disputed';

    protected $fillable = [
        'ulid', 'claim_reference', 'user_id', 'vehicle_id', 'dealership_id', 'private_seller_id',
        'purchase_date', 'purchase_price', 'provider_reference', 'description', 'status',
        'commission_amount', 'reward_amount', 'retained_amount', 'commission_snapshot',
        'reward_snapshot', 'fraud_flags', 'declarations', 'bank_account_name', 'bank_name',
        'bank_account_number', 'bank_branch_code', 'declaration_consent', 'seller_verified_by',
        'seller_verified_at', 'seller_verification_note', 'verified_by', 'verified_at',
        'approved_by', 'approved_at', 'rejected_by', 'rejected_at', 'rejection_reason',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'reward_amount' => 'decimal:2',
            'retained_amount' => 'decimal:2',
            'commission_snapshot' => 'array',
            'reward_snapshot' => 'array',
            'fraud_flags' => 'array',
            'declarations' => 'array',
            'seller_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'declaration_consent' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }

    public function privateSeller(): BelongsTo
    {
        return $this->belongsTo(PrivateSeller::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PurchaseClaimDocument::class);
    }

    public function buyerReward()
    {
        return $this->hasOne(BuyerReward::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_SELLER_VERIFICATION => 'Seller Verification',
            self::STATUS_TRANSACTION_VERIFIED => 'Transaction Verified',
            self::STATUS_REWARD_APPROVED => 'Reward Approved',
            self::STATUS_PAYMENT_PROCESSING => 'Payment Processing',
            self::STATUS_PAID => 'Paid',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_DISPUTED => 'Disputed',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}