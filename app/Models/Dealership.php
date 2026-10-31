<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealership extends Model
{
    use HasFactory;
    use GeneratesUlid;
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'ulid', 'business_name', 'trading_name', 'registration_number', 'vat_number',
        'contact_person_name', 'email', 'phone', 'website', 'logo_path', 'description',
        'street_address', 'city', 'province', 'postal_code', 'status', 'verified_at',
        'bank_account_name', 'bank_name', 'bank_account_number', 'bank_branch_code',
        'bank_account_type', 'commission_agreement_path', 'contract_path', 'admin_notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'is_active' => 'boolean',
            'bank_account_number' => 'encrypted',
            'bank_branch_code' => 'encrypted',
        ];
    }

    public function users()
    {
        return $this->hasMany(DealershipUser::class);
    }

    public function userAccounts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dealership_users')
            ->withPivot('role_title', 'is_primary');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function purchaseClaims(): HasMany
    {
        return $this->hasMany(PurchaseClaim::class);
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED && $this->is_active;
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function resolveRouteKeyName(): string
    {
        return 'ulid';
    }
}