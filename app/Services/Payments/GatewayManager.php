<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\Contracts\PayoutGateway;

class GatewayManager implements PaymentGateway
{
    public function __construct(protected array $gateways = [])
    {
    }

    public function supportsProvider(string $provider): bool
    {
        return isset($this->gateways[$provider]);
    }

    public function resolve(string $provider): PayoutGateway
    {
        if (! isset($this->gateways[$provider])) {
            throw new \RuntimeException("No payout gateway registered for provider [{$provider}].");
        }

        return $this->gateways[$provider];
    }
}