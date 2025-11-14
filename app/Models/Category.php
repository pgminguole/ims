<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'description', 'parent_id', 'is_active'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function subcategoryAssets()
    {
        return $this->hasMany(Asset::class, 'subcategory_id');
    }
}