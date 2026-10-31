<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSpecification extends Model
{
    protected $fillable = ['vehicle_id', 'spec_key', 'label', 'value', 'sort_order'];
}