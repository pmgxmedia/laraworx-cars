<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const SUPER_ADMIN = 'super_admin';
    public const OPERATIONS_ADMIN = 'operations_admin';
    public const FINANCE_ADMIN = 'finance_admin';
    public const CONTENT_ADMIN = 'content_admin';
    public const SUPPORT_ADMIN = 'support_admin';
    public const DEALERSHIP = 'dealership';
    public const PRIVATE_SELLER = 'private_seller';
    public const CUSTOMER = 'customer';

    protected $fillable = ['name', 'label', 'description'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}