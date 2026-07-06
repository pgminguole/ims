<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_locations');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $query = Location::with(['region', 'assets', 'courts']);

        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            $query->where('region_id', $user->region_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%");
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $locations = $query->orderBy('name')->paginate(20)->withQueryString();
        
        $statsQuery = clone $query;
        $totalLocations = (clone $statsQuery)->count();
        $activeLocations = (clone $statsQuery)->where('is_active', true)->count();
        $totalBuildings = (clone $statsQuery)->distinct('building')->count('building');
        // Calculate total assets efficiently over the filtered query
        $totalAssets = (clone $statsQuery)->withCount('assets')->get()->sum('assets_count');
        
        $regionsQuery = Region::query();
        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
        }
        $regions = $regionsQuery->get();

        return view('locations.index', compact('locations', 'regions', 'totalLocations', 'activeLocations', 'totalBuildings', 'totalAssets'));
    }

    public function create()
    {
        $this->authorize('create_locations');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $regionsQuery = Region::where('is_active', true)->orderBy('name');
        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
        }
        $regions = $regionsQuery->get();
        return view('locations.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_locations');
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'region_id' => 'required|exists:regions,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !$user->hasAssignedRole('super_admin')) {
            $validated['region_id'] = $user->region_id;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();

        Location::create($validated);

        return redirect()->route('locations')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        $this->authorize('edit_locations');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        if ($isRegionalAdmin && $location->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to location in another region.');
        }

        $regionsQuery = Region::where('is_active', true)->orderBy('name');
        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
        }
        $regions = $regionsQuery->get();
        return view('locations.edit', compact('location', 'regions'));
    }

    public function update(Request $request, Location $location)
    {
        $this->authorize('edit_locations');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        if ($isRegionalAdmin && $location->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to location in another region.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations')->ignore($location->id)
            ],
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'region_id' => 'required|exists:regions,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($user->region_id && $user->hasRole('admin') && !$user->hasAssignedRole('super_admin')) {
             if (isset($validated['region_id']) && $validated['region_id'] != $user->region_id) {
                 abort(403, 'Unauthorized access to another region.');
             }
             $validated['region_id'] = $user->region_id;
        }

        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        return redirect()->route('locations')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete_locations');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        if ($isRegionalAdmin && $location->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to location in another region.');
        }

        // Check if location has assets
        if ($location->assets()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete location with associated assets.']);
        }

        // Check if location has courts
        if ($location->courts()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete location with associated courts.']);
        }

        $location->delete();

        return redirect()->route('locations')->with('success', 'Location deleted successfully.');
    }

    public function fetchLocations(Request $request)
    {
        $regionId = $request->region_id;
        
        if (!$regionId && $request->route('region')) {
            $regionId = $request->route('region');
        }

        $locations = Location::where('region_id', $regionId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'locations' => $locations
        ]);
    }
}