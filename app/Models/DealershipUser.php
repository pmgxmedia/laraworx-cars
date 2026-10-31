<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealershipUser extends Model
{
    protected $table = 'dealership_users';

    protected $fillable = ['dealership_id', 'user_id', 'role_title', 'is_primary'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}