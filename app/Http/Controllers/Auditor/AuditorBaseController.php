<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditorBaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && 
                auth()->user()->role->name !== 'auditor' && 
                auth()->user()->role->name !== 'super_admin') {
                abort(403, 'Unauthorized access for auditors only.');
            }
            return $next($request);
        });
    }

    protected function applyFilters($query, Request $request, $filters = [])
    {
        // Year filter
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Duration filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Region filter (Enforce if Auditor has region)
        if (auth()->check() && auth()->user()->region_id && auth()->user()->role->name === 'auditor') {
            $query->where('region_id', auth()->user()->region_id);
        } elseif ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Court type filter
        if ($request->filled('court_type')) {
            $query->where('type', $request->court_type);
        }

        // User type filter
        if ($request->filled('user_type') && method_exists($query->getModel(), 'role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->user_type);
            });
        }

        // Apply custom filters
        foreach ($filters as $filter => $column) {
            if ($request->filled($filter)) {
                $query->where($column, $request->$filter);
            }
        }

        return $query;
    }

    protected function getFilterData()
    {
        return [
            'years' => \App\Models\Asset::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year'),
            'regions' => \App\Models\Region::active()->get(),
            'courtTypes' => \App\Models\Court::distinct()->pluck('type'),
            'userTypes' => \App\Models\Role::pluck('name')
        ];
    }
}