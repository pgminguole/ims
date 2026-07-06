<?php

namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Region;

class AuditorRegionController extends AuditorBaseController
{
    public function index(Request $request)
    {
        $query = Region::withCount(['courts', 'assets', 'users']);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        $regions = $query->latest()->paginate(12);
        $filterData = $this->getFilterData();

        // Statistics
        $totalRegions = Region::count();
        $totalCourts = \App\Models\Court::count();
        $totalAssets = \App\Models\Asset::count();
        $totalUsers = \App\Models\User::count();

        return view('auditor.regions.index', compact(
            'regions', 'filterData', 'totalRegions', 
            'totalCourts', 'totalAssets', 'totalUsers'
        ));
    }
}