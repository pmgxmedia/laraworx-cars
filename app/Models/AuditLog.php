<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use GeneratesUlid;

    protected $fillable = [
        'ulid', 'user_id', 'entity_type', 'entity_id', 'action', 'description',
        'before', 'after', 'ip_address', 'user_agent', 'occurred_at',
    ];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array', 'occurred_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}