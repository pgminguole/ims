<?php

namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Region;
use App\Models\Category;
use App\Models\Court;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditorAssetController extends AuditorBaseController
{
    public function index(Request $request)
    {
        // Enable query logging for debugging
        DB::enableQueryLog();

        $query = Asset::with([
            'region', 
            'category', 
            'subcategory',
            'court',
            'courtAssignment',
            'office',
            'officeAssignment',
            'assignedUser',
            'location'
        ])->where('record_type', 'assignment');

        // Log all request parameters
        Log::info('=== ASSET FILTER REQUEST ===');
        Log::info('All Parameters:', $request->all());

        // Initial count
        $initialCount = $query->count();
        Log::info('Initial asset count (no filters):', ['count' => $initialCount]);

        // Apply YEAR filter on assigned_date ONLY
        if ($request->filled('year')) {
            $year = $request->year;
            Log::info('Applying YEAR filter on assigned_date:', ['year' => $year]);
            
            $query->whereYear('assigned_date', $year);
            
            $afterYearCount = $query->count();
            Log::info('After YEAR filter count:', ['count' => $afterYearCount]);
        }

        // Apply MONTH filter on assigned_date ONLY
        if ($request->filled('month')) {
            $month = $request->month;
            Log::info('Applying MONTH filter on assigned_date:', ['month' => $month]);
            
            $query->whereMonth('assigned_date', $month);
            
            $afterMonthCount = $query->count();
            Log::info('After MONTH filter count:', ['count' => $afterMonthCount]);
        }

        // Date range filter - FROM (on assigned_date)
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->format('Y-m-d');
            Log::info('Applying DATE_FROM filter on assigned_date:', ['date' => $dateFrom]);
            
            $query->whereDate('assigned_date', '>=', $dateFrom);
            
            $afterDateFromCount = $query->count();
            Log::info('After DATE_FROM filter count:', ['count' => $afterDateFromCount]);
        }

        // Date range filter - TO (on assigned_date)
        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->format('Y-m-d');
            Log::info('Applying DATE_TO filter on assigned_date:', ['date' => $dateTo]);
            
            $query->whereDate('assigned_date', '<=', $dateTo);
            
            $afterDateToCount = $query->count();
            Log::info('After DATE_TO filter count:', ['count' => $afterDateToCount]);
        }

        // Create a clean request without date filters for applyFilters
        // This prevents applyFilters from applying date filters again
        $cleanRequest = new Request($request->except(['year', 'month', 'date_from', 'date_to']));
        
        Log::info('Clean request for applyFilters:', $cleanRequest->all());

        // Apply other filters (excluding date filters)
        $query = $this->applyFilters($query, $cleanRequest, [
            'category_id' => 'category_id',
            'region_id' => 'region_id',
            'court_id' => 'court_id',
            'office_id' => 'office_id',
            'status' => 'status',
            'condition' => 'condition'
        ]);

        Log::info('After applyFilters count:', ['count' => $query->count()]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            Log::info('Applying SEARCH filter:', ['search' => $search]);
            
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });

            Log::info('After SEARCH filter count:', ['count' => $query->count()]);
        }

        // Final count before pagination
        $finalCount = $query->count();
        Log::info('Final count before pagination:', ['count' => $finalCount]);

        // Log the actual SQL query
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        Log::info('Final SQL Query:', ['sql' => $sqlQuery, 'bindings' => $bindings]);

        $assets = $query->latest('assigned_date')->paginate(20)->withQueryString();

        // Log executed queries
        $queries = DB::getQueryLog();
        Log::info('All executed queries:', ['queries' => $queries]);

        // Get filter data
        $filterData = $this->getFilterData();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $regions = Region::active()->orderBy('name')->get();
        $courts = Court::active()->orderBy('name')->get();
        $offices = Office::active()->orderBy('name')->get();

        // Get years for date filter
        try {
            $years = Asset::whereNotNull('assigned_date')
                ->where('assigned_date', '!=', '0000-00-00')
                ->where('assigned_date', '!=', '')
                ->selectRaw('YEAR(assigned_date) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->filter(function($year) {
                    return !empty($year) && is_numeric($year) && $year > 2000;
                })
                ->values();

            Log::info('Available years:', ['years' => $years->toArray()]);
        } catch (\Exception $e) {
            Log::error('Error fetching years:', ['error' => $e->getMessage()]);
            $currentYear = now()->year;
            $years = collect(range($currentYear - 10, $currentYear));
        }

        Log::info('=== END ASSET FILTER REQUEST ===');

        return view('auditor.assets.index', compact(
            'assets', 
            'filterData', 
            'categories', 
            'regions', 
            'courts', 
            'offices',
            'years'
        ));
    }

    /**
     * Diagnostic endpoint to check assigned_date data
     */
    public function diagnoseYearFilter(Request $request)
    {
        $year = $request->year ?? date('Y');

        // Check total assets
        $totalAssets = Asset::count();

        // Check assets with assigned_date
        $assetsWithDate = Asset::whereNotNull('assigned_date')
            ->where('assigned_date', '!=', '0000-00-00')
            ->where('assigned_date', '!=', '')
            ->count();

        // Get sample assigned_date values
        $sampleDates = Asset::whereNotNull('assigned_date')
            ->select('id', 'asset_name', 'assigned_date')
            ->orderBy('assigned_date', 'desc')
            ->limit(20)
            ->get();

        // Test different year filtering methods
        $method1Count = Asset::whereYear('assigned_date', $year)->count();
        $method2Count = Asset::whereRaw("YEAR(assigned_date) = ?", [$year])->count();
        $method3Count = Asset::whereBetween('assigned_date', [
            "{$year}-01-01",
            "{$year}-12-31"
        ])->count();

        // Get all unique years in database
        $allYears = Asset::whereNotNull('assigned_date')
            ->selectRaw('YEAR(assigned_date) as year, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        // Check data type of assigned_date column
        $columnInfo = DB::select("SHOW COLUMNS FROM assets WHERE Field = 'assigned_date'");

        return response()->json([
            'requested_year' => $year,
            'total_assets' => $totalAssets,
            'assets_with_assigned_date' => $assetsWithDate,
            'filtering_results' => [
                'whereYear' => $method1Count,
                'whereRaw_YEAR' => $method2Count,
                'whereBetween' => $method3Count,
            ],
            'sample_dates' => $sampleDates,
            'all_years_with_counts' => $allYears,
            'column_info' => $columnInfo,
            'current_date' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'region',
            'category',
            'subcategory',
            'court',
            'courtAssignment',
            'office',
            'officeAssignment',
            'assignedUser',
            'location',
            'attachments',
            'maintenanceLogs',
            'histories' => function($query) {
                $query->latest()->take(10);
            }
        ]);

        return view('auditor.assets.show', compact('asset'));
    }

    public function verify(Asset $asset)
    {
        $asset->update([
            'is_audited' => true,
            'audited_at' => now(),
            'audited_by_id' => auth()->id()
        ]);

        return back()->with('success', 'Asset assignment marked as verified/audited successfully.');
    }
}