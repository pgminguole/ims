<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dts extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts';

    protected $fillable = [
        'court_id',
        'name',
        'monitors_count',
        'splitters_count',
        'hdmi_short_cables_count',
        'hdmi_long_cables_count',
        'extension_boards_count',
        'trucking_count',
        'sony_recorders_count',
        'is_available',
        'notes',
        // Asset foreign keys (nullable)
        'monitor_asset_id',
        'splitter_asset_id',
        'hdmi_short_cable_asset_id',
        'hdmi_long_cable_asset_id',
        'extension_board_asset_id',
        'trucking_asset_id',
        'sony_recorder_asset_id',
          'date_assigned',
       
    ];

    protected $casts = [
        'is_available' => 'boolean',
         'date_assigned' => 'date',
    ];

    /**
     * Relationships
     */
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Asset relationships (nullable)
    public function monitorAsset()
    {
        return $this->belongsTo(Asset::class, 'monitor_asset_id');
    }

    public function splitterAsset()
    {
        return $this->belongsTo(Asset::class, 'splitter_asset_id');
    }

    public function hdmiShortCableAsset()
    {
        return $this->belongsTo(Asset::class, 'hdmi_short_cable_asset_id');
    }

    public function hdmiLongCableAsset()
    {
        return $this->belongsTo(Asset::class, 'hdmi_long_cable_asset_id');
    }

    public function extensionBoardAsset()
    {
        return $this->belongsTo(Asset::class, 'extension_board_asset_id');
    }

    public function truckingAsset()
    {
        return $this->belongsTo(Asset::class, 'trucking_asset_id');
    }

    public function sonyRecorderAsset()
    {
        return $this->belongsTo(Asset::class, 'sony_recorder_asset_id');
    }

    /**
     * Get all related assets
     */
    public function getAllAssetsAttribute()
    {
        return collect([
            $this->monitorAsset,
            $this->splitterAsset,
            $this->hdmiShortCableAsset,
            $this->hdmiLongCableAsset,
            $this->extensionBoardAsset,
            $this->truckingAsset,
            $this->sonyRecorderAsset
        ])->filter();
    }

    /**
     * Scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByCourt($query, $courtId)
    {
        return $query->where('court_id', $courtId);
    }

    public function scopeHasAssets($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('monitor_asset_id')
              ->orWhereNotNull('splitter_asset_id')
              ->orWhereNotNull('hdmi_short_cable_asset_id')
              ->orWhereNotNull('hdmi_long_cable_asset_id')
              ->orWhereNotNull('extension_board_asset_id')
              ->orWhereNotNull('trucking_asset_id')
              ->orWhereNotNull('sony_recorder_asset_id');
        });
    }

    /**
     * Accessors
     */
    public function getTotalComponentsAttribute()
    {
        return $this->monitors_count + 
               $this->splitters_count + 
               $this->hdmi_short_cables_count + 
               $this->hdmi_long_cables_count + 
               $this->extension_boards_count + 
               $this->trucking_count + 
               $this->sony_recorders_count;
    }

    public function getHdmiCablesSummaryAttribute()
    {
        return "{$this->hdmi_short_cables_count} (5M) & {$this->hdmi_long_cables_count} (20M)";
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_available 
            ? '<span class="badge bg-success">Available</span>'
            : '<span class="badge bg-secondary">Not Available</span>';
    }

    public function getHasDetailedAssetsAttribute()
    {
        return $this->monitor_asset_id !== null || 
               $this->splitter_asset_id !== null ||
               $this->hdmi_short_cable_asset_id !== null ||
               $this->hdmi_long_cable_asset_id !== null ||
               $this->extension_board_asset_id !== null ||
               $this->trucking_asset_id !== null ||
               $this->sony_recorder_asset_id !== null;
    }

    /**
     * Check if DTS has all required components
     */
    public function isComplete()
    {
        return $this->monitors_count > 0 && 
               $this->splitters_count > 0 && 
               $this->hdmi_short_cables_count > 0 && 
               $this->hdmi_long_cables_count > 0;
    }

    /**
     * Link an asset to this DTS
     */
    public function linkAsset(Asset $asset, $componentType)
    {
        $fieldMap = [
            'monitor' => 'monitor_asset_id',
            'splitter' => 'splitter_asset_id',
            'hdmi_short' => 'hdmi_short_cable_asset_id',
            'hdmi_long' => 'hdmi_long_cable_asset_id',
            'extension_board' => 'extension_board_asset_id',
            'trucking' => 'trucking_asset_id',
            'sony_recorder' => 'sony_recorder_asset_id'
        ];

        if (isset($fieldMap[$componentType])) {
            $this->update([$fieldMap[$componentType] => $asset->id]);
            return true;
        }

        return false;
    }
}