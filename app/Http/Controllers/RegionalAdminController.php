<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Region;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RegionalAdminController extends Controller
{
    // List all Regional Administrators
    public function index()
    {
        // Fetch users with 'admin' role (Regional ICT Administrator)
        $query = User::query()->role('admin')->with('region', 'assignedRole');

        if (auth()->user()->region_id) {
            $query->where('region_id', auth()->user()->region_id);
        }

        $admins = $query->get();
        return view('admin.regional_admins.index', compact('admins'));
    }

    // Show form to create a new Regional Administrator
    public function create()
    {
        $regions = Region::all();
        // Exclude permissions unrelated to regional tasks if needed, or list all
        $permissions = Permission::all(); 
        return view('admin.regional_admins.create', compact('regions', 'permissions'));
    }

    // Store a new Regional Administrator
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',
            'password' => 'required|string|min:8|confirmed',
            'permissions' => 'array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'region_id' => $request->region_id,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        $role = \App\Models\Role::where('name', 'admin')->first();
        if ($role) {
             $user->assignRole($role->name);
             $user->update(['role_id' => $role->id]);
        } else {
            // Fallback or create? For now, let's try to assign it anyway via Spatie
            $user->assignRole('admin'); 
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('regional-admins.index')
            ->with('success', 'Regional ICT Administrator created successfully.');
    }

    // Show details of a Regional Administrator including Audit Logs
    public function show($id)
    {
        $admin = User::with(['region', 'assignedRole', 'permissions'])->findOrFail($id);
        
        // Fetch audit logs (AssetHistory) performed by this user
        $activities = AssetHistory::with('asset')
            ->where('performed_by', $admin->id)
            ->latest('performed_at')
            ->paginate(20);

        return view('admin.regional_admins.show', compact('admin', 'activities'));
    }
    
    // Edit Permissions
    public function edit(User $regionalAdmin)
    {
         $regions = Region::all();
         $permissions = Permission::all();
         return view('admin.regional_admins.edit', compact('regionalAdmin', 'regions', 'permissions'));
    }

    // Update Regional Admin
    public function update(Request $request, User $regionalAdmin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $regionalAdmin->id,
            'region_id' => 'required',
            'permissions' => 'array'
        ]);

        $regionalAdmin->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'region_id' => $request->region_id,
        ]);
        
        if ($request->filled('password')) {
            $regionalAdmin->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('permissions')) {
            $regionalAdmin->syncPermissions($request->permissions);
        }

        return redirect()->route('regional-admins.show', $regionalAdmin)
            ->with('success', 'Regional ICT Administrator permissions updated.');
    }
    
    public function destroy(User $regionalAdmin)
    {
        $regionalAdmin->delete();
        return redirect()->route('regional-admins.index')
            ->with('success', 'Regional ICT Administrator removed.');
    }
}
