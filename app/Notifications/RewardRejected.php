<?php

namespace App\Notifications;

use App\Models\BuyerReward;

class RewardRejected extends WorxCarsNotification
{
    public function __construct(public BuyerReward $reward, public ?string $reason = null)
    {
    }

    protected function title(): string
    {
        return 'Buyer reward update';
    }

    protected function body(): string
    {
        $reason = $this->reason ?? 'the purchase could not be fully verified.';

        return "Your WorxCars Buyer Reward request for {$this->reward->vehicle?->displayName()} was not approved because {$reason}";
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