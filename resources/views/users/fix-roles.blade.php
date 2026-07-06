@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Bulk Update User Roles</h1>
            <p class="text-muted">Fix incorrect role assignments</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('users') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('users.fix-roles') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Current Role</label>
                    <select name="current_role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $currentRole == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('users.fix-roles.process') }}" method="POST" id="bulkUpdateForm">
        @csrf
        <input type="hidden" name="current_role" value="{{ $currentRole }}">
        
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users ({{ $users->count() }})</h5>
                    
                    <div class="d-flex gap-2 align-items-center">
                        <select name="new_role_id" class="form-select form-select-sm" required style="width: 200px;">
                            <option value="">Change Selected To...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to update the roles for selected users?')">
                            Apply Change
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40px;" class="ps-4">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Location</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $user->name }}</div>
                                <div class="text-tiny text-muted">{{ $user->username }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst($user->role->name ?? 'None') }}
                                </span>
                            </td>
                            <td>
                                <div class="text-small">{{ $user->court->name ?? 'N/A' }}</div>
                                <div class="text-tiny text-muted">{{ $user->location->name ?? 'N/A' }}</div>
                            </td>
                            <td class="text-small text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users mb-2 fs-4"></i>
                                <p>No users found matching the filter.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
@endsection
