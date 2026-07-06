<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'description', 'is_active', 'created_by'];

    public function courts()
    {
        return $this->hasMany(Court::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
    
    

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active regions.
     * Active regions are those with a non-null name.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('name');
    }

    /**
     * Alternative scope if you also want to consider the is_active field
     * This would check both that name is not null AND is_active is true
     */
    public function scopeFullyActive(Builder $query): Builder
    {
        return $query->whereNotNull('name')->where('is_active', true);
    }
}