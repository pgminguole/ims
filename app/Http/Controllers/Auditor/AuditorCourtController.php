<?php

namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\Court;
use App\Models\Dts;

class AuditorCourtController extends AuditorBaseController
{
    public function index(Request $request)
    {
        $query = Court::with(['region', 'location'])
            ->withCount(['assets', 'users', 'dts']);

        // Apply filters
        $query = $this->applyFilters($query, $request, [
            'type' => 'type'
        ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $courts = $query->latest()->paginate(20);
        $filterData = $this->getFilterData();

        // Statistics
        $totalCourts = Court::count();
        $totalAssets = \App\Models\Asset::count();
        $totalUsers = \App\Models\User::count();
        $totalDts = Dts::count();
        $activeRegions = \App\Models\Region::active()->count();
        $courtTypes = Court::distinct()->pluck('type');

        return view('auditor.courts.index', compact(
            'courts', 'filterData', 'totalCourts', 'totalAssets',
            'totalUsers', 'totalDts', 'activeRegions', 'courtTypes'
        ));
    }

    public function show(Court $court)
    {
        $court->load([
            'region', 
            'location', 
            'presidingJudge', 
            'registryOfficer'
        ]);

        // Load device counts
        $court->loadCount([
            'assets',
            'users',
            'dts'
        ]);

        // Recent assets
        $recentAssets = $court->assets()
            ->with(['category', 'assignedUser', 'office'])
            ->latest()
            ->take(10)
            ->get();

        // DTS systems
        $dtsSystems = $court->dts()
            ->with(['monitorAsset', 'splitterAsset'])
            ->latest()
            ->get();

        // User statistics
        $judgesCount = $court->users()->whereHas('role', function($q) {
            $q->where('name', 'judge');
        })->count();

        $staffCount = $court->users()->whereHas('role', function($q) {
            $q->where('name', 'staff');
        })->count();

        $directorsCount = $court->users()->whereHas('role', function($q) {
            $q->where('name', 'director');
        })->count();

        $availableDtsCount = $dtsSystems->where('is_available', true)->count();
        $completeDtsCount = $dtsSystems->filter(function($dts) {
            return $dts->isComplete();
        })->count();

        return view('auditor.courts.show', compact(
            'court', 'recentAssets', 'dtsSystems', 'judgesCount',
            'staffCount', 'directorsCount', 'availableDtsCount', 'completeDtsCount'
        ));
    }
}