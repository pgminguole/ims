<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Court;
use App\Models\Region;
use App\Models\Category;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        // Core Query
        $assetQuery = Asset::query();
        if ($isRegionalAdmin) {
            $assetQuery->where('region_id', $user->region_id);
        }

        // Core Statistics
        $totalAssets = (clone $assetQuery)->count();
        $assignedAssets = (clone $assetQuery)->where('status', 'assigned')->count();
        $availableAssets = (clone $assetQuery)->where('status', 'available')->count();
        $retiredAssets = (clone $assetQuery)->where('status', 'retired')->count();
        
        // Device Type Statistics
        $laptopsCount = (clone $assetQuery)->whereHas('category', function($query) {
            $query->where('name', 'like', '%laptop%');
        })->count();
        
        $computersCount = (clone $assetQuery)->whereHas('category', function($query) {
            $query->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%desktop%');
        })->count();
        
        $printersCount = (clone $assetQuery)->whereHas('category', function($query) {
            $query->where('name', 'like', '%printer%');
        })->count();
        
        $networkDevicesCount = (clone $assetQuery)->whereHas('category', function($query) {
            $query->where('name', 'like', '%network%')
                  ->orWhere('name', 'like', '%router%')
                  ->orWhere('name', 'like', '%switch%');
        })->count();

        // User Device Statistics
        $userQuery = User::query();
        if ($isRegionalAdmin) {
            $userQuery->where('region_id', $user->region_id);
        }

        $judgesWithLaptops = (clone $userQuery)->whereHas('assignedRole', function($query) {
            $query->where('name', 'like', '%judge%');
        })->whereHas('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        $judgesWithoutLaptops = (clone $userQuery)->whereHas('assignedRole', function($query) {
            $query->where('name', 'like', '%judge%');
        })->whereDoesntHave('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        $registryStaffWithComputers = (clone $userQuery)->whereHas('assignedRole', function($query) {
            $query->where('name', 'like', '%registry%')
                  ->orWhere('name', 'like', '%staff%');
        })->whereHas('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%desktop%');
            });
        })->count();

        // Court Statistics
        $courtQuery = Court::query();
        if ($isRegionalAdmin) {
            $courtQuery->where('region_id', $user->region_id);
        }

        $courtsWithLaptops = (clone $courtQuery)->whereHas('assets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        $courtsWithComputers = (clone $courtQuery)->whereHas('assets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%desktop%');
            });
        })->count();

        $courtsWithPrinters = (clone $courtQuery)->whereHas('assets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%printer%');
            });
        })->count();

        // Assets by Status
        $assetsByStatus = collect([
            ['status' => 'Assigned', 'count' => $assignedAssets, 'color' => '#10B981'],
            ['status' => 'Available', 'count' => $availableAssets, 'color' => '#3B82F6'],
            ['status' => 'Retired', 'count' => $retiredAssets, 'color' => '#6B7280'],
        ])->filter(fn($item) => $item['count'] > 0);

        // Assets by Category
        $assetsByCategory = Category::withCount(['assets' => function($query) use ($user, $isRegionalAdmin) {
                if ($isRegionalAdmin) {
                    $query->where('region_id', $user->region_id);
                }
            }])
            ->having('assets_count', '>', 0)
            ->orderByDesc('assets_count')
            ->limit(8)
            ->get()
            ->map(function($category, $index) {
                return [
                    'name' => $category->name,
                    'count' => $category->assets_count,
                    'color' => $this->getCategoryColor($index)
                ];
            });

        // Recent Activities
        $historyQuery = AssetHistory::with(['asset', 'performedBy']);
        if ($isRegionalAdmin) {
            $historyQuery->whereNotNull('performed_by')
                ->whereHas('performedBy', function($q) use ($user) {
                    $q->where('region_id', $user->region_id);
                });
        }

        $recentActivities = $historyQuery
            ->latest('performed_at')
            ->limit(8)
            ->get();

        // Device Distribution by Court (Top 10)
        $distributionQuery = (clone $courtQuery)
            ->withCount(['assets as laptops_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'like', '%laptop%');
                });
            }])
            ->withCount(['assets as computers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'like', '%computer%')
                      ->orWhere('name', 'like', '%desktop%');
                });
            }])
            ->withCount(['assets as printers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'like', '%printer%');
                });
            }])
            ->with('region')
            ->having('laptops_count', '>', 0)
            ->orHaving('computers_count', '>', 0)
            ->orHaving('printers_count', '>', 0);
        
        $deviceDistributionByCourt = $distributionQuery
            ->orderByRaw('(laptops_count + computers_count + printers_count) DESC')
            ->limit(10)
            ->get();

        // Top Courts by Total Assets
        $topCourts = (clone $courtQuery)
            ->withCount('assets')
            ->with('region')
            ->having('assets_count', '>', 0)
            ->orderByDesc('assets_count')
            ->limit(6)
            ->get();

        // Asset Age Distribution
        $assetAgeDistribution = collect([
            'New (<1 yr)' => (clone $assetQuery)->whereNotNull('purchase_date')
                ->where('purchase_date', '>=', now()->subYear())->count(),
            'Young (1-3 yrs)' => (clone $assetQuery)->whereNotNull('purchase_date')
                ->where('purchase_date', '>=', now()->subYears(3))
                ->where('purchase_date', '<', now()->subYear())->count(),
            'Mid (3-5 yrs)' => (clone $assetQuery)->whereNotNull('purchase_date')
                ->where('purchase_date', '>=', now()->subYears(5))
                ->where('purchase_date', '<', now()->subYears(3))->count(),
            'Aging (5+ yrs)' => (clone $assetQuery)->whereNotNull('purchase_date')
                ->where('purchase_date', '<', now()->subYears(5))->count(),
        ]);

        // Assets by Region for geographic distribution
        $regionQuery = Region::query();
        if ($isRegionalAdmin) {
            $regionQuery->where('id', $user->region_id);
        }

        $assetsByRegion = $regionQuery->withCount(['assets' => function($query) use ($user, $isRegionalAdmin) {
                if ($isRegionalAdmin) {
                    $query->where('region_id', $user->region_id);
                }
            }])
            ->having('assets_count', '>', 0)
            ->orderByDesc('assets_count')
            ->limit(10)
            ->get()
            ->map(function($region, $index) {
                return [
                    'name' => $region->name,
                    'count' => $region->assets_count,
                    'color' => $this->getRegionColor($index)
                ];
            });

        return view('dashboard', compact(
            'totalAssets',
            'assignedAssets',
            'availableAssets',
            'retiredAssets',
            'laptopsCount',
            'computersCount',
            'printersCount',
            'networkDevicesCount',
            'judgesWithLaptops',
            'judgesWithoutLaptops',
            'registryStaffWithComputers',
            'courtsWithLaptops',
            'courtsWithComputers',
            'courtsWithPrinters',
            'assetsByStatus',
            'assetsByCategory',
            'recentActivities',
            'deviceDistributionByCourt',
            'topCourts',
            'assetAgeDistribution',
            'assetsByRegion'
        ));
    }

    private function getCategoryColor($index)
    {
        $colors = ['#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
        return $colors[$index % count($colors)];
    }

    private function getRegionColor($index)
    {
        $colors = ['#6366F1', '#8B5CF6', '#EC4899', '#F43F5E', '#F59E0B', '#10B981', '#06B6D4', '#3B82F6'];
        return $colors[$index % count($colors)];
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'assigned' => '#10B981',
            'available' => '#3B82F6',
            'maintenance' => '#F59E0B',
            'retired' => '#6B7280',
            default => '#9CA3AF'
        };
    }

    private function getConditionColor($condition)
    {
        return match($condition) {
            'excellent' => '#10B981',
            'good' => '#3B82F6',
            'fair' => '#F59E0B',
            'poor' => '#EF4444',
            default => '#6B7280'
        };
    }

    public function reports()
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $regions = Region::where('is_active', true);
        if ($isRegionalAdmin) {
            $regions->where('id', $user->region_id);
        }
        $regions = $regions->get();

        $courts = Court::where('is_active', true);
        if ($isRegionalAdmin) {
            $courts->where('region_id', $user->region_id);
        }
        $courts = $courts->get();
        
        return view('reports.index', compact('regions', 'courts'));
    }

    public function generateReport(Request $request)
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');
        
        $query = Asset::with(['category', 'region', 'court', 'assignedUser']);

        if ($isRegionalAdmin) {
            $query->where('region_id', $user->region_id);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }

        $assets = $query->get();

        if ($request->report_type === 'detailed') {
            return view('reports.detailed', compact('assets'));
        }

        return view('reports.summary', compact('assets'));
    }
}