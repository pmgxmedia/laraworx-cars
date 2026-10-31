<?php

namespace App\Services;

use App\Models\BuyerReward;
use App\Models\RewardPayout;
use App\Support\AuditLogger;
use App\Services\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayoutService
{
    public function __construct(protected PaymentGateway $gateways, protected TransactionService $transactions)
    {
    }

    public function schedule(BuyerReward $reward, ?int $userId = null): RewardPayout
    {
        return DB::transaction(function () use ($reward, $userId) {
            $payout = RewardPayout::create([
                'payout_reference' => $this->nextReference(),
                'buyer_reward_id' => $reward->id,
                'user_id' => $reward->user_id,
                'amount' => $reward->amount,
                'currency' => $reward->currency,
                'status' => RewardPayout::STATUS_SCHEDULED,
                'provider' => config('worxcars.payout_provider', 'manual'),
                'recipient' => [
                    'account_name' => $reward->purchaseClaim?->bank_account_name,
                    'bank_name' => $reward->purchaseClaim?->bank_name,
                    'bank_branch_code' => $reward->purchaseClaim?->bank_branch_code,
                ],
                'requested_at' => now(),
            ]);

            AuditLogger::log('payout_scheduled', 'reward_payout', $payout->id, 'Reward payout scheduled', null, ['amount' => $payout->amount]);

            return $payout;
        });
    }

    public function execute(RewardPayout $payout, ?int $userId = null): RewardPayout
    {
        return DB::transaction(function () use ($payout, $userId) {
            if ($payout->status === RewardPayout::STATUS_COMPLETED) {
                return $payout;
            }

            $payout->update(['status' => RewardPayout::STATUS_PROCESSING, 'provider_response' => null]);

            $gateway = $this->gateways->resolve($payout->provider);
            $result = $gateway->pay($payout);

            if (! ($result['success'] ?? false)) {
                $payout->update([
                    'status' => RewardPayout::STATUS_FAILED,
                    'failure_reason' => $result['message'] ?? 'Payout failed',
                    'completed_at' => now(),
                ]);

                AuditLogger::log('payout_failed', 'reward_payout', $payout->id, 'Reward payout failed', null, ['reason' => $payout->failure_reason], $userId);

                return $payout;
            }

            $payout->update([
                'status' => RewardPayout::STATUS_COMPLETED,
                'provider_transaction_id' => $result['provider_transaction_id'] ?? null,
                'provider_response' => $result,
                'completed_at' => now(),
            ]);

            $reward = $payout->buyerReward;
            $reward->update([
                'status' => BuyerReward::STATUS_PAID,
                'paid_at' => now(),
                'payout_snapshot' => [
                    'reference' => $payout->payout_reference,
                    'provider' => $payout->provider,
                    'provider_transaction_id' => $payout->provider_transaction_id,
                    'paid_at' => $payout->completed_at?->toDateTimeString(),
                ],
            ]);

            $reward->purchaseClaim?->update(['status' => \App\Models\PurchaseClaim::STATUS_PAID]);

            $this->transactions->recordPayout($payout);

            AuditLogger::log('payout_completed', 'reward_payout', $payout->id, 'Reward payout completed', null, ['amount' => $payout->amount, 'provider' => $payout->provider], $userId);

            return $payout;
        });
    }

    protected function nextReference(): string
    {
        do {
            $reference = 'WC-P-'.strtoupper(Str::random(6));
        } while (RewardPayout::where('payout_reference', $reference)->exists());

        return $reference;
    }
}