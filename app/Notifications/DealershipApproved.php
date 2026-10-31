<?php

namespace App\Notifications;

use App\Models\Dealership;

class DealershipApproved extends WorxCarsNotification
{
    public function __construct(public Dealership $dealership)
    {
    }

    protected function title(): string
    {
        return 'Your dealership has been approved';
    }

    protected function body(): string
    {
        return "{$this->dealership->business_name} is now a verified WorxCars dealership. You can start submitting vehicles and campaigns.";
    }

    protected function url(): ?string
    {
        return '/dealer/dashboard';
    }

    protected function type(): string
    {
        return 'dealership';
    }
}