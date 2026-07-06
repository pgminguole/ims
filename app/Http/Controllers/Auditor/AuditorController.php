<?php

namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Court;
use App\Models\Region;
use App\Models\Office;
use App\Models\Dts;
use Illuminate\Support\Facades\DB;

class AuditorController extends AuditorBaseController
{
    public function dashboard(Request $request)
    {
        // Total counts
        $totalAssets = Asset::count();
        $totalUsers = User::count();
        $totalCourts = Court::active()->count();
        $totalRegions = Region::active()->count();
        $totalDepartments = Office::active()->count();
        $totalDts = Dts::count();

        // Asset assignment statistics
        $assetsAssignedToUsers = Asset::where('assigned_type', 'user')
            ->whereNotNull('assigned_to')
            ->count();
        
        $assetsAssignedToOffices = Asset::where('assigned_type', 'office')
            ->whereNotNull('office_id')
            ->count();
        
        $assetsAssignedToCourts = Asset::whereNotNull('court_id')->count();
        
        $unassignedAssets = Asset::whereNull('assigned_to')
            ->whereNull('office_id')
            ->whereNull('court_id')
            ->count();

        // Asset status distribution
        $assetStatusCounts = Asset::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status');

        // Assets by category (top 5)
        $assetsByCategoryData = Asset::select('category_id', DB::raw('count(*) as total'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name ?? 'Unknown' => $item->total];
            });

        // Assets requiring maintenance soon (within 30 days)
        $assetsNeedingMaintenance = Asset::whereNotNull('next_maintenance')
            ->where('next_maintenance', '<=', now()->addDays(30))
            ->count();

        // Assets never audited
        $assetsNeverAudited = Asset::whereNull('last_audited_at')->count();

        // Assets audited in last 30 days
        $recentlyAuditedAssets = Asset::where('last_audited_at', '>=', now()->subDays(30))
            ->count();

        // Recent assets
        $recentAssets = Asset::with(['region', 'category', 'court', 'office', 'assignedUser'])
            ->latest()
            ->take(10)
            ->get();

        // Court statistics with asset counts
        $courtStats = Court::withCount(['assets', 'users', 'dts'])
            ->active()
            ->orderBy('assets_count', 'desc')
            ->take(5)
            ->get();

        // Office statistics
        $officeStats = Office::withCount(['assets'])
            ->active()
            ->orderBy('assets_count', 'desc')
            ->take(5)
            ->get();

        // Region distribution
        $assetsByRegion = Asset::select('region_id', DB::raw('count(*) as total'))
            ->with('region:id,name')
            ->groupBy('region_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('auditor.dashboard', compact(
            'totalAssets',
            'totalUsers',
            'totalCourts',
            'totalRegions',
            'totalDepartments',
            'totalDts',
            'assetsAssignedToUsers',
            'assetsAssignedToOffices',
            'assetsAssignedToCourts',
            'unassignedAssets',
            'assetStatusCounts',
            'assetsByCategoryData',
            'assetsNeedingMaintenance',
            'assetsNeverAudited',
            'recentlyAuditedAssets',
            'recentAssets',
            'courtStats',
            'officeStats',
            'assetsByRegion'
        ));
    }
}