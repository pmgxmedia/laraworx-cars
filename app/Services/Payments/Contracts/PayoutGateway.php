<?php

namespace App\Services\Payments\Contracts;

use App\Models\RewardPayout;

interface PayoutGateway
{
    /**
     * @return array{success: bool, provider_transaction_id?: string, message?: string}
     */
    public function pay(RewardPayout $payout): array;

    public function name(): string;
}