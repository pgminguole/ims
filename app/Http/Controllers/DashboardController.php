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
        // Core Statistics
        $totalAssets = Asset::count();
        $assignedAssets = Asset::where('status', 'assigned')->count();
        $availableAssets = Asset::where('status', 'available')->count();
        $retiredAssets = Asset::where('status', 'retired')->count();
        
        // Device Type Statistics
        $laptopsCount = Asset::whereHas('category', function($query) {
            $query->where('name', 'like', '%laptop%');
        })->count();
        
        $computersCount = Asset::whereHas('category', function($query) {
            $query->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%desktop%');
        })->count();
        
        $printersCount = Asset::whereHas('category', function($query) {
            $query->where('name', 'like', '%printer%');
        })->count();
        
        $networkDevicesCount = Asset::whereHas('category', function($query) {
            $query->where('name', 'like', '%network%')
                  ->orWhere('name', 'like', '%router%')
                  ->orWhere('name', 'like', '%switch%');
        })->count();

        // User Device Statistics
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

        $registryStaffWithComputers = User::whereHas('role', function($query) {
            $query->where('name', 'like', '%registry%')
                  ->orWhere('name', 'like', '%staff%');
        })->whereHas('assignedAssets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%desktop%');
            });
        })->count();

        // Court Statistics
        $courtsWithLaptops = Court::whereHas('assets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%laptop%');
            });
        })->count();

        $courtsWithComputers = Court::whereHas('assets', function($query) {
            $query->whereHas('category', function($q) {
                $q->where('name', 'like', '%computer%')
                  ->orWhere('name', 'like', '%desktop%');
            });
        })->count();

        $courtsWithPrinters = Court::whereHas('assets', function($query) {
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
        $assetsByCategory = Category::withCount('assets')
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

        // Monthly Asset Acquisition Trend (last 12 months)
        $assetAcquisitionTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Asset::whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->count();
            
            $assetAcquisitionTrend->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }

        // Recent Activities
        $recentActivities = AssetHistory::with(['asset', 'performedBy'])
            ->latest('performed_at')
            ->limit(10)
            ->get();

        // Device Distribution by Court
        $deviceDistributionByCourt = Court::withCount(['assets as laptops_count' => function($query) {
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
            ->having('laptops_count', '>', 0)
            ->orHaving('computers_count', '>', 0)
            ->orHaving('printers_count', '>', 0)
            ->orderByDesc('laptops_count')
            ->limit(10)
            ->get();

        // User Device Assignment Stats
        $userDeviceStats = User::withCount(['assignedAssets as laptops_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'like', '%laptop%');
                });
            }])
            ->withCount(['assignedAssets as computers_count' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->where('name', 'like', '%computer%')
                      ->orWhere('name', 'like', '%desktop%');
                });
            }])
            ->whereHas('role', function($query) {
                $query->where('name', 'like', '%judge%')
                      ->orWhere('name', 'like', '%registry%');
            })
            ->having('laptops_count', '>', 0)
            ->orHaving('computers_count', '>', 0)
            ->orderByDesc('laptops_count')
            ->limit(10)
            ->get();

        // Top Courts by Total Assets
        $topCourts = Court::withCount('assets')
            ->with('region')
            ->having('assets_count', '>', 0)
            ->orderByDesc('assets_count')
            ->limit(6)
            ->get();

        // Asset Age Distribution
        $assetAgeDistribution = collect([
            'New (<1 yr)' => Asset::whereNotNull('purchase_date')
                ->where('purchase_date', '>=', now()->subYear())->count(),
            'Young (1-3 yrs)' => Asset::whereNotNull('purchase_date')
                ->where('purchase_date', '>=', now()->subYears(3))
                ->where('purchase_date', '<', now()->subYear())->count(),
            'Mid (3-5 yrs)' => Asset::whereNotNull('purchase_date')
                ->where('purchase_date', '>=', now()->subYears(5))
                ->where('purchase_date', '<', now()->subYears(3))->count(),
            'Aging (5+ yrs)' => Asset::whereNotNull('purchase_date')
                ->where('purchase_date', '<', now()->subYears(5))->count(),
        ]);

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
            'assetAcquisitionTrend',
            'recentActivities',
            'deviceDistributionByCourt',
            'userDeviceStats',
            'topCourts',
            'assetAgeDistribution'
        ));
    }

    private function getCategoryColor($index)
    {
        $colors = ['#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
        return $colors[$index % count($colors)];
    }

    // private function getCategoryColor($index)
    // {
    //     $colors = ['#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
    //     return $colors[$index % count($colors)];
    // }

    // public function index()
    // {
    //     // Core Statistics with enhanced calculations
    //     $totalAssets = Asset::count();
    //     $assignedAssets = Asset::assigned()->count();
    //     $underMaintenance = Asset::underMaintenance()->count();
    //     $availableAssets = Asset::available()->count();
    //     $retiredAssets = Asset::where('status', 'retired')->count();
        
    //     // Financial Metrics with enhanced analysis
    //     $totalAssetValue = Asset::sum('current_value');
    //     $totalPurchaseCost = Asset::sum('purchase_cost');
    //     $depreciationAmount = $totalPurchaseCost - $totalAssetValue;
    //     $depreciationRate = $totalPurchaseCost > 0 ? ($depreciationAmount / $totalPurchaseCost) * 100 : 0;
        
    //     // Asset Age Analysis
    //     $averageAssetAge = Asset::whereNotNull('purchase_date')
    //         ->get()
    //         ->avg(function($asset) {
    //             return $asset->purchase_date ? $asset->purchase_date->diffInMonths(now()) : 0;
    //         });

    //     // Enhanced Utilization Metrics
    //     $utilizationRate = $totalAssets > 0 ? ($assignedAssets / $totalAssets) * 100 : 0;
    //     $idleRate = $totalAssets > 0 ? ($availableAssets / $totalAssets) * 100 : 0;
        
    //     // Maintenance Statistics with trends
    //     $totalMaintenanceCost = MaintenanceLog::sum('cost');
    //     $maintenanceThisMonth = MaintenanceLog::whereMonth('maintenance_date', now()->month)
    //         ->whereYear('maintenance_date', now()->year)
    //         ->sum('cost');
    //     $maintenanceLastMonth = MaintenanceLog::whereMonth('maintenance_date', now()->subMonth()->month)
    //         ->whereYear('maintenance_date', now()->subMonth()->year)
    //         ->sum('cost');
    //     $maintenanceCount = MaintenanceLog::count();
    //     $maintenanceTrend = $maintenanceLastMonth > 0 ? 
    //         (($maintenanceThisMonth - $maintenanceLastMonth) / $maintenanceLastMonth) * 100 : 0;
        
    //     // Enhanced Charts Data
    //     // Assets by Status (for donut chart)
    //     $assetsByStatus = Asset::select('status', DB::raw('count(*) as count'))
    //         ->groupBy('status')
    //         ->get()
    //         ->map(function($item) {
    //             return [
    //                 'status' => ucfirst($item->status),
    //                 'count' => $item->count,
    //                 'color' => $this->getStatusColor($item->status)
    //             ];
    //         });

    //     // Assets by Region (for horizontal bar chart)
    //     $assetsByRegion = Region::withCount('assets')
    //         ->having('assets_count', '>', 0)
    //         ->get()
    //         ->map(function($region) {
    //             return [
    //                 'name' => $region->name,
    //                 'count' => $region->assets_count,
    //                 'value' => $region->assets_count
    //             ];
    //         });

    //     // Assets by Category (for pie chart)
    //     $assetsByCategory = Category::withCount('assets')
    //         ->having('assets_count', '>', 0)
    //         ->orderByDesc('assets_count')
    //         ->limit(8)
    //         ->get()
    //         ->map(function($category, $index) {
    //             return [
    //                 'name' => $category->name,
    //                 'count' => $category->assets_count,
    //                 'color' => $this->getCategoryColor($index)
    //             ];
    //         });

    //     // Assets by Condition (for gauge chart)
    //     $assetsByCondition = Asset::select('condition', DB::raw('count(*) as count'))
    //         ->whereNotNull('condition')
    //         ->groupBy('condition')
    //         ->get()
    //         ->map(function($item) {
    //             return [
    //                 'condition' => ucfirst($item->condition),
    //                 'count' => $item->count,
    //                 'color' => $this->getConditionColor($item->condition)
    //             ];
    //         });

    //     // Enhanced Trend Analysis
    //     // Monthly Asset Acquisition Trend (last 12 months)
    //     $assetAcquisitionTrend = collect();
    //     $assetValueTrend = collect();
        
    //     for ($i = 11; $i >= 0; $i--) {
    //         $date = now()->subMonths($i);
    //         $monthAssets = Asset::whereYear('purchase_date', $date->year)
    //             ->whereMonth('purchase_date', $date->month)
    //             ->get();
            
    //         $count = $monthAssets->count();
    //         $value = $monthAssets->sum('purchase_cost');
            
    //         $assetAcquisitionTrend->push([
    //             'month' => $date->format('M Y'),
    //             'count' => $count,
    //             'value' => $value
    //         ]);
    //     }

    //     // Maintenance Cost Trend (last 12 months)
    //     $maintenanceCostTrend = collect();
    //     for ($i = 11; $i >= 0; $i--) {
    //         $date = now()->subMonths($i);
    //         $cost = MaintenanceLog::whereYear('maintenance_date', $date->year)
    //             ->whereMonth('maintenance_date', $date->month)
    //             ->sum('cost');
            
    //         $maintenanceCostTrend->push([
    //             'month' => $date->format('M Y'),
    //             'cost' => (float) $cost,
    //             'count' => MaintenanceLog::whereYear('maintenance_date', $date->year)
    //                 ->whereMonth('maintenance_date', $date->month)
    //                 ->count()
    //         ]);
    //     }

    //     // Enhanced Recent Activities
    //     $recentActivities = AssetHistory::with(['asset', 'performedBy'])
    //         ->latest('performed_at')
    //         ->limit(10)
    //         ->get();

    //     // Maintenance Alerts
    //     $maintenanceDue = Asset::where('next_maintenance', '<=', now()->addDays(30))
    //         ->where('next_maintenance', '>=', now())
    //         ->orderBy('next_maintenance')
    //         ->limit(8)
    //         ->get();

    //     $overdueMaintenance = Asset::where('next_maintenance', '<', now())
    //         ->whereNotNull('next_maintenance')
    //         ->count();

    //     // Warranty Alerts
    //     $warrantyExpiring = Asset::where('warranty_expiry', '<=', now()->addDays(90))
    //         ->where('warranty_expiry', '>=', now())
    //         ->orderBy('warranty_expiry')
    //         ->limit(8)
    //         ->get();

    //     $expiredWarranties = Asset::where('warranty_expiry', '<', now())
    //         ->whereNotNull('warranty_expiry')
    //         ->count();

    //     // Performance Metrics
    //     $topCourts = Court::withCount('assets')
    //         ->having('assets_count', '>', 0)
    //         ->orderByDesc('assets_count')
    //         ->limit(6)
    //         ->get();

    //     // Enhanced Asset Age Distribution
    //     $assetAgeDistribution = collect([
    //         'New (<1 yr)' => Asset::whereNotNull('purchase_date')
    //             ->where('purchase_date', '>=', now()->subYear())->count(),
    //         'Young (1-3 yrs)' => Asset::whereNotNull('purchase_date')
    //             ->where('purchase_date', '>=', now()->subYears(3))
    //             ->where('purchase_date', '<', now()->subYear())->count(),
    //         'Mid (3-5 yrs)' => Asset::whereNotNull('purchase_date')
    //             ->where('purchase_date', '>=', now()->subYears(5))
    //             ->where('purchase_date', '<', now()->subYears(3))->count(),
    //         'Aging (5+ yrs)' => Asset::whereNotNull('purchase_date')
    //             ->where('purchase_date', '<', now()->subYears(5))->count(),
    //     ]);

    //     // Audit & Compliance
    //     $assetsAuditedThisMonth = Asset::whereMonth('last_audited_at', now()->month)
    //         ->whereYear('last_audited_at', now()->year)
    //         ->count();
    //     $assetsNeverAudited = Asset::whereNull('last_audited_at')->count();
    //     $auditComplianceRate = $totalAssets > 0 
    //         ? (($totalAssets - $assetsNeverAudited) / $totalAssets) * 100 
    //         : 0;

    //     // Cost Analysis
    //     $maintenanceToAssetValueRatio = $totalAssetValue > 0 ? 
    //         ($totalMaintenanceCost / $totalAssetValue) * 100 : 0;

    //     return view('dashboard', compact(
    //         'totalAssets',
    //         'assignedAssets',
    //         'underMaintenance',
    //         'availableAssets',
    //         'retiredAssets',
    //         'totalAssetValue',
    //         'totalPurchaseCost',
    //         'depreciationAmount',
    //         'depreciationRate',
    //         'averageAssetAge',
    //         'utilizationRate',
    //         'idleRate',
    //         'totalMaintenanceCost',
    //         'maintenanceThisMonth',
    //         'maintenanceTrend',
    //         'maintenanceCount',
    //         'assetsByStatus',
    //         'assetsByRegion',
    //         'assetsByCategory',
    //         'assetsByCondition',
    //         'assetAcquisitionTrend',
    //         'maintenanceCostTrend',
    //         'recentActivities',
    //         'maintenanceDue',
    //         'overdueMaintenance',
    //         'warrantyExpiring',
    //         'expiredWarranties',
    //         'topCourts',
    //         'assetAgeDistribution',
    //         'assetsAuditedThisMonth',
    //         'assetsNeverAudited',
    //         'auditComplianceRate',
    //         'maintenanceToAssetValueRatio'
    //     ));
    // }

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
        $regions = Region::where('is_active', true)->get();
        $courts = Court::where('is_active', true)->get();
        
        return view('reports.index', compact('regions', 'courts'));
    }

    public function generateReport(Request $request)
    {
        // Your existing report generation logic
        $query = Asset::with(['category', 'region', 'court', 'assignedUser']);

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