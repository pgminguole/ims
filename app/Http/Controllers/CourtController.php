<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Region;
use App\Models\Location;
use App\Models\User;
use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetHistory;
use App\Models\Dts;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CourtsImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class CourtController extends Controller
{



public function createAsset(Request $request, Court $court)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'brand' => 'nullable|string|max:255',
        'model' => 'nullable|string|max:255',
        'quantity' => 'required|integer|min:1|max:50',
        'assigned_date' => 'required|date',
        'condition' => 'required|in:excellent,good,fair,poor',
        'warranty_months' => 'nullable|integer|min:0',
        'purchase_date' => 'nullable|date',
        'purchase_price' => 'nullable|numeric|min:0',
        'comments' => 'nullable|string',
        'assigned_to' => 'nullable|exists:users,id',
        'record_type' => 'nullable|in:assignment,inventory'
    ]);

    $currentUser = auth()->user();
    $isRegionalAdmin = $currentUser->region_id && $currentUser->hasRole('rao');

    $createdCount = 0;
    
    // DB::transaction(function () use ($validated, $court, &$createdCount) {
    //     $category = Category::find($validated['category_id']);
        
    //     // Get the starting number for asset tags ONCE before the loop
    //     $latestAsset = Asset::where('asset_tag', 'like', $category->code . '-%')
    //         ->orderBy('asset_tag', 'desc')
    //         ->first();

    //     if ($latestAsset) {
    //         $currentNumber = intval(str_replace($category->code . '-', '', $latestAsset->asset_tag));
    //     } else {
    //         $currentNumber = 0;
    //     }
        
    //     for ($i = 0; $i < $validated['quantity']; $i++) {
    //         // Increment for each new asset
    //         $currentNumber++;
            
    //         // Generate asset name from category or model/brand
    //         if (!empty($validated['model'])) {
    //             $assetName = $validated['model'];
    //         } elseif (!empty($validated['brand'])) {
    //             $assetName = $validated['brand'] . ' ' . $category->name;
    //         } else {
    //             $assetName = $category->name;
    //         }
            
    //         $assetData = [
    //             'asset_name' => $assetName,
    //             'slug' => $this->generateAssetSlug($assetName . '-' . $currentNumber),
    //             'asset_tag' => $category->code . '-' . str_pad($currentNumber, 6, '0', STR_PAD_LEFT),
    //             'serial_number' => $this->generateSerialNumber(),
    //             'category_id' => $validated['category_id'],
    //             'brand' => $validated['brand'] ?? null,
    //             'asset_id' => 'AST-' . strtoupper(uniqid()),
    //             'model' => $validated['model'] ?? null,
    //             'assigned_date' => $validated['assigned_date'],
    //             'condition' => $validated['condition'],
    //             'purchase_date' => $validated['purchase_date'] ?? null,
    //             'status' => 'assigned',
    //             'court_id' => $court->id,
    //             'assigned_to' => $validated['assigned_to'] ?? null,
    //             'assigned_type' => !empty($validated['assigned_to']) ? 'user' : 'court',
    //             'comments' => $validated['comments'] ?? null,
    //             'created_by' => auth()->id()
    //         ];

    //         $asset = Asset::create($assetData);

    //         // Create description for history
    //         $deviceDescription = $validated['brand'] && $validated['model'] 
    //             ? "{$validated['brand']} {$validated['model']}" 
    //             : $category->name;

    //         // Log assignment history
    //         $assigneeName = !empty($validated['assigned_to']) 
    //             ? "user: " . User::find($validated['assigned_to'])->name 
    //             : "court: {$court->name}";

    //         AssetHistory::create([
    //             'asset_id' => $asset->id,
    //             'action' => 'assigned',
    //             'description' => "New {$deviceDescription} created and assigned to {$assigneeName}. Comments: {$validated['comments']}",
    //             'performed_by' => auth()->id(),
    //             'performed_at' => now()
    //         ]);

    //         $createdCount++;
    //     }
    // });

    DB::transaction(function () use ($validated, $court, &$createdCount) {
    $category = Category::find($validated['category_id']);

    $tags = Asset::generateNextTags($validated['category_id'], $validated['quantity']);

    for ($i = 0; $i < $validated['quantity']; $i++) {
        $assetTag = $tags[$i];

        $assetName = !empty($validated['model'])
            ? $validated['model']
            : (!empty($validated['brand'])
                ? $validated['brand'] . ' ' . $category->name
                : $category->name);

        $asset = Asset::create([
            'asset_name'    => $assetName,
            'slug'          => $this->generateAssetSlug($assetName . '-' . $assetTag),
            'asset_tag'     => $assetTag,
            'serial_number' => $this->generateSerialNumber(),
            'category_id'   => $validated['category_id'],
            'brand'         => $validated['brand'] ?? null,
            'asset_id'      => 'AST-' . strtoupper(uniqid()),
            'model'         => $validated['model'] ?? null,
            'assigned_date' => $validated['assigned_date'],
            'condition'     => $validated['condition'],
            'purchase_date' => $validated['purchase_date'] ?? null,
            'status'        => 'assigned',
            'court_id'      => $court->id,
            'assigned_to'   => $validated['assigned_to'] ?? null,
            'assigned_type' => !empty($validated['assigned_to']) ? 'user' : 'court',
            'comments'      => $validated['comments'] ?? null,
            'created_by'    => auth()->id(),
            'region_id'     => $isRegionalAdmin ? $currentUser->region_id : $court->region_id,
            'record_type'   => $isRegionalAdmin ? 'inventory' : ($validated['record_type'] ?? 'assignment'),
        ]);

        $deviceDescription = $validated['brand'] && $validated['model']
            ? "{$validated['brand']} {$validated['model']}"
            : $category->name;

        $assigneeName = !empty($validated['assigned_to'])
            ? 'user: ' . User::find($validated['assigned_to'])->name
            : "court: {$court->name}";

        AssetHistory::create([
            'asset_id'     => $asset->id,
            'action'       => 'assigned',
            'description'  => "New {$deviceDescription} created and assigned to {$assigneeName}. Comments: {$validated['comments']}",
            'performed_by' => auth()->id(),
            'performed_at' => now(),
        ]);

        $createdCount++;
    }
});

    return redirect()->back()->with('success', "Successfully created and assigned {$createdCount} asset(s) to {$court->name}.");
}

/**
 * Generate a unique slug for the asset
 */
private function generateAssetSlug($assetName)
{
    $baseSlug = Str::slug($assetName);
    $slug = $baseSlug;
    $counter = 1;

    while (Asset::where('slug', $slug)->exists()) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

/**
 * Generate a unique serial number
 */
private function generateSerialNumber()
{
    do {
        $serial = 'SN-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(3));
    } while (Asset::where('serial_number', $serial)->exists());

    return $serial;
}


// In AssetController.php - modify the changeAssignedDate method
public function changeAssetDate(Request $request)
{
    $request->validate([
        'asset_id' => 'required|exists:assets,id',
        'assigned_date' => 'required|date',
        'reason' => 'nullable|string|max:500'
    ]);

    try {
        $asset = Asset::findOrFail($request->asset_id);
        $oldDate = $asset->assigned_date;
        
        $asset->assigned_date = $request->assigned_date;
        $asset->save();

        // Optional: Log the change if you have an audit log
        // AuditLog::create([
        //     'user_id' => auth()->id(),
        //     'action' => 'changed_asset_date',
        //     'description' => "Changed assigned date from {$oldDate} to {$request->assigned_date}",
        //     'reason' => $request->reason
        // ]);

        return redirect()->back()->with('success', 'Asset assigned date updated successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update asset date: ' . $e->getMessage());
    }
}

public function changeDtsDate(Request $request)
{
    $request->validate([
        'dts_id' => 'required|exists:dts,id',
        'date_assigned' => 'required|date'
    ]);

    try {
        $dts = Dts::findOrFail($request->dts_id);
        $oldDate = $dts->date_assigned;
        
        $dts->date_assigned = $request->date_assigned;
        $dts->save();

        return redirect()->back()->with('success', 'DTS assigned date updated successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update DTS date: ' . $e->getMessage());
    }
}




    public function duplicates()
    {
        // Find courts with same name but different IDs
        $duplicates = Court::select('name', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();
            
        return view('courts.duplicates', compact('duplicates'));
    }

    public function index(Request $request)
    {
        $this->authorize('view_courts');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $regionsQuery = Region::query();
        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
        }
        $regions = $regionsQuery->get();
    
    \DB::enableQueryLog();
    Log::info('=== NEW REQUEST ===');
    Log::info('Request region_id: ' . $request->region_id);
    Log::info('Request filled check: ' . ($request->filled('region_id') ? 'true' : 'false'));
    
    // Use withDeviceCounts() scope to load all the device counts
    $query = Court::with(['region', 'location']);

    if ($isRegionalAdmin) {
        $query->where('region_id', $user->region_id);
    }
    
    Log::info('Base table: ' . $query->getModel()->getTable());
    Log::info('Deleted at column: ' . $query->getModel()->getDeletedAtColumn());

    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('code', 'like', "%{$request->search}%")
           //   ->orWhere('region', 'like', "%{$request->search}%")
              ->orWhere('type', 'like', "%{$request->search}%");
        });
    }

    if ($request->filled('region_id')) {
        $regionId = (int) $request->region_id;
        Log::info('Region ID (casted): ' . $regionId);
        Log::info('Region ID type: ' . gettype($regionId));
        $query->where('region_id', $regionId);
        
        $sql = str_replace(['?'], ['\'%s\''], $query->toSql());
        $sql = vsprintf($sql, $query->getBindings());
        Log::info('Full SQL with bindings: ' . $sql);
    }

    if ($request->filled('type')) {
        
        $query->where('type', $request->type);
    }

    if ($request->filled('is_active')) {
        $query->where('is_active', $request->is_active);
    }

    // Statistics - clone before pagination mutates query
    $statsQuery = clone $query;
    $courts = $query->latest()->paginate(20)->withQueryString();
    
    $queries = \DB::getQueryLog();
    Log::info('All executed queries:', $queries);
    Log::info('Courts found: ' . $courts->total());

    $totalCourts = (clone $statsQuery)->count();
    $activeCourts = (clone $statsQuery)->where('is_active', true)->count();
    $highCourts = (clone $statsQuery)->where('type', 'high_court')->count();
    $districtCourts = (clone $statsQuery)->where('type', 'district_court')->count();

    return view('courts.index', compact('courts', 'regions', 'totalCourts', 'activeCourts', 'highCourts', 'districtCourts'));
}
  public function create()
{
    $this->authorize('create_courts');
    $user = auth()->user();
    $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

    $regionsQuery = Region::query();
    if ($isRegionalAdmin) {
        $regionsQuery->where('id', $user->region_id);
    }
    $regions = $regionsQuery->get();
    
    // Get judges using role relationship
    $judges = User::whereHas('role', function($query) {
        $query->where('name', 'Judge')
              ->orWhere('name', 'judge');
    })
    ->where('is_active', true) // assuming you have is_active column
    ->orderBy('name')
    ->get();
    
    // Get registry officers using role relationship
    $registryOfficers = User::whereHas('role', function($query) {
        $query->where('name', 'Registry Officer')
              ->orWhere('name', 'Registry')
              ->orWhere('name', 'registry');
    })
    ->where('is_active', true)
    ->orderBy('name')
    ->get();
    
    // Get locations
    $locationsQuery = Location::query();
    if ($isRegionalAdmin) {
        $locationsQuery->where('region_id', $user->region_id);
    }
    $locations = $locationsQuery->where('is_active', true)->orderBy('name')->get();
    
    return view('courts.create', compact('regions', 'judges', 'registryOfficers', 'locations'));
}
    public function store(Request $request)
    {
        $this->authorize('create_courts');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courts',
            'type' => 'required|in:supreme_court,appeal_court,high_court,district_court,magistrate_court,special_court,circuit_court,probate_court',
            'region_id' => 'required|exists:regions,id',
            'location_id' => 'nullable|exists:locations,id',
            'address' => 'nullable|string',
            'presiding_judge' => 'nullable|exists:users,id',
            'registry_officer' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ]);

        $user = auth()->user();
        if ($user->hasRole('rao')) {
            $validated['region_id'] = $user->region_id;
        }

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['created_by'] = auth()->id();

        Court::create($validated);

        return redirect()->route('courts')->with('success', 'Court created successfully.');
    }
    

 public function show(Court $court)
{
    $this->authorize('view_courts');
    $user = auth()->user();
    if ($user->hasRole('rao') && $court->region_id !== $user->region_id) {
        abort(403, 'Unauthorized access to court in another region.');
    }

    $court->load([
        'region', 
        'location', 
        'users', 
        'assets.category',
        'dts',
        'assets' => function($query) {
            $query->with('category')->latest()->take(10);
        }
    ]);
    
    // Get available DTS assets for assignment
    $availableDtsAssets = Asset::whereHas('category', function($q) {
        $q->where('name', 'DTS')->orWhere('code', 'DTS');
    })
    ->whereNull('court_id')
    ->orWhere('court_id', $court->id)
    ->with('category')
    ->get();
    
    // Get all available assets for assignment (excluding DTS)
    $availableAssets = Asset::where(function($query) use ($court) {
        $query->where('status', 'available')
              ->where(function($q) use ($court) {
                  $q->whereNull('court_id')
                    ->orWhereNull('assigned_to')
                    ->orWhereNull('office_id');
                    //->orWhere('court_id', $court->id);
              });
    })
    // ->whereDoesntHave('category', function($q) {
    //     $q->where('name', 'DTS');
    // })
    ->with('category')
    ->get();
    
    // Get categories for creating new assets
    $categories = Category::orderBy('name')->get();
    $users = User::active()->orderBy('name')->get();
    
    return view('courts.show', compact(
        'court', 
        'availableDtsAssets', 
        'availableAssets',
        'categories',
        'users'
    ));
}
    /**
     * Assign DTS to court
     */
    public function assignDts(Request $request, Court $court)
    {
        $request->validate([
            'dts_id' => 'required|exists:dts,id'
        ]);

       
        $court->update(['dts_id' => $request->dts_id]);

        return redirect()->back()->with('success', 'DTS assigned successfully.');
    }
    
    // In CourtController - add these methods

/**
 * Update DTS
 */
public function updateDts(Request $request, Court $court)
{
    $request->validate([
        'dts_id' => 'required|exists:dts,id',
        'name' => 'required|string|max:255',
        'monitors_count' => 'integer|min:0',
        'splitters_count' => 'integer|min:0',
        'hdmi_short_cables_count' => 'integer|min:0',
        'hdmi_long_cables_count' => 'integer|min:0',
        'extension_boards_count' => 'integer|min:0',
        'trucking_count' => 'integer|min:0',
        'sony_recorders_count' => 'integer|min:0',
    ]);

    $dts = Dts::find($request->dts_id);
    
    // Verify DTS belongs to this court
    if ($dts->court_id !== $court->id) {
        return redirect()->back()->with('error', 'DTS does not belong to this court.');
    }

    $dts->update($request->all());

    return redirect()->back()->with('success', 'DTS updated successfully.');
}

/**
 * Remove DTS from court
 */
public function removeDts(Request $request, Court $court)
{
    $request->validate([
        'dts_id' => 'required|exists:dts,id'
    ]);

    $dts = Dts::find($request->dts_id);
    
    // Verify DTS belongs to this court
    if ($dts->court_id !== $court->id) {
        return redirect()->back()->with('error', 'DTS does not belong to this court.');
    }

    $dts->delete();

    return redirect()->back()->with('success', 'DTS removed successfully.');
}

    /**
     * Assign asset to court
     */
    public function assignAsset(Request $request, Court $court)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);
        $asset = Asset::find($request->asset_id);
        $asset->update([
            'court_id' => $court->id,
            'office_id' => null,
            'assigned_to' => null,
            'assigned_type' => 'court',
            'status' => 'assigned'
        ]);

        return redirect()->back()->with('success', 'Asset assigned successfully.');
    }

    /**
     * Remove asset from court
     */
    public function removeAsset(Request $request, Court $court)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::find($request->asset_id);
        
        // Check if asset belongs to this court
        if ($asset->court_id !== $court->id) {
            return redirect()->back()->with('error', 'Asset does not belong to this court.');
        }

        $asset->update(['court_id' => null]);

        return redirect()->back()->with('success', 'Asset removed successfully.');
    }

    /**
     * Create new DTS for court
     */
    public function storeDts(Request $request, Court $court)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monitors_count' => 'integer|min:0',
            'splitters_count' => 'integer|min:0',
            'hdmi_short_cables_count' => 'integer|min:0',
            'hdmi_long_cables_count' => 'integer|min:0',
            'extension_boards_count' => 'integer|min:0',
            'trucking_count' => 'integer|min:0',
            'sony_recorders_count' => 'integer|min:0',
        ]);

        $dts = Dts::create([
            'court_id' => $court->id,
            'name' => $request->name,
            'monitors_count' => $request->monitors_count ?? 0,
            'splitters_count' => $request->splitters_count ?? 0,
            'hdmi_short_cables_count' => $request->hdmi_short_cables_count ?? 0,
            'hdmi_long_cables_count' => $request->hdmi_long_cables_count ?? 0,
            'extension_boards_count' => $request->extension_boards_count ?? 0,
            'trucking_count' => $request->trucking_count ?? 0,
            'sony_recorders_count' => $request->sony_recorders_count ?? 0,
            'is_available' => true,
        ]);

        // Optionally assign as primary DTS
        if ($request->assign_as_primary) {
            $court->update(['dts_id' => $dts->id]);
        }

        return redirect()->back()->with('success', 'DTS created successfully.');
    }


    public function edit(Court $court)
    {
        $this->authorize('edit_courts');
        $user = auth()->user();
        if ($user->hasRole('rao') && $court->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to court in another region.');
        }

        $regionsQuery = Region::query();
        if ($user->region_id && !$user->hasRole('admin')) {
            $regionsQuery->where('id', $user->region_id);
        }
        $regions = $regionsQuery->get();
        $judges = User::where('access_type', 'judge')->active()->get();
        $registryOfficers = User::where('access_type', 'registry')->active()->get();
        
        return view('courts.edit', compact('court', 'regions', 'judges', 'registryOfficers'));
    }

    public function update(Request $request, Court $court)
    {
        $this->authorize('edit_courts');
        $user = auth()->user();
        if ($user->hasRole('rao') && $court->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to court in another region.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courts,code,' . $court->id,
            'type' => 'required|in:high_court,district_court,magistrate_court,special_court',
            'region_id' => 'required|exists:regions,id',
            'location_id' => 'nullable|exists:locations,id',
            'address' => 'required|string',
            'presiding_judge' => 'nullable|exists:users,id',
            'registry_officer' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ]);

        $user = auth()->user();
        if ($user->hasRole('rao')) {
             if (isset($validated['region_id']) && $validated['region_id'] != $user->region_id) {
                 abort(403, 'Cannot change court to another region.');
             }
             $validated['region_id'] = $user->region_id;
        }

        $court->update($validated);

        return redirect()->route('courts.show', $court)->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        $this->authorize('delete_courts');
        $user = auth()->user();
        if ($user->hasRole('rao') && $court->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to court in another region.');
        }
        $court->delete();
        return redirect()->route('courts')->with('success', 'Court deleted successfully.');
    }


public function importForm()
{
    return view('courts.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
    ]);

    try {
        $import = new \App\Imports\CourtsImport();
        Excel::import($import, $request->file('file'));
        
        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();
        
        if (count($errors) > 0) {
            return redirect()
                ->route('courts')
                ->with('success', "{$successCount} courts imported successfully.")
                ->with('errors', $errors);
        }
        
        return redirect()
            ->route('courts')
            ->with('success', "{$successCount} courts imported successfully.");
            
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
        return back()->with('error', 'Error importing courts: ' . $e->getMessage());
    }
}

public function downloadTemplate()
{
    $headers = [
        'name',
        'code',
        'type',
        'region',
        'location',
        'address',
        'presiding_judge',
        'registry_officer',
        'is_active'
    ];
    
    $sampleData = [
        [
            'name' => 'Accra High Court',
            'code' => 'HC-ACC-001',
            'type' => 'high_court',
            'region' => 'Greater Accra',
            'location' => 'Accra',
            'address' => 'High Street, Accra',
            'presiding_judge' => 'judge@example.com',
            'registry_officer' => 'registry@example.com',
            'is_active' => 'yes'
        ],
        [
            'name' => 'Kumasi District Court',
            'code' => 'DC-KSI-001',
            'type' => 'district_court',
            'region' => 'Ashanti',
            'location' => 'Kumasi',
            'address' => 'Prempeh II Street, Kumasi',
            'presiding_judge' => 'John Doe',
            'registry_officer' => 'Jane Smith',
            'is_active' => '1'
        ]
    ];
    
    return Excel::download(new \App\Exports\CourtsTemplateExport($headers, $sampleData), 'courts_import_template.xlsx');
}
}