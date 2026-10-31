<?php

namespace App\Notifications;

use App\Models\BuyerReward;

class RewardApproved extends WorxCarsNotification
{
    public function __construct(public BuyerReward $reward)
    {
    }

    protected function title(): string
    {
        return 'Buyer reward approved';
    }

    protected function body(): string
    {
        return "Good news! Your WorxCars Buyer Reward of ".money($this->reward->amount).
            " has been approved and is being prepared for payout.";
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