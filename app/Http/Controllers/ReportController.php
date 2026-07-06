<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Dts;
use App\Models\User;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\Court;
use App\Models\Office;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $this->authorize('view_reports');

        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        // Basic statistics for dashboard
        $query = Asset::query();
        if ($isRegionalAdmin) {
            $query->where('region_id', $user->region_id);
        }

        $totalAssets = (clone $query)->count();
        $totalValue = (clone $query)->sum('current_value');
        $assignedAssets = (clone $query)->where('status', 'assigned')->count();
        $maintenanceAssets = (clone $query)->where('status', 'maintenance')->count();

        // User statistics for dashboard
        $userQuery = User::query();
        if ($isRegionalAdmin) {
            $userQuery->where('region_id', $user->region_id);
        }

        $totalUsers = (clone $userQuery)->count();
        
        $judgesWithLaptopsQuery = (clone $userQuery)->whereHas('assignedRole', function($query) {
            $query->where('name', 'like', '%judge%');
        })->whereHas('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        });

        $judgesWithLaptops = $judgesWithLaptopsQuery->count();

        $judgesWithoutLaptops = (clone $userQuery)->whereHas('assignedRole', function($query) {
            $query->where('name', 'like', '%judge%');
        })->whereDoesntHave('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        // DTS Statistics - Updated to use Dts model
        $dtsQuery = Dts::query();
        if ($isRegionalAdmin) {
            $dtsQuery->whereHas('court', function($q) use ($user) {
                $q->where('region_id', $user->region_id);
            });
        }
        $totalDts = (clone $dtsQuery)->count();
        
        // Courts with DTS (where court_id is not null)
        $courtsWithDts = (clone $dtsQuery)->whereNotNull('court_id')->distinct('court_id')->count('court_id');
        
        // Total courts count
        $courtQuery = Court::query();
        if ($isRegionalAdmin) {
            $courtQuery->where('region_id', $user->region_id);
        }
        $totalCourts = (clone $courtQuery)->count();
        
        // Courts without DTS (total courts minus courts with DTS)
        $courtsWithoutDts = $totalCourts - $courtsWithDts;
        
        $regions = $isRegionalAdmin ? Region::where('id', $user->region_id)->get() : Region::all();

        return view('reports.index', compact(
            'totalAssets',
            'totalValue',
            'assignedAssets',
            'maintenanceAssets',
            'totalUsers',
            'judgesWithLaptops',
            'judgesWithoutLaptops',
            'totalDts',
            'courtsWithDts',
            'courtsWithoutDts',
            'regions'
        ));
    }

    public function users(Request $request)
    {
        $this->authorize('view_reports');

        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $query = User::with(['assignedRole', 'assignedAssets.category', 'court', 'region']);

        if ($isRegionalAdmin) {
            $query->where('region_id', $user->region_id);
        }

        // Apply filters
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role . '%');
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        if ($request->filled('has_laptop')) {
            if ($request->has_laptop === 'yes') {
                $query->whereHas('assignedAssets', function($q) {
                    $q->whereHas('category', function($categoryQuery) {
                        $categoryQuery->where('name', 'like', '%laptop%');
                    });
                });
            } else {
                $query->whereDoesntHave('assignedAssets', function($q) {
                    $q->whereHas('category', function($categoryQuery) {
                        $categoryQuery->where('name', 'like', '%laptop%');
                    });
                });
            }
        }

        $users = $query->get();

        // Summary statistics
        $summary = [
            'total' => $users->count(),
            'by_role' => $users->groupBy('role.name')->map->count()->toArray(),
            'by_region' => $users->groupBy('region.name')->map->count()->toArray(),
            'by_court' => $users->groupBy('court.name')->map->count()->toArray(),
            'with_laptops' => $users->filter(function($user) {
                return $user->assignedAssets->contains(function($asset) {
                    return stripos($asset->category->name ?? '', 'laptop') !== false;
                });
            })->count(),
            'without_laptops' => $users->filter(function($user) {
                return !$user->assignedAssets->contains(function($asset) {
                    return stripos($asset->category->name ?? '', 'laptop') !== false;
                });
            })->count(),
        ];

        $regions = $isRegionalAdmin ? Region::where('id', $user->region_id)->get() : Region::all();
        $courts = $isRegionalAdmin ? Court::where('region_id', $user->region_id)->get() : Court::all();
        $roles = \App\Models\Role::all();

        return view('reports.users', compact('users', 'summary', 'regions', 'courts', 'roles'));
    }

    public function courts(Request $request)
    {
        $this->authorize('view_reports');

        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $query = Court::with(['region', 'location', 'assets.category', 'users.assignedRole']);

        if ($isRegionalAdmin) {
            $query->where('region_id', $user->region_id);
        }

        // Apply filters
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('has_dts')) {
            if ($request->has_dts === 'yes') {
                $query->whereHas('dts');
            } else {
                $query->whereDoesntHave('dts');
            }
        }

        $courts = $query->get();

        // Summary statistics with DTS information - Updated to use Dts model
        $summary = [
            'total' => $courts->count(),
            'by_type' => $courts->groupBy('type')->map->count()->toArray(),
            'by_region' => $courts->groupBy('region.name')->map->count()->toArray(),
            'courts_with_laptops' => $courts->filter(function($court) {
                return $court->assets->contains(function($asset) {
                    return stripos($asset->category->name ?? '', 'laptop') !== false;
                });
            })->count(),
            'courts_with_computers' => $courts->filter(function($court) {
                return $court->assets->contains(function($asset) {
                    return stripos($asset->category->name ?? '', 'computer') !== false || 
                           stripos($asset->category->name ?? '', 'desktop') !== false;
                });
            })->count(),
            'courts_with_dts' => $courts->filter(function($court) {
                return $court->dts->isNotEmpty();
            })->count(),
            'courts_without_dts' => $courts->filter(function($court) {
                return $court->dts->isEmpty();
            })->count(),
            'total_dts_assets' => Dts::count(),
            'total_assets' => $courts->sum(function($court) {
                return $court->assets->count();
            }),
            'total_users' => $courts->sum(function($court) {
                return $court->users->count();
            }),
        ];

        $regions = $isRegionalAdmin ? Region::where('id', $user->region_id)->get() : Region::all();

        return view('reports.courts', compact('courts', 'summary', 'regions'));
    }

    public function assets(Request $request)
    {
        $this->authorize('view_reports');

        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $query = Asset::with(['category', 'region', 'court', 'assignedUser']);

        if ($isRegionalAdmin) {
            $query->where('region_id', $user->region_id);
        }

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('purchase_year')) {
            $query->whereYear('purchase_date', $request->purchase_year);
        }

        // Filter for DTS assets - Updated to check if asset is linked to DTS
        if ($request->filled('asset_type')) {
            if ($request->asset_type === 'dts') {
                $query->whereHas('dts');
            } elseif ($request->asset_type === 'non_dts') {
                $query->whereDoesntHave('dts');
            }
        }

        $assets = $query->get();

        // Summary statistics with DTS information - Updated to use Dts model
        $summary = [
            'total' => $assets->count(),
            'by_status' => $assets->groupBy('status')->map->count()->toArray(),
            'by_condition' => $assets->groupBy('condition')->map->count()->toArray(),
            'by_category' => $assets->groupBy('category.name')->map->count()->toArray(),
            'by_region' => $assets->groupBy('region.name')->map->count()->toArray(),
            'dts_assets' => $assets->filter(function($asset) {
                return $asset->dts !== null;
            })->count(),
            'non_dts_assets' => $assets->filter(function($asset) {
                return $asset->dts === null;
            })->count(),
        ];

        $categories = Category::all();
        $regions = $isRegionalAdmin ? Region::where('id', $user->region_id)->get() : Region::all();

        return view('reports.assets', compact('assets', 'summary', 'categories', 'regions'));
    }

    // Add a dedicated DTS report method - Updated to use Dts model
    public function dts(Request $request)
    {
        $this->authorize('view_reports');

        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $query = Dts::with(['asset.category', 'court.region', 'asset.assignedUser']);

        if ($isRegionalAdmin) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('region_id', $user->region_id);
            });
        }

        // Apply filters
        if ($request->filled('region_id')) {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('condition')) {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('condition', $request->condition);
            });
        }

        $dtsAssets = $query->get();

        // Courts with DTS - Updated to use Dts model relationship
        $courtsWithDts = Court::with(['region', 'location'])
            ->whereHas('dts')
            ->get();

        // Courts without DTS - Updated to use Dts model relationship
        $courtsWithoutDts = Court::with(['region', 'location'])
            ->whereDoesntHave('dts')
            ->get();

        $summary = [
            'total_dts_assets' => $dtsAssets->count(),
            'dts_by_status' => $dtsAssets->groupBy('asset.status')->map->count()->toArray(),
            'dts_by_condition' => $dtsAssets->groupBy('asset.condition')->map->count()->toArray(),
            'dts_by_region' => $dtsAssets->groupBy('court.region.name')->map->count()->toArray(),
            'dts_by_court' => $dtsAssets->groupBy('court.name')->map->count()->toArray(),
            'courts_with_dts' => $courtsWithDts->count(),
            'courts_without_dts' => $courtsWithoutDts->count(),
            'total_courts' => $courtsWithDts->count() + $courtsWithoutDts->count(),
        ];

        $regions = $isRegionalAdmin ? Region::where('id', $user->region_id)->get() : Region::all();

        return view('reports.dts', compact('dtsAssets', 'courtsWithDts', 'courtsWithoutDts', 'summary', 'regions'));
    }

    public function exportUsersReport(Request $request)
    {
        $this->authorize('view_reports');
        // Implementation for exporting users report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function exportCourtsReport(Request $request)
    {
        $this->authorize('view_reports');
        // Implementation for exporting courts report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function exportAssetsReport(Request $request)
    {
        $this->authorize('view_reports');
        // Implementation for exporting assets report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function exportDtsReport(Request $request)
    {
        $this->authorize('view_reports');
        // Implementation for exporting DTS report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}