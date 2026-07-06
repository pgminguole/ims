<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Region;
use App\Models\Court;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class OfficeController extends Controller
{
    
    public function createAsset(Request $request, Office $office)
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

    $createdCount = 0;
    
    DB::transaction(function () use ($validated, $office, &$createdCount) {
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
                'slug' => $this->generateAssetSlug($assetName . '-' . $assetTag),
                'asset_tag' => $assetTag,
                'serial_number' => $this->generateSerialNumber(),
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
                'office_id' => $office->id,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'assigned_type' => !empty($validated['assigned_to']) ? 'user' : 'office',
                'comments' => $validated['comments'] ?? null,
                'created_by' => auth()->id(),
                'region_id' => $isRegionalAdmin ? $currentUser->region_id : ($validated['region_id'] ?? null),
                'record_type' => $isRegionalAdmin ? 'inventory' : ($validated['record_type'] ?? 'assignment')
            ];

            $asset = Asset::create($assetData);

            // Create description for history
            $deviceDescription = $validated['brand'] && $validated['model'] 
                ? "{$validated['brand']} {$validated['model']}" 
                : $category->name;

            // Log assignment history
            $assigneeName = !empty($validated['assigned_to']) 
                ? "user: " . User::find($validated['assigned_to'])->name 
                : "office: {$office->name}";

            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'assigned',
                'description' => "New {$deviceDescription} created and assigned to {$assigneeName}. Comments: {$validated['comments']}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            $createdCount++;
        }
    });

    return redirect()->back()->with('success', "Successfully created and assigned {$createdCount} asset(s) to {$office->name}.");
}

// Add these helper methods to OfficeController
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

private function generateSerialNumber()
{
    do {
        $serial = 'SN-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(3));
    } while (Asset::where('serial_number', $serial)->exists());

    return $serial;
}
  public function index(Request $request)
{
    $this->authorize('view_offices');
    $user = auth()->user();
    $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

    $query = Office::with(['region', 'manager', 'assets']);

    if ($isRegionalAdmin) {
        $query->where('region_id', $user->region_id);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('offices.name', 'like', "%{$search}%")
              ->orWhere('offices.code', 'like', "%{$search}%")
              ->orWhere('offices.email', 'like', "%{$search}%")
              ->orWhereHas('region', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('region')) {
        $query->where('region_id', $request->region);
    }

    if ($request->filled('status')) {
        $query->where('is_active', $request->status === 'active');
    }

    $offices = $query->latest()->paginate(20);

    // Statistics
    $statsQuery = Office::query();
    if ($isRegionalAdmin) {
        $statsQuery->where('region_id', $user->region_id);
    }

    $totalOffices = (clone $statsQuery)->count();
    $activeOffices = (clone $statsQuery)->where('is_active', true)->count();
    $officesWithAssets = (clone $statsQuery)->has('assets')->count();

    $regionsQuery = Region::query();
    if ($isRegionalAdmin) {
        $regionsQuery->where('id', $user->region_id);
    }
    $regions = $regionsQuery->get();

    return view('offices.index', compact('offices', 'totalOffices', 'activeOffices', 'officesWithAssets', 'regions'));
}
    public function create()
    {
        $this->authorize('create_offices');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $regionsQuery = Region::query();
        $managersQuery = User::where('status', 'active');
        $locationsQuery = Location::query();

        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
            $managersQuery->where('region_id', $user->region_id);
            $locationsQuery->where('region_id', $user->region_id);
        }

        $regions = $regionsQuery->get();
        $managers = $managersQuery->get();
        $locations = $locationsQuery->get();

        return view('offices.create', compact('regions', 'managers','locations'));
    }

    public function store(Request $request)
{
    $this->authorize('create_offices');
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:50|unique:offices',
        'description' => 'nullable|string',
        'region_id' => 'required|exists:regions,id',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email',
        'address' => 'nullable|string',
        'is_active' => 'required|boolean',
        'capacity' => 'nullable|integer|min:1',
        'manager_id' => 'nullable|exists:users,id',
    ]);
    
    // Generate unique slug
    $slug = Str::slug($validated['name']);
    $originalSlug = $slug;
    $counter = 1;
    
    while (Office::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    $validated['slug'] = $slug;
    $validated['created_by'] = auth()->id();
    
    $office = Office::create($validated);
    return redirect()->route('offices.index')->with('success', 'Office created successfully.');
}
    /**
     * Assign asset to office
     */
    public function assignAsset(Request $request, Office $office)
    {
        $this->authorize('assign_assets');
        $currentUser = auth()->user();
        if ($currentUser->region_id && $currentUser->hasRole('admin') && $office->region_id !== $currentUser->region_id) {
            abort(403, 'Cannot assign assets to office in another region.');
        }

        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::find($request->asset_id);
        
        if ($currentUser->region_id && $currentUser->hasRole('admin') && $asset->region_id !== $currentUser->region_id) {
            abort(403, 'Cannot assign assets from another region.');
        }
        
             $asset->update([
                'office_id' => $office->id,
                'assigned_to' => null,
                'assigned_type' => 'office',
                'status' => 'assigned'
            ]);

        return redirect()->back()->with('success', 'Asset assigned to office successfully.');
    }

    /**
     * Remove asset from office
     */
    public function removeAsset(Request $request, Office $office)
    {
        $this->authorize('return_assets');
        $currentUser = auth()->user();
        if ($currentUser->region_id && $currentUser->hasRole('admin') && $office->region_id !== $currentUser->region_id) {
            abort(403, 'Cannot remove assets from office in another region.');
        }

        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::find($request->asset_id);
        
        if ($currentUser->region_id && $currentUser->hasRole('admin') && $asset->region_id !== $currentUser->region_id) {
            abort(403, 'Cannot manage assets from another region.');
        }

        // Check if asset belongs to this office
        if ($asset->office_id !== $office->id) {
            return redirect()->back()->with('error', 'Asset does not belong to this office.');
        }

        $asset->update(['office_id' => null, 'status' => 'available']);

        return redirect()->back()->with('success', 'Asset removed from office successfully.');
    }
    
    public function show(Office $office)
    {
        $this->authorize('view_offices');
        $user = auth()->user();
        if ($user->hasRole('rao') && $office->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to office in another region.');
        }

        $office->load([
            'region', 
            'manager',
            'assets.category',
            'assets' => function($query) {
                $query->with('category')->latest()->take(10);
            }
        ]);

        // Get available assets for assignment
        $availableAssetsQuery = Asset::query();
        if ($user->hasRole('rao')) {
            $availableAssetsQuery->where('region_id', $user->region_id);
        }

        $availableAssets = (clone $availableAssetsQuery)->where(function($query) use ($office) {
            $query->whereNull('office_id')
                  ->orWhere('office_id', $office->id);
        })
        ->with('category')
        ->get();
        
        
        
    $availableAssets = (clone $availableAssetsQuery)->where(function($query) use ($office) {
        $query->where('status', 'available')
              ->where(function($q) use ($office) {
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
    $categories = Category::orderBy('name')->get();
    $users = User::active()->orderBy('name')->get();

        return view('offices.show', compact(
            'office', 
            'availableAssets','categories', 'users'
        ));
    }

    public function edit(Office $office)
    {
        $this->authorize('edit_offices');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        if ($isRegionalAdmin && $office->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to office in another region.');
        }

        $regionsQuery = Region::query();
        $managersQuery = User::where('status', 'active');

        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
            $managersQuery->where('region_id', $user->region_id);
        }

        $regions = $regionsQuery->get();
        $managers = $managersQuery->get();

        return view('offices.edit', compact('office', 'regions', 'managers'));
    }

    public function update(Request $request, Office $office)
    {
        $this->authorize('edit_offices');
        $user = auth()->user();
        if ($user->hasRole('rao') && $office->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to office in another region.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:offices,code,' . $office->id,
            'description' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
            'capacity' => 'nullable|integer|min:1',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        if ($user->hasRole('rao')) {
            if (isset($validated['region_id']) && $validated['region_id'] != $user->region_id) {
                abort(403, 'Unauthorized access to another region.');
            }
            $validated['region_id'] = $user->region_id;
        }

        $validated['slug'] = Str::slug($validated['name']);

        $office->update($validated);

        return redirect()->route('offices.show', $office)->with('success', 'Office updated successfully.');
    }

    public function destroy(Office $office)
    {
        $this->authorize('delete_offices');
        $user = auth()->user();
        if ($user->hasRole('rao') && $office->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to office in another region.');
        }

        $office->delete();
        return redirect()->route('offices')->with('success', 'Office deleted successfully.');
    }
}