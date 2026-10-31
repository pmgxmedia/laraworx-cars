<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phone',
        'street_address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'avatar_path',
        'id_number_tail',
        'is_active',
        'login_attempts',
        'locked_until',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'password' => 'hashed',
        ];
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_OPERATIONS = 'operations_admin';

    public const ROLE_FINANCE = 'finance_admin';

    public const ROLE_CONTENT = 'content_admin';

    public const ROLE_SUPPORT = 'support_admin';

    public const ROLE_DEALERSHIP = 'dealership';

    public const ROLE_PRIVATE_SELLER = 'private_seller';

    public const ROLE_CUSTOMER = 'customer';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        $permissionIds = $this->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->pluck('id')->unique();

        return Permission::whereIn('id', $permissionIds);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole(self::ROLE_SUPER_ADMIN)) {
            return true;
        }

        return $this->roles()->whereHas('permissions', fn ($q) => $q->where('name', $permission))->exists();
    }

    public function isAdmin(): bool
    {
        return $this->roles()->whereIn('name', [
            self::ROLE_SUPER_ADMIN, self::ROLE_OPERATIONS, self::ROLE_FINANCE,
            self::ROLE_CONTENT, self::ROLE_SUPPORT,
        ])->exists();
    }

    public function isDealership(): bool
    {
        return $this->hasRole(self::ROLE_DEALERSHIP);
    }

    public function isPrivateSeller(): bool
    {
        return $this->hasRole(self::ROLE_PRIVATE_SELLER);
    }

    public function fullName(): string
    {
        return trim($this->name.' '.$this->surname) ?: $this->email;
    }

    public function dealership(): ?Dealership
    {
        $link = DealershipUser::where('user_id', $this->id)->first();

        return $link?->dealership;
    }

    public function dealershipUsers(): HasMany
    {
        return $this->hasMany(DealershipUser::class);
    }

    public function privateSellers(): HasMany
    {
        return $this->hasMany(PrivateSeller::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(BuyerEnquiry::class);
    }

    public function purchaseClaims(): HasMany
    {
        return $this->hasMany(PurchaseClaim::class);
    }

    public function buyerRewards(): HasMany
    {
        return $this->hasMany(BuyerReward::class);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', $role));
    }
}
