<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'maintenance_date', 'type', 'description',
        'actions_taken', 'cost', 'technician', 'next_maintenance_date', 'performed_by'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}