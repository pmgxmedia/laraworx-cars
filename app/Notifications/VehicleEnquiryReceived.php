<?php

namespace App\Notifications;

use App\Models\BuyerEnquiry;

class VehicleEnquiryReceived extends WorxCarsNotification
{
    public function __construct(public BuyerEnquiry $enquiry)
    {
    }

    protected function title(): string
    {
        return 'New vehicle enquiry';
    }

    protected function body(): string
    {
        $vehicle = $this->enquiry->vehicle?->displayName() ?? 'a listed vehicle';

        return "A new enquiry has been received for {$vehicle} from {$this->enquiry->name}. Reply promptly to qualify the lead.";
    }

    protected function url(): ?string
    {
        return '/admin/enquiries/'.$this->enquiry->id;
    }

    protected function type(): string
    {
        return 'enquiry';
    }
}