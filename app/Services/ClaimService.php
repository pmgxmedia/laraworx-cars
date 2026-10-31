<?php

namespace App\Services;

use App\Events\PurchaseClaimStatusChanged;
use App\Models\BuyerReward;
use App\Models\PurchaseClaim;
use App\Models\RewardPayout;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\AuditLogger;
use App\Services\Payments\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClaimService
{
    public function __construct(
        protected CommissionService $commissions,
        protected PayoutService $payouts,
        protected PaymentGateway $gateways
    ) {
    }

    public function submit(User $user, Vehicle $vehicle, array $data, array $documents = []): PurchaseClaim
    {
        return DB::transaction(function () use ($user, $vehicle, $data, $documents) {
            $breakdown = $this->commissions->breakdown($vehicle);
            $reference = $this->nextReference('WC-C', PurchaseClaim::class, 'claim_reference');

            $claim = PurchaseClaim::create([
                'claim_reference' => $reference,
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'dealership_id' => $vehicle->dealership_id,
                'private_seller_id' => $vehicle->private_seller_id,
                'purchase_date' => $data['purchase_date'] ?? now()->toDateString(),
                'purchase_price' => $data['purchase_price'] ?? $vehicle->price,
                'provider_reference' => $data['provider_reference'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => PurchaseClaim::STATUS_SUBMITTED,
                'commission_amount' => $breakdown['commission_amount'],
                'reward_amount' => $breakdown['reward_amount'],
                'retained_amount' => $breakdown['retained_amount'],
                'commission_snapshot' => $breakdown['config'],
                'reward_snapshot' => [
                    'reward_amount' => $breakdown['reward_amount'],
                    'commission_amount' => $breakdown['commission_amount'],
                    'retained_amount' => $breakdown['retained_amount'],
                    'recorded_at' => now()->toDateTimeString(),
                ],
                'fraud_flags' => $this->fraudCheck($user, $vehicle, $data),
                'declarations' => collect($data['declarations'] ?? [])->mapWithKeys(fn ($v, $k) => [$k => filter_var($v, FILTER_VALIDATE_BOOLEAN)])->all(),
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_branch_code' => $data['bank_branch_code'] ?? null,
                'declaration_consent' => ! empty($data['declaration_consent']),
            ]);

            $reward = BuyerReward::create([
                'reward_reference' => $this->nextReference('WC-R', BuyerReward::class, 'reward_reference'),
                'purchase_claim_id' => $claim->id,
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'campaign_id' => $vehicle->campaign_id,
                'amount' => $breakdown['reward_amount'],
                'status' => BuyerReward::STATUS_PENDING,
                'status_at' => now(),
                'configuration_snapshot' => [
                    'config' => $breakdown['config'],
                    'source' => $breakdown['source'],
                    'recorded_at' => now()->toDateTimeString(),
                ],
            ]);

            foreach ($documents as $type => $path) {
                $claim->documents()->create([
                    'type' => $type,
                    'file_path' => $path['path'],
                    'file_name' => $path['name'] ?? null,
                    'mime_type' => $path['mime'] ?? null,
                    'size' => $path['size'] ?? null,
                ]);
            }

            if ($vehicle->status === Vehicle::STATUS_PUBLISHED) {
                $vehicle->update(['status' => Vehicle::STATUS_RESERVED]);
            }

            AuditLogger::log('claim_submitted', 'purchase_claim', $claim->id, 'Purchase claim submitted', null, ['reference' => $claim->claim_reference]);

            PurchaseClaimStatusChanged::dispatch($claim);

            return $claim;
        });
    }

    /**
     * @return array<int, string>
     */
    protected function fraudCheck(User $user, Vehicle $vehicle, array $data): array
    {
        $flags = [];

        if (PurchaseClaim::withTrashed()->where('user_id', $user->id)->where('vehicle_id', $vehicle->id)->exists()) {
            $flags[] = 'duplicate_vehicle_claim';
        }

        $duplicate = PurchaseClaim::withTrashed()
            ->where('user_id', $user->id)
            ->where('provider_reference', $data['provider_reference'] ?? null)
            ->whereNotNull('provider_reference')
            ->where('provider_reference', '!=', '')
            ->exists();

        if ($duplicate) {
            $flags[] = 'duplicate_transaction_reference';
        }

        $sameIdentityAccounts = User::where('email', '<>', $user->email)
            ->where('phone', $user->phone)
            ->whereNotNull('phone')
            ->count();

        if ($user->phone && $sameIdentityAccounts > 0) {
            $flags[] = 'shared_contact_information';
        }

        if ($vehicle->campaign_id && $vehicle->campaign?->end_date?->isPast()) {
            $flags[] = 'claim_after_campaign_expiry';
        }

        return $flags;
    }

    public function moveToUnderReview(PurchaseClaim $claim, ?int $adminId = null): void
    {
        $this->transition($claim, PurchaseClaim::STATUS_UNDER_REVIEW);
        $claim->buyerReward?->update(['status' => BuyerReward::STATUS_UNDER_VERIFICATION, 'status_at' => now()]);
    }

    public function sellerConfirm(PurchaseClaim $claim, ?string $note = null, ?int $userId = null): void
    {
        if ($claim->status !== PurchaseClaim::STATUS_SUBMITTED && $claim->status !== PurchaseClaim::STATUS_UNDER_REVIEW && $claim->status !== PurchaseClaim::STATUS_SELLER_VERIFICATION) {
            throw new \RuntimeException('Claim cannot be confirmed by the seller in its current state.');
        }

        $claim->update([
            'status' => PurchaseClaim::STATUS_TRANSACTION_VERIFIED,
            'seller_verified_by' => $userId,
            'seller_verified_at' => now(),
            'seller_verification_note' => $note,
        ]);

        $claim->buyerReward?->update(['status' => BuyerReward::STATUS_ELIGIBLE, 'eligibility_confirmed_at' => now(), 'status_at' => now()]);

        PurchaseClaimStatusChanged::dispatch($claim);
        AuditLogger::log('seller_confirmed_sale', 'purchase_claim', $claim->id, 'Seller confirmed the sale for purchase claim');
    }

    public function verifyTransaction(PurchaseClaim $claim, ?int $adminId = null): void
    {
        if ($claim->status !== PurchaseClaim::STATUS_TRANSACTION_VERIFIED && $claim->status !== PurchaseClaim::STATUS_SUBMITTED && $claim->status !== PurchaseClaim::STATUS_UNDER_REVIEW && $claim->status !== PurchaseClaim::STATUS_SELLER_VERIFICATION) {
            throw new \RuntimeException('Claim cannot be verified in its current state.');
        }

        $claim->update([
            'status' => PurchaseClaim::STATUS_TRANSACTION_VERIFIED,
            'verified_by' => $adminId ?? auth()->id(),
            'verified_at' => now(),
        ]);

        $claim->buyerReward?->update(['status' => BuyerReward::STATUS_ELIGIBLE, 'eligibility_confirmed_at' => now(), 'status_at' => now()]);

        PurchaseClaimStatusChanged::dispatch($claim);
        AuditLogger::log('claim_verified', 'purchase_claim', $claim->id, 'WorxCars verified the transaction for purchase claim');
    }

    public function approveReward(PurchaseClaim $claim, ?int $adminId = null): void
    {
        if ($claim->status !== PurchaseClaim::STATUS_TRANSACTION_VERIFIED) {
            throw new \RuntimeException('Claim must be transaction verified before the reward can be approved.');
        }

        $reward = $claim->buyerReward;

        if (! $reward) {
            throw new \RuntimeException('No reward record attached to this claim.');
        }

        $claim->update([
            'status' => PurchaseClaim::STATUS_REWARD_APPROVED,
            'approved_by' => $adminId ?? auth()->id(),
            'approved_at' => now(),
        ]);

        $reward->update([
            'status' => BuyerReward::STATUS_APPROVED,
            'approved_by' => $adminId ?? auth()->id(),
            'approved_at' => now(),
            'status_at' => now(),
        ]);

        PurchaseClaimStatusChanged::dispatch($claim);

        $this->payouts->schedule($reward, $adminId ?? auth()->id());
        $claim->update(['status' => PurchaseClaim::STATUS_PAYMENT_PROCESSING]);

        AuditLogger::log('reward_approved', 'buyer_reward', $reward->id, 'Buyer reward approved and scheduled for payout', null, ['amount' => $reward->amount]);
    }

    public function reject(PurchaseClaim $claim, string $reason, ?int $adminId = null): void
    {
        $claim->update([
            'status' => PurchaseClaim::STATUS_REJECTED,
            'rejected_by' => $adminId ?? auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $claim->buyerReward?->update(['status' => BuyerReward::STATUS_REJECTED, 'status_at' => now()]);

        PurchaseClaimStatusChanged::dispatch($claim);
        AuditLogger::log('claim_rejected', 'purchase_claim', $claim->id, 'Purchase claim rejected', null, ['reason' => $reason]);
    }

    public function markDisputed(PurchaseClaim $claim, ?int $adminId = null, ?string $note = null): void
    {
        $claim->update(['status' => PurchaseClaim::STATUS_DISPUTED, 'admin_notes' => $note]);
        PurchaseClaimStatusChanged::dispatch($claim);
        AuditLogger::log('claim_disputed', 'purchase_claim', $claim->id, 'Purchase claim marked as disputed');
    }

    protected function transition(PurchaseClaim $claim, string $status): void
    {
        $claim->update(['status' => $status]);
        PurchaseClaimStatusChanged::dispatch($claim);
        AuditLogger::log('claim_status_changed', 'purchase_claim', $claim->id, 'Purchase claim status changed', null, ['status' => $status]);
    }

    protected function nextReference(string $prefix, string $modelClass, string $column = 'claim_reference'): string
    {
        do {
            $reference = $prefix.'-'.strtoupper(Str::random(6));
        } while ($modelClass::where($column, $reference)->exists());

        return $reference;
    }
}