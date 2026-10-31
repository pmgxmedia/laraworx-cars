<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $fillable = ['user_id', 'name', 'filters', 'hash'];

    protected function casts(): array
    {
        return ['filters' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}