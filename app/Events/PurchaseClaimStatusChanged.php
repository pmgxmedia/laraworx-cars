<?php

namespace App\Events;

use App\Models\PurchaseClaim;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseClaimStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public PurchaseClaim $claim)
    {
    }
}