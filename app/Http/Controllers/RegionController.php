<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Asset;
use App\Models\Category;
use App\Models\AssetHistory;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount(['courts', 'locations', 'assets', 'users'])
            ->orderBy('name')
            ->paginate(20);

        return view('regions.index', compact('regions'));
    }
    
    //   public function show(Region $region)
    // {
    //     $region->load([
           
    //         'assets.category',
    //         'assets' => function($query) {
    //             $query->with('category')->latest()->take(10);
    //         }
    //     ]);

    //     // Get available assets for assignment
    //     $availableAssets = Asset::where(function($query) use ($region) {
    //         $query->whereNull('region_id')
    //               ->orWhere('region_id', $region->id);
    //     })
    //     ->with('category')
    //     ->get();
        
        
        
    // $availableAssets = Asset::where(function($query) use ($region) {
    //     $query->where('status', 'available')
    //           ->where(function($q) use ($region) {
    //               $q->whereNull('court_id')
    //                 ->orWhereNull('assigned_to')
    //                 ->orWhereNull('region_id');
    //                 //->orWhere('court_id', $court->id);
    //           });
    // })
    // // ->whereDoesntHave('category', function($q) {
    // //     $q->where('name', 'DTS');
    // // })
    // ->with('category')
    // ->get();
    // $categories = Category::orderBy('name')->get();

    //     return view('regions.show', compact(
    //         'region', 
    //         'availableAssets','categories'
    //     ));
    // }
    
      public function show(Region $region)
    {
        $region->load([
            'courts',
            'locations',
            'assets.category',
            'users',
            'assets' => function($query) {
                $query->with('category')->latest()->take(10);
            }
        ]);

        // Get available assets for assignment
        $availableAssets = Asset::where(function($query) use ($region) {
            $query->whereNull('region_id')
                  ->orWhere('region_id', $region->id);
        })
        ->where('status', 'available')
        ->with('category')
        ->get();

        // Get assets specifically assigned to this region
        $regionAssets = Asset::where('region_id', $region->id)
            ->with('category')
            ->get();

        $categories = Category::orderBy('name')->get();
        $locations = Location::where('region_id', $region->id)->get();
        $users = User::where('region_id', $region->id)->get();

        // Statistics
        $assetStats = [
            'total' => $region->assets->count(),
            'available' => $region->assets->where('status', 'available')->count(),
            'assigned' => $region->assets->where('status', 'assigned')->count(),
            'maintenance' => $region->assets->where('status', 'maintenance')->count(),
            'by_category' => $region->assets->groupBy('category.name')->map->count()
        ];

        return view('regions.show', compact(
            'region', 
            'availableAssets', 
            'regionAssets',
            'categories',
            'locations',
            'users',
            'assetStats'
        ));
    }

    public function create()
    {
        
        $regions = Region::withCount(['courts', 'locations', 'assets', 'users'])
            ->orderBy('name')
            ->paginate(20);
           return view('regions.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regions',
            'code' => 'required|string|max:50|unique:regions',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();

        Region::create($validated);

        return redirect()->route('regions.index')->with('success', 'Region created successfully.');
    }

    public function edit(Region $region)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $region->id !== $user->region_id) {
            abort(403, 'Unauthorized access to another region.');
        }
        $region->load(['courts', 'locations', 'assets', 'users']);
        return view('regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $region->id !== $user->region_id) {
            abort(403, 'Unauthorized access to another region.');
        }
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions')->ignore($region->id)
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('regions')->ignore($region->id)
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $region->update($validated);

        return redirect()->route('regions.index')->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        // Check if region has associated records
        if ($region->courts()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete region with associated courts. Please reassign or delete the courts first.']);
        }

        if ($region->locations()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete region with associated locations. Please reassign or delete the locations first.']);
        }

        if ($region->assets()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete region with associated assets. Please reassign the assets first.']);
        }

        if ($region->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete region with associated users. Please reassign the users first.']);
        }

        $region->delete();

        return redirect()->route('regions.index')->with('success', 'Region deleted successfully.');
    }
    
    
    public function assignAsset(Request $request, Region $region)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $region->id !== $user->region_id) {
            abort(403, 'Unauthorized access to another region.');
        }
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::find($request->asset_id);
        
        DB::transaction(function () use ($asset, $region) {
            $oldAssignment = $asset->region_id ? "region ID {$asset->region_id}" : "no region";
            
            $asset->update([
                'region_id' => $region->id,
                'office_id' => null,
                'court_id' => null,
                'assigned_to' => null,
                'status' => 'assigned',
                'assigned_type' => 'region'
            ]);

            // Log assignment history
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'assigned',
                'description' => "Asset assigned to region: {$region->name} (from {$oldAssignment})",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Asset assigned to region successfully.');
    }

    /**
     * Remove asset from region
     */
    public function removeAsset(Request $request, Region $region)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $region->id !== $user->region_id) {
            abort(403, 'Unauthorized access to another region.');
        }
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::find($request->asset_id);
        
        // Check if asset belongs to this region
        if ($asset->region_id !== $region->id) {
            return redirect()->back()->with('error', 'Asset does not belong to this region.');
        }

        DB::transaction(function () use ($asset, $region) {
            $asset->update([
                'region_id' => null,
                'assigned_type' => null
            ]);

            // Log removal history
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'unassigned',
                'description' => "Asset removed from region: {$region->name}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Asset removed from region successfully.');
    }

    /**
     * Create new asset directly for region
     */
    public function createAsset(Request $request, Region $region)
{
    $user = auth()->user();
    if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $region->id !== $user->region_id) {
        abort(403, 'Unauthorized access to another region.');
    }
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
        'assigned_to' => 'nullable|exists:users,id'
    ]);

    $createdCount = 0;
    
    DB::transaction(function () use ($validated, $region, &$createdCount) {
        $category = Category::find($validated['category_id']);
        
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
                'assigned_date' => $validated['assigned_date'] ?? now(),
                'condition' => $validated['condition'] ?? 'good',
                'warranty_months' => $validated['warranty_months'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? null,
                'status' => 'assigned',
                'region_id' => $region->id,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'assigned_type' => !empty($validated['assigned_to']) ? 'user' : 'region',
                'comments' => $validated['comments'] ?? null,
                'created_by' => auth()->id()
            ];

            $asset = Asset::create($assetData);

            // Create description for history
            $deviceDescription = $validated['brand'] && $validated['model'] 
                ? "{$validated['brand']} {$validated['model']}" 
                : $category->name;

            // Log assignment history
            $assigneeName = !empty($validated['assigned_to']) 
                ? "user: " . User::find($validated['assigned_to'])->name 
                : "region: {$region->name}";

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

    return redirect()->back()->with('success', "Successfully created and assigned {$createdCount} asset(s) to {$region->name}.");
}
    /**
     * Helper methods for asset generation
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

    private function generateSerialNumber()
    {
        do {
            $serial = 'SN-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(3));
        } while (Asset::where('serial_number', $serial)->exists());

        return $serial;
    }
}