<?php

namespace App\Notifications;

use App\Models\PurchaseClaim;

class PurchaseClaimSubmitted extends WorxCarsNotification
{
    public function __construct(public PurchaseClaim $claim)
    {
    }

    protected function title(): string
    {
        return 'New purchase claim submitted';
    }

    protected function body(): string
    {
        return "Purchase claim {$this->claim->claim_reference} for {$this->claim->vehicle?->displayName()} has been submitted and requires verification.";
    }

    protected function url(): ?string
    {
        return '/admin/claims/'.$this->claim->id;
    }

    protected function type(): string
    {
        return 'claim';
    }
}