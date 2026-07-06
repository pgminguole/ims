<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Exports\CourtsExport;
use App\Exports\OfficesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // Users Export
    public function exportUsers()
    {
        $filters = request()->all();
        
        return Excel::download(new UsersExport($filters), 'users-' . date('Y-m-d') . '.xlsx');
    }

    // Courts Export
    public function exportCourts()
    {
        $filters = request()->all();
        
        return Excel::download(new CourtsExport($filters), 'courts-' . date('Y-m-d') . '.xlsx');
    }

    // Offices Export
    public function exportOffices()
    {
        $filters = request()->all();
        
        return Excel::download(new OfficesExport($filters), 'offices-' . date('Y-m-d') . '.xlsx');
    }

    // Bulk Export - All entities
    public function exportAll()
    {
        $filters = request()->all();
        $type = request('type', 'users');

        switch ($type) {
            case 'users':
                return Excel::download(new UsersExport($filters), 'users-' . date('Y-m-d') . '.xlsx');
            case 'courts':
                return Excel::download(new CourtsExport($filters), 'courts-' . date('Y-m-d') . '.xlsx');
            case 'offices':
                return Excel::download(new OfficesExport($filters), 'offices-' . date('Y-m-d') . '.xlsx');
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }
}