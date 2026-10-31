<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use GeneratesUlid;

    protected $fillable = [
        'ulid', 'documentable_type', 'documentable_id', 'category', 'file_path', 'file_name',
        'mime_type', 'size', 'notes', 'uploaded_by', 'is_verified', 'verified_at',
    ];

    protected function casts(): array
    {
        return ['is_verified' => 'boolean', 'verified_at' => 'datetime'];
    }

    public function documentable()
    {
        return $this->morphTo();
    }
}