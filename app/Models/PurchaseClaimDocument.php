<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseClaimDocument extends Model
{
    protected $fillable = [
        'purchase_claim_id', 'type', 'file_path', 'file_name', 'mime_type', 'size', 'notes',
    ];

    public function purchaseClaim()
    {
        return $this->belongsTo(PurchaseClaim::class);
    }
}