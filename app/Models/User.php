<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'password',
        'phone_verified_at',
        'status',
        'access_type',
        'approved_at',
        'is_approved',
        'status',
        'block',
        'require_password_reset',
        'is_expire',
        'expire_date',
        'invited_by',
        'invited_date',
        'accepted',
        'accepted_date',
        'is_online',
        'login_at',
        'logout_at',
        'location_id',
        'registry_id',
        'role_id'
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'invited_date' => 'datetime',
        'accepted_date' => 'datetime',
        'expire_date' => 'datetime',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function scopeActive($query)
{
    return $query->where('status', 'active'); // or whatever indicates "active"
}

 public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    // Helper method to check role
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    protected $append = ['full_name'];

    public function FullName(): Attribute
    {
        return new Attribute(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }


    /**
 * Get the location that the user belongs to.
 */


 public function court()
{
    return $this->belongsTo(Court::class);
}

 public function region()
{
    return $this->belongsTo(Region::class);
}


public function location()
{
    return $this->belongsTo(Location::class);
}

/**
 * Get the registry that the user belongs to.
 */
public function registry()
{
    return $this->belongsTo(Registry::class);
}

/**
 * Get the user who invited this user.
 */
public function inviter()
{
    return $this->belongsTo(User::class, 'invited_by');
}

/**
 * Get the users invited by this user.
 */
public function invitees()
{
    return $this->hasMany(User::class, 'invited_by');
}

/**
 * Get the assets assigned to this user.
 */
public function assets()
{
    return $this->hasMany(Asset::class, 'assigned_to'); // or whatever the FK is
}


public function assignedAssets()
{
    return $this->hasMany(Asset::class, 'assigned_to'); // or whatever the FK is
}

/**
 * Get the asset requests made by this user.
 */
public function assetRequests()
{
    return $this->hasMany(AssetRequest::class);
}

/**
 * Get the maintenance records created by this user.
 */
public function maintenanceRecords()
{
    return $this->hasMany(Maintenance::class, 'created_by');
}

/**
 * Get the audit logs for this user.
 */
public function auditLogs()
{
    return $this->hasMany(AuditLog::class);
}
 
}
