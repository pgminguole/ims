<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Region;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CourtsImport;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $query = Court::with(['region', 'location']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('type', 'like', "%{$request->search}%");
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $courts = $query->latest()->paginate(20);
        $regions = Region::all();

        // Statistics
        $totalCourts = Court::count();
        $activeCourts = Court::where('is_active', true)->count();
        $highCourts = Court::where('type', 'high_court')->count();
        $districtCourts = Court::where('type', 'district_court')->count();

        return view('courts.index', compact('courts', 'regions', 'totalCourts', 'activeCourts', 'highCourts', 'districtCourts'));
    }

    public function create()
    {
        $regions = Region::all();
        $judges = User::where('access_type', 'judge')->active()->get();
        $registryOfficers = User::where('access_type', 'registry')->active()->get();
        
        return view('courts.create', compact('regions', 'judges', 'registryOfficers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courts',
            'type' => 'required|in:high_court,district_court,magistrate_court,special_court',
            'region_id' => 'required|exists:regions,id',
            'location_id' => 'nullable|exists:locations,id',
            'address' => 'required|string',
            'presiding_judge' => 'nullable|exists:users,id',
            'registry_officer' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        Court::create($validated);

        return redirect()->route('courts.index')->with('success', 'Court created successfully.');
    }

    public function show(Court $court)
    {
        $court->load(['region', 'location', 'users', 'assets']);
        return view('courts.show', compact('court'));
    }

    public function edit(Court $court)
    {
        $regions = Region::all();
        $judges = User::where('access_type', 'judge')->active()->get();
        $registryOfficers = User::where('access_type', 'registry')->active()->get();
        
        return view('courts.edit', compact('court', 'regions', 'judges', 'registryOfficers'));
    }

    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courts,code,' . $court->id,
            'type' => 'required|in:high_court,district_court,magistrate_court,special_court',
            'region_id' => 'required|exists:regions,id',
            'location_id' => 'nullable|exists:locations,id',
            'address' => 'required|string',
            'presiding_judge' => 'nullable|exists:users,id',
            'registry_officer' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ]);

        $court->update($validated);

        return redirect()->route('courts.show', $court)->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        $court->delete();
        return redirect()->route('courts.index')->with('success', 'Court deleted successfully.');
    }


public function importForm()
{
    return view('courts.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
    ]);

    try {
        $import = new \App\Imports\CourtsImport();
        Excel::import($import, $request->file('file'));
        
        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();
        
        if (count($errors) > 0) {
            return redirect()
                ->route('courts')
                ->with('success', "{$successCount} courts imported successfully.")
                ->with('errors', $errors);
        }
        
        return redirect()
            ->route('courts')
            ->with('success', "{$successCount} courts imported successfully.");
            
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $errors = [];
        
        foreach ($failures as $failure) {
            $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        
        return back()
            ->with('error', 'Validation errors occurred during import.')
            ->with('errors', $errors);
            
    } catch (\Exception $e) {
        return back()->with('error', 'Error importing courts: ' . $e->getMessage());
    }
}

public function downloadTemplate()
{
    $headers = [
        'name',
        'code',
        'type',
        'region',
        'location',
        'address',
        'presiding_judge',
        'registry_officer',
        'is_active'
    ];
    
    $sampleData = [
        [
            'name' => 'Accra High Court',
            'code' => 'HC-ACC-001',
            'type' => 'high_court',
            'region' => 'Greater Accra',
            'location' => 'Accra',
            'address' => 'High Street, Accra',
            'presiding_judge' => 'judge@example.com',
            'registry_officer' => 'registry@example.com',
            'is_active' => 'yes'
        ],
        [
            'name' => 'Kumasi District Court',
            'code' => 'DC-KSI-001',
            'type' => 'district_court',
            'region' => 'Ashanti',
            'location' => 'Kumasi',
            'address' => 'Prempeh II Street, Kumasi',
            'presiding_judge' => 'John Doe',
            'registry_officer' => 'Jane Smith',
            'is_active' => '1'
        ]
    ];
    
    return Excel::download(new \App\Exports\CourtsTemplateExport($headers, $sampleData), 'courts_import_template.xlsx');
}
}