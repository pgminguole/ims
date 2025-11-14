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
        $query = Location::with(['region', 'assets', 'courts']);

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

        $locations = $query->orderBy('name')->paginate(20);
        $regions = Region::all();

        return view('locations.index', compact('locations', 'regions'));
    }

    public function create()
    {
        $regions = Region::where('is_active', true)->orderBy('name')->get();
        return view('locations.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'region_id' => 'required|exists:regions,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Location::create($validated);

        return redirect()->route('locations')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        $regions = Region::where('is_active', true)->orderBy('name')->get();
        return view('locations.edit', compact('location', 'regions'));
    }

    public function update(Request $request, Location $location)
    {
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

        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        return redirect()->route('locations')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
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
        $locations = Location::where('region_id', $request->region_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($locations);
    }
}