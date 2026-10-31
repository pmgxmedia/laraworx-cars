<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use App\Services\CommissionService;
use App\Support\FinancialRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Vehicle extends Model
{
    use HasFactory;
    use GeneratesUlid;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_SOLD = 'sold';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'ulid', 'vehicle_reference', 'dealership_id', 'private_seller_id', 'seller_type', 'status',
        'make', 'model', 'variant', 'year', 'mileage', 'price', 'currency', 'transmission',
        'fuel_type', 'body_type', 'engine_size', 'engine_cc', 'power_kw', 'torque_nm', 'colour',
        'doors', 'seats', 'condition', 'vehicle_condition_detail', 'registration_status',
        'description', 'features', 'location_province', 'location_city', 'location_suburb',
        'vin', 'reg_number', 'is_featured', 'is_promoted', 'views_count', 'slug', 'seo_title',
        'seo_description', 'date_first_registered', 'campaign_id', 'admin_notes',
        'published_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage' => 'integer',
            'price' => 'decimal:2',
            'engine_cc' => 'decimal:0',
            'power_kw' => 'decimal:2',
            'torque_nm' => 'decimal:2',
            'doors' => 'integer',
            'seats' => 'integer',
            'views_count' => 'integer',
            'features' => 'array',
            'admin_notes' => 'array',
            'vin' => 'encrypted',
            'reg_number' => 'encrypted',
            'date_first_registered' => 'date',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_promoted' => 'boolean',
        ];
    }

    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }

    public function privateSeller(): BelongsTo
    {
        return $this->belongsTo(PrivateSeller::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(VehicleCampaign::class, 'campaign_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function activeImages(): HasMany
    {
        return $this->hasMany(VehicleImage::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(VehicleSpecification::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(BuyerEnquiry::class);
    }

    public function purchaseClaims(): HasMany
    {
        return $this->hasMany(PurchaseClaim::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(VehicleCampaign::class, 'campaign_vehicles', 'vehicle_id', 'campaign_id')
            ->withPivot(['commission_type', 'commission_value', 'reward_type', 'reward_value', 'reward_percent', 'reward_min', 'reward_max', 'is_active'])
            ->withTimestamps();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function comparisons(): HasMany
    {
        return $this->hasMany(Comparison::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)->whereNotNull('published_at');
    }

    public function scopeFeatured(Builder $query, bool $only = true): Builder
    {
        return $only ? $query->where('is_featured', true) : $query;
    }

    public function scopeNewListings(Builder $query, bool $only = true): Builder
    {
        return $only ? $query->where('published_at', '>=', now()->subDays(14)) : $query;
    }

    public function scopeSearchQuery(Builder $query, ?string $term): Builder
    {
        if ($term === null || trim($term) === '') {
            return $query;
        }

        $terms = preg_split('/\s+/', trim($term));

        return $query->where(function (Builder $query) use ($terms) {
            foreach ($terms as $word) {
                $needle = "%{$word}%";

                $query->where(function (Builder $query) use ($needle) {
                    $query->where('make', 'like', $needle)
                        ->orWhere('model', 'like', $needle)
                        ->orWhere('variant', 'like', $needle)
                        ->orWhere('description', 'like', $needle)
                        ->orWhere('vehicle_reference', 'like', $needle)
                        ->orWhere('location_city', 'like', $needle)
                        ->orWhere('location_province', 'like', $needle);
                });
            }
        });
    }

    public function scopeByMaker(Builder $query, ?string $make): Builder
    {
        return $query->when($make, fn (Builder $q) => $q->where('make', $make));
    }

    public function scopeByModel(Builder $query, ?string $model): Builder
    {
        return $query->when($model, fn (Builder $q) => $q->where('model', $model));
    }

    public function scopeWithinPrice(Builder $query, ?float $min, ?float $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', $max));
    }

    public function scopeWithinYear(Builder $query, ?int $min, ?int $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('year', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('year', '<=', $max));
    }

    public function scopeWithinMileage(Builder $query, ?int $max): Builder
    {
        return $query->when($max !== null, fn (Builder $q) => $q->where('mileage', '<=', $max));
    }

    public function scopeWithTransmission(Builder $query, ?string $transmission): Builder
    {
        return $query->when($transmission, fn (Builder $q) => $q->where('transmission', $transmission));
    }

    public function scopeWithFuelType(Builder $query, ?string $fuelType): Builder
    {
        return $query->when($fuelType, fn (Builder $q) => $q->where('fuel_type', $fuelType));
    }

    public function scopeWithBodyType(Builder $query, ?string $bodyType): Builder
    {
        return $query->when($bodyType, fn (Builder $q) => $q->where('body_type', $bodyType));
    }

    public function scopeWithinLocation(Builder $query, ?string $province, ?string $city): Builder
    {
        return $query
            ->when($province, fn (Builder $q) => $q->where('location_province', $province))
            ->when($city, fn (Builder $q) => $q->where('location_city', $city));
    }

    public function scopeWithSellerType(Builder $query, ?string $sellerType): Builder
    {
        return $query->when($sellerType, fn (Builder $q) => $q->where('seller_type', $sellerType));
    }

    public function scopeWithReward(Builder $query, float|int $min = 0): Builder
    {
        if ($min <= 0) {
            return $query;
        }

        $now = now();

        return $query->where(function (Builder $query) use ($min, $now) {
            $query->whereExists(function ($sub) use ($min) {
                $sub->selectRaw('1')
                    ->from('commissions')
                    ->whereColumn('commissions.vehicle_id', 'vehicles.id')
                    ->whereNull('commissions.campaign_id')
                    ->where('commissions.reward_amount', '>=', $min);
            });

            $query->orWhereExists(function ($sub) use ($now) {
                $sub->selectRaw('1')
                    ->from('campaign_vehicles')
                    ->join('vehicle_campaigns', 'vehicle_campaigns.id', '=', 'campaign_vehicles.campaign_id')
                    ->whereColumn('campaign_vehicles.vehicle_id', 'vehicles.id')
                    ->where('campaign_vehicles.is_active', true)
                    ->where('vehicle_campaigns.status', VehicleCampaign::STATUS_ACTIVE)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('vehicle_campaigns.start_date')
                            ->orWhere('vehicle_campaigns.start_date', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('vehicle_campaigns.end_date')
                            ->orWhere('vehicle_campaigns.end_date', '>=', $now);
                    })
                    ->where(function ($q) {
                        $q->where('campaign_vehicles.reward_value', '>', 0)
                            ->orWhere('campaign_vehicles.reward_percent', '>', 0)
                            ->orWhere('campaign_vehicles.reward_type', 'commission_based');
                    });
            });
        });
    }

    public function scopeWithEffectiveReward(Builder $query): Builder
    {
        if ($query->getQuery()->columns !== null) {
            return $query;
        }

        $rules = FinancialRules::instance();
        $commissionPercent = $rules->defaultCommissionPercent();
        $rewardPercent = $rules->rewardPercentOfCommission();
        $minReward = $rules->minReward();
        $maxReward = $rules->maxReward();

        $rewardCalc = '(('.$rewardPercent.'/100.0) * ('.$commissionPercent.'/100.0) * vehicles.price)';

        $defaultExpr = 'CASE WHEN '.$rewardCalc.' < '.$minReward.' THEN '.$minReward
            .' WHEN '.$rewardCalc.' > '.$maxReward.' THEN '.$maxReward
            .' ELSE '.$rewardCalc.' END';

        $commissionExpr = 'SELECT commissions.reward_amount FROM commissions '
            .'WHERE commissions.vehicle_id = vehicles.id '
            .'AND commissions.campaign_id IS NULL '
            .'ORDER BY commissions.id DESC LIMIT 1';

        $campaignRawReward = 'CASE cv.reward_type '
            .'WHEN \'percent\' THEN (cv.reward_value / 100.0) * vehicles.price '
            .'WHEN \'commission_based\' THEN (cv.reward_percent / 100.0) * CASE WHEN cv.commission_type = \'percent\' THEN (cv.commission_value / 100.0) * vehicles.price ELSE cv.commission_value END '
            .'ELSE cv.reward_value END';

        $campaignExpr = 'CASE '
            .'WHEN ('.$campaignRawReward.') > 0 AND cv.reward_max > 0 AND ('.$campaignRawReward.') > cv.reward_max THEN cv.reward_max '
            .'WHEN ('.$campaignRawReward.') > 0 AND cv.reward_min > 0 AND ('.$campaignRawReward.') < cv.reward_min THEN cv.reward_min '
            .'ELSE ('.$campaignRawReward.') END';

        $expression = 'COALESCE(('
            .'SELECT '.$campaignExpr.' FROM campaign_vehicles cv '
            .'JOIN vehicle_campaigns vc ON vc.id = cv.campaign_id '
            .'WHERE cv.vehicle_id = vehicles.id '
            .'AND cv.is_active = 1 '
            .'AND vc.status = \''.VehicleCampaign::STATUS_ACTIVE.'\' '
            .'AND (vc.start_date IS NULL OR vc.start_date <= \''.now()->format('Y-m-d H:i:s').'\') '
            .'AND (vc.end_date IS NULL OR vc.end_date >= \''.now()->format('Y-m-d H:i:s').'\') '
            .'ORDER BY cv.id DESC LIMIT 1'
            .'), ('.$commissionExpr.'), '.$defaultExpr.')';

        return $query
            ->select('vehicles.*')
            ->addSelect(DB::raw($expression.' as effective_reward_amount'));
    }

    public function campaignRule(): ?CampaignVehicle
    {
        if ($this->relationLoaded('campaigns')) {
            foreach ($this->campaigns as $campaign) {
                $pivot = $campaign->pivot;
                $pivotVehicleId = $pivot?->vehicle_id ?? $this->id;

                if (! $pivot?->is_active || $pivotVehicleId != $this->id) {
                    continue;
                }

                if (! $campaign->isActive()) {
                    continue;
                }

                return new CampaignVehicle([
                    'campaign_id' => $campaign->id,
                    'vehicle_id' => $this->id,
                    'commission_type' => $pivot->commission_type,
                    'commission_value' => $pivot->commission_value,
                    'reward_type' => $pivot->reward_type,
                    'reward_value' => $pivot->reward_value,
                    'reward_percent' => $pivot->reward_percent,
                    'reward_min' => $pivot->reward_min,
                    'reward_max' => $pivot->reward_max,
                    'is_active' => true,
                ]);
            }
        }

        return CampaignVehicle::query()
            ->where('vehicle_id', $this->id)
            ->where('is_active', true)
            ->whereHas('campaign', function ($q) {
                $q->where('status', VehicleCampaign::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                    });
            })
            ->latest('id')
            ->first();
    }

    public function activeCommission(): ?Commission
    {
        if ($this->relationLoaded('commissions')) {
            return $this->commissions
                ->filter(fn (Commission $commission) => $commission->campaign_id === null)
                ->sortByDesc('id')
                ->first();
        }

        return Commission::query()
            ->where('vehicle_id', $this->id)
            ->whereNull('campaign_id')
            ->latest('id')
            ->first();
    }

    public function activeRewardAmount(): float
    {
        try {
            $breakdown = app(CommissionService::class)->breakdown($this);

            return (float) $breakdown['reward_amount'];
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function displayName(): string
    {
        return trim(implode(' ', array_filter([
            $this->make,
            $this->model,
            $this->variant,
        ]))) ?: ($this->vehicle_reference ?? 'Vehicle');
    }

    public function sellerName(): ?string
    {
        if ($this->seller_type === 'private' && $this->privateSeller) {
            return $this->privateSeller->user?->fullName() ?? $this->privateSeller->contact_email;
        }

        if ($this->dealership) {
            return $this->dealership->trading_name ?? $this->dealership->business_name;
        }

        return null;
    }

    public function mainImage(): ?VehicleImage
    {
        return $this->activeImages->first();
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_SOLD => 'Sold',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_REJECTED => 'Rejected',
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