@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
<!-- Replace the header section in your users index view with this -->
<div class="row mb-4">
    <div class="col">
        <h1 class="text-heading mb-0">Users Management</h1>
        <p class="text-muted">Manage system users and their permissions</p>
    </div>
    <div class="col-auto">
        <div class="btn-group-compact">
            <a href="{{ route('users.import.form') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-upload"></i> Import
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-compact">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>
    </div>
</div>

<!-- Add success/error messages at the top after header -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('errors') && is_array(session('errors')))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <h5><i class="fas fa-exclamation-triangle me-2"></i>Import Warnings</h5>
    <ul class="mb-0">
        @foreach(session('errors') as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

    <!-- Statistics -->
<!-- Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-compact">
            <div class="d-flex align-items-center">
                <div class="stat-icon-circle primary me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-compact">
            <div class="d-flex align-items-center">
                <div class="stat-icon-circle success me-3">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $activeUsers }}</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dynamic Role Statistics -->
    @php
        $roleIcons = [
            'judge' => 'fas fa-gavel',
            'admin' => 'fas fa-crown',
            'staff' => 'fas fa-user-tie',
            'registry' => 'fas fa-file-contract',
            'manager' => 'fas fa-tasks',
            'clerk' => 'fas fa-clipboard-list',
            'default' => 'fas fa-user'
        ];
        
        $roleColors = [
            'judge' => 'warning',
            'admin' => 'danger',
            'staff' => 'info',
            'registry' => 'primary',
            'manager' => 'success',
            'clerk' => 'secondary',
            'default' => 'dark'
        ];
    @endphp
    
    @foreach($topRoles as $roleName => $stats)
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-compact">
            <div class="d-flex align-items-center">
                <div class="stat-icon-circle {{ $roleColors[strtolower($roleName)] ?? $roleColors['default'] }} me-3">
                    <i class="{{ $roleIcons[strtolower($roleName)] ?? $roleIcons['default'] }}"></i>
                </div>
                <div>
                    <div class="stat-number">{{ $stats['count'] }}</div>
                    <div class="stat-label">{{ ucfirst($roleName) }} Users</div>
                    <small class="text-muted">{{ $stats['percentage'] }}% of total</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('users') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                           placeholder="Search users...">
                </div>
               
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role">
                        <option value="">All Roles</option>
                        @foreach($roleDistribution->keys() as $roleName)
                            <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>
                                {{ ucfirst($roleName) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-compact w-100">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-responsive-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Role & Access</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="asset-name-text">{{ $user->full_name }}</div>
                                    <div class="asset-brand-text">{{ $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="detail-value">{{ $user->email }}</div>
                            <div class="asset-brand-text">{{ $user->phone ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="role-badge {{ $user->access_type }}">
                                    {{ ucfirst($user->access_type) }}
                                </span>
                                
                            </div>
                        </td>
                        <td>
                            <span class="status-badge-user {{ $user->status }}">
                                <span class="status-dot"></span>
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="date-text">
                                {{ $user->login_at?->format('M d, Y H:i') ?? 'Never' }}
                            </div>
                        </td>
                        <td>
                            <div class="action-btn-group">
                                <a href="{{ route('users.show', $user) }}" class="action-btn-compact view-btn" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="action-btn-compact edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-compact delete-btn" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state-compact">
                                <i class="fas fa-users"></i>
                                <h5>No Users Found</h5>
                                <p>No users match your search criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection