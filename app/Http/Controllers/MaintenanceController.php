<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceLog::with(['asset', 'performedBy']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('next_maintenance_date', '>=', now())
                      ->where('next_maintenance_date', '<=', now()->addDays(30));
            } elseif ($request->status === 'overdue') {
                $query->where('next_maintenance_date', '<', now());
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('maintenance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('maintenance_date', '<=', $request->date_to);
        }

        $maintenanceLogs = $query->latest('maintenance_date')->paginate(20);
        
        // Statistics
        $totalMaintenance = MaintenanceLog::count();
        $upcomingMaintenance = MaintenanceLog::where('next_maintenance_date', '<=', now()->addDays(30))->count();
        $overdueMaintenance = MaintenanceLog::where('next_maintenance_date', '<', now())->count();

        return view('maintenance.index', compact(
            'maintenanceLogs', 
            'totalMaintenance',
            'upcomingMaintenance',
            'overdueMaintenance'
        ));
    }

    public function scheduled(Request $request)
    {
        $query = Asset::whereNotNull('next_maintenance');

        if ($request->filled('status')) {
            if ($request->status === 'due') {
                $query->where('next_maintenance', '<=', now());
            } elseif ($request->status === 'upcoming') {
                $query->whereBetween('next_maintenance', [now(), now()->addDays(30)]);
            }
        }

        $scheduledAssets = $query->with(['category', 'region'])->paginate(20);

        $dueCount = Asset::where('next_maintenance', '<=', now())->count();
        $upcomingCount = Asset::whereBetween('next_maintenance', [now(), now()->addDays(30)])->count();

        return view('maintenance.scheduled', compact(
            'scheduledAssets',
            'dueCount',
            'upcomingCount'
        ));
    }

    public function create()
    {
        $assets = Asset::where('status', '!=', 'retired')->get();
        $users = User::active()->get();
        
        return view('maintenance.create', compact('assets', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_date' => 'required|date',
            'type' => 'required|in:preventive,corrective,routine,emergency',
            'description' => 'required|string',
            'actions_taken' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'technician' => 'required|string|max:255',
            'next_maintenance_date' => 'nullable|date|after:maintenance_date',
            'performed_by' => 'required|exists:users,id'
        ]);

        // Create maintenance log
        $maintenanceLog = MaintenanceLog::create($validated);

        // Update asset's maintenance information
        $asset = Asset::find($validated['asset_id']);
        $asset->update([
            'last_maintenance' => $validated['maintenance_date'],
            'next_maintenance' => $validated['next_maintenance_date'],
            'maintenance_notes' => $validated['description']
        ]);

        // Log in asset history
        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'maintenance',
            'description' => "Maintenance performed: {$validated['type']} - {$validated['description']}",
            'performed_by' => auth()->id(),
            'performed_at' => now()
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance log created successfully.');
    }

    public function show(MaintenanceLog $maintenance)
    {
        $maintenance->load(['asset.category', 'asset.region', 'performedBy']);
        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(MaintenanceLog $maintenance)
    {
        $assets = Asset::where('status', '!=', 'retired')->get();
        $users = User::active()->get();
        
        return view('maintenance.edit', compact('maintenance', 'assets', 'users'));
    }

    public function update(Request $request, MaintenanceLog $maintenance)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_date' => 'required|date',
            'type' => 'required|in:preventive,corrective,routine,emergency',
            'description' => 'required|string',
            'actions_taken' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'technician' => 'required|string|max:255',
            'next_maintenance_date' => 'nullable|date|after:maintenance_date',
            'performed_by' => 'required|exists:users,id'
        ]);

        $maintenance->update($validated);

        // Update asset's maintenance information if this is the most recent maintenance
        $latestMaintenance = MaintenanceLog::where('asset_id', $validated['asset_id'])
            ->latest('maintenance_date')
            ->first();

        if ($latestMaintenance->id === $maintenance->id) {
            $asset = Asset::find($validated['asset_id']);
            $asset->update([
                'last_maintenance' => $validated['maintenance_date'],
                'next_maintenance' => $validated['next_maintenance_date']
            ]);
        }

        return redirect()->route('maintenance.show', $maintenance)->with('success', 'Maintenance log updated successfully.');
    }

    public function destroy(MaintenanceLog $maintenance)
    {
        $assetId = $maintenance->asset_id;
        $maintenance->delete();

        // Update asset's maintenance information if this was the most recent maintenance
        $latestMaintenance = MaintenanceLog::where('asset_id', $assetId)
            ->latest('maintenance_date')
            ->first();

        $asset = Asset::find($assetId);
        if ($latestMaintenance) {
            $asset->update([
                'last_maintenance' => $latestMaintenance->maintenance_date,
                'next_maintenance' => $latestMaintenance->next_maintenance_date
            ]);
        } else {
            $asset->update([
                'last_maintenance' => null,
                'next_maintenance' => null
            ]);
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance log deleted successfully.');
    }
}