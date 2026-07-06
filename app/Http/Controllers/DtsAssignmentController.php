<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Dts;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DtsAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $query = Dts::with(['court', 'court.region']);

        if ($isRegionalAdmin) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('region_id', $user->region_id);
            });
        }

        // Search and Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('court', function($courtQuery) use ($search) {
                      $courtQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        if ($request->filled('region_id')) {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        $dtsAssignments = $query->latest()->paginate(20);
        
        $courtsQuery = Court::active();
        $regionsQuery = Region::query();

        if ($isRegionalAdmin) {
            $courtsQuery->where('region_id', $user->region_id);
            $regionsQuery->where('id', $user->region_id);
        }

        $courts = $courtsQuery->get();
        $regions = $regionsQuery->get();

        return view('dts-assignments.index', compact('dtsAssignments', 'courts', 'regions'));
    }


public function updateDate(Request $request)
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

    public function create()
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $courtsQuery = Court::active()->with(['location', 'region']);
        $regionsQuery = Region::query();

        if ($isRegionalAdmin) {
            $courtsQuery->where('region_id', $user->region_id);
            $regionsQuery->where('id', $user->region_id);
        }

        $courts = $courtsQuery->get();
        $regions = $regionsQuery->get();
        
        return view('dts-assignments.create', compact('courts', 'regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
             'assigned_date' => 'required|date',
            'dts_name' => 'required|string|max:255',
            'monitors_count' => 'required|integer|min:0',
            'splitters_count' => 'required|integer|min:0',
            'hdmi_short_cables_count' => 'required|integer|min:0',
            'hdmi_long_cables_count' => 'required|integer|min:0',
            'extension_boards_count' => 'required|integer|min:0',
            'trucking_count' => 'required|integer|min:0',
            'sony_recorders_count' => 'required|integer|min:0',
            'assign_as_primary' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $court = Court::find($validated['court_id']);
                
                $dts = Dts::create([
                    'court_id' => $court->id,
                    'name' => "{$court->name} - {$validated['dts_name']}",
                      'date_assigned' => $validated['assigned_date'],
                    'monitors_count' => $validated['monitors_count'],
                    'splitters_count' => $validated['splitters_count'],
                    'hdmi_short_cables_count' => $validated['hdmi_short_cables_count'],
                    'hdmi_long_cables_count' => $validated['hdmi_long_cables_count'],
                    'extension_boards_count' => $validated['extension_boards_count'],
                    'trucking_count' => $validated['trucking_count'],
                    'sony_recorders_count' => $validated['sony_recorders_count'],
                    'notes' => $validated['notes'] ?? null,
                    'is_available' => false,
                ]);

                // Assign as primary DTS if requested
                // if (isset($validated['assign_as_primary']) && $validated['assign_as_primary']) {
                //     $court->update(['dts_id' => $dts->id]);
                // }
            });

            return redirect()->route('dts-assignments.index')
                ->with('success', 'DTS system assigned successfully.');

        } catch (\Exception $e) {
            Log::error('DTS Assignment Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to assign DTS system. Please try again.'])->withInput();
        }
    }

    public function bulkCreate()
    {
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $courtsQuery = Court::active()->with(['location', 'region']);
        $regionsQuery = Region::query();

        if ($isRegionalAdmin) {
            $courtsQuery->where('region_id', $user->region_id);
            $regionsQuery->where('id', $user->region_id);
        }

        $courts = $courtsQuery->get();
        $regions = $regionsQuery->get();
        
        return view('dts-assignments.bulk-create', compact('courts', 'regions'));
    }
public function storeBulk(Request $request)
{
    $validated = $request->validate([
         'assigned_date' => 'required|date',
        'targets' => 'required|array',
        'targets.*' => 'required|exists:courts,id',
        'dts_name' => 'required|string|max:255',
        'monitors_count' => 'required|integer|min:0',
        'splitters_count' => 'required|integer|min:0',
        'hdmi_short_cables_count' => 'required|integer|min:0',
        'hdmi_long_cables_count' => 'required|integer|min:0',
        'extension_boards_count' => 'required|integer|min:0',
        'trucking_count' => 'required|integer|min:0',
        'sony_recorders_count' => 'required|integer|min:0',
        'assign_as_primary' => 'nullable|boolean',
        'notes' => 'nullable|string'
    ]);

    $createdCount = 0;

    DB::transaction(function () use ($validated, &$createdCount) {
        foreach ($validated['targets'] as $courtId) {
            $court = Court::find($courtId);
            if ($court) {
            

                $dts = Dts::create([
                    'court_id' => $court->id,
                    'name' => "{$court->name} - {$validated['dts_name']}",
                    'monitors_count' => $validated['monitors_count'],
                    'splitters_count' => $validated['splitters_count'],
                    'hdmi_short_cables_count' => $validated['hdmi_short_cables_count'],
                    'hdmi_long_cables_count' => $validated['hdmi_long_cables_count'],
                    'extension_boards_count' => $validated['extension_boards_count'],
                    'trucking_count' => $validated['trucking_count'],
                    'sony_recorders_count' => $validated['sony_recorders_count'],
                    'notes' => $validated['notes'] ?? null,
                    'is_available' => false,
                    'date_assigned' => $validated['assigned_date'],
                    
                ]);

                // Assign as primary if requested
                if (isset($validated['assign_as_primary']) && $validated['assign_as_primary']) {
                    $court->update(['dts_id' => $dts->id]);
                }

                $createdCount++;
            }
        }
    });

    return redirect()->route('dts-assignments.index')
        ->with('success', "Successfully created ");
}
    public function show(Dts $dtsAssignment)
    {
        $dtsAssignment->load(['court', 'court.region']);
        return view('dts-assignments.show', compact('dtsAssignment'));
    }

    public function edit(Dts $dtsAssignment)
    {
        $courts = Court::active()->with(['location', 'region'])->get();
        return view('dts-assignments.edit', compact('dtsAssignment', 'courts'));
    }

    public function update(Request $request, Dts $dtsAssignment)
    {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'name' => 'required|string|max:255',
            'monitors_count' => 'required|integer|min:0',
            'splitters_count' => 'required|integer|min:0',
            'hdmi_short_cables_count' => 'required|integer|min:0',
            'hdmi_long_cables_count' => 'required|integer|min:0',
            'extension_boards_count' => 'required|integer|min:0',
            'trucking_count' => 'required|integer|min:0',
            'sony_recorders_count' => 'required|integer|min:0',
            'is_available' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        $dtsAssignment->update($validated);

        return redirect()->route('dts-assignments.index')
            ->with('success', 'DTS system updated successfully.');
    }

    public function destroy(Dts $dtsAssignment)
    {
        // Check if this DTS is set as primary for any court
        $courtUsingAsPrimary = Court::where('dts_id', $dtsAssignment->id)->first();
        if ($courtUsingAsPrimary) {
            return back()->withErrors(['error' => 'This DTS system is set as primary for a court. Please reassign before deleting.']);
        }

        $dtsAssignment->delete();

        return redirect()->route('dts-assignments.index')
            ->with('success', 'DTS system deleted successfully.');
    }
}