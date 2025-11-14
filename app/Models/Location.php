<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'building', 'floor', 'room', 'region_id', 'description', 'is_active'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function courts()
    {
        return $this->hasMany(Court::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}