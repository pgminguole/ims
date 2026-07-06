<?php

namespace App\Http\Controllers;

use App\Models\Asset;
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
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    public function bulkModelAssignment()
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $categories = Category::with(['assets' => function($q) use ($user, $isRegionalAdmin) {
            if ($isRegionalAdmin) {
                $q->where('region_id', $user->region_id);
            }
        }])->get();

        $usersQuery = User::active()->with('region');
        $courtsQuery = Court::active()->with(['location', 'region']);
        $officesQuery = Office::active()->with(['location', 'region']);
        $regionsQuery = Region::query();

        if ($isRegionalAdmin) {
            $usersQuery->where('region_id', $user->region_id);
            $courtsQuery->where('region_id', $user->region_id);
            $officesQuery->where('region_id', $user->region_id);
            $regionsQuery->where('id', $user->region_id);
        }

        $users = $usersQuery->get();
        $courts = $courtsQuery->get();
        $offices = $officesQuery->get();
        $regions = $regionsQuery->get();
        
        return view('assignments.bulk-model', compact('categories', 'users', 'courts', 'offices', 'regions'));
    }
public function storeBulkModelAssignment(Request $request)
{
    Log::info('Bulk assignment request:', $request->all());
    
    // First, check if devices array exists and has items
    if (!$request->has('devices') || empty($request->devices)) {
        return back()->withErrors(['devices' => 'Please add at least one device.'])->withInput();
    }

    // Filter out any empty device entries
    $devices = array_filter($request->devices, function($device) {
        return !empty($device['category_id']) && !empty($device['quantity']);
    });

    if (empty($devices)) {
        return back()->withErrors(['devices' => 'Please add at least one device with Category and Quantity.'])->withInput();
    }

    // Reindex the array to ensure continuous indices
    $request->merge(['devices' => array_values($devices)]);
    
    $validated = $request->validate([
        'targets' => 'required|array',
        'targets.users' => 'sometimes|array',
        'targets.users.*' => 'exists:users,id',
        'targets.courts' => 'sometimes|array',
        'targets.courts.*' => 'exists:courts,id',
        'targets.offices' => 'sometimes|array',
        'targets.offices.*' => 'exists:offices,id',
        'devices' => 'required|array|min:1',
        'devices.*.category_id' => 'required|exists:categories,id',
        'devices.*.brand' => 'nullable|string|max:255',
        'devices.*.model' => 'nullable|string|max:255',
        'devices.*.quantity' => 'required|integer|min:1|max:100',
        'assigned_date' => 'required|date',
        'condition' => 'required|in:excellent,good,fair,poor',
        'warranty_months' => 'nullable|integer|min:0',
        'purchase_date' => 'nullable|date',
        'purchase_price' => 'nullable|numeric|min:0',
        'comments' => 'nullable|string'
    ], [
        'devices.required' => 'Please add at least one device.',
        'devices.min' => 'Please add at least one device.',
        'devices.*.category_id.required' => 'Category is required for all devices.',
        'devices.*.quantity.required' => 'Quantity is required for all devices.',
        'targets.required' => 'Please select at least one target (user, court, or office).',
    ]);

    // Check that at least one target type has selections
    $hasTargets = !empty($validated['targets']['users']) || 
                  !empty($validated['targets']['courts']) || 
                  !empty($validated['targets']['offices']);
    
    if (!$hasTargets) {
        return back()->withErrors(['targets' => 'Please select at least one target (user, court, or office).'])->withInput();
    }

    $createdCount = 0;
    $deviceSummary = [];
    $targetSummary = [
        'users' => 0,
        'courts' => 0,
        'offices' => 0
    ];

    DB::transaction(function () use ($validated, &$createdCount, &$deviceSummary, &$targetSummary) {
        // Prepare all targets with their types
        $allTargets = [];
        
        if (!empty($validated['targets']['users'])) {
            foreach ($validated['targets']['users'] as $userId) {
                $allTargets[] = ['type' => 'user', 'id' => $userId];
                $targetSummary['users']++;
            }
        }
        
        if (!empty($validated['targets']['courts'])) {
            foreach ($validated['targets']['courts'] as $courtId) {
                $allTargets[] = ['type' => 'court', 'id' => $courtId];
                $targetSummary['courts']++;
            }
        }
        
        if (!empty($validated['targets']['offices'])) {
            foreach ($validated['targets']['offices'] as $officeId) {
                $allTargets[] = ['type' => 'office', 'id' => $officeId];
                $targetSummary['offices']++;
            }
        }

        // Generate all required asset tags upfront for each device type to avoid redundant DB interactions
        $deviceTagsMap = [];
        foreach ($validated['devices'] as $idx => $deviceData) {
            $totalQtyForDevice = $deviceData['quantity'] * count($allTargets);
            $deviceTagsMap[$idx] = Asset::generateNextTags($deviceData['category_id'], $totalQtyForDevice);
        }

        // Loop through each target
        foreach ($allTargets as $targetIndex => $target) {
            $targetType = $target['type'];
            $targetId = $target['id'];
            
            // Loop through each device type
            foreach ($validated['devices'] as $deviceIndex => $deviceData) {
                $category = Category::find($deviceData['category_id']);
                
                // Create the specified quantity of assets for this device type
                for ($i = 0; $i < $deviceData['quantity']; $i++) {
                    // Pick the next tag from our pre-generated list
                    $tagIndex = ($targetIndex * $deviceData['quantity']) + $i;
                    $assetTag = $deviceTagsMap[$deviceIndex][$tagIndex];

                    // Generate asset name from category or model/brand
                    if (!empty($deviceData['model'])) {
                        $assetName = $deviceData['model'];
                    } elseif (!empty($deviceData['brand'])) {
                        $assetName = $deviceData['brand'] . ' ' . $category->name;
                    } else {
                        $assetName = $category->name;
                    }
                    
                    $assetData = [
                        'asset_name' => $assetName,
                        'slug' => $this->generateAssetSlug($assetName . '-' . $assetTag),
                        'asset_tag' => $assetTag,
                        'serial_number' => $this->generateSerialNumber(),
                        'category_id' => $deviceData['category_id'],
                        'brand' => $deviceData['brand'] ?? null,
                        'asset_id' => 'AST-' . strtoupper(uniqid()),
                        'model' => $deviceData['model'] ?? null,
                        'assigned_date' => $validated['assigned_date'],
                        'condition' => $validated['condition'],
                        'warranty_months' => $validated['warranty_months'],
                        'purchase_date' => $validated['purchase_date'],
                        'purchase_price' => $validated['purchase_price'],
                        'status' => 'assigned',
                        'created_by' => auth()->id()
                    ];

                    // Set assignment fields based on target type
                    switch ($targetType) {
                        case 'user':
                            $assetData['assigned_to'] = $targetId;
                            $assetData['assigned_type'] = 'user';
                            break;
                        case 'court':
                            $assetData['court_id'] = $targetId;
                            $assetData['assigned_type'] = 'court';
                            break;
                        case 'office':
                            $assetData['office_id'] = $targetId;
                            $assetData['assigned_type'] = 'office';
                            break;
                    }

                    $asset = Asset::create($assetData);

                    // Get target name for history
                    $targetName = $this->getTargetName($targetType, $targetId);

                    // Create description for history
                    $deviceDescription = $deviceData['brand'] && $deviceData['model'] 
                        ? "{$deviceData['brand']} {$deviceData['model']}" 
                        : $category->name;

                    // Log assignment history
                    AssetHistory::create([
                        'asset_id' => $asset->id,
                        'action' => 'assigned',
                        'description' => "New {$deviceDescription} created and assigned to {$targetType}: {$targetName}. Comments: {$validated['comments']}",
                        'performed_by' => auth()->id(),
                        'performed_at' => now()
                    ]);

                    $createdCount++;
                    
                    // Track device summary for success message
                    $deviceKey = $deviceData['brand'] && $deviceData['model']
                        ? "{$deviceData['brand']} {$deviceData['model']}"
                        : ($deviceData['brand'] 
                            ? "{$deviceData['brand']} {$category->name}"
                            : $category->name);
                    
                    if (!isset($deviceSummary[$deviceKey])) {
                        $deviceSummary[$deviceKey] = 0;
                    }
                    $deviceSummary[$deviceKey]++;
                }
            }
        }
    });

    // Build detailed success message
    $deviceList = [];
    foreach ($deviceSummary as $device => $count) {
        $deviceList[] = "{$count} {$device}";
    }
    
    $targetList = [];
    if ($targetSummary['users'] > 0) {
        $targetList[] = "{$targetSummary['users']} user(s)";
    }
    if ($targetSummary['courts'] > 0) {
        $targetList[] = "{$targetSummary['courts']} court(s)";
    }
    if ($targetSummary['offices'] > 0) {
        $targetList[] = "{$targetSummary['offices']} office(s)";
    }
    
    $successMessage = "Successfully created and assigned {$createdCount} assets (" . implode(', ', $deviceList) . ") to " . implode(', ', $targetList) . ".";

    return redirect()->route('assignments.index')
        ->with('success', $successMessage);
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

/**
 * Get the name of the assignment target
 */
private function getTargetName($type, $id)
{
    switch ($type) {
        case 'user':
            $user = User::find($id);
            return $user ? $user->name : 'Unknown User';
        case 'court':
            $court = Court::find($id);
            return $court ? $court->name : 'Unknown Court';
        case 'office':
            $office = Office::find($id);
            return $office ? $office->name : 'Unknown Office';
        default:
            return 'Unknown';
    }
}

    public function index(Request $request)
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        // Build query for all assigned assets
        $query = Asset::query()
            ->with(['category', 'region', 'court', 'office', 'assignedUser'])
            ->where('record_type', 'assignment');

        if ($isRegionalAdmin) {
            $query->where('region_id', $user->region_id);
        }

        // Filter by status - only show assigned assets
        $query->where('status', 'assigned');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Category filter (Device Type)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Assignment target filter - filter by which entity the asset is assigned to
        if ($request->filled('assignment_target')) {
            $query->where(function($q) use ($request) {
                switch ($request->assignment_target) {
                    case 'user':
                        $q->whereNotNull('assigned_to')
                          ->whereNull('court_id')
                          ->whereNull('office_id')
                          ->whereNull('region_id');
                        break;
                    case 'court':
                        $q->whereNotNull('court_id')
                          ->whereNull('assigned_to')
                          ->whereNull('office_id')
                          ->whereNull('region_id');
                        break;
                    case 'office':
                        $q->whereNotNull('office_id')
                          ->whereNull('assigned_to')
                          ->whereNull('court_id')
                          ->whereNull('region_id');
                        break;
                    case 'region':
                        $q->whereNotNull('region_id')
                          ->whereNull('assigned_to')
                          ->whereNull('court_id')
                          ->whereNull('office_id');
                        break;
                }
            });
        } else {
            // If no target filter, show only assets that are assigned to something
            $query->where(function($q) {
                $q->whereNotNull('assigned_to')
                  ->orWhereNotNull('office_id')
                  ->orWhereNotNull('court_id')
                  ->orWhereNotNull('region_id');
            });
        }

        // Assigned type filter (judge, staff, department, etc.)
        if ($request->filled('assigned_type')) {
            $query->where('assigned_type', $request->assigned_type);
        }

        // Date filters
        if ($request->filled('assigned_date_from')) {
            $query->whereDate('assigned_date', '>=', $request->assigned_date_from);
        }

        if ($request->filled('assigned_date_to')) {
            $query->whereDate('assigned_date', '<=', $request->assigned_date_to);
        }

        $assignments = $query->latest('created_at')->paginate(20)->withQueryString();
        
        $usersQuery = User::active();
        if ($isRegionalAdmin) {
            $usersQuery->where('region_id', $user->region_id);
        }
        $users = $usersQuery->get();
        
        $categories = Category::orderBy('name')->get();

        return view('assignments.index', compact('assignments', 'users', 'categories'));
    }

    public function create()
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $assetQuery = Asset::available()->with(['category', 'region']);
        $usersQuery = User::active();
        $courtsQuery = Court::active();
        $officesQuery = Office::active();
        $regionsQuery = Region::query();

        if ($isRegionalAdmin) {
            $assetQuery->where('region_id', $user->region_id);
            $usersQuery->where('region_id', $user->region_id);
            $courtsQuery->where('region_id', $user->region_id);
            $officesQuery->where('region_id', $user->region_id);
            $regionsQuery->where('id', $user->region_id);
        }

        $availableAssets = $assetQuery->get();
        $users = $usersQuery->get();
        $courts = $courtsQuery->get();
        $offices = $officesQuery->get();
        $regions = $regionsQuery->get();
        
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $subcategories = Category::whereNotNull('parent_id')->orderBy('name')->get();

        return view('assignments.create', compact('availableAssets', 'users', 'courts', 'offices', 'regions', 'categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'assignment_target' => 'required|in:user,court,office,region',
            'target_id' => 'required',
            'assigned_type' => 'required|in:judge,staff,department,court,office,region',
            'assigned_date' => 'required|date',
            'comments' => 'nullable|string'
        ]);

        $asset = Asset::find($validated['asset_id']);

        // Check if asset is available
        if ($asset->status !== 'available') {
            return back()->withErrors(['asset_id' => 'Selected asset is not available for assignment.'])->withInput();
        }

        DB::transaction(function () use ($asset, $validated) {
            $updateData = [
                'assigned_type' => $validated['assigned_type'],
                'assigned_date' => $validated['assigned_date'],
                'status' => 'assigned'
            ];

            // Set the appropriate foreign key based on assignment target
            switch ($validated['assignment_target']) {
                case 'user':
                    $updateData['assigned_to'] = $validated['target_id'];
                    break;
                case 'court':
                    $updateData['court_id'] = $validated['target_id'];
                    break;
                case 'office':
                    $updateData['office_id'] = $validated['target_id'];
                    break;
                case 'region':
                    $updateData['region_id'] = $validated['target_id'];
                    break;
            }

            $asset->update($updateData);

            // Get target name for history
            $targetName = $this->getTargetName($validated['assignment_target'], $validated['target_id']);

            // Log assignment history
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'assigned',
                'description' => "Asset assigned to {$validated['assignment_target']}: {$targetName} (Type: {$validated['assigned_type']}). Comments: {$validated['comments']}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        });

        return redirect()->route('assignments.index')->with('success', 'Asset assigned successfully.');
    }

    public function createAsset(Request $request)
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
            'record_type' => 'nullable|in:assignment,inventory',
            'assignment_target' => 'required|in:user,court,office,region',
            'target_id' => 'required',
            'assigned_type' => 'required|in:judge,staff,department,court,office,region'
        ]);

        $createdCount = 0;
        
        DB::transaction(function () use ($validated, &$createdCount) {
            $category = Category::find($validated['category_id']);
            $currentUser = auth()->user();
            $isRegionalAdmin = $currentUser->region_id && $currentUser->hasRole('rao');
            
            // Generate unique asset tags for the entire batch
            $tags = Asset::generateNextTags($validated['category_id'], $validated['quantity']);

            for ($i = 0; $i < $validated['quantity']; $i++) {
                $assetTag = $tags[$i];
                
                // Generate asset name from category or model/brand
                if (!empty($validated['model'])) {
                    $assetName = $validated['model'];
                } elseif (!empty($validated['brand'])) {
                    $assetName = $validated['brand'] . ' ' . $category->name;
                } else {
                    $assetName = $category->name;
                }
                
                $assetData = [
                    'asset_name' => $assetName,
                    'slug' => Str::slug($assetName . '-' . $assetTag) . '-' . uniqid(),
                    'asset_tag' => $assetTag,
                    'serial_number' => 'SN-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)),
                    'category_id' => $validated['category_id'],
                    'brand' => $validated['brand'] ?? null,
                    'asset_id' => 'AST-' . strtoupper(uniqid()),
                    'model' => $validated['model'] ?? null,
                    'assigned_date' => $validated['assigned_date'],
                    'condition' => $validated['condition'],
                    'warranty_months' => $validated['warranty_months'] ?? null,
                    'purchase_date' => $validated['purchase_date'] ?? null,
                    'purchase_price' => $validated['purchase_price'] ?? null,
                    'status' => 'assigned',
                    'assigned_type' => $validated['assigned_type'],
                    'comments' => $validated['comments'] ?? null,
                    'created_by' => auth()->id(),
                    'region_id' => $isRegionalAdmin ? $currentUser->region_id : null,
                    'record_type' => $isRegionalAdmin ? 'inventory' : ($validated['record_type'] ?? 'assignment')
                ];

                switch ($validated['assignment_target']) {
                    case 'user':
                        $assetData['assigned_to'] = $validated['target_id'];
                        if (!$isRegionalAdmin) {
                            $assetData['court_id'] = User::find($validated['target_id'])->court_id ?? null;
                        }
                        break;
                    case 'court':
                        $assetData['court_id'] = $validated['target_id'];
                        break;
                    case 'office':
                        $assetData['office_id'] = $validated['target_id'];
                        break;
                    case 'region':
                        $assetData['region_id'] = $validated['target_id'];
                        break;
                }

                $asset = Asset::create($assetData);

                $deviceDescription = ($validated['brand'] ?? null) && ($validated['model'] ?? null)
                    ? "{$validated['brand']} {$validated['model']}" 
                    : $category->name;

                $targetName = $this->getTargetName($validated['assignment_target'], $validated['target_id']);

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'assigned',
                    'description' => "New {$deviceDescription} created and assigned to {$validated['assignment_target']}: {$targetName}. Comments: " . ($validated['comments'] ?? 'None'),
                    'performed_by' => auth()->id(),
                    'performed_at' => now()
                ]);

                $createdCount++;
            }
        });

        return redirect()->route('assignments.index')->with('success', "Successfully created and assigned {$createdCount} asset(s).");
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
            'assignment_target' => 'required|in:user,court,office,region',
            'target_id' => 'required',
            'assigned_type' => 'required|in:judge,staff,department,court,office,region',
            'assigned_date' => 'required|date',
            'comments' => 'nullable|string'
        ]);

        $targetName = $this->getTargetName($validated['assignment_target'], $validated['target_id']);
        $assignedCount = 0;

        DB::transaction(function () use ($validated, $targetName, &$assignedCount) {
            foreach ($validated['asset_ids'] as $assetId) {
                $asset = Asset::find($assetId);
                
                // Only assign available assets
                if ($asset->status === 'available') {
                    $updateData = [
                        'assigned_type' => $validated['assigned_type'],
                        'assigned_date' => $validated['assigned_date'],
                        'status' => 'assigned'
                    ];

                    // Set the appropriate foreign key
                    switch ($validated['assignment_target']) {
                        case 'user':
                            $updateData['assigned_to'] = $validated['target_id'];
                            break;
                        case 'court':
                            $updateData['court_id'] = $validated['target_id'];
                            break;
                        case 'office':
                            $updateData['office_id'] = $validated['target_id'];
                            break;
                        case 'region':
                            $updateData['region_id'] = $validated['target_id'];
                            break;
                    }

                    $asset->update($updateData);

                    AssetHistory::create([
                        'asset_id' => $asset->id,
                        'action' => 'assigned',
                        'description' => "Asset assigned to {$validated['assignment_target']}: {$targetName} (Type: {$validated['assigned_type']}). Comments: {$validated['comments']}",
                        'performed_by' => auth()->id(),
                        'performed_at' => now()
                    ]);

                    $assignedCount++;
                }
            }
        });

        return redirect()->route('assignments.index')->with('success', "Successfully assigned {$assignedCount} assets.");
    }

    public function returnAsset(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'returned_date' => 'required|date',
            'returned_reason' => 'required|string',
            'returnee' => 'required|string|max:255',
            'returned_to' => 'nullable|string|max:255',
            'condition' => 'required|in:excellent,good,fair,poor,broken',
            'comments' => 'nullable|string'
        ]);

        DB::transaction(function () use ($asset, $validated) {
            // Get old assignment info for history
            $oldAssignedInfo = $this->getAssignmentInfo($asset);

            $asset->update([
                'returned_date' => $validated['returned_date'],
                'returned_reason' => $validated['returned_reason'],
                'returnee' => $validated['returnee'],
                'returned_to' => $validated['returned_to'],
                'condition' => $validated['condition'],
                'status' => 'available',
                'assigned_to' => null,
                'court_id' => null,
                'office_id' => null,
                'region_id' => null,
                'assigned_type' => null,
                'assigned_date' => null
            ]);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'returned',
                'description' => "Asset returned from {$oldAssignedInfo}. Returned by: {$validated['returnee']}. Reason: {$validated['returned_reason']}. Condition: {$validated['condition']}. Comments: {$validated['comments']}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        });

        return redirect()->route('assignments.index')->with('success', 'Asset returned successfully.');
    }

    public function history(Request $request)
    {
        $query = AssetHistory::where('action', 'assigned')
            ->orWhere('action', 'returned')
            ->with(['asset', 'performedBy']);

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('performed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('performed_at', '<=', $request->date_to);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $history = $query->latest('performed_at')->paginate(20);

        return view('assignments.history', compact('history'));
    }

    public function getModelsByCategory($categoryId)
    {
        $models = Asset::where('category_id', $categoryId)
            ->whereNotNull('model')
            ->distinct()
            ->pluck('model')
            ->toArray();

        return response()->json($models);
    }

    /**
     * Helper method to get assignment info from asset
     */
    private function getAssignmentInfo($asset)
    {
        if ($asset->assignedUser) {
            return "User: {$asset->assignedUser->name}";
        } elseif ($asset->court) {
            return "Court: {$asset->court->name}";
        } elseif ($asset->office) {
            return "Office: {$asset->office->name}";
        } elseif ($asset->region) {
            return "Region: {$asset->region->name}";
        }
        return "Unknown";
    }

    /**
     * Generate a unique slug for the asset
     */
    // private function generateAssetSlug($assetName)
    // {
    //     $baseSlug = Str::slug($assetName);
    //     $slug = $baseSlug;
    //     $counter = 1;

    //     while (Asset::where('slug', $slug)->exists()) {
    //         $slug = $baseSlug . '-' . $counter;
    //         $counter++;
    //     }

    //     return $slug;
    // }

    // private function generateAssetTag($prefix)
    // {
    //     $latestAsset = Asset::where('asset_tag', 'like', $prefix . '-%')
    //         ->orderBy('asset_tag', 'desc')
    //         ->first();

    //     if ($latestAsset) {
    //         $lastNumber = intval(str_replace($prefix . '-', '', $latestAsset->asset_tag));
    //         $newNumber = $lastNumber + 1;
    //     } else {
    //         $newNumber = 1;
    //     }

    //     return $prefix . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    // }

    // private function generateSerialNumber()
    // {
    //     do {
    //         $serial = 'SN-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(3));
    //     } while (Asset::where('serial_number', $serial)->exists());

    //     return $serial;
    // }

    // private function getTargetName($type, $id)
    // {
    //     switch ($type) {
    //         case 'user':
    //             $user = User::find($id);
    //             return $user ? $user->name : 'Unknown User';
    //         case 'court':
    //             $court = Court::find($id);
    //             return $court ? $court->name : 'Unknown Court';
    //         case 'office':
    //             $office = Office::find($id);
    //             return $office ? $office->name : 'Unknown Office';
    //         case 'region':
    //             $region = Region::find($id);
    //             return $region ? $region->name : 'Unknown Region';
    //         default:
    //             return 'Unknown';
    //     }
    // }
}