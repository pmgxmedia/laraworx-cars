<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Generates a public ULID into the `ulid` column when creating the model.
 * Kept separate from Laravel's HasUlids (which repurposes the primary key).
 */
trait GeneratesUlid
{
    public static function bootGeneratesUlid(): void
    {
        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }
}