<?php

namespace App\Services\Payments;

use App\Models\RewardPayout;
use App\Services\Payments\Contracts\PayoutGateway;

/**
 * Default inserter that simulates the payout provider.
 *
 * In production a real South African payout provider (EFT / PayShap / payment
 * gateway token) would be bound to the PayoutGateway contract through the
 * configured provider name. Swap this class for a real integration without
 * touching the rest of the reward flow.
 */
class MockPayoutGateway implements PayoutGateway
{
    public function pay(RewardPayout $payout): array
    {
        $mock = (bool) config('worxcars.payout_mock_failure', false);

        if ($mock) {
            return [
                'success' => false,
                'message' => 'Mock provider simulated a payout failure for testing.',
            ];
        }

        return [
            'success' => true,
            'provider_transaction_id' => 'MOCK-'.strtoupper(bin2hex(random_bytes(6))),
            'message' => 'Mock payout processed successfully (no real funds moved).',
        ];
    }

    public function name(): string
    {
        return 'mock';
    }
}