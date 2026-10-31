<?php

namespace App\Notifications;

use App\Models\VehicleCampaign;

class CampaignActivated extends WorxCarsNotification
{
    public function __construct(public VehicleCampaign $campaign)
    {
    }

    protected function title(): string
    {
        return 'Campaign activated';
    }

    protected function body(): string
    {
        return "The {$this->campaign->name} campaign is now active. Eligible vehicles are carrying their buyer rewards.";
    }

    protected function url(): ?string
    {
        return '/campaigns/'.$this->campaign->slug;
    }

    protected function type(): string
    {
        return 'campaign';
    }
}