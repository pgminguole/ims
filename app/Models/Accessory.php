<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accessory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'serial_number', 'model', 'asset_id',
        'condition', 'date_acquired', 'cost', 'notes'
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'cost' => 'decimal:2'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}