<?php
namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Court;
use App\Models\Dts;
use App\Models\Region;
use App\Models\Office;
use App\Models\Category;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Log;
class AuditorReportController extends AuditorBaseController
{
    public function index()
    {
        $filterData = $this->getFilterData();
        return view('auditor.reports.index', compact('filterData'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:assets,users,courts,dts,summary,offices',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'region_id' => 'nullable|exists:regions,id',
            'court_type' => 'nullable|string',
            'user_type' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'asset_status' => 'nullable|string'
        ]);

        $reportData = $this->generateReportData($request);
        
        // If format is specified and not HTML, export directly
        if ($request->format && $request->format !== 'html') {
            return $this->export($request);
        }
        
        return view('auditor.reports.results', array_merge($reportData, [
            'filters' => $request->all(),
            'filterData' => $this->getFilterData(),
            'generated_at' => now()
        ]));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,csv',
            'report_type' => 'required|in:assets,users,courts,dts,summary,offices',
        ]);

        // Generate report data
        $reportData = $this->generateReportData($request);
        $reportData['filters'] = $request->all();
        $reportData['generated_at'] = now();

        $format = $request->format;
        $reportType = $request->report_type;
        $filename = $reportType . '_report_' . now()->format('Y-m-d_His');

        switch ($format) {
            case 'pdf':
                return $this->exportPdf($reportData, $filename);
            
            case 'excel':
                return $this->exportExcel($reportData, $filename);
            
            case 'csv':
                return $this->exportCsv($reportData, $filename);
            
            default:
                return back()->with('error', 'Invalid export format');
        }
    }

    private function exportPdf($reportData, $filename)
    {
        $pdf = Pdf::loadView('auditor.reports.pdf', $reportData);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename . '.pdf');
    }

    private function exportExcel($reportData, $filename)
    {
        return Excel::download(new ReportExport($reportData), $filename . '.xlsx');
    }

    private function exportCsv($reportData, $filename)
    {
        $reportType = $reportData['reportType'];
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($reportData, $reportType) {
            $file = fopen('php://output', 'w');

            if ($reportType === 'assets') {
                fputcsv($file, ['Asset Name', 'Category', 'Subcategory', 'Region', 'Court', 'Status', 'Condition', 'Assigned To']);
                
                foreach ($reportData['assets'] as $asset) {
                    $assignedTo = 'Not Assigned';
                    if ($asset->assigned_type === 'user' && $asset->assignedUser) {
                        $assignedTo = $asset->assignedUser->name;
                    } elseif ($asset->office) {
                        $assignedTo = $asset->office->name;
                    } elseif ($asset->court) {
                        $assignedTo = $asset->court->name;
                    }

                    fputcsv($file, [
                        $asset->asset_name,
                        $asset->category->name ?? 'N/A',
                        $asset->subcategory->name ?? 'N/A',
                        $asset->region->name ?? 'N/A',
                        $asset->court->name ?? 'N/A',
                        ucfirst($asset->status),
                        ucfirst($asset->condition),
                        $assignedTo
                    ]);
                }
            } elseif ($reportType === 'dts') {
                fputcsv($file, ['DTS Name', 'Court', 'Region', 'Monitors', 'Splitters', 'HDMI Cables', 'Extension Boards', 'Trucking', 'Sony Recorders', 'Status']);
                
                foreach ($reportData['dtsSystems'] as $dts) {
                    fputcsv($file, [
                        $dts->name,
                        $dts->court->name,
                        $dts->court->region->name ?? 'N/A',
                        $dts->monitors_count,
                        $dts->splitters_count,
                        $dts->hdmi_short_cables_count . ' (5M) & ' . $dts->hdmi_long_cables_count . ' (20M)',
                        $dts->extension_boards_count,
                        $dts->trucking_count,
                        $dts->sony_recorders_count,
                        $dts->is_available ? 'Available' : 'Unavailable'
                    ]);
                }
            } elseif ($reportType === 'users') {
                fputcsv($file, ['Name', 'Email', 'Phone', 'Role', 'Region', 'Court', 'Status', 'Assets Assigned']);
                
                foreach ($reportData['users'] as $user) {
                    fputcsv($file, [
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->role->name ?? 'N/A',
                        $user->region->name ?? 'N/A',
                        $user->court->name ?? 'N/A',
                        ucfirst($user->status),
                        $user->assignedAssets->count()
                    ]);
                }
            } elseif ($reportType === 'courts') {
                fputcsv($file, ['Court Name', 'Type', 'Region', 'Location', 'Assets Count', 'DTS Count', 'Users Count', 'Status']);
                
                foreach ($reportData['courts'] as $court) {
                    fputcsv($file, [
                        $court->name,
                        $court->type,
                        $court->region->name ?? 'N/A',
                        $court->location->name ?? 'N/A',
                        $court->assets->count(),
                        $court->dts->count(),
                        $court->users->count(),
                        $court->is_active ? 'Active' : 'Inactive'
                    ]);
                }
            } elseif ($reportType === 'offices') {
                fputcsv($file, ['Office Name', 'Code', 'Region', 'Court', 'Manager', 'Assets Count', 'Users Count', 'Status']);
                
                foreach ($reportData['offices'] as $office) {
                    fputcsv($file, [
                        $office->name,
                        $office->code,
                        $office->region->name ?? 'N/A',
                        $office->court->name ?? 'N/A',
                        $office->manager->name ?? 'N/A',
                        $office->assets->count(),
                        $office->users->count(),
                        $office->is_active ? 'Active' : 'Inactive'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateReportData(Request $request)
    {
        $reportType = $request->report_type;
        
        switch ($reportType) {
            case 'assets':
                return $this->generateAssetReport($request);
            case 'users':
                return $this->generateUserReport($request);
            case 'courts':
                return $this->generateCourtReport($request);
            case 'dts':
                return $this->generateDtsReport($request);
            case 'offices':
                return $this->generateOfficeReport($request);
            case 'summary':
                return $this->generateSummaryReport($request);
            default:
                return [];
        }
    }

    private function generateAssetReport(Request $request)
    {
        $query = Asset::with(['region', 'category', 'subcategory', 'court', 'office', 'assignedUser']);
        $query = $this->applyFilters($query, $request);
        $assets = $query->get();
        
        $context = null;
        if ($request->court_id) {
            $context = [
                'type' => 'court',
                'data' => Court::with(['region', 'presidingJudge', 'registryOfficer'])->find($request->court_id)
            ];
        } elseif ($request->office_id) {
            $context = [
                'type' => 'office',
                'data' => Office::with(['region', 'court', 'manager'])->find($request->office_id)
            ];
        } elseif ($request->assigned_to && $request->assigned_type === 'user') {
            $context = [
                'type' => 'user',
                'data' => User::with(['role', 'region', 'court'])->find($request->assigned_to)
            ];
        }

        $summary = [
            'total_assets' => $assets->count(),
            'by_status' => $assets->groupBy('status')->map->count(),
            'by_condition' => $assets->groupBy('condition')->map->count(),
            'by_region' => $assets->groupBy('region.name')->map->count(),
            'by_category' => $assets->groupBy('category.name')->map->count(),
        ];

        return [
            'reportType' => 'assets',
            'assets' => $assets,
            'summary' => $summary,
            'context' => $context
        ];
    }

    private function generateUserReport(Request $request)
    {
        Log::info($request->all());
        $query = User::with(['role', 'region', 'court', 'assignedAssets']);
        
        // Exclude specific users
        $query->whereNotIn('name', ['System Administrator', 'ICT Manager', 'Auditor']);
        
        $query = $this->applyFilters($query, $request);
        $users = $query->get();
        
           Log::info($users);
        
        
        $summary = [
            'total_users' => $users->count(),
            'by_status' => $users->groupBy('status')->map->count(),
            'by_role' => $users->groupBy('role.name')->map->count(),
            'by_region' => $users->groupBy('region.name')->map->count(),
            'active_users' => $users->where('status', 'active')->count(),
        ];

        return [
            'reportType' => 'users',
            'users' => $users,
            'summary' => $summary
        ];
    }

    private function generateCourtReport(Request $request)
    {
        $query = Court::with(['region', 'location', 'assets', 'dts', 'users']);
        $query = $this->applyFilters($query, $request);
        $courts = $query->get();
        
        $summary = [
            'total_courts' => $courts->count(),
            'by_type' => $courts->groupBy(function ($court) {
                $type = strtolower($court->type);
                if (str_contains($type, 'supreme')) return 'Supreme Court';
                if (str_contains($type, 'appeal')) return 'Court of Appeal';
                if (str_contains($type, 'high court')) return 'High Court';
                if (str_contains($type, 'circuit')) return 'Circuit Court';
                if (str_contains($type, 'district')) return 'District Court';
                return $court->type;
            })->map->count()->filter(function ($value, $key) {
                return in_array($key, ['Supreme Court', 'Court of Appeal', 'High Court', 'Circuit Court', 'District Court']);
            }),
            'by_region' => $courts->groupBy('region.name')->map->count(),
            'active_courts' => $courts->where('is_active', true)->count(),
        ];

        return [
            'reportType' => 'courts',
            'courts' => $courts,
            'summary' => $summary
        ];
    }

    private function generateDtsReport(Request $request)
    {
        $query = Dts::with(['court.region']);
        $query = $this->applyFilters($query, $request);
        $dtsSystems = $query->get();
        
        $summary = [
            'total_dts' => $dtsSystems->count(),
            'available_dts' => $dtsSystems->where('is_available', true)->count(),
            'by_court' => $dtsSystems->groupBy('court.name')->map->count(),
            'complete_systems' => $dtsSystems->filter(function($dts) {
                return $dts->isComplete();
            })->count(),
        ];

        return [
            'reportType' => 'dts',
            'dtsSystems' => $dtsSystems,
            'summary' => $summary
        ];
    }

    private function generateOfficeReport(Request $request)
    {
        $query = Office::with(['region', 'court', 'manager', 'assets']);
        $query = $this->applyFilters($query, $request);
        $offices = $query->get();
        
        $summary = [
            'total_offices' => $offices->count(),
            'active_offices' => $offices->where('is_active', true)->count(),
            'by_region' => $offices->groupBy('region.name')->map->count(),
            'by_court' => $offices->groupBy('court.name')->map->count(),
        ];

        return [
            'reportType' => 'offices',
            'offices' => $offices,
            'summary' => $summary
        ];
    }

  private function generateSummaryReport(Request $request)
{
    $totalAssets = Asset::count();
    $totalUsers = User::count();
    $totalCourts = Court::count();
    $totalDts = Dts::count();
    $totalOffices = Office::count();
    
    $assetsByStatus = Asset::groupBy('status')->selectRaw('status, count(*) as count')->get()->pluck('count', 'status');
    $assetsByCondition = Asset::groupBy('condition')->selectRaw('`condition`, count(*) as count')->get()->pluck('count', 'condition');
    $usersByStatus = User::groupBy('status')->selectRaw('status, count(*) as count')->get()->pluck('count', 'status');
    
    return [
        'reportType' => 'summary',
        'totalAssets' => $totalAssets,
        'totalUsers' => $totalUsers,
        'totalCourts' => $totalCourts,
        'totalDts' => $totalDts,
        'totalOffices' => $totalOffices,
        'assetsByStatus' => $assetsByStatus,
        'assetsByCondition' => $assetsByCondition,
        'usersByStatus' => $usersByStatus,
    ];
}

 public function quickGenerate(Request $request)
{
    $request->validate([
        'report_type' => 'required|in:assets,users,courts,dts,summary,offices',
        'format' => 'nullable|in:html,pdf,excel,csv',
    ]);

    // Create a new request with the GET parameters
    $newRequest = new Request($request->all());
    
    // Generate report data
    $reportData = $this->generateReportData($newRequest);
    
    // If format is specified and not HTML, export directly
    if ($request->format && $request->format !== 'html') {
        return $this->export($newRequest);
    }
    
    return view('auditor.reports.results', array_merge($reportData, [
        'filters' => $request->all(),
        'filterData' => $this->getFilterData(),
        'generated_at' => now()
    ]));
}

// protected function applyFilters($query, Request $request, $filters = [])
// {
//     // Date filters
//     if ($request->start_date) {
//         $query->whereDate('created_at', '>=', $request->start_date);
//     }
//     if ($request->end_date) {
//         $query->whereDate('created_at', '<=', $request->end_date);
//     }

//     // Region filter
//     if ($request->region_id) {
//         $query->where('region_id', $request->region_id);
//     }

//     // Court type filter
//     if ($request->court_type) {
//         $query->where('type', $request->court_type);
//     }

//     // User type filter
//     if ($request->user_type) {
//         $query->where('access_type', $request->user_type);
//     }

//     // Asset status filter
//     if ($request->asset_status) {
//         $query->where('status', $request->asset_status);
//     }

//     // Category filter
//     if ($request->category_id) {
//         $query->where('category_id', $request->category_id);
//     }

//     // Apply custom filters passed as parameter
//     foreach ($filters as $filter => $column) {
//         if ($request->filled($filter)) {
//             $query->where($column, $request->$filter);
//         }
//     }

//     return $query;
// }
protected function applyFilters($query, Request $request, $filters = [])
{
    // Determine the model being queried
    $modelClass = get_class($query->getModel());
    
    // Search filter
    if ($request->search) {
        $searchTerm = '%' . $request->search . '%';
        if ($modelClass === 'App\Models\User') {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        } elseif ($modelClass === 'App\Models\Asset') {
            $query->where('asset_name', 'like', $searchTerm);
        } elseif ($modelClass === 'App\Models\Court' || $modelClass === 'App\Models\Office' || $modelClass === 'App\Models\Dts') {
            $query->where('name', 'like', $searchTerm);
        }
    }

    // Date filters - use appropriate date field based on model
    if ($request->start_date) {
        if ($modelClass === 'App\Models\Asset') {
            // For assets, use assigned_date
            $query->whereDate('assigned_date', '>=', $request->start_date);
        } elseif ($modelClass === 'App\Models\Dts') {
            // For DTS, use date_assigned
            $query->whereDate('date_assigned', '>=', $request->start_date);
        } else {
            // For other models (User, Court, Office), use created_at
            $query->whereDate('created_at', '>=', $request->start_date);
        }
    }
    
    if ($request->end_date) {
        if ($modelClass === 'App\Models\Asset') {
            // For assets, use assigned_date
            $query->whereDate('assigned_date', '<=', $request->end_date);
        } elseif ($modelClass === 'App\Models\Dts') {
            // For DTS, use date_assigned
            $query->whereDate('date_assigned', '<=', $request->end_date);
        } else {
            // For other models (User, Court, Office), use created_at
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    }

    // Year filter
    if ($request->year) {
        if ($modelClass === 'App\Models\Asset') {
            $query->whereYear('assigned_date', $request->year);
        } elseif ($modelClass === 'App\Models\Dts') {
            $query->whereYear('date_assigned', $request->year);
        } else {
            $query->whereYear('created_at', $request->year);
        }
    }

    // Region filter
    if (auth()->check() && auth()->user()->region_id && auth()->user()->role->name === 'auditor') {
        $query->where('region_id', auth()->user()->region_id);
    } elseif ($request->region_id) {
        $query->where('region_id', $request->region_id);
    }

    // Court type filter
    if ($request->court_type) {
        $query->where('type', $request->court_type);
    }

    // User type filter
    if ($request->user_type) {
        $query->where('access_type', $request->user_type);
    }

    // Court ID filter
    if ($request->court_id) {
        $query->where('court_id', $request->court_id);
    }

    // Office ID filter
    if ($request->office_id) {
        $query->where('office_id', $request->office_id);
    }

    // Assigned To filter
    if ($request->assigned_to) {
        $query->where('assigned_to', $request->assigned_to);
    }

    // Assigned Type filter
    if ($request->assigned_type) {
        $query->where('assigned_type', $request->assigned_type);
    }

    // Asset status filter (standardized name)
    if ($request->asset_status || $request->status) {
        $status = $request->asset_status ?: $request->status;
        $query->where('status', $status);
    }

    // Condition filter
    if ($request->condition) {
        $query->where('condition', $request->condition);
    }

    // Apply custom filters passed as parameter
    foreach ($filters as $filter => $column) {
        if ($request->filled($filter)) {
            $query->where($column, $request->$filter);
        }
    }

    return $query;
}
    protected function getFilterData()
    {
        return [
            'years' => range(now()->year, now()->year - 5),
            'regions' => Region::active()->get(),
            'courtTypes' => Court::distinct()->pluck('type')->toArray(),
            'userTypes' => User::distinct()->pluck('access_type')->toArray(),
            'categories' => Category::whereNull('parent_id')->get(),
        ];
    }
}