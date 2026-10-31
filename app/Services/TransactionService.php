<?php

namespace App\Services;

use App\Models\PurchaseClaim;
use App\Models\RewardPayout;
use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionService
{
    public function recordClaimLedger(PurchaseClaim $claim): Transaction
    {
        return Transaction::firstOrCreate(
            ['transaction_reference' => $this->reference('WC-T'), 'purchase_claim_id' => $claim->id],
            [
                'type' => 'commission',
                'vehicle_id' => $claim->vehicle_id,
                'user_id' => $claim->user_id,
                'dealership_id' => $claim->dealership_id,
                'amount' => $claim->commission_amount,
                'commission_amount' => $claim->commission_amount,
                'reward_amount' => $claim->reward_amount,
                'retained_amount' => $claim->retained_amount,
                'direction' => 'in',
                'status' => 'completed',
                'description' => 'WorxCars commission on verified purchase for claim '.$claim->claim_reference,
                'metadata' => [
                    'claim_reference' => $claim->claim_reference,
                    'sale_price' => $claim->purchase_price,
                    'recorded_at' => now()->toDateTimeString(),
                ],
                'occurred_at' => $claim->verified_at ?? now(),
            ]
        );
    }

    public function recordPayout(RewardPayout $payout): Transaction
    {
        $reward = $payout->buyerReward;

        return Transaction::firstOrCreate(
            [
                'transaction_reference' => $this->reference('WC-T'),
                'purchase_claim_id' => $reward->purchase_claim_id,
            ],
            [
                'type' => 'payout',
                'vehicle_id' => $reward->vehicle_id,
                'user_id' => $reward->user_id,
                'dealership_id' => $reward->vehicle?->dealership_id,
                'amount' => $payout->amount,
                'reward_amount' => $payout->amount,
                'direction' => 'out',
                'status' => 'completed',
                'description' => 'WorxCars Buyer Reward payout '.$payout->payout_reference,
                'metadata' => [
                    'payout_reference' => $payout->payout_reference,
                    'reward_reference' => $reward->reward_reference,
                    'provider' => $payout->provider,
                    'provider_transaction_id' => $payout->provider_transaction_id,
                ],
                'occurred_at' => $payout->completed_at ?? now(),
            ]
        );
    }

    protected function reference(string $prefix): string
    {
        return $prefix.'-'.strtoupper(Str::random(8));
    }
}