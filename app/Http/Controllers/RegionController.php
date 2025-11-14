<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount(['courts', 'locations', 'assets', 'users'])
            ->orderBy('name')
            ->paginate(20);

        return view('regions.index', compact('regions'));
    }

    public function create()
    {
        return view('regions.create');
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

        Region::create($validated);

        return redirect()->route('regions.index')->with('success', 'Region created successfully.');
    }

    public function edit(Region $region)
    {
        $region->load(['courts', 'locations', 'assets', 'users']);
        return view('regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
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
}