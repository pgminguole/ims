<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registry extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function  locations()
    {
        return $this->belongsTo(Location::class, 'location_id');

    }

    public function  regions()
    {
        return $this->belongsTo(Region::class, 'region_id');

    }

    public function  courts()
    {
        return $this->hasMany(Court::class, 'registry_id');

    }

}
