<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Court;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function index()
    {
        // Basic statistics for dashboard
        $totalAssets = Asset::count();
        $totalValue = Asset::sum('current_value');
        $assignedAssets = Asset::where('status', 'assigned')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();

        // User statistics for dashboard
        $totalUsers = User::count();
        $judgesWithLaptops = User::whereHas('role', function($query) {
            $query->where('name', 'like', '%judge%');
        })->whereHas('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        $judgesWithoutLaptops = User::whereHas('role', function($query) {
            $query->where('name', 'like', '%judge%');
        })->whereDoesntHave('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        return view('reports.index', compact(
            'totalAssets',
            'totalValue',
            'assignedAssets',
            'maintenanceAssets',
            'totalUsers',
            'judgesWithLaptops',
            'judgesWithoutLaptops'
        ));
    }

    public function users(Request $request)
    {
        $query = User::with(['role', 'assignedAssets.category', 'court', 'region']);

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

        $regions = Region::all();
        $courts = Court::all();
        $roles = \App\Models\Role::all();

        return view('reports.users', compact('users', 'summary', 'regions', 'courts', 'roles'));
    }

    public function courts(Request $request)
    {
        $query = Court::with(['region', 'location', 'assets.category', 'users.role']);

        // Apply filters
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $courts = $query->get();

        // Summary statistics
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
            'total_assets' => $courts->sum(function($court) {
                return $court->assets->count();
            }),
            'total_users' => $courts->sum(function($court) {
                return $court->users->count();
            }),
        ];

        $regions = Region::all();

        return view('reports.courts', compact('courts', 'summary', 'regions'));
    }

    public function assets(Request $request)
    {
        $query = Asset::with(['category', 'region', 'court', 'assignedUser']);

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

        $assets = $query->get();

        // Summary statistics
        $summary = [
            'total' => $assets->count(),
            'total_value' => $assets->sum('current_value'),
            'by_status' => $assets->groupBy('status')->map->count()->toArray(),
            'by_condition' => $assets->groupBy('condition')->map->count()->toArray(),
            'by_category' => $assets->groupBy('category.name')->map->count()->toArray(),
            'by_region' => $assets->groupBy('region.name')->map->count()->toArray()
        ];

        $categories = Category::all();
        $regions = Region::all();

        return view('reports.assets', compact('assets', 'summary', 'categories', 'regions'));
    }

    public function exportUsersReport(Request $request)
    {
        // Implementation for exporting users report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function exportCourtsReport(Request $request)
    {
        // Implementation for exporting courts report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function exportAssetsReport(Request $request)
    {
        // Implementation for exporting assets report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}