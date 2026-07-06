@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Users Management</h4>
                <p class="text-muted text-small mb-0">Manage system users, roles, and permissions.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('users.duplicates') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-filter me-1"></i> Duplicates
                </a>
                <a href="{{ route('exports.users', request()->all()) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Export
                </a>
                <a href="{{ route('users.import.form') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-file-import me-1"></i> Import
                </a>
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add User
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Users</div>
                <div class="metric-v2-value">{{ $totalUsers }}</div>
                <div class="text-tiny text-muted mt-2">Registered Accounts</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Active Users</div>
                <div class="metric-v2-value">{{ $activeUsers }}</div>
                <div class="text-tiny text-success mt-2">Currently Active</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    
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
    @endphp

    {{-- @foreach($topRoles as $roleName => $stats)
    @if($loop->index < 2) 
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">{{ $roleName === 'registry' ? 'Judges' : ucfirst($roleName) . 's' }}</div>
                <div class="metric-v2-value">{{ $stats['count'] }}</div>
                <div class="text-tiny text-muted mt-2">{{ $stats['percentage'] }}% of Total</div>
            </div>
            <div class="metric-v2-icon">
                <i class="{{ $roleIcons[strtolower($roleName)] ?? $roleIcons['default'] }}"></i>
            </div>
        </div>
    </div>
    @endif
    @endforeach --}}

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>User Filters</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('users.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('users') }}">
                        <div class="row g-2">
                            <!-- Search -->
                            <div class="col-lg-5 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="Name, Email, Phone..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <!-- Role -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Role</label>
                                <select name="role" class="form-select form-select-sm text-small">
                                    <option value="">All Roles</option>
                                    @foreach($roleDistribution->keys() as $roleName)
                                        <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>{{ $roleName === 'registry' ? 'Judge' : ucfirst($roleName) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>

                            <!-- Submit -->
                            <div class="col-lg-1 col-md-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">User Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Added By</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded-circle me-3 fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-small text-muted">
                                <div class="fw-bold">{{ $user->creator->name ?? 'Superadmin' }}</div>
                                <div class="text-tiny">{{ $user->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="confirmDelete(event, 'Delete this user?', 'Yes, delete it!')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-users fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No Users Found</h6>
                                    <p class="text-muted text-small mb-0">Try adjusting your filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $users->firstItem() }} - {{ $users->lastItem() }} of {{ $users->total() }}
                    </div>
                    <div>{{ $users->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection