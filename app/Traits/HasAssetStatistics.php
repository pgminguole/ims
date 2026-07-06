<?php

namespace App\Traits;

trait HasAssetStatistics
{
    /**
     * Helper to get count by category names or codes
     */
    protected function getAssetCountByCategory(array $names, array $codes = [])
    {
        // Try to verify if we have the counts pre-loaded (from scopeWithDeviceCounts)
        // This requires standardizing the attribute names like 'computers_count'
        // Since different views use different sets, we'll stick to dynamic queries if not loaded.
        
        return $this->assets()->whereHas('category', function($q) use ($names, $codes) {
            $q->where(function($query) use ($names, $codes) {
                // Handle Names (Singular and Plural)
                foreach ($names as $name) {
                    $query->orWhere('name', 'LIKE', $name); // Use LIKE for flexibility? Or exact?
                    // Let's use exact checks for singular/plural pairs passed in $names
                    $query->orWhere('name', $name);
                }
                
                // Handle Codes
                if (!empty($codes)) {
                    $query->orWhereIn('code', $codes);
                }
            });
        })->count();
    }

    public function getTotalAssetsAttribute()
    {
        return $this->assets()->count();
    }

    public function getComputersAttribute()
    {
        if (isset($this->attributes['computers_count'])) return $this->attributes['computers_count'];
        return $this->getAssetCountByCategory(['Computers', 'Computer', 'Desktops', 'Desktop'], ['COMP', 'DESKTOP', 'PC']);
    }

    public function getLaptopsAttribute()
    {
        if (isset($this->attributes['laptops_count'])) return $this->attributes['laptops_count'];
        return $this->getAssetCountByCategory(['Laptops', 'Laptop'], ['LAPTOP']);
    }

    public function getPrintersAttribute()
    {
        if (isset($this->attributes['printers_count'])) return $this->attributes['printers_count'];
        return $this->getAssetCountByCategory(['Printers', 'Printer'], ['PRINTER']);
    }

    public function getScannersAttribute()
    {
        if (isset($this->attributes['scanners_count'])) return $this->attributes['scanners_count'];
        return $this->getAssetCountByCategory(['Scanners', 'Scanner'], ['SCANNER']);
    }

    public function getPhotocopiersAttribute()
    {
        if (isset($this->attributes['photocopiers_count'])) return $this->attributes['photocopiers_count'];
        return $this->getAssetCountByCategory(['Photocopiers', 'Photocopier', 'Copier'], ['PHOTOCOPIER', 'COPIER']);
    }

    public function getUpsAttribute()
    {
        if (isset($this->attributes['ups_count'])) return $this->attributes['ups_count'];
        return $this->getAssetCountByCategory(['UPS'], ['UPS']);
    }

    public function getStabilizersAttribute()
    {
        if (isset($this->attributes['stabilizers_count'])) return $this->attributes['stabilizers_count'];
        return $this->getAssetCountByCategory(['Stabilizers', 'Stabilizer'], ['STAB']);
    }

    public function getCamerasAttribute()
    {
        if (isset($this->attributes['cameras_count'])) return $this->attributes['cameras_count'];
        return $this->getAssetCountByCategory(['Cameras', 'Camera'], ['CAM']);
    }

    public function getTelevisionsAttribute()
    {
        if (isset($this->attributes['televisions_count'])) return $this->attributes['televisions_count'];
        return $this->getAssetCountByCategory(['Televisions', 'Television', 'TV'], ['TEL', 'TV']);
    }
    
    public function getDtsAssetsAttribute()
    {
        if (isset($this->attributes['dts_assets_count'])) return $this->attributes['dts_assets_count'];
        return $this->getAssetCountByCategory(['DTS'], ['DTS', 'dts']);
    }
    
    public function getChildFriendlyAttribute()
    {
        if (isset($this->attributes['child_friendly_count'])) return $this->attributes['child_friendly_count'];
        return $this->getAssetCountByCategory(['Child Friendly Court', 'Child Friendly'], ['CFA', 'cfa']);
    }
    
    public function getNetworkingAttribute()
    {
         if (isset($this->attributes['networking_count'])) return $this->attributes['networking_count'];
         return $this->getAssetCountByCategory(
             ['Networking', 'Routers', 'Router', 'Switches', 'Switch'], 
             ['NET', 'ROUTER', 'SWITCH']
         );
    }
}
