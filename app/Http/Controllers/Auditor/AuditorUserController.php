<?php

namespace App\Http\Controllers\Auditor;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetHistory;

class AuditorUserController extends AuditorBaseController
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'region', 'court', 'office'])
            ->withCount(['assets as assigned_assets_count' => function($query) {
                $query->where('assigned_type', 'user');
            }]);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                 
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $filterData = $this->getFilterData();

        // Statistics
        $totalJudges = User::whereHas('role', function($q) { $q->where('name', 'judge'); })->count();
        $totalStaff = User::whereHas('role', function($q) { $q->where('name', 'staff'); })->count();
        $totalDirectors = User::whereHas('role', function($q) { $q->where('name', 'director'); })->count();
        $usersWithAssets = User::has('assets')->count();
        $regionsCount = \App\Models\Region::active()->count();
        $courtsCount = \App\Models\Court::active()->count();

        return view('auditor.users.index', compact(
            'users', 'filterData', 'totalJudges', 'totalStaff', 
            'totalDirectors', 'usersWithAssets', 'regionsCount', 'courtsCount'
        ));
    }

    public function show(User $user)
    {
        $user->load(['role', 'region', 'court', 'office']);
        
        $assignedAssets = Asset::where('assigned_to', $user->id)
            ->where('assigned_type', 'user')
            ->with(['category', 'region', 'court'])
            ->get();

        $assetHistory = AssetHistory::where('performed_by', $user->id)
            ->orWhereHas('asset', function($q) use ($user) {
                $q->where('assigned_to', $user->id)->where('assigned_type', 'user');
            })
            ->with(['asset', 'performedBy'])
            ->latest()
            ->take(20)
            ->get();

        return view('auditor.users.show', compact('user', 'assignedAssets', 'assetHistory'));
    }
}