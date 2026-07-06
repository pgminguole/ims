<?php

namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Dts;
use App\Models\Court;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AuditorDtsController extends AuditorBaseController
{
//     public function index(Request $request)
//     {
//         Log::info($request->all());
//         $query = Dts::with([
//             'court.region',
//             'monitorAsset',
//             'splitterAsset',
//             'hdmiShortCableAsset',
//             'hdmiLongCableAsset',
//             'extensionBoardAsset',
//             'truckingAsset',
//             'sonyRecorderAsset'
//         ]);

//         // Apply YEAR filter on created_at
//         if ($request->filled('year')) {
//             $year = $request->year;
//             $query->whereYear('created_at', $year);
//         }

//         // Apply MONTH filter on created_at
//         if ($request->filled('month')) {
//             $month = $request->month;
//             $query->whereMonth('created_at', $month);
//         }

//         // Date range filter - FROM
//         if ($request->filled('date_from')) {
//             $dateFrom = Carbon::parse($request->date_from)->format('Y-m-d');
//             $query->whereDate('created_at', '>=', $dateFrom);
//         }

//         // Date range filter - TO
//         if ($request->filled('date_to')) {
//             $dateTo = Carbon::parse($request->date_to)->format('Y-m-d');
//             $query->whereDate('created_at', '<=', $dateTo);
//         }

//         // Create clean request without date filters for applyFilters
//         $cleanRequest = new Request($request->except(['year', 'month', 'date_from', 'date_to']));

//     $query = $this->applyFilters($query, $cleanRequest, [
//     'court_id' => 'court_id',
//     'is_available' => 'is_available',
//     'region_id' => function($q) use ($cleanRequest) {
//         $q->whereHas('court', function($courtQuery) use ($cleanRequest) {
//             $courtQuery->where('region_id', $cleanRequest->region_id);
//         });
//     }
// ]);
        

//         // Search functionality
//         if ($request->filled('search')) {
//             $search = $request->search;
//             $query->where(function($q) use ($search) {
//                 $q->where('name', 'like', "%{$search}%")
//                   ->orWhereHas('court', function($courtQuery) use ($search) {
//                       $courtQuery->where('name', 'like', "%{$search}%")
//                               ->orWhere('code', 'like', "%{$search}%");
//                   });
//             });
//         }

//         $dtsSystems = $query->latest()->paginate(20);
//         $filterData = $this->getFilterData();

//         // Calculate statistics
//         $totalDts = Dts::count();
//         $availableDts = Dts::where('is_available', true)->count();
        
//         // Calculate complete systems (you might need to adjust this logic based on your definition of "complete")
//         $completeSystems = Dts::whereHas('monitorAsset')
//             ->whereHas('splitterAsset')
//             ->whereHas('hdmiShortCableAsset')
//             ->whereHas('hdmiLongCableAsset')
//             ->count();

//         // Courts with DTS
//         $courtsWithDts = Court::has('dts')->count();

//         // Get years for filter
//         $years = Dts::whereNotNull('created_at')
//             ->selectRaw('YEAR(created_at) as year')
//             ->distinct()
//             ->orderBy('year', 'desc')
//             ->pluck('year')
//             ->filter(function($year) {
//                 return !empty($year) && is_numeric($year) && $year > 2000;
//             })
//             ->values();

//         // Get courts for filter
//         $courts = Court::active()->orderBy('name')->get();

//         return view('auditor.dts.index', compact(
//             'dtsSystems', 
//             'filterData', 
//             'totalDts', 
//             'availableDts', 
//             'completeSystems', 
//             'courtsWithDts',
//             'years',
//             'courts'
//         ));
//     }

public function index(Request $request)
{
    Log::info($request->all());
    
    $query = Dts::with([
        'court.region',
        'monitorAsset',
        'splitterAsset',
        'hdmiShortCableAsset',
        'hdmiLongCableAsset',
        'extensionBoardAsset',
        'truckingAsset',
        'sonyRecorderAsset'
    ]);
    
    // Apply YEAR filter on created_at
    if ($request->filled('year')) {
        $year = $request->year;
        $query->whereYear('created_at', $year);
    }
    
    // Apply MONTH filter on created_at
    if ($request->filled('month')) {
        $month = $request->month;
        $query->whereMonth('created_at', $month);
    }
    
    // Date range filter - FROM
    if ($request->filled('date_from')) {
        $dateFrom = Carbon::parse($request->date_from)->format('Y-m-d');
        $query->whereDate('created_at', '>=', $dateFrom);
    }
    
    // Date range filter - TO
    if ($request->filled('date_to')) {
        $dateTo = Carbon::parse($request->date_to)->format('Y-m-d');
        $query->whereDate('created_at', '<=', $dateTo);
    }
    
    // Create clean request excluding date filters AND region_id
    $cleanRequest = new Request($request->except(['year', 'month', 'date_from', 'date_to', 'region_id']));
    
    // Apply other filters (only court_id and is_available)
    $query = $this->applyFilters($query, $cleanRequest, [
        'court_id' => 'court_id',
        'is_available' => 'is_available'
    ]);
    
    // Handle region_id filter separately since it's on the related court table
    if ($request->filled('region_id')) {
        $query->whereHas('court', function($courtQuery) use ($request) {
            $courtQuery->where('region_id', $request->region_id);
        });
    }
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('court', function($courtQuery) use ($search) {
                  $courtQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('code', 'like', "%{$search}%");
              });
        });
    }
    
    // Get paginated results
    $dtsSystems = $query->latest()->paginate(20);
    
    // Get filter data
    $filterData = $this->getFilterData();
    
    // Calculate statistics
    $totalDts = Dts::count();
    $availableDts = Dts::where('is_available', true)->count();
    
    // Calculate complete systems (systems with all required assets)
    $completeSystems = Dts::whereHas('monitorAsset')
        ->whereHas('splitterAsset')
        ->whereHas('hdmiShortCableAsset')
        ->whereHas('hdmiLongCableAsset')
        ->count();
    
    // Courts with DTS
    $courtsWithDts = Court::has('dts')->count();
    
    // Get years for filter dropdown
    $years = Dts::whereNotNull('created_at')
        ->selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->filter(function($year) {
            return !empty($year) && is_numeric($year) && $year > 2000;
        })
        ->values();
    
    // Get courts for filter dropdown
    $courts = Court::active()->orderBy('name')->get();
    
    return view('auditor.dts.index', compact(
        'dtsSystems', 
        'filterData', 
        'totalDts', 
        'availableDts', 
        'completeSystems', 
        'courtsWithDts',
        'years',
        'courts'
    ));
}
    public function show(Dts $dts)
    {
        $dts->load([
            'court.region',
            'monitorAsset',
            'splitterAsset',
            'hdmiShortCableAsset',
            'hdmiLongCableAsset',
            'extensionBoardAsset',
            'truckingAsset',
            'sonyRecorderAsset'
        ]);

        return view('auditor.dts.show', compact('dts'));
    }
}