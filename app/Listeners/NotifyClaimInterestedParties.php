<?php

namespace App\Listeners;

use App\Events\PurchaseClaimStatusChanged;
use App\Models\PurchaseClaim;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ClaimStatusChanged as ClaimStatusChangedNotification;
use App\Notifications\PurchaseClaimSubmitted;

class NotifyClaimInterestedParties
{
    public function handle(PurchaseClaimStatusChanged $event): void
    {
        $claim = $event->claim;

        $customer = $claim->user;
        $sellerUser = $claim->dealership_id
            ? $claim->dealership?->userAccounts()->wherePivot('is_primary', true)->first()
            : $claim->privateSeller?->user;

        if ($claim->status === PurchaseClaim::STATUS_SUBMITTED) {
            $admins = User::role(Role::SUPER_ADMIN)->get()
                ->concat(User::role(Role::OPERATIONS_ADMIN)->get())
                ->concat(User::role(Role::FINANCE_ADMIN)->get());

            foreach ($admins->unique('id') as $admin) {
                $admin->notify(new PurchaseClaimSubmitted($claim));
            }

            if ($sellerUser) {
                $sellerUser->notify(new PurchaseClaimSubmitted($claim));
            }

            $customer->notify(new PurchaseClaimSubmitted($claim));

            return;
        }

        $customer->notify(new ClaimStatusChangedNotification($claim));

        if (in_array($claim->status, [PurchaseClaim::STATUS_TRANSACTION_VERIFIED, PurchaseClaim::STATUS_REWARD_APPROVED, PurchaseClaim::STATUS_PAID])) {
            $sellerUser?->notify(new PurchaseClaimSubmitted($claim));
        }
    }
}