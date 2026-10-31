<?php

namespace App\Services\Payments\Contracts;

use App\Models\RewardPayout;

interface PaymentGateway
{
    public function supportsProvider(string $provider): bool;

    public function resolve(string $provider): PayoutGateway;
}