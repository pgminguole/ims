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
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Category;
use App\Models\AssetHistory;


class UserController extends Controller

{
    
    public function createAsset(Request $request, User $user)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'brand' => 'nullable|string|max:255',
        'model' => 'nullable|string|max:255',
        'quantity' => 'required|integer|min:1|max:50',
        'assigned_date' => 'required|date',
        'condition' => 'required|in:excellent,good,fair,poor',
        'warranty_months' => 'nullable|integer|min:0',
        'purchase_date' => 'nullable|date',
        'purchase_price' => 'nullable|numeric|min:0',
        'comments' => 'nullable|string',
        'record_type' => 'nullable|in:assignment,inventory'
    ]);

    $createdCount = 0;
    
    DB::transaction(function () use ($validated, $user, &$createdCount) {
        $category = Category::find($validated['category_id']);
        $currentUser = auth()->user();
        $isRegionalAdmin = $currentUser->region_id && $currentUser->hasRole('rao');
        
        // Generate unique asset tags for the entire batch
        $tags = Asset::generateNextTags($validated['category_id'], $validated['quantity']);

        for ($i = 0; $i < $validated['quantity']; $i++) {
            $assetTag = $tags[$i];
            
            // Generate asset name from category or model/brand
            if (!empty($validated['model'])) {
                $assetName = $validated['model'];
            } elseif (!empty($validated['brand'])) {
                $assetName = $validated['brand'] . ' ' . $category->name;
            } else {
                $assetName = $category->name;
            }
            
            $assetData = [
                'asset_name' => $assetName,
                'slug' => $this->generateAssetSlug($assetName . '-' . $assetTag),
                'asset_tag' => $assetTag,
                'serial_number' => $this->generateSerialNumber(),
                'category_id' => $validated['category_id'],
                'brand' => $validated['brand'] ?? null,
                'asset_id' => 'AST-' . strtoupper(uniqid()),
                'model' => $validated['model'] ?? null,
                'assigned_date' => $validated['assigned_date'],
                'condition' => $validated['condition'],
                'warranty_months' => $validated['warranty_months'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? null,
                'status' => 'assigned',
                'assigned_to' => $user->id,
                'assigned_type' => 'user',
                'comments' => $validated['comments'] ?? null,
                'created_by' => auth()->id(),
                'region_id' => $isRegionalAdmin ? $currentUser->region_id : ($validated['region_id'] ?? null),
                'court_id' => $user->court_id,
                'record_type' => $isRegionalAdmin ? 'inventory' : ($validated['record_type'] ?? 'assignment')
            ];

            $asset = Asset::create($assetData);

            // Create description for history
            $deviceDescription = ($validated['brand'] ?? null) && ($validated['model'] ?? null)
                ? "{$validated['brand']} {$validated['model']}" 
                : $category->name;

            // Log assignment history
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'assigned',
                'description' => "New {$deviceDescription} created and assigned to user: {$user->name}. Comments: " . ($validated['comments'] ?? 'None'),
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            $createdCount++;
        }
    });

    return redirect()->back()->with('success', "Successfully created and assigned {$createdCount} asset(s) to {$user->name}.");
}

private function generateAssetSlug($assetName)
{
    $baseSlug = Str::slug($assetName);
    $slug = $baseSlug;
    $counter = 1;

    while (Asset::where('slug', $slug)->exists()) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

private function generateSerialNumber()
{
    do {
        $serial = 'SN-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(3));
    } while (Asset::where('serial_number', $serial)->exists());

    return $serial;
}
    
    /**
     * Show the form to fix user roles
     */
    public function fixRolesForm(Request $request)
    {
        $this->authorize('edit_users');
        
        $roles = UserRole::orderBy('name')->get();
        $currentRole = $request->input('current_role', 'registry');
        
        $query = User::with(['role', 'location', 'court'])
            ->where('status', 'active');
            
        if ($currentRole) {
            $query->whereHas('role', function($q) use ($currentRole) {
                $q->where('name', $currentRole);
            });
        }
        
        $users = $query->orderBy('name')->get();
        
        return view('users.fix-roles', compact('users', 'roles', 'currentRole'));
    }

    /**
     * Process the bulk role update
     */
    public function processFixRoles(Request $request)
    {
        $this->authorize('edit_users');
        
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'new_role_id' => 'required|exists:user_roles,id'
        ]);
        
        $userIds = $request->input('user_ids');
        $newRoleId = $request->input('new_role_id');
        $newRole = UserRole::find($newRoleId);
        
        $count = 0;
        
        DB::transaction(function() use ($userIds, $newRoleId, $newRole, &$count) {
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->role_id = $newRoleId;
                    $user->save();
                    
                    // Sync Spatie roles
                    $user->syncRoles([$newRole->name]);
                    
                    $count++;
                }
            }
        });
        
        return redirect()->route('users.fix-roles', ['current_role' => $request->input('current_role')])
            ->with('success', "Successfully updated roles for {$count} users to {$newRole->name}.");
    }

    public function duplicates(Request $request)
    {
        // Get similarity threshold from request (default 85%)
        $threshold = $request->input('threshold', 85);
        
        // Find duplicate users based on name similarity
        $duplicateGroups = $this->findDuplicateUsers($threshold);
        
        return view('users.duplicates', compact('duplicateGroups', 'threshold'));
    }

    /**
     * Find duplicate users using Levenshtein distance
     */
    private function findDuplicateUsers($threshold = 85)
    {
        $users = User::select('id', 'name', 'email', 'phone', 'status', 'created_at', 'login_at')
            ->with(['role', 'location', 'court'])
            ->orderBy('name')
            ->get();

        $duplicateGroups = [];
        $processedUsers = [];

        foreach ($users as $user) {
            // Skip if already processed
            if (in_array($user->id, $processedUsers)) {
                continue;
            }

            $matches = [$user];
            $processedUsers[] = $user->id;

            // Compare with remaining users
            foreach ($users as $compareUser) {
                if ($user->id === $compareUser->id || in_array($compareUser->id, $processedUsers)) {
                    continue;
                }

                // Calculate similarity
                $similarity = $this->calculateNameSimilarity($user->name, $compareUser->name);

                if ($similarity >= $threshold) {
                    $matches[] = $compareUser;
                    $processedUsers[] = $compareUser->id;
                }
            }

            // Only add groups with duplicates
            if (count($matches) > 1) {
                $duplicateGroups[] = [
                    'key' => md5($user->name),
                    'name' => $user->name,
                    'users' => $matches,
                    'count' => count($matches)
                ];
            }
        }

        return collect($duplicateGroups);
    }

    /**
     * Calculate name similarity percentage
     */
    private function calculateNameSimilarity($name1, $name2)
    {
        // Normalize names
        $name1 = strtolower(trim($name1));
        $name2 = strtolower(trim($name2));

        // Exact match
        if ($name1 === $name2) {
            return 100;
        }

        // Use similar_text for percentage
        similar_text($name1, $name2, $percent);
        
        return round($percent, 2);
    }

    /**
     * Show merge preview for selected users
     */
    public function mergePreview(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        
        if (count($userIds) < 2) {
            return redirect()->route('users.duplicates')
                ->with('error', 'Please select at least 2 users to merge.');
        }

        $users = User::whereIn('id', $userIds)
            ->with(['role', 'location', 'court', 'assignedAssets'])
            ->get();

        // Suggest primary user (most recently logged in or created)
        $suggestedPrimary = $users->sortByDesc(function ($user) {
            return $user->login_at ?? $user->created_at;
        })->first();

        return view('users.merge-preview', compact('users', 'suggestedPrimary'));
    }

    /**
     * Merge selected users
     */
    public function merge(Request $request)
    {
        $request->validate([
            'primary_user_id' => 'required|exists:users,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $primaryUserId = $request->input('primary_user_id');
        $userIdsToMerge = array_filter($request->input('user_ids'), function($id) use ($primaryUserId) {
            return $id != $primaryUserId;
        });

        if (empty($userIdsToMerge)) {
            return redirect()->route('users.duplicates')
                ->with('error', 'No users to merge.');
        }

        DB::beginTransaction();
        try {
            $primaryUser = User::findOrFail($primaryUserId);
            
            // Merge data from duplicate users
            foreach ($userIdsToMerge as $userId) {
                $duplicateUser = User::findOrFail($userId);
                
                // Transfer assets
                DB::table('assets')
                    ->where('assigned_to', $userId)
                    ->update(['assigned_to' => $primaryUserId]);
                
                // Transfer asset requests if exists
                if (DB::getSchemaBuilder()->hasTable('asset_requests')) {
                    DB::table('asset_requests')
                        ->where('user_id', $userId)
                        ->update(['user_id' => $primaryUserId]);
                }
                
                // Transfer maintenance records if exists
                if (DB::getSchemaBuilder()->hasTable('maintenances')) {
                    DB::table('maintenances')
                        ->where('created_by', $userId)
                        ->update(['created_by' => $primaryUserId]);
                }
                
                // Transfer audit logs if exists
                if (DB::getSchemaBuilder()->hasTable('audit_logs')) {
                    DB::table('audit_logs')
                        ->where('user_id', $userId)
                        ->update(['user_id' => $primaryUserId]);
                }
                
                // Update invited_by references
                User::where('invited_by', $userId)
                    ->update(['invited_by' => $primaryUserId]);
                
                // Update registry_id references
                User::where('registry_id', $userId)
                    ->update(['registry_id' => $primaryUserId]);
                
                // Merge contact info if primary user is missing data
                // Only update if the email doesn't already exist and primary user has no email
                // if (empty($primaryUser->email) && !empty($duplicateUser->email)) {
                //     // Check if this email is unique (not used by another user)
                //     $emailExists = User::where('email', $duplicateUser->email)
                //         ->where('id', '!=', $primaryUserId)
                //         ->where('id', '!=', $userId)
                //         ->exists();
                    
                //     if (!$emailExists) {
                //         $primaryUser->email = $duplicateUser->email;
                //     }
                // }
                
                // Only update phone if primary user has no phone
                if (empty($primaryUser->phone) && !empty($duplicateUser->phone)) {
                    $primaryUser->phone = $duplicateUser->phone;
                }
                
                // Keep the most recent login date
                if ($duplicateUser->login_at && 
                    (!$primaryUser->login_at || $duplicateUser->login_at > $primaryUser->login_at)) {
                    $primaryUser->login_at = $duplicateUser->login_at;
                }
                
                // Delete the duplicate user
                $duplicateUser->delete();
            }
            
            $primaryUser->save();
            
            DB::commit();
            
            return redirect()->route('users.duplicates')
                ->with('success', 'Successfully merged ' . count($userIdsToMerge) . ' duplicate user(s) into ' . $primaryUser->name);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('users.duplicates')
                ->with('error', 'Error merging users: ' . $e->getMessage());
        }
    }
    
    
  public function index(Request $request)
{
    $this->authorize('view_users');
    $user = auth()->user();
    $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

    $query = User::with(['location', 'court', 'role']); 

    if ($isRegionalAdmin) {
        $query->where('region_id', $user->region_id);
    }

    if ($request->filled('search')) {
        $query->where('name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%");
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

    // Statistics based on custom roles
    $statsQuery = clone $query;
    $users = $query->latest()->paginate(20)->withQueryString();

    $totalUsers = (clone $statsQuery)->count();
    $activeUsers = (clone $statsQuery)->where('status', 'active')->count();
    
    // Get role distribution from your custom roles
    $roleDistribution = (clone $statsQuery)->with('role')
        ->get()
        ->groupBy('role.name')
        ->map(function ($users, $roleName) use ($totalUsers) {
            return [
                'count' => $users->count(),
                'percentage' => $totalUsers > 0 ? round(($users->count() / $totalUsers) * 100, 1) : 0
            ];
        });

    // Get top roles for display
    $topRoles = $roleDistribution->sortByDesc('count')->take(4);

    return view('users.index', compact('users', 'totalUsers', 'activeUsers', 'roleDistribution', 'topRoles'));
}

public function show(User $user)
{
    $this->authorize('view_users');
    $currentUser = auth()->user();
    if ($currentUser->hasRole('rao') && $user->region_id !== $currentUser->region_id) {
        abort(403, 'Unauthorized access to user in another region.');
    }

    $user->load([
        'court.region', 
        'location', 
        'assignedAssets', 
        'registry', 
        'roles',
        'assignedAssets.category',
        'assignedAssets.office'
    ]);

    // Get available assets for assignment (excluding those already assigned to this user and those with office_id)
    $availableAssets = Asset::where(function($query) {
        $query->whereDoesntHave('assignedUser')
              ->whereNull('office_id')
              ->whereNull('assigned_to')
              ->whereNull('court_id');
    })
    ->orWhere(function($query) {
        $query->whereNull('assigned_to')
              ->whereNull('court_id')
              ->whereNull('office_id');
    })
    ->with(['category'])
    ->get();
    $categories = Category::orderBy('name')->get();
    return view('users.show', compact('user', 'availableAssets','categories'));
}

//  public function show(User $user)
//     {
//         $user->load([
//             'court.region', 
//             'location', 
//             'assignedAssets', 
//             'registry', 
//             'roles',
//             'assignedAssets.category',
//             'assignedAssets.office'
//         ]);

//         // Get available assets for assignment (excluding those already assigned to this user and those with office_id)
//         $availableAssets = Asset::whereDoesntHave('assignedUser')
//         ->whereNull('office_id') 
//         ->orWhereNull('assigned_to') 
//         ->orWhereNull('court_id') 
//         ->with(['category'])
//         ->get();

//         return view('users.show', compact('user', 'availableAssets'));
//     }
    /**
     * Assign asset to user
     */
  public function assignAsset(Request $request, User $user)
{
    $this->authorize('assign_assets');
    $currentUser = auth()->user();
    if ($currentUser->region_id && $currentUser->hasRole('admin') && $user->region_id !== $currentUser->region_id) {
        abort(403, 'Cannot assign assets to user in another region.');
    }

    $request->validate([
        'asset_id' => 'required|exists:assets,id'
    ]);

    $asset = Asset::find($request->asset_id);

    if ($currentUser->region_id && $currentUser->hasRole('admin') && $asset->region_id !== $currentUser->region_id) {
        abort(403, 'Cannot assign assets from another region.');
    }
    
    // Check if asset is already assigned to any user
    if ($asset->assigned_to) {
        return response()->json([
            'success' => false,
            'message' => 'Asset is already assigned to another user.'
        ], 422);
    }

    // Assign asset to user directly
    $asset->update([
        'assigned_to' => $user->id,
        'assigned_type' => 'user',
        'assigned_date' => now(),
        'status' => 'assigned'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Asset assigned successfully.'
    ]);
}

    /**
     * Remove asset from user
     */
    public function removeAsset(Request $request, User $user)
    {
        $this->authorize('return_assets');
        $currentUser = auth()->user();
        if ($currentUser->region_id && $currentUser->hasRole('admin') && $user->region_id !== $currentUser->region_id) {
            abort(403, 'Cannot remove assets from user in another region.');
        }

        Log::info($request->all());
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $asset = Asset::find($request->asset_id);
        
        if ($currentUser->region_id && $currentUser->hasRole('admin') && $asset->region_id !== $currentUser->region_id) {
            abort(403, 'Cannot manage assets from another region.');
        }
        
            $asset->update([
        'assigned_to' => null,
        'assigned_type' => '',
        
        'status' => 'available'
    ]);

       

        return response()->json([
            'success' => true,
            'message' => 'Asset removed successfully.'
        ]);
    }






    public function create()
    {
        $this->authorize('create_users');
        $user = auth()->user();
        $isRegionalAdmin = $user->region_id && $user->hasRole('rao');

        $regionsQuery = Region::query();
        $courtsQuery = Court::query();
        $locationsQuery = Location::query();

        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $user->region_id);
            $courtsQuery->where('region_id', $user->region_id);
            $locationsQuery->where('region_id', $user->region_id);
        }

        $regions = $regionsQuery->get();
        $courts = $courtsQuery->get();
        $roles = UserRole::all(); 
        $locations = $locationsQuery->get();
        return view('users.create', compact('regions', 'courts', 'roles','locations'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_users');
        Log::info($request->all());
        $validationRules = [
            'name' => 'required|string|max:255',
      
            'email' => 'nullable|email|unique:users',
            
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
            'region_id' => 'nullable|exists:regions,id',
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

        $validated['slug'] = Str::slug($validated['name'] . '-user');
        
        // Set default values if not provided
        $validated['is_approved'] = $validated['is_approved'] ?? true;
        $validated['approved_at'] = $validated['approved_at'] ?? now();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['created_by'] = auth()->id();

        // Enforce Regional Admin Scoping
        $currentUser = auth()->user();
        if ($currentUser->region_id && $currentUser->hasRole('admin')) {
             // If validation didn't catch it (though we should ensure they can't select others)
             // We can force relevant fields or check them.
             // For simplicity, let's assume the form filters correctly, but we can verify:
             if(isset($validated['court_id'])) {
                 $court = Court::find($validated['court_id']);
                 if($court && $court->region_id !== $currentUser->region_id) {
                      abort(403, 'Cannot assign user to court in another region.');
                 }
             }
             // Force user to be in the same region
             $validated['region_id'] = $currentUser->region_id;
        }
        
        $user = User::create($validated);
        
        // Assign role
      //  $user->assignRole($request->role);

        return redirect()->route('users')->with('success', 'User created successfully.');
    }



    public function edit(User $user)
    {
        $this->authorize('edit_users');
        $currentUser = auth()->user();
        $isRegionalAdmin = $currentUser->region_id && $currentUser->hasRole('rao');

        if ($isRegionalAdmin && $user->region_id !== $currentUser->region_id) {
            abort(403, 'Unauthorized access to user in another region.');
        }

        $regionsQuery = Region::query();
        $courtsQuery = Court::query();
        $locationsQuery = Location::query();
        $usersQuery = User::where('id', '!=', $user->id);

        if ($isRegionalAdmin) {
            $regionsQuery->where('id', $currentUser->region_id);
            $courtsQuery->where('region_id', $currentUser->region_id);
            $locationsQuery->where('region_id', $currentUser->region_id);
            $usersQuery->where('region_id', $currentUser->region_id);
        }

        $regions = $regionsQuery->get();
        $courts = $courtsQuery->get();
        $roles = UserRole::all();  
        $allUsers = $usersQuery->get();
        $locations = $locationsQuery->get();
        return view('users.edit', compact('user', 'regions', 'courts', 'roles', 'allUsers','locations'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('edit_users');
        $currentUser = auth()->user();
        if ($currentUser->hasRole('rao') && $user->region_id !== $currentUser->region_id) {
            abort(403, 'Unauthorized access to user in another region.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
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
            'region_id' => 'nullable|exists:regions,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Enforce Regional Admin Scoping
        if ($currentUser->hasRole('rao')) {
             $user->region_id = $currentUser->region_id;
             $user->save();
             
             // Verify court/location if changed...
             if($user->court && $user->court->region_id !== $currentUser->region_id) {
                  // Revert or error? For now, we trust the validation/form filter but let's double check.
                  // Actually, just ensuring region_id stays correct is good first step.
             }
        }
        $user->syncRoles([$request->role]);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete_users');
        $currentUser = auth()->user();
        if ($currentUser->hasRole('rao') && $user->region_id !== $currentUser->region_id) {
            abort(403, 'Unauthorized access to user in another region.');
        }

        $user->delete();
        return redirect()->route('users')->with('success', 'User deleted successfully.');
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
        'name',
        'email',
        'phone',
        'role', // This is the main role field that maps to role_id
        'roles', // Optional: comma-separated Spatie roles
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
            'name' => 'John Mensah',
            'email' => 'john.mensah@court.gov.gh',
            'phone' => '0244123456',
            'role' => 'judge',
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
            'name' => 'Sarah Osei',
            'email' => 'sarah.osei@court.gov.gh',
            'phone' => '0201234567',
            'role' => 'registry',
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
            'name' => 'Kwame Asante',
            'email' => 'kwame.asante@court.gov.gh',
            'phone' => '0554567890',
            'role' => 'director',
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