<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use GeneratesUlid;

    protected $fillable = [
        'ulid', 'transaction_reference', 'type', 'vehicle_id', 'purchase_claim_id', 'user_id',
        'dealership_id', 'amount', 'commission_amount', 'reward_amount', 'retained_amount',
        'currency', 'direction', 'status', 'description', 'metadata', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'reward_amount' => 'decimal:2',
            'retained_amount' => 'decimal:2',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function purchaseClaim()
    {
        return $this->belongsTo(PurchaseClaim::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dealership()
    {
        return $this->belongsTo(Dealership::class);
    }
}