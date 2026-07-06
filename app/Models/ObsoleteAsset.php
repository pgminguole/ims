<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObsoleteAsset extends Model
{
    protected $fillable = [
        'asset_name',
        'serial_number',
        'category',
        'brand',
        'model',
        'date_acquired',
        'date_obsolete',
        'reason',
        'disposal_method',
        'user_id',
        'reported_by_name',
        'region_id',
        'court_id',
        'office_id',
        'owner_user_id',
        'target_type',
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'date_obsolete' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
