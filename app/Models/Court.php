<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 
        'type', 
        'code', 
        'region_id', 
        'location_id', 
        'address', 
        'presiding_judge', 
        'registry_officer', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function presidingJudge()
    {
        return $this->belongsTo(User::class, 'presiding_judge');
    }

    public function registryOfficer()
    {
        return $this->belongsTo(User::class, 'registry_officer');
    }

    /**
     * Scopes for Device Counts
     */
    public function scopeWithDeviceCounts($query)
    {
        return $query->withCount([
            'assets as total_assets',
            'assets as computers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->whereIn('name', ['Computers', 'Desktops'])
                      ->orWhereIn('code', ['COMP', 'DESKTOP']);
                });
            },
            'assets as laptops_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Laptops')
                      ->orWhere('code', 'LAPTOP');
                });
            },
            'assets as dts_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'DTS')
                      ->orWhere('code', 'DTS');
                });
            },
            'assets as ups_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'UPS')
                      ->orWhere('code', 'UPS');
                });
            },
            'assets as stabilizers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Stabilizer')
                      ->orWhere('code', 'STAB');
                });
            },
            'assets as photocopiers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Photocopier')
                      ->orWhere('code', 'PHOTOCOPIER');
                });
            },
            'assets as printers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Printers')
                      ->orWhere('code', 'PRINTER');
                });
            },
            'assets as scanners_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Scanners')
                      ->orWhere('code', 'SCANNER');
                });
            },
            'assets as cameras_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Camera')
                      ->orWhere('code', 'CAM');
                });
            },
            'assets as televisions_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Televisions')
                      ->orWhere('code', 'TEL');
                });
            },
            'assets as child_friendly_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Child Friendly Court')
                      ->orWhere('code', 'CFA');
                });
            },
            'assets as networking_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'Networking')
                      ->orWhereIn('name', ['Routers', 'Switches'])
                      ->orWhereIn('code', ['NET', 'ROUTER', 'SWITCH']);
                });
            }
        ]);
    }

    /**
     * General Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhereHas('region', function($regionQuery) use ($search) {
                  $regionQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    public function scopeHasAssets($query)
    {
        return $query->has('assets');
    }

    public function scopeHasNoAssets($query)
    {
        return $query->doesntHave('assets');
    }

    /**
     * Accessors for Blade Compatibility
     */
    public function getTotalAssetsAttribute()
    {
        if (isset($this->attributes['total_assets'])) {
            return $this->attributes['total_assets'];
        }
        return $this->assets()->count();
    }

    public function getComputersAttribute()
    {
        if (isset($this->attributes['computers_count'])) {
            return $this->attributes['computers_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->whereIn('name', ['Computers', 'Desktops'])
              ->orWhereIn('code', ['COMP', 'DESKTOP']);
        })->count();
    }

    public function getLaptopsAttribute()
    {
        if (isset($this->attributes['laptops_count'])) {
            return $this->attributes['laptops_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Laptops')->orWhere('code', 'LAPTOP');
        })->count();
    }

    public function getDtsAttribute()
    {
        if (isset($this->attributes['dts_count'])) {
            return $this->attributes['dts_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'DTS')->orWhere('code', 'DTS');
        })->count();
    }

    public function getUpsAttribute()
    {
        if (isset($this->attributes['ups_count'])) {
            return $this->attributes['ups_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'UPS')->orWhere('code', 'UPS');
        })->count();
    }

    public function getStabilizersAttribute()
    {
        if (isset($this->attributes['stabilizers_count'])) {
            return $this->attributes['stabilizers_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Stabilizer')->orWhere('code', 'STAB');
        })->count();
    }

    public function getPhotocopiersAttribute()
    {
        if (isset($this->attributes['photocopiers_count'])) {
            return $this->attributes['photocopiers_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Photocopier')->orWhere('code', 'PHOTOCOPIER');
        })->count();
    }

    public function getPrintersAttribute()
    {
        if (isset($this->attributes['printers_count'])) {
            return $this->attributes['printers_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Printers')->orWhere('code', 'PRINTER');
        })->count();
    }

    public function getScannersAttribute()
    {
        if (isset($this->attributes['scanners_count'])) {
            return $this->attributes['scanners_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Scanners')->orWhere('code', 'SCANNER');
        })->count();
    }

    public function getCamerasAttribute()
    {
        if (isset($this->attributes['cameras_count'])) {
            return $this->attributes['cameras_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Camera')->orWhere('code', 'CAM');
        })->count();
    }

    public function getTelevisionsAttribute()
    {
        if (isset($this->attributes['televisions_count'])) {
            return $this->attributes['televisions_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Televisions')->orWhere('code', 'TEL');
        })->count();
    }

    public function getChildFriendlyAttribute()
    {
        if (isset($this->attributes['child_friendly_count'])) {
            return $this->attributes['child_friendly_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Child Friendly Court')->orWhere('code', 'CFA');
        })->count();
    }

    public function getNetworkingAttribute()
    {
        if (isset($this->attributes['networking_count'])) {
            return $this->attributes['networking_count'];
        }
        return $this->assets()->whereHas('category', function($q) {
            $q->where('name', 'Networking')
              ->orWhereIn('name', ['Routers', 'Switches'])
              ->orWhereIn('code', ['NET', 'ROUTER', 'SWITCH']);
        })->count();
    }

    /**
     * Convenience Accessors
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} ({$this->code})";
    }

    public function getTypeFormattedAttribute()
    {
        return str_replace('_', ' ', ucfirst($this->type));
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }

    /**
     * Methods
     */
    public function hasAssets()
    {
        return $this->total_assets > 0;
    }

    public function hasDeviceType($deviceType)
    {
        $method = 'get' . ucfirst($deviceType) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->{$deviceType} > 0;
        }
        return false;
    }

    public function getDeviceSummary()
    {
        return [
            'total_assets' => $this->total_assets,
            'computers' => $this->computers,
            'laptops' => $this->laptops,
            'printers' => $this->printers,
            'scanners' => $this->scanners,
            'photocopiers' => $this->photocopiers,
            'ups' => $this->ups,
            'stabilizers' => $this->stabilizers,
            'dts' => $this->dts,
            'cameras' => $this->cameras,
            'televisions' => $this->televisions,
            'child_friendly' => $this->child_friendly,
            'networking' => $this->networking,
        ];
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($court) {
            if ($court->isForceDeleting()) {
                // Handle force delete
                $court->assets()->forceDelete();
            } else {
                // Handle soft delete
                $court->assets()->delete();
            }
        });

        static::restoring(function($court) {
            $court->assets()->restore();
        });
    }
}