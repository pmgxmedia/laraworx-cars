<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateSeller extends Model
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
        'ulid', 'user_id', 'contact_email', 'phone', 'id_number', 'street_address',
        'city', 'province', 'postal_code', 'id_document_path', 'registration_document_path',
        'status', 'verified_at', 'admin_notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'id_number' => 'encrypted',
            'verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
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
}