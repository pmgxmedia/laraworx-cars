<?php

namespace App\Notifications;

use App\Models\RewardPayout;

class RewardPaid extends WorxCarsNotification
{
    public function __construct(public RewardPayout $payout)
    {
    }

    protected function title(): string
    {
        return 'Buyer reward paid';
    }

    protected function body(): string
    {
        return "Your WorxCars Buyer Reward of ".money($this->payout->amount).
            " (reference {$this->payout->payout_reference}) has been settled. Thank you for choosing WorxCars.";
    }

    protected function url(): ?string
    {
        return '/my/rewards';
    }

    protected function type(): string
    {
        return 'reward';
    }
}