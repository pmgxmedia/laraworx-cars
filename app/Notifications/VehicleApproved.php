<?php

namespace App\Notifications;

use App\Models\Vehicle;

class VehicleApproved extends WorxCarsNotification
{
    public function __construct(public Vehicle $vehicle)
    {
    }

    protected function title(): string
    {
        return 'Vehicle listing approved';
    }

    protected function body(): string
    {
        return "Your {$this->vehicle->displayName()} listing is now live on WorxCars and visible to buyers.";
    }

    protected function url(): ?string
    {
        return '/vehicles/'.$this->vehicle->slug;
    }

    protected function type(): string
    {
        return 'vehicle';
    }
}