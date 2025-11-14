<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Region;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class AssetManagement extends Component
{
    use WithPagination;

    public $regions;
    public $searchQuery;
    public $regionData;
    public $status;

    public function mount()
    {
        $this->regions = $this->getRegions();

    }

    public function getRegions()
    {
        return Cache::rememberForever('regions_with_countries_gh', function () {
            return Region::query()
                ->with('countries')
                ->whereHas('countries', function ($query) {
                    $query->where('code', 'GH');
                })
                ->orderBy('name', 'asc')
                ->get();
        });

    }

    public function search()
    {
        sleep(1);

//        dd($this->regionData, $this->status);

        return Asset::query()
            ->with('categories', 'subcategories', 'regions', 'attachments')
            ->when($this->searchQuery, function ($query) {
                $query->whereLike([
                    'asset_id', 'asset_name', 'status', 'serial_number', 'vin_number',
                    'license_plate', 'categories.name', 'regions.name', 'locations.name', 'courts.name'
                ], $this->searchQuery);
            })
            ->when($this->regionData, function ($query) {
                $query->where('region_id', $this->regionData); // Filter by region_id
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status); // Filter by status
            })
            ->latest()
            ->limit(500)
            ->paginate(15);

    }

    public function clear()
    {
        //clear inputs
        $this->reset(['regionData', 'status', 'searchQuery']);
        // Update transactions after clearing filters
        $this->search();
        $this->dispatch('resetForm');
    }

    public function render()
    {
        return view('livewire.asset-management', [
            'assets' => $this->search(),
        ]);
    }
}
