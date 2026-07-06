<?php

namespace App\Http\Controllers;

use App\Models\ObsoleteAsset;
use Illuminate\Http\Request;

class ObsoleteAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = ObsoleteAsset::query();
        
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
            $query->where('region_id', $user->region_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $obsoleteAssets = $query->orderBy('date_obsolete', 'desc')->paginate(10);
        
        // Get unique categories for filter
        $categories = ObsoleteAsset::select('category')->distinct()->whereNotNull('category')->pluck('category');

        return view('obsolete.index', compact('obsoleteAssets', 'categories'));
    }

    public function create()
    {
        $regions = \App\Models\Region::all();
        $courts = \App\Models\Court::active()->get();
        $offices = \App\Models\Office::all();
        $users = \App\Models\User::active()->get();

        return view('obsolete.create', compact('regions', 'courts', 'offices', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'date_acquired' => 'nullable|date',
            'date_obsolete' => 'required|date',
            'reason' => 'required|string',
            'disposal_method' => 'nullable|string|max:255',
            'reported_by_name' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'court_id' => 'nullable|exists:courts,id',
            'office_id' => 'nullable|exists:offices,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'target_type' => 'nullable|in:court,office,user',
        ]);

        $validated['user_id'] = auth()->id();
        if (empty($validated['reported_by_name'])) {
            $validated['reported_by_name'] = auth()->user()->name;
        }

        if (auth()->user()->region_id && auth()->user()->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin')) {
             $validated['region_id'] = auth()->user()->region_id;
        }

        ObsoleteAsset::create($validated);

        return redirect()->route('obsolete-assets.index')
            ->with('success', 'Obsolete asset recorded successfully.');
    }

    public function show(ObsoleteAsset $obsoleteAsset)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $obsoleteAsset->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to obsolete asset in another region.');
        }
        return view('obsolete.show', compact('obsoleteAsset'));
    }

    public function edit(ObsoleteAsset $obsoleteAsset)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $obsoleteAsset->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to obsolete asset in another region.');
        }
        return view('obsolete.edit', compact('obsoleteAsset'));
    }

    public function update(Request $request, ObsoleteAsset $obsoleteAsset)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && !auth()->user()->hasAssignedRole('super_admin') && $obsoleteAsset->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to obsolete asset in another region.');
        }
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'date_acquired' => 'nullable|date',
            'date_obsolete' => 'required|date',
            'reason' => 'required|string',
            'disposal_method' => 'nullable|string|max:255',
            'reported_by_name' => 'nullable|string|max:255',
        ]);

        $obsoleteAsset->update($validated);

        return redirect()->route('obsolete-assets.index')->with('success', 'Obsolete asset updated successfully.');
    }

    public function destroy(ObsoleteAsset $obsoleteAsset)
    {
        $user = auth()->user();
        if ($user->region_id && $user->hasRole('admin') && $obsoleteAsset->region_id !== $user->region_id) {
            abort(403, 'Unauthorized access to obsolete asset in another region.');
        }

        $obsoleteAsset->delete();

        return redirect()->route('obsolete-assets.index')->with('success', 'Obsolete asset deleted successfully.');
    }
}
