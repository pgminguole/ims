<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasAssetStatistics;

class Office extends Model
{
    use HasFactory, SoftDeletes, HasAssetStatistics;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'location_id',
        'region_id',
        'court_id',
        'phone',
        'email',
        'address',
        'is_active',
        'capacity',
        'manager_id',
        'created_by'
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($office) {
            if (empty($office->slug)) {
                $office->slug = $office->generateSlug();
            }
        });

        static::updating(function ($office) {
            if ($office->isDirty('name') && empty($office->slug)) {
                $office->slug = $office->generateSlug();
            }
        });
    }
    
      private function generateSlug(): string
    {
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug already exists and increment until we find a unique one
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? null)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get the location that the office belongs to.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the region that the office belongs to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the court that the office belongs to.
     */
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * Get the manager of the office.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the assets assigned to this office.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'office_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users assigned to this office.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'office_id');
    }

    /**
     * Scope active offices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}