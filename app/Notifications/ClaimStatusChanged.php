<?php

namespace App\Notifications;

use App\Models\PurchaseClaim;

class ClaimStatusChanged extends WorxCarsNotification
{
    public function __construct(public PurchaseClaim $claim)
    {
    }

    protected function title(): string
    {
        return 'Purchase claim update';
    }

    protected function body(): string
    {
        return "Your purchase claim {$this->claim->claim_reference} status has changed to ".
            strtolower($this->claim->statusLabel()).'.';
    }

    protected function url(): ?string
    {
        return '/my/claims/'.$this->claim->id;
    }

    protected function type(): string
    {
        return 'claim';
    }
}