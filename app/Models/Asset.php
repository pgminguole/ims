<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Generate unique asset tags for a given category.
     * Use database locking to prevent race conditions during concurrent creation.
     */
    public static function generateNextTags($categoryId, $quantity = 1)
    {
        return DB::transaction(function () use ($categoryId, $quantity) {
            $category = Category::findOrFail($categoryId);
            $prefix = $category->code;

            // Find the latest asset tag for this category using a numeric sort
            // lockForUpdate prevents other requests from reading the same "latest" tag simultaneously
            $latestAsset = self::where('asset_tag', 'like', $prefix . '-%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING_INDEX(asset_tag, "-", -1) AS UNSIGNED) DESC')
                ->first();

            if ($latestAsset) {
                // Extract number from tag like "UPS-000001" -> 1
                $lastNumber = intval(substr($latestAsset->asset_tag, strlen($prefix) + 1));
            } else {
                $lastNumber = 0;
            }

            $tags = [];
            for ($i = 1; $i <= $quantity; $i++) {
                $currentNumber = $lastNumber + $i;
                $tag = $prefix . '-' . str_pad($currentNumber, 6, '0', STR_PAD_LEFT);
                
                // Safety net: check if tag exists (could happen if tags were manually entered or previous failed transactions)
                while (self::where('asset_tag', $tag)->exists()) {
                    $currentNumber++;
                    $tag = $prefix . '-' . str_pad($currentNumber, 6, '0', STR_PAD_LEFT);
                    $lastNumber = $currentNumber - $i; // Update lastNumber to keep sequence
                }
                
                $tags[] = $tag;
            }

            return $tags;
        });
    }

    protected $fillable = [
        'slug',
        'asset_id',
        'asset_name',
        'description',
        'comments',
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
        'audit_notes',
        'office_id',
        'created_by',
        'record_type',
        'is_audited',
        'audited_at',
        'audited_by_id'
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
        'audited_at' => 'datetime',
        'is_audited' => 'boolean',
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
    
  // In Asset model
public function office()
{
    return $this->belongsTo(Office::class, 'office_id')->withTrashed();
}







public function assignedUser()
{
    return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
}

public function courtAssignment()
{
    return $this->belongsTo(Court::class, 'assigned_to')->withTrashed();
}

public function officeAssignment()
{
    return $this->belongsTo(Office::class, 'assigned_to')->withTrashed();
}

// Helper method to get assigned entity
    public function getAssignedEntityAttribute()
    {
        $type = strtolower($this->assigned_type ?? '');
        
        // Prioritize explicit type-based assignment
        if (in_array($type, ['user', 'judge', 'staff'])) {
            return $this->assignedUser;
        } 
        
        if ($type === 'office') {
            return $this->office ?? $this->officeAssignment;
        } 
        
        if ($type === 'court') {
            return $this->court ?? $this->courtAssignment;
        }

        if ($type === 'region') {
            return $this->region;
        }

        // Fallback to location-based detection if type is missing (legacy support)
        if ($this->office_id) {
            return $this->office;
        } 
        
        if ($this->court_id) {
            return $this->court;
        }

        if ($this->region_id) {
            return $this->region;
        }

        return null;
    }

// Helper method to get assigned entity name
public function getAssignedEntityNameAttribute()
{
    $entity = $this->assigned_entity;
    if ($entity) {
        return $this->assigned_type === 'user' ? ($entity->full_name ?? $entity->name) : ($entity->name ?? 'N/A');
    }
    return 'N/A';
}

/**
 * Get the effective region name.
 * Fallback to the assigned entity's region if the asset's direct region_id is missing.
 */
public function getEffectiveRegionNameAttribute()
{
    if ($this->region) {
        return $this->region->name;
    }
    
    $entity = $this->assigned_entity;
    
    // Check if the assigned entity (Court, Office, or User) has a region
    if ($entity && $entity->region) {
        return $entity->region->name;
    }
    
    return 'N/A';
}



    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class)->withTrashed();
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



    public function registry()
    {
        return $this->belongsTo(User::class, 'registry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastAuditedBy()
    {
        return $this->belongsTo(User::class, 'last_audited_by');
    }

    public function auditedBy()
    {
        return $this->belongsTo(User::class, 'audited_by_id');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function dts()
    {
        return $this->hasOne(Dts::class);
    }

    // Scopes
    public function scopeAssignments($query)
    {
        return $query->where('record_type', 'assignment');
    }

    public function scopeInventory($query)
    {
        return $query->where('record_type', 'inventory');
    }

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