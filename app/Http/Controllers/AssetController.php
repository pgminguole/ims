<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Court;
use App\Models\Office;
use App\Models\Region;
use App\Models\Location;
use App\Models\User;
use App\Models\Accessory;
use App\Models\AssetHistory;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetsExport;
use App\Exports\AssetsFilteredExport;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AssetController extends Controller
{


public function importForm()
{
    return view('assets.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
    ]);

    try {
        $import = new \App\Imports\AssetsImport();
        Excel::import($import, $request->file('file'));
        
        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();
        
        if (count($errors) > 0) {
            return redirect()
                ->route('assets.index')
                ->with('success', "{$successCount} assets imported successfully.")
                ->with('errors', $errors);
        }
        
        return redirect()
            ->route('assets.index')
            ->with('success', "{$successCount} assets imported successfully.");
            
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $errors = [];
        foreach ($failures as $failure) {
            $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        return back()
            ->with('error', 'Validation errors occurred during import.')
            ->with('errors', $errors);
            
    } catch (\Exception $e) {
        return back()->with('error', 'Error importing assets: ' . $e->getMessage());
    }
}



public function changeAssignedDate(Request $request, $slug)
{
    Log::info('AssetController@changeAssignedDate called', [
        'slug' => $slug,
        'request_data' => $request->all(),
        'user_id' => auth()->id()
    ]);
    try {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        if ($asset->status !== 'assigned') {
            return back()->with('error', 'This asset is not currently assigned.');
        }
        $validated = $request->validate([
            'assigned_date' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);
        $oldDate = $asset->assigned_date?->format('M d, Y') ?? 'N/A';
        $newDate = Carbon::parse($validated['assigned_date'])->format('M d, Y');
        DB::transaction(function () use ($asset, $validated, $oldDate, $newDate) {
            $asset->update([
                'assigned_date' => $validated['assigned_date']
            ]);
        });
        return redirect()->route('assets.show', $asset)->with('success', 'Assigned date updated successfully.');

    } catch (\Exception $e) {
        Log::error('Error in changeAssignedDate method', [
            'error' => $e->getMessage(),
            'slug' => $slug
        ]);
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
public function downloadTemplate()
{
    $headers = [
        'asset_name',
        'asset_tag',
        'serial_number',
        'model',
        'brand',
        'manufacturer',
        'category',
        'subcategory',
        'region',
        'court',
        'location',
        'status',
        'condition',
        'purchase_date',
        'received_date',
        'assigned_date',
        'purchase_cost',
        'current_value',
        'supplier',
        'warranty_period',
        'warranty_expiry',
        'warranty_information',
        'ip_address',
        'mac_address',
        'specifications',
        'description',
        'assigned_to',
        'assigned_type',
        'depreciation_method',
        'maintenance_schedule',
        'last_maintenance',
        'next_maintenance',
        'maintenance_notes'
    ];
    
    $sampleData = [
        [
            'asset_name' => 'Dell Latitude 5420 Laptop',
            'asset_tag' => 'LAP-ACC-001',
            'serial_number' => 'DL12345678',
            'model' => 'Latitude 5420',
            'brand' => 'Dell',
            'manufacturer' => 'Dell Inc.',
            'category' => 'Computers',
            'subcategory' => 'Laptops',
            'region' => 'Greater Accra',
            'court' => 'HC-ACC-001',
            'location' => 'Accra',
            'status' => 'available',
            'condition' => 'excellent',
            'purchase_date' => '2024-01-15',
            'received_date' => '2024-01-20',
            'assigned_date' => '',
            'purchase_cost' => '3500.00',
            'current_value' => '3200.00',
            'supplier' => 'Tech Solutions Ghana',
            'warranty_period' => '3 years',
            'warranty_expiry' => '2027-01-15',
            'warranty_information' => 'Full parts and labor coverage',
            'ip_address' => '192.168.1.100',
            'mac_address' => '00:1B:44:11:3A:B7',
            'specifications' => 'Intel i5, 16GB RAM, 512GB SSD',
            'description' => 'High-performance laptop for judicial staff',
            'assigned_to' => '',
            'assigned_type' => '',
            'depreciation_method' => 'Straight Line',
            'maintenance_schedule' => 'Quarterly',
            'last_maintenance' => '',
            'next_maintenance' => '2024-06-15',
            'maintenance_notes' => 'Regular cleaning and updates required'
        ],
        [
            'asset_name' => 'HP LaserJet Pro M404dn',
            'asset_tag' => 'PRT-KSI-001',
            'serial_number' => 'HP987654321',
            'model' => 'LaserJet Pro M404dn',
            'brand' => 'HP',
            'manufacturer' => 'HP Inc.',
            'category' => 'Office Equipment',
            'subcategory' => 'Printers',
            'region' => 'Ashanti',
            'court' => 'Kumasi District Court',
            'location' => 'Kumasi',
            'status' => 'assigned',
            'condition' => 'good',
            'purchase_date' => '2024-03-20',
            'received_date' => '2024-03-25',
            'assigned_date' => '2024-04-01',
            'purchase_cost' => '1200.00',
            'current_value' => '1100.00',
            'supplier' => 'Office Supplies Ltd',
            'warranty_period' => '2 years',
            'warranty_expiry' => '2026-03-20',
            'warranty_information' => 'Parts only, no labor',
            'ip_address' => '192.168.2.50',
            'mac_address' => 'A4:5E:60:E8:9F:22',
            'specifications' => '40 ppm, Duplex, Network',
            'description' => 'Network printer for court registry',
            'assigned_to' => 'registry@court.gov.gh',
            'assigned_type' => 'staff',
            'depreciation_method' => 'Declining Balance',
            'maintenance_schedule' => 'Monthly',
            'last_maintenance' => '2024-05-01',
            'next_maintenance' => '2024-06-01',
            'maintenance_notes' => 'Replace toner when needed'
        ],
        [
            'asset_name' => 'Canon EOS R6 Camera',
            'asset_tag' => 'CAM-TAM-001',
            'serial_number' => 'CN456789012',
            'model' => 'EOS R6',
            'brand' => 'Canon',
            'manufacturer' => 'Canon Inc.',
            'category' => 'Electronics',
            'subcategory' => 'Cameras',
            'region' => 'Northern',
            'court' => 'Tamale High Court',
            'location' => 'Tamale',
            'status' => 'available',
            'condition' => 'excellent',
            'purchase_date' => '2024-02-10',
            'received_date' => '2024-02-15',
            'assigned_date' => '',
            'purchase_cost' => '8500.00',
            'current_value' => '8000.00',
            'supplier' => 'Camera World Ghana',
            'warranty_period' => '1 year',
            'warranty_expiry' => '2025-02-10',
            'warranty_information' => 'International warranty',
            'ip_address' => '',
            'mac_address' => '',
            'specifications' => '20MP, 4K Video, Weather Sealed',
            'description' => 'Professional camera for court documentation',
            'assigned_to' => '',
            'assigned_type' => '',
            'depreciation_method' => 'Straight Line',
            'maintenance_schedule' => 'Annual',
            'last_maintenance' => '',
            'next_maintenance' => '2025-02-10',
            'maintenance_notes' => 'Professional cleaning annually'
        ]
    ];
    
    return Excel::download(
        new \App\Exports\AssetsTemplateExport($headers, $sampleData), 
        'assets_import_template.xlsx'
    );
}

      public function showAssets(Request $request)
    {
        return $this->index($request);
    }

    public function createAsset()
    {
        return $this->create();
    }

    public function saveAsset(Request $request)
    {
        return $this->store($request);
    }

  public function export(Request $request)
    {
        $fileName = 'assets-export-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        
        return Excel::download(new AssetsFilteredExport($request->all()), $fileName);
    }

    /**
     * Export all assets to Excel
     */
    public function exportAll()
    {
        $fileName = 'all-assets-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        
        return Excel::download(new AssetsExport(), $fileName);
    }

    /**
     * Show export options modal or page
     */
    public function exportOptions()
    {
        return view('assets.export-options');
    }


    public function updateAsset(Request $request, $slug)
    {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        return $this->update($request, $asset);
    }

    public function index(Request $request)
    {
        $this->authorize('view_assets');

        $query = Asset::with(['category', 'subcategory', 'region', 'court', 'location', 'assignedUser', 'accessories'])
                      ->where('record_type', 'inventory');
        
        // Regional Scoping
        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            $query->where('region_id', auth()->user()->region_id);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Condition filter
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        // Region filter
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        
        // Court filter
        if ($request->filled('court_id')) {
     
            $query->where('court_id', $request->court_id);
        }
        
     if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }


    if ($request->filled('subcategory_id')) {
        $query->where('subcategory_id', $request->subcategory_id);
    }

        // Purchase year filter
        if ($request->filled('purchase_year')) {
            $query->whereYear('purchase_date', $request->purchase_year);
        }

        // Purchase date range filter
        if ($request->filled('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $request->purchase_date_from);
        }
        if ($request->filled('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $request->purchase_date_to);
        }

        // Received date range filter
        if ($request->filled('received_date_from')) {
            $query->whereDate('recieved_date', '>=', $request->received_date_from);
        }
        if ($request->filled('received_date_to')) {
            $query->whereDate('recieved_date', '<=', $request->received_date_to);
        }

        // Assigned date range filter
        if ($request->filled('assigned_date_from')) {
            $query->whereDate('assigned_date', '>=', $request->assigned_date_from);
        }
        if ($request->filled('assigned_date_to')) {
            $query->whereDate('assigned_date', '<=', $request->assigned_date_to);
        }

        // Warranty expiry filter
        if ($request->filled('warranty_status')) {
            if ($request->warranty_status === 'expired') {
                $query->where('warranty_expiry', '<', now());
            } elseif ($request->warranty_status === 'expiring_soon') {
                $query->whereBetween('warranty_expiry', [now(), now()->addMonths(3)]);
            } elseif ($request->warranty_status === 'valid') {
                $query->where('warranty_expiry', '>', now()->addMonths(3));
            }
        }

        // Maintenance due filter
        if ($request->filled('maintenance_status')) {
            if ($request->maintenance_status === 'due') {
                $query->where('next_maintenance', '<=', now());
            } elseif ($request->maintenance_status === 'upcoming') {
                $query->whereBetween('next_maintenance', [now(), now()->addDays(30)]);
            }
        }


           

                if ($request->filled('assigned_type')) {
                    $query->where('assigned_type', $request->assigned_type);
                }


        // Get summary statistics
        $statsQuery = clone $query;
        $totalAssets = (clone $statsQuery)->count();
        $availableCount = (clone $statsQuery)->where('status', 'available')->count();
        $assignedCount = (clone $statsQuery)->where('status', 'assigned')->count();
        $maintenanceCount = (clone $statsQuery)->where('status', 'maintenance')->count();

        // Paginate results
        $assets = $query->latest('created_at')->paginate(20)->withQueryString();
        
        // Get filter options
        $regions = Region::all();
        $courts = Court::all();
        $categories = Category::all();
       
        return view('assets.index', compact(
            'assets', 
            'regions', 
            'courts', 
            'categories',

            'totalAssets',
            'availableCount',
            'assignedCount',
            'maintenanceCount'
        ));
    }

    public function create()
    {
        $this->authorize('create_assets');

        $regions = Region::all();
        $courts = Court::all();
        
        // If regional admin, filter data
        // If regional admin, filter data
        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            $courts = $courts->where('region_id', auth()->user()->region_id);
            $regions = $regions->where('id', auth()->user()->region_id);
        }

        $locations=Location::all();
        $categories = Category::all();
        $subcategories = Category::whereNotNull('parent_id')->get();
        $users = User::active()->get();
        $offices = \App\Models\Office::all();
        $quantity = 1; 

        return view('assets.create', compact('regions', 'courts', 'categories', 'subcategories', 'users', 'quantity','locations', 'offices'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_assets');

        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_tag' => 'nullable|unique:assets',
            'serial_number' => 'nullable|string|max:255', 
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'court_id' => 'nullable|exists:courts,id',
            'location_id' => 'nullable|exists:locations,id',
            'purchase_cost' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'recieved_date' => 'nullable|date',
            'assigned_date' => 'nullable|date',
            'supplier' => 'nullable|string|max:255',
            'warranty_period' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'warranty_information' => 'nullable|string',
            'specifications' => 'nullable|string',
            'description' => 'nullable|string',
            'condition' => 'nullable|in:excellent,good,fair,poor,broken',
            'status' => 'nullable|in:available,assigned,maintenance,retired,lost,disposed',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string|max:255',
            'depreciation_method' => 'nullable|string|max:255',
            'maintenance_schedule' => 'nullable|string',
            'last_maintenance' => 'nullable|date',
            'next_maintenance' => 'nullable|date',
            'maintenance_notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'office_id' => 'nullable|exists:offices,id',
            'assigned_type' => 'nullable|in:user,judge,staff,office,court,region',
            'quantity' => 'nullable|integer|min:1',
            'record_type' => 'nullable|in:assignment,inventory',
        ]);

        // If user is a regional admin (RAO), force record_type to inventory
        if (auth()->user()->region_id && auth()->user()->hasRole('rao')) {
            $validated['record_type'] = 'inventory';
        } else {
            // For admins/super_admins, use provided or default to assignment
            $validated['record_type'] = $validated['record_type'] ?? 'assignment';
        }

        // Force region_id for regional admins
        if (auth()->user()->hasRole('rao')) {
            $validated['region_id'] = auth()->user()->region_id;
        }

        // Infer assigned_type and ensure consistency
        if ($request->filled('assigned_to')) {
            $validated['assigned_type'] = 'user';
            $validated['office_id'] = null;
        } elseif ($request->filled('office_id')) {
            $validated['assigned_type'] = 'office';
            $validated['assigned_to'] = null;
        } elseif ($request->filled('court_id') && ($validated['status'] ?? '') === 'assigned') {
            $validated['assigned_type'] = 'court';
            $validated['assigned_to'] = null;
            $validated['office_id'] = null;
        } elseif ($request->filled('region_id') && ($validated['status'] ?? '') === 'assigned') {
            // Note: Region is often set for metadata, but if status is 'assigned' 
            // and no other target is set, we treat it as assigned to the region.
            if (!$request->filled('assigned_to') && !$request->filled('office_id') && !$request->filled('court_id')) {
                $validated['assigned_type'] = 'region';
            }
        }

    DB::transaction(function () use ($validated) {
        $quantity = $validated['quantity'] ?? 1;
        
        // Generate unique asset tags if category is provided
        $tags = !empty($validated['category_id']) 
            ? Asset::generateNextTags($validated['category_id'], $quantity)
            : null;

        for ($i = 0; $i < $quantity; $i++) {
            $assetData = $validated;
            
            if ($tags) {
                $assetData['asset_tag'] = $tags[$i];
            } else {
                // Fallback for cases without category
                $tagBase = $validated['asset_tag'] ?? 'AST-' . strtoupper(\Illuminate\Support\Str::random(6));
                $assetData['asset_tag'] = ($i === 0) ? $tagBase : $tagBase . '-' . ($i + 1);
            }
            
            if (!empty($validated['serial_number'])) {
                $assetData['serial_number'] = $this->generateUniqueSerialNumber($validated['serial_number'], $i);
            } else {
                $assetData['serial_number'] = null;
            }
            
            // Generate slug and asset_id
            $assetData['slug'] = \Str::slug($assetData['asset_name'] . '-' . $assetData['asset_tag']);
            $assetData['asset_id'] = 'AST-' . strtoupper(uniqid());
            $assetData['registry_id'] = auth()->id();
            $assetData['created_by'] = auth()->id();

            $asset = Asset::create($assetData);

            // Log the creation
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'created',
                'description' => 'Asset created in the system',
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        }
    });

    
        
    return redirect()->route('assets.index')->with('success');
}

// Helper methods for generating unique identifiers
private function generateUniqueSerialNumber($baseSerial, $index)
{
    if ($index === 0) {
        return $baseSerial;
    }
    return $baseSerial . '-' . ($index + 1);
}

//   public function show(Asset $asset)
// {
//     $asset->load([
//         'category', 'subcategory', 'region', 'court', 'location',
//         'assignedUser', 'accessories', 'attachments', 'histories.performedBy',
//         'maintenanceLogs', 'registry', 'lastAuditedBy'
//     ]);

//     // Load active users for assignment dropdown
//     $users = User::active()->orderBy('first_name')->get();

//     return view('assets.show', compact('asset', 'users'));
// }

// public function show(Asset $asset)
// {
//     $asset->load([
//         'category', 'subcategory', 'region', 'court', 'location',
//         'assignedUser', 'office', 'accessories', 'attachments', 
//         'histories.performedBy', 'maintenanceLogs', 'registry', 'lastAuditedBy'
//     ]);

//     // Load active users and offices for assignment dropdown
//     $users = User::active()->orderBy('first_name')->get();
//     $offices = Office::active()->orderBy('name')->get();

//     return view('assets.show', compact('asset', 'users', 'offices'));
// }

    public function cleanup()
    {
        // Based on debug findings: 
        // We have assets marked as 'assigned' but missing 'assigned_to' (1830 records) or 'assigned_type' (142 records).
        
        $assets = Asset::where('status', 'assigned')
        ->where(function($q) {
            $q->whereNull('assigned_to')
              ->orWhereNull('assigned_type')
              ->orWhere('assigned_type', '')
              ->orWhere('assigned_type', 'NULL');
        })
        ->orderBy('id', 'desc')
        ->paginate(50);
        
        return view('assets.cleanup', compact('assets'));
    }

    public function show(Asset $asset)
    {
        $this->authorize('view_assets');

        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            if ($asset->region_id !== auth()->user()->region_id) {
                abort(403, 'You are not authorized to view assets outside your region.');
            }
        }
    $asset->load([
        'category', 'subcategory', 'region', 'court', 'location',
        'assignedUser', 'office', 'accessories', 'attachments', 
        'histories.performedBy', 'maintenanceLogs', 'registry', 'lastAuditedBy',
        'courtAssignment', 'officeAssignment'
    ]);

    // Load active users, offices, courts, and regions for assignment dropdown
    $users = User::active()->orderBy('name')->get();
    $offices = Office::active()->orderBy('name')->get();
    $courts = Court::orderBy('name')->get();
    $regions = Region::orderBy('name')->get();

    return view('assets.show', compact('asset', 'users', 'offices', 'courts', 'regions'));
}

    public function edit(Asset $asset)
    {
        $this->authorize('edit_assets');

        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            if ($asset->region_id !== auth()->user()->region_id) {
                abort(403, 'You are not authorized to edit assets outside your region.');
            }
        }
        $regions = Region::all();
        $courts = Court::all();
        $offices = Office::active()->orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->get();
        $subcategories = Category::whereNotNull('parent_id')->get();
        $users = User::active()->get();
        $locations = Location::all();
        return view('assets.edit', compact('asset', 'regions', 'courts', 'offices', 'categories', 'subcategories', 'users', 'locations'));
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorize('edit_assets');

        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            if ($asset->region_id !== auth()->user()->region_id) {
                abort(403, 'You are not authorized to update assets outside your region.');
            }
        }
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_tag' => 'nullable|unique:assets,asset_tag,' . $asset->id,
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'court_id' => 'nullable|exists:courts,id',
            'location_id' => 'nullable|exists:locations,id',
            'office_id' => 'nullable|exists:offices,id',
            'purchase_cost' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'recieved_date' => 'nullable|date',
            'assigned_date' => 'nullable|date',
            'supplier' => 'nullable|string|max:255',
            'warranty_period' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'warranty_information' => 'nullable|string',
            'specifications' => 'nullable|string',
            'description' => 'nullable|string',
            'comments' => 'nullable|string',
            'condition' => 'required|in:excellent,good,fair,poor,broken',
            'status' => 'required|in:available,assigned,maintenance,retired,lost,disposed',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string|max:255',
            'depreciation_method' => 'nullable|string|max:255',
            'maintenance_schedule' => 'nullable|string',
            'last_maintenance' => 'nullable|date',
            'next_maintenance' => 'nullable|date',
            'maintenance_notes' => 'nullable|string',
            'assigned_to' => 'nullable', // Flexible as it could be user_id, court_id, etc.
            'assigned_type' => 'nullable|string',
            'returned_date' => 'nullable|date',
            'returned_reason' => 'nullable|string',
            'returnee' => 'nullable|string|max:255',
            'returned_to' => 'nullable|string|max:255',
            'record_type' => 'nullable|in:assignment,inventory',
        ]);

        if (auth()->user()->region_id && auth()->user()->hasRole('rao')) {
            $validated['record_type'] = 'inventory';
        } elseif (!isset($validated['record_type'])) {
            // Keep existing record_type if not provided
            $validated['record_type'] = $asset->record_type;
        }

        if ($request->filled('assigned_to')) {
            $validated['status'] = 'assigned';
            
            // Logic to determine assigned_type if not explicitly sent or to ensure consistency
            if ($request->has('court_id') && $request->assigned_to == $request->court_id) {
                $validated['assigned_type'] = 'court';
            } elseif ($request->has('office_id') && $request->assigned_to == $request->office_id) {
                $validated['assigned_type'] = 'office';
            } elseif ($request->has('region_id') && $request->assigned_to == $request->region_id) {
                $validated['assigned_type'] = 'region';
            } elseif (!$request->has('assigned_type')) {
                // Default to user if no type provided and no ID match found
                $validated['assigned_type'] = 'user';
            }
        }

        DB::transaction(function () use ($asset, $validated) {
            $oldValues = $asset->toArray();
            $asset->update($validated);
            $newValues = $asset->fresh()->toArray();

            // Log significant changes
            $changes = array_diff_assoc($newValues, $oldValues);
            if (!empty($changes)) {
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'updated',
                    'description' => 'Asset details updated',
                    'old_values' => json_encode($oldValues),
                    'new_values' => json_encode($newValues),
                    'performed_by' => auth()->id(),
                    'performed_at' => now()
                ]);
            }
        });

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated successfully.');
    }

    // ... rest of your existing methods remain the same
    public function destroy(Asset $asset)
    {
        $this->authorize('delete_assets');

        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            if ($asset->region_id !== auth()->user()->region_id) {
                abort(403, 'You are not authorized to delete assets outside your region.');
            }
        }
        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'deleted',
            'description' => 'Asset deleted from the system',
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        $asset->delete();
        
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

public function bulkDelete(Request $request)
{
    $this->authorize('delete_assets');

    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:assets,id'
    ]);

    try {
        DB::transaction(function () use ($request) {
            $assets = Asset::whereIn('id', $request->ids)->get();
            
            foreach ($assets as $asset) {
                // Log the deletion
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'deleted',
                    'description' => 'Asset deleted from the system via bulk delete',
                    'performed_by' => auth()->id(),
                    'performed_at' => now()
                ]);

                $asset->delete();
            }
        });

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' assets deleted successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting assets: ' . $e->getMessage()
        ], 500);
    }
}
//   public function assign(Request $request, $slug)
// {
//     $asset = Asset::where('slug', $slug)->firstOrFail();
    
//     $validated = $request->validate([
//         'assigned_to' => 'required|exists:users,id',
//         'assigned_type' => 'required|in:judge,staff,department,court',
//         'assigned_date' => 'required|date',
//         'comments' => 'nullable|string'
//     ]);

//     $user = User::find($validated['assigned_to']);
//     $previousUser = $asset->assignedUser;

//     DB::transaction(function () use ($asset, $validated, $user, $previousUser) {
//         $asset->update([
//             'assigned_to' => $validated['assigned_to'],
//             'assigned_type' => $validated['assigned_type'],
//             'assigned_date' => $validated['assigned_date'],
//             'status' => 'assigned'
//         ]);

//         $action = $previousUser ? 'reassigned' : 'assigned';
//         $description = $previousUser 
//             ? "Asset reassigned from {$previousUser->full_name} to {$validated['assigned_type']}: {$user->full_name}"
//             : "Asset assigned to {$validated['assigned_type']}: {$user->full_name}";

//         if (!empty($validated['comments'])) {
//             $description .= ". Comments: {$validated['comments']}";
//         }

//         AssetHistory::create([
//             'asset_id' => $asset->id,
//             'action' => $action,
//             'description' => $description,
//             'performed_by' => auth()->id(),
//             'performed_at' => now()
//         ]);
//     });

//     $message = $previousUser ? 'Asset reassigned successfully.' : 'Asset assigned successfully.';
//     return redirect()->route('assets.show', $asset)->with('success', $message);
// }


    // ... (keep your edit, update, destroy, bulkDelete methods as they are)

public function assign(Request $request, $slug)
{
    $this->authorize('assign_assets');

    Log::info('AssetController@assign called', [
        'slug' => $slug,
        'request_data' => $request->all(),
        'user_id' => auth()->id()
    ]);

    try {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        
        if (auth()->user()->region_id && !auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            if ($asset->region_id !== auth()->user()->region_id) {
                abort(403, 'You are not authorized to assign assets outside your region.');
            }
        }
        
        Log::debug('Asset found for assignment', [
            'asset_id' => $asset->id,
            'current_status' => $asset->status,
            'current_assigned_type' => $asset->assigned_type,
            'current_assigned_to' => $asset->assigned_to,
            'current_office_id' => $asset->office_id
        ]);

        // Validate the assignment type first
        $request->validate([
            'assigned_type' => 'required|in:user,office,court,region',
            'assigned_date' => 'required|date',
            'comments' => 'nullable|string',
        ]);

        $assignedType = $request->input('assigned_type');
        
        // Conditional validation based on assignment type
        if ($assignedType === 'user') {
            $request->validate([
                'assigned_to' => 'required|exists:users,id'
            ]);
            $assignedToId = $request->input('assigned_to');
            $entity = User::findOrFail($assignedToId);
            $entityName = $entity->full_name;
            
        } elseif ($assignedType === 'office') {
            $request->validate([
                'assigned_to' => 'required|exists:offices,id'
            ]);
            $assignedToId = $request->input('assigned_to');
            $entity = Office::findOrFail($assignedToId);
            $entityName = $entity->name;
        } elseif ($assignedType === 'court') {
            $request->validate([
                'assigned_to' => 'required|exists:courts,id'
            ]);
            $assignedToId = $request->input('assigned_to');
            $entity = Court::findOrFail($assignedToId);
            $entityName = $entity->name;
        } elseif ($assignedType === 'region') {
            $request->validate([
                'assigned_to' => 'required|exists:regions,id'
            ]);
            $assignedToId = $request->input('assigned_to');
            $entity = Region::findOrFail($assignedToId);
            $entityName = $entity->name;
        }

        Log::debug('Assignment validation passed', [
            'assigned_type' => $assignedType,
            'assigned_to_id' => $assignedToId,
            'entity_name' => $entityName
        ]);

        $previousEntity = $asset->assignedEntity;
        $previousEntityName = $previousEntity ? 
            ($asset->assigned_type === 'user' ? $previousEntity->full_name : $previousEntity->name) : 
            'Unassigned';

        DB::transaction(function () use ($asset, $request, $assignedType, $assignedToId, $entityName, $previousEntity, $previousEntityName) {
            $updateData = [
                'assigned_type' => $assignedType,
                'assigned_date' => $request->input('assigned_date'),
                'status' => 'assigned'
            ];

            // Reset assignment keys and set the correct one
            $updateData['assigned_to'] = null;
            $updateData['office_id'] = null;
            // We keep region_id and court_id if they are NOT the target of assignment, 
            // but if they ARE the target, we set them specifically.
            // Actually, if assigned to a user/office, it should still belong to the court/region it was in.
            
            if ($assignedType === 'user') {
                $updateData['assigned_to'] = $assignedToId;
                $updateData['office_id'] = null;
            } elseif ($assignedType === 'office') {
                $updateData['office_id'] = $assignedToId;
                $updateData['assigned_to'] = null;
            } elseif ($assignedType === 'court') {
                $updateData['court_id'] = $assignedToId;
                $updateData['assigned_to'] = null;
                $updateData['office_id'] = null;
            } elseif ($assignedType === 'region') {
                $updateData['region_id'] = $assignedToId;
                $updateData['assigned_to'] = null;
                $updateData['office_id'] = null;
                $updateData['court_id'] = null;
            }

            Log::debug('Updating asset with data', $updateData);
            $asset->update($updateData);

            $action = $previousEntity ? 'reassigned' : 'assigned';
            $description = $previousEntity 
                ? "Asset reassigned from {$previousEntityName} to {$assignedType}: {$entityName}"
                : "Asset assigned to {$assignedType}: {$entityName}";

            if ($request->filled('comments')) {
                $description .= ". Comments: {$request->input('comments')}";
            }

            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => $action,
                'description' => $description,
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            Log::info('Asset assignment completed successfully', [
                'asset_id' => $asset->id,
                'action' => $action,
                'new_assignment' => $entityName
            ]);
        });

        $message = $previousEntity ? 'Asset reassigned successfully.' : 'Asset assigned successfully.';
        return redirect()->route('assets.show', $asset)->with('success', $message);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed in assign method', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);
        return back()->withErrors($e->errors())->withInput();
        
    } catch (\Exception $e) {
        Log::error('Error in assign method', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'slug' => $slug,
            'request_data' => $request->all()
        ]);
        return back()->with('error', 'An error occurred while assigning the asset: ' . $e->getMessage());
    }
}

public function returnAsset(Request $request, Asset $asset)
{
    Log::info($request->all());

    try {
        $validated = $request->validate([
            'returned_date' => 'required|date',
            'returned_reason' => 'required|string',
            'returnee' => 'required|string|max:255',
            'returned_to' => 'nullable|string|max:255',
            'condition' => 'required|in:excellent,good,fair,poor,broken',
        ]);

        Log::debug('Return validation passed', ['validated_data' => $validated]);

        DB::transaction(function () use ($asset, $validated) {
            $previousEntity = $asset->assignedEntity;
            $previousEntityName = $previousEntity ? 
                ($asset->assigned_type === 'user' ? $previousEntity->full_name : $previousEntity->name) : 
                'Unknown';

            Log::debug('Previous assignment for return', [
                'previous_entity' => $previousEntityName,
                'previous_type' => $asset->assigned_type
            ]);

            $updateData = [
                'returned_date' => $validated['returned_date'],
                'returned_reason' => $validated['returned_reason'],
                'returnee' => $validated['returnee'],
                'returned_to' => $validated['returned_to'],
                'condition' => $validated['condition'],
                'status' => 'available',
                'assigned_to' => null,
                'office_id' => null,
                'assigned_type' => null,
                'assigned_date' => null,
            ];

            Log::debug('Updating asset for return', $updateData);
            $asset->update($updateData);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'returned',
                'description' => "Asset returned from {$previousEntityName}. Reason: {$validated['returned_reason']}. Returned by: {$validated['returnee']}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            Log::info('Asset return completed successfully', [
                'asset_id' => $asset->id,
                'returned_by' => $validated['returnee']
            ]);
        });

        return redirect()->route('assets.show', $asset)->with('success', 'Asset returned successfully.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed in returnAsset method', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);
        return back()->withErrors($e->errors())->withInput();
        
    } catch (\Exception $e) {
        Log::error('Error in returnAsset method', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'asset_id' => $asset->id,
            'request_data' => $request->all()
        ]);
        return back()->with('error', 'An error occurred while returning the asset: ' . $e->getMessage());
    }
}

    // Update the assigned method to handle both user and office assignments
    public function assigned(Request $request)
    {
        $query = Asset::where('status', 'assigned')
            ->where(function($q) {
                $q->whereNotNull('assigned_to')
                  ->orWhereNotNull('office_id');
            })
            ->with(['category', 'region', 'court', 'assignedUser', 'office', 'accessories']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhereHas('assignedUser', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                     
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('office', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
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

        if ($request->filled('assigned_type')) {
            $query->where('assigned_type', $request->assigned_type);
        }

        if ($request->filled('assigned_date_from')) {
            $query->whereDate('assigned_date', '>=', $request->assigned_date_from);
        }

        if ($request->filled('assigned_date_to')) {
            $query->whereDate('assigned_date', '<=', $request->assigned_date_to);
        }

        // Get summary statistics by category and assignment type
        $categorySummary = Asset::where('status', 'assigned')
            ->where(function($q) {
                $q->whereNotNull('assigned_to')
                  ->orWhereNotNull('office_id');
            })
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function($items, $category) {
                return [
                    'count' => $items->count(),
                    'category' => $category
                ];
            })
            ->sortByDesc('count');

        $assignmentTypeSummary = Asset::where('status', 'assigned')
            ->where(function($q) {
                $q->whereNotNull('assigned_to')
                  ->orWhereNotNull('office_id');
            })
            ->select('assigned_type', DB::raw('count(*) as count'))
            ->groupBy('assigned_type')
            ->get()
            ->pluck('count', 'assigned_type');

        $totalAssigned = Asset::where('status', 'assigned')
            ->where(function($q) {
                $q->whereNotNull('assigned_to')
                  ->orWhereNotNull('office_id');
            })
            ->count();

        $assets = $query->latest('assigned_date')->paginate(20)->withQueryString();
        $regions = Region::all();
        $courts = Court::all();
        $categories = Category::whereNull('parent_id')->get();

        return view('assets.assigned', compact(
            'assets', 
            'regions', 
            'courts', 
            'categories',
            'categorySummary',
            'assignmentTypeSummary',
            'totalAssigned'
        ));
    }

    // Update the available method
    public function available(Request $request)
    {
        
           $users = User::active()->orderBy('name')->get();
    $offices = Office::active()->orderBy('name')->get();
        $query = Asset::available()->with(['category', 'region', 'court', 'accessories']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Get summary statistics by category
        $categorySummary = Asset::available()
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function($items, $category) {
                return [
                    'count' => $items->count(),
                    'category' => $category
                ];
            })
            ->sortByDesc('count');

        $totalAvailable = Asset::available()->count();
        $assets = $query->latest()->paginate(20)->withQueryString();
        $regions = Region::all();
        $courts = Court::all();
        $categories = Category::whereNull('parent_id')->get();

        return view('assets.available', compact(
            'assets', 
            'regions', 
            'courts', 
            'categories',
            'categorySummary',
            'totalAvailable',
            'users',
            'offices'
        ));
    }
    // Debug method to check current asset state
    public function debugAsset($slug)
    {
        try {
            $asset = Asset::where('slug', $slug)->firstOrFail();
            
            $debugInfo = [
                'asset_id' => $asset->id,
                'slug' => $asset->slug,
                'status' => $asset->status,
                'assigned_type' => $asset->assigned_type,
                'assigned_to' => $asset->assigned_to,
                'office_id' => $asset->office_id,
                'assigned_date' => $asset->assigned_date,
                'assigned_user' => $asset->assignedUser ? $asset->assignedUser->only(['id', 'full_name', 'email']) : null,
                'assigned_office' => $asset->office ? $asset->office->only(['id', 'name', 'code']) : null,
                'assigned_entity_name' => $asset->assigned_entity_name,
                'created_at' => $asset->created_at,
                'updated_at' => $asset->updated_at,
            ];

            Log::debug('Asset debug information', $debugInfo);

            return response()->json([
                'success' => true,
                'debug_info' => $debugInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Error in debugAsset method', [
                'error' => $e->getMessage(),
                'slug' => $slug
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }



// public function assigned(Request $request)
// {
//     $query = Asset::where('status', 'assigned')
//               ->whereNotNull('assigned_to')
//               ->with(['category', 'region', 'court', 'assignedUser', 'accessories']);

//     // Apply same filters as index
//     if ($request->filled('search')) {
//         $search = $request->search;
//         $query->where(function($q) use ($search) {
//             $q->where('asset_name', 'like', "%{$search}%")
//               ->orWhere('serial_number', 'like', "%{$search}%")
//               ->orWhere('asset_tag', 'like', "%{$search}%");
//         });
//     }

//     if ($request->filled('region_id')) {
//         $query->where('region_id', $request->region_id);
//     }

//     if ($request->filled('court_id')) {
//         $query->where('court_id', $request->court_id);
//     }

//     if ($request->filled('category_id')) {
//         $query->where('category_id', $request->category_id);
//     }

//     if ($request->filled('assigned_date_from')) {
//         $query->whereDate('assigned_date', '>=', $request->assigned_date_from);
//     }

//     if ($request->filled('assigned_date_to')) {
//         $query->whereDate('assigned_date', '<=', $request->assigned_date_to);
//     }

//     // Get summary statistics by category
//     $categorySummary = Asset::where('status', 'assigned')
//         ->whereNotNull('assigned_to')
//         ->with('category')
//         ->get()
//         ->groupBy('category.name')
//         ->map(function($items, $category) {
//             return [
//                 'count' => $items->count(),
//                 'category' => $category
//             ];
//         })
//         ->sortByDesc('count');

//     $totalAssigned = Asset::where('status', 'assigned')->whereNotNull('assigned_to')->count();
//     $assets = $query->latest('assigned_date')->paginate(20)->withQueryString();
//     $regions = Region::all();
//     $courts = Court::all();
//     $categories = Category::whereNull('parent_id')->get();

//     return view('assets.assigned', compact(
//         'assets', 
//         'regions', 
//         'courts', 
//         'categories',
//         'categorySummary',
//         'totalAssigned'
//     ));
// }

// public function available(Request $request)
// {
//     $query = Asset::available()->with(['category', 'region', 'court', 'accessories']);
    
//     // Apply filters
//     if ($request->filled('search')) {
//         $search = $request->search;
//         $query->where(function($q) use ($search) {
//             $q->where('asset_name', 'like', "%{$search}%")
//               ->orWhere('serial_number', 'like', "%{$search}%")
//               ->orWhere('asset_tag', 'like', "%{$search}%");
//         });
//     }

//     if ($request->filled('region_id')) {
//         $query->where('region_id', $request->region_id);
//     }

//     if ($request->filled('court_id')) {
//         $query->where('court_id', $request->court_id);
//     }

//     if ($request->filled('condition')) {
//         $query->where('condition', $request->condition);
//     }

//     if ($request->filled('category_id')) {
//         $query->where('category_id', $request->category_id);
//     }

//     // Get summary statistics by category
//     $categorySummary = Asset::available()
//         ->with('category')
//         ->get()
//         ->groupBy('category.name')
//         ->map(function($items, $category) {
//             return [
//                 'count' => $items->count(),
//                 'category' => $category
//             ];
//         })
//         ->sortByDesc('count');

//     $totalAvailable = Asset::available()->count();
//     $assets = $query->latest()->paginate(20)->withQueryString();
//     $regions = Region::all();
//     $courts = Court::all();
//     $categories = Category::whereNull('parent_id')->get();

//     return view('assets.available', compact(
//         'assets', 
//         'regions', 
//         'courts', 
//         'categories',
//         'categorySummary',
//         'totalAvailable'
//     ));
// }

//  public function returnAsset(Request $request, Asset $asset)
// {
//     $validated = $request->validate([
//         'returned_date' => 'required|date',
//         'returned_reason' => 'required|string',
//         'returnee' => 'required|string|max:255',
//         'returned_to' => 'nullable|string|max:255',
//         'condition' => 'required|in:excellent,good,fair,poor,broken',
//     ]);

//     DB::transaction(function () use ($asset, $validated) {
//         $previousEntity = $asset->assignedEntity;
//         $previousEntityName = $previousEntity ? 
//             ($asset->assigned_type === 'user' ? $previousEntity->full_name : $previousEntity->name) : 
//             'Unknown';

//         $asset->update([
//             'returned_date' => $validated['returned_date'],
//             'returned_reason' => $validated['returned_reason'],
//             'returnee' => $validated['returnee'],
//             'returned_to' => $validated['returned_to'],
//             'condition' => $validated['condition'],
//             'status' => 'available',
//             'assigned_to' => null,
//             'office_id' => null,
//             'assigned_type' => null,
//         ]);

//         AssetHistory::create([
//             'asset_id' => $asset->id,
//             'action' => 'returned',
//             'description' => "Asset returned from {$previousEntityName}. Reason: {$validated['returned_reason']}",
//             'performed_by' => auth()->id(),
//             'performed_at' => now()
//         ]);
//     });

//     return back()->with('success', 'Asset returned successfully.');
// }

    // public function returnAsset(Request $request, Asset $asset)
    // {
    //     $validated = $request->validate([
    //         'returned_date' => 'required|date',
    //         'returned_reason' => 'required|string',
    //         'returnee' => 'required|string|max:255',
    //         'returned_to' => 'nullable|string|max:255',
    //         'condition' => 'required|in:excellent,good,fair,poor,broken',
    //     ]);

    //     DB::transaction(function () use ($asset, $validated) {
    //         $asset->update([
    //             'returned_date' => $validated['returned_date'],
    //             'returned_reason' => $validated['returned_reason'],
    //             'returnee' => $validated['returnee'],
    //             'returned_to' => $validated['returned_to'],
    //             'condition' => $validated['condition'],
    //             'status' => 'available',
    //             'assigned_to' => null,
    //             'assigned_type' => null,
    //         ]);

    //         AssetHistory::create([
    //             'asset_id' => $asset->id,
    //             'action' => 'returned',
    //             'description' => "Asset returned. Reason: {$validated['returned_reason']}",
    //             'performed_by' => auth()->id(),
    //             'performed_at' => now()
    //         ]);
    //     });

    //     return back()->with('success', 'Asset returned successfully.');
    // }

    public function audit(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'audit_notes' => 'required|string',
            'condition' => 'required|in:excellent,good,fair,poor,broken',
            'status' => 'required|in:available,assigned,maintenance,retired,lost,disposed'
        ]);

        $asset->markAsAudited(auth()->id(), $validated['audit_notes']);
        $asset->update([
            'condition' => $validated['condition'],
            'status' => $validated['status']
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'audited',
            'description' => 'Asset audit completed',
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        return back()->with('success', 'Asset audit completed successfully.');
    }

    // Accessory Management
    public function addAccessory(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'condition' => 'required|in:excellent,good,fair,poor,broken',
            'notes' => 'nullable|string'
        ]);

        $accessory = $asset->accessories()->create($validated);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'accessory_added',
            'description' => "Accessory added: {$validated['name']}",
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        return back()->with('success', 'Accessory added successfully.');
    }

    public function removeAccessory(Asset $asset, Accessory $accessory)
    {
        $accessory->delete();

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'accessory_removed',
            'description' => "Accessory removed: {$accessory->name}",
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        return back()->with('success', 'Accessory removed successfully.');
    }

    // File attachments
    public function assetFilesPage($slug)
    {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        return view('assets.attachments', compact('asset'));
    }

    public function saveAssetFiles(Request $request, $slug)
    {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'attachments.*' => 'required|file|max:10240',
            'descriptions' => 'nullable|array'
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $key => $file) {
                $filename = $file->store('attachments/' . $asset->id, 'public');
                
                Attachment::create([
                    'asset_id' => $asset->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $filename,
                    'description' => $request->descriptions[$key] ?? null,
                    'uploaded_by' => auth()->id()
                ]);
            }

            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'files_uploaded',
                'description' => 'New files uploaded to asset',
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        }

        return back()->with('success', 'Files uploaded successfully.');
    }

    public function removeAttachment(Asset $asset, Attachment $attachment)
    {
        \Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'file_removed',
            'description' => "File removed: {$attachment->original_name}",
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        return back()->with('success', 'File removed successfully.');
    }
}