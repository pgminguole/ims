<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'asset_id',
        'asset_name',
        'description',
        'serial_number',
        'model',
        'region_id',
        'location_id',
        'court_id',
        'category_id',
        'status',
        'condition',
        'purchase_date',
        'recieved_date',
        'assigned_date',
        'returned_date',
        'returned_reason',
        'returnee',
         'returned_to',
        'current_value',
        'depreciation_method',
        'attachments',
        'maintenance_schedule',
        'manufacturer',
        'warranty_information',
        'asset_tag',
        'brand',
        'supplier',
        'warranty_period',
        'warranty_expiry',
        'last_maintenance',
        'next_maintenance',
        'maintenance_notes',
        'assigned_to',
        'assigned_type',
        'subcategory_id',
        'registry_id',
        'ip_address',
        'mac_address',
        'specifications',
        'last_audited_at',
        'last_audited_by',
        'audit_notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'recieved_date' => 'date',
        'recieved_date' => 'date',
        'assigned_date' => 'date',
        'returned_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
        'last_audited_at' => 'datetime',
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'attachments' => 'array',
    ];

//     protected $appends = ['sub_category'];

// public function getSubCategoryAttribute()
// {
//     try {
//         return $this->subcategory()->first();
//     } catch (\Exception $e) {
//         return null;
//     }
// }
    // Relationships
    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function registry()
    {
        return $this->belongsTo(User::class, 'registry_id');
    }

    public function lastAuditedBy()
    {
        return $this->belongsTo(User::class, 'last_audited_by');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeByCourt($query, $courtId)
    {
        return $query->where('court_id', $courtId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

        public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->where('subcategory_id', $subCategoryId);
    }

    // Methods
    public function assignTo($assignable, $type, $date = null)
    {
        $this->update([
            'assigned_to' => $assignable->id,
            'assigned_type' => $type,
            'status' => 'assigned'
        ]);

        // Log the assignment
        AssetHistory::create([
            'asset_id' => $this->id,
            'action' => 'assigned',
            'description' => "Asset assigned to {$type}: {$assignable->name}",
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);
    }

    public function markAsAudited($userId, $notes = null)
    {
        $this->update([
            'last_audited_at' => now(),
            'last_audited_by' => $userId,
            'audit_notes' => $notes
        ]);
    }

    // Accessors
    public function getFullAssetNameAttribute()
    {
        return $this->asset_name . ' (' . $this->asset_id . ')';
    }

    // Check if asset is due for maintenance
    public function getIsDueForMaintenanceAttribute()
    {
        if (!$this->next_maintenance) {
            return false;
        }
        
        return $this->next_maintenance <= now()->addDays(30);
    }
}