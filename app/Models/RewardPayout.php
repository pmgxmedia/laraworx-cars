<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPayout extends Model
{
    use GeneratesUlid;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REVERSED = 'reversed';

    public const PROVIDER_MANUAL = 'manual';

    protected $fillable = [
        'ulid', 'payout_reference', 'buyer_reward_id', 'user_id', 'amount', 'currency',
        'status', 'provider', 'provider_transaction_id', 'provider_response', 'recipient',
        'requested_at', 'completed_at', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'provider_response' => 'array',
            'recipient' => 'array',
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function buyerReward(): BelongsTo
    {
        return $this->belongsTo(BuyerReward::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}