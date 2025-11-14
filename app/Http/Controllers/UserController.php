<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Region;
use App\Models\Court;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Models\Role as UserRole;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
  public function index(Request $request)
{
    $query = User::with(['location', 'court', 'role']); // Changed 'roles' to 'role'

    if ($request->filled('search')) {
        $query->where('first_name', 'like', "%{$request->search}%")
              ->orWhere('last_name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%")
              ->orWhere('username', 'like', "%{$request->search}%");
    }

    // Filter by custom role instead of Spatie role
    if ($request->filled('role')) {
        $query->whereHas('role', function ($q) use ($request) {
            $q->where('name', $request->role);
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $users = $query->latest()->paginate(20);

    // Statistics based on custom roles
    $totalUsers = User::count();
    $activeUsers = User::where('status', 'active')->count();
    
    // Get role distribution from your custom roles
    $roleDistribution = User::with('role')
        ->get()
        ->groupBy('role.name')
        ->map(function ($users, $roleName) {
            return [
                'count' => $users->count(),
                'percentage' => round(($users->count() / User::count()) * 100, 1)
            ];
        });

    // Get top roles for display
    $topRoles = $roleDistribution->sortByDesc('count')->take(4);

    return view('users.index', compact('users', 'totalUsers', 'activeUsers', 'roleDistribution', 'topRoles'));
}
    public function create()
    {
        $regions = Region::all();
        $courts = Court::all();
       $roles = UserRole::all(); 
           $locations=Location::all();
        return view('users.create', compact('regions', 'courts', 'roles','locations'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'username' => 'nullable|unique:users',
            'phone' => 'nullable|string|max:20',
            'create_password' => 'required|in:0,1',
            'password' => 'nullable|min:8|confirmed',
            'court_id' => 'nullable|exists:courts,id',
            'location_id' => 'nullable|exists:locations,id',
            'role_id' => 'required|exists:user_roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'phone_verified_at' => 'nullable|date',
            'approved_at' => 'nullable|date',
            'is_approved' => 'nullable|boolean',
            'block' => 'nullable|boolean',
            'require_password_reset' => 'nullable|boolean',
            'is_expire' => 'nullable|boolean',
            'expire_date' => 'nullable|date',
            'invited_by' => 'nullable|exists:users,id',
            'invited_date' => 'nullable|date',
            'accepted' => 'nullable|boolean',
            'accepted_date' => 'nullable|date',
            'is_online' => 'nullable|boolean',
            'login_at' => 'nullable|date',
            'logout_at' => 'nullable|date',
            'registry_id' => 'nullable|exists:users,id',
        ];

        // If create_password is 1, make password required
        if ($request->create_password == '1') {
            $validationRules['password'] = 'required|min:8|confirmed';
        }

        $validated = $request->validate($validationRules);

        // Handle password
        if ($request->create_password == '1' && $request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Set password to null if not creating one
            $validated['password'] = null;
        }

        $validated['slug'] = Str::slug($validated['first_name'] . '-' . $validated['last_name']);
        
        // Set default values if not provided
        $validated['is_approved'] = $validated['is_approved'] ?? true;
        $validated['approved_at'] = $validated['approved_at'] ?? now();
        $validated['status'] = $validated['status'] ?? 'active';

        $user = User::create($validated);
        
        // Assign role
      //  $user->assignRole($request->role);

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['court.region', 'location', 'assignedAssets', 'registry', 'roles']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $regions = Region::all();
        $courts = Court::all();
        $roles = UserRole::all();  
        $allUsers = User::where('id', '!=', $user->id)->get();
           $locations=Location::all();
        return view('users.edit', compact('user', 'regions', 'courts', 'roles', 'allUsers','locations'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'username' => 'nullable|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'court_id' => 'nullable|exists:courts,id',
            'location_id' => 'nullable|exists:locations,id',
            'role_id' => 'required|exists:user_roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'phone_verified_at' => 'nullable|date',
            'approved_at' => 'nullable|date',
            'is_approved' => 'nullable|boolean',
            'block' => 'nullable|boolean',
            'require_password_reset' => 'nullable|boolean',
            'is_expire' => 'nullable|boolean',
            'expire_date' => 'nullable|date',
            'invited_by' => 'nullable|exists:users,id',
            'invited_date' => 'nullable|date',
            'accepted' => 'nullable|boolean',
            'accepted_date' => 'nullable|date',
            'is_online' => 'nullable|boolean',
            'login_at' => 'nullable|date',
            'logout_at' => 'nullable|date',
            'registry_id' => 'nullable|exists:users,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles([$request->role]);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function importForm()
    {
        return view('users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        try {
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));
            
            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();
            
            if (count($errors) > 0) {
                return redirect()
                    ->route('users')
                    ->with('success', "{$successCount} users imported successfully.")
                    ->with('errors', $errors);
            }
            
            return redirect()
                ->route('users')
                ->with('success', "{$successCount} users imported successfully.");
                
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
            return back()->with('error', 'Error importing users: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'email',
            'username',
            'phone',
            'roles',
            'court',
            'location',
            'status',
            'is_approved',
            'password',
            'registry_officer',
            'block',
            'require_password_reset'
        ];
        
        $sampleData = [
            [
                'first_name' => 'John',
                'last_name' => 'Mensah',
                'email' => 'john.mensah@court.gov.gh',
                'username' => 'jmensah',
                'phone' => '0244123456',
                'roles' => 'Judge,Case Manager',
                'court' => 'HC-ACC-001',
                'location' => 'Accra',
                'status' => 'active',
                'is_approved' => 'yes',
                'password' => 'Password123!',
                'registry_officer' => 'registry@court.gov.gh',
                'block' => 'no',
                'require_password_reset' => 'yes'
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Osei',
                'email' => 'sarah.osei@court.gov.gh',
                'username' => 'sosei',
                'phone' => '0201234567',
                'roles' => 'Registry Officer',
                'court' => 'Kumasi District Court',
                'location' => 'Kumasi',
                'status' => 'active',
                'is_approved' => '1',
                'password' => '',
                'registry_officer' => '',
                'block' => '0',
                'require_password_reset' => '1'
            ],
            [
                'first_name' => 'Kwame',
                'last_name' => 'Asante',
                'email' => 'kwame.asante@court.gov.gh',
                'username' => 'kasante',
                'phone' => '0554567890',
                'roles' => 'Court Clerk,Document Manager',
                'court' => 'DC-KSI-001',
                'location' => 'Kumasi',
                'status' => 'active',
                'is_approved' => 'yes',
                'password' => 'SecurePass2024!',
                'registry_officer' => 'Sarah Osei',
                'block' => 'no',
                'require_password_reset' => 'no'
            ]
        ];
        
        return Excel::download(
            new \App\Exports\UsersTemplateExport($headers, $sampleData), 
            'users_import_template.xlsx'
        );
    }

    public function exportJudges()
    {
        return Excel::download(new UsersExport, 'judges.xlsx');
    }
}