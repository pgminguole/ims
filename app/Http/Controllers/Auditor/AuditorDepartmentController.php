<?php
namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\AssetHistory;

class AuditorDepartmentController extends AuditorBaseController
{
    public function index(Request $request)
    {
        $query = Office::with(['region', 'court', 'manager'])
            ->withCount(['assets']);

        // Apply filters
        $query = $this->applyFilters($query, $request, [
            'court_id' => 'court_id'
        ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $departments = $query->latest()->paginate(20);
        $filterData = $this->getFilterData();

        // Statistics
        $totalDepartments = Office::count();
        $totalAssets = \App\Models\Asset::count();
        $totalStaff = \App\Models\User::count();
        $activeRegions = \App\Models\Region::active()->count();
        $courts = \App\Models\Court::active()->get();

        return view('auditor.departments.index', compact(
            'departments', 'filterData', 'totalDepartments', 
            'totalAssets', 'totalStaff', 'activeRegions', 'courts'
        ));
    }
    
    
    public function show(Office $department)
    {
        // Load relationships
        $department->load([
            'region',
            'court',
            'location',
            'manager'
        ]);

        // Load assets with pagination - THIS IS THE MAIN FIX
        $ds = $department->assets()
            ->with(['category', 'assignedUser', 'office'])
            ->latest()
            ->paginate(10);

        // Asset statistics
        $assignedAssetsCount = $department->assets()->where('status', 'assigned')->count();
        $availableAssetsCount = $department->assets()->where('status', 'available')->count();
        $maintenanceAssetsCount = $department->assets()->where('status', 'maintenance')->count();

        // Asset status counts
        $assetStatusCounts = $department->assets()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Asset condition counts - ADDED THIS (wrap 'condition' in backticks as it's a reserved keyword)
        $assetConditionCounts = $department->assets()
            ->selectRaw('`condition`, count(*) as count')
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        // Asset category counts
        $assetCategoryCounts = $department->assets()
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, count(*) as count')
            ->groupBy('categories.name')
            ->pluck('count', 'category_name')
            ->toArray();

        // Recent activities for department assets
        $recentActivities = AssetHistory::whereHas('asset', function($query) use ($department) {
                $query->where('office_id', $department->id);
            })
            ->with(['asset', 'performedBy'])
            ->latest()
            ->take(5)
            ->get();

        return view('auditor.departments.show', compact(
            'department',
            'ds',
            'assignedAssetsCount',
            'availableAssetsCount',
            'maintenanceAssetsCount',
            'assetStatusCounts',
            'assetConditionCounts',
            'assetCategoryCounts',
            'recentActivities'
        ));
    }
}