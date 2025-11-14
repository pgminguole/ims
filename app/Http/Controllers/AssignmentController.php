<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::where('status', 'assigned')
            ->whereNotNull('assigned_to')
            ->with(['category', 'region', 'court', 'assignedUser']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
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

        $assignments = $query->latest('assigned_date')->paginate(20);
        $users = User::active()->get();

        return view('assignments.index', compact('assignments', 'users'));
    }

    public function create()
    {
        $availableAssets = Asset::available()
            ->with(['category', 'region'])
            ->get();
            
        $users = User::active()->get();
        
        return view('assignments.create', compact('availableAssets', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'assigned_to' => 'required|exists:users,id',
            'assigned_type' => 'required|in:judge,staff,department,court',
            'assigned_date' => 'required|date',
            'comments' => 'nullable|string'
        ]);

        $asset = Asset::find($validated['asset_id']);
        $user = User::find($validated['assigned_to']);

        // Check if asset is available
        if ($asset->status !== 'available') {
            return back()->withErrors(['asset_id' => 'Selected asset is not available for assignment.'])->withInput();
        }

        DB::transaction(function () use ($asset, $validated, $user) {
            // Update asset assignment
            $asset->update([
                'assigned_to' => $validated['assigned_to'],
                'assigned_type' => $validated['assigned_type'],
                'assigned_date' => $validated['assigned_date'],
                'status' => 'assigned'
            ]);

            // Log assignment history
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'assigned',
                'description' => "Asset assigned to {$validated['assigned_type']}: {$user->full_name}. Comments: {$validated['comments']}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        });

        return redirect()->route('assignments.index')->with('success', 'Asset assigned successfully.');
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

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
            'assigned_to' => 'required|exists:users,id',
            'assigned_type' => 'required|in:judge,staff,department,court',
            'assigned_date' => 'required|date',
            'comments' => 'nullable|string'
        ]);

        $user = User::find($validated['assigned_to']);
        $assignedCount = 0;

        DB::transaction(function () use ($validated, $user, &$assignedCount) {
            foreach ($validated['asset_ids'] as $assetId) {
                $asset = Asset::find($assetId);
                
                // Only assign available assets
                if ($asset->status === 'available') {
                    $asset->update([
                        'assigned_to' => $validated['assigned_to'],
                        'assigned_type' => $validated['assigned_type'],
                        'assigned_date' => $validated['assigned_date'],
                        'status' => 'assigned'
                    ]);

                    AssetHistory::create([
                        'asset_id' => $asset->id,
                        'action' => 'assigned',
                        'description' => "Asset assigned to {$validated['assigned_type']}: {$user->full_name}. Comments: {$validated['comments']}",
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
            $oldAssignedTo = $asset->assignedUser ? $asset->assignedUser->full_name : 'Unknown';

            $asset->update([
                'returned_date' => $validated['returned_date'],
                'returned_reason' => $validated['returned_reason'],
                'returnee' => $validated['returnee'],
                'returned_to' => $validated['returned_to'],
                'condition' => $validated['condition'],
                'status' => 'available',
                'assigned_to' => null,
                'assigned_type' => null,
                'assigned_date' => null
            ]);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'returned',
                'description' => "Asset returned by {$validated['returnee']}. Reason: {$validated['returned_reason']}. Condition: {$validated['condition']}. Comments: {$validated['comments']}",
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        });

        return redirect()->route('assignments.index')->with('success', 'Asset returned successfully.');
    }
}