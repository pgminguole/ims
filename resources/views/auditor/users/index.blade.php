@extends('layouts.app')

@section('title', 'Users Audit')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users me-2"></i>Users Audit
        </h1>
        
    </div>

    <!-- Statistics Cards -->
    {{-- <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-user-tie fs-1 text-primary mb-2"></i>
                    <h3 class="fw-bold">{{ $totalJudges }}</h3>
                    <p class="text-muted mb-0">Judges</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-user fs-1 text-info mb-2"></i>
                    <h3 class="fw-bold">{{ $totalStaff }}</h3>
                    <p class="text-muted mb-0">Staff</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-building fs-1 text-success mb-2"></i>
                    <h3 class="fw-bold">{{ $totalDirectors }}</h3>
                    <p class="text-muted mb-0">Directors</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-laptop fs-1 text-warning mb-2"></i>
                    <h3 class="fw-bold">{{ $usersWithAssets }}</h3>
                    <p class="text-muted mb-0">With Assets</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fs-1 text-danger mb-2"></i>
                    <h3 class="fw-bold">{{ $regionsCount }}</h3>
                    <p class="text-muted mb-0">Regions</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('auditor.users.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">User Type</label>
                        <select name="user_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="judge" {{ request('user_type') == 'judge' ? 'selected' : '' }}>Judges</option>
                            <option value="staff" {{ request('user_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="director" {{ request('user_type') == 'director' ? 'selected' : '' }}>Directors</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Region</label>
                        <select name="region_id" class="form-select">
                            <option value="">All Regions</option>
                            @foreach($filterData['regions'] as $region)
                                <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('auditor.users.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                      
                         
                            <th>Region</th>
                            <th>Court</th>
                            <th>Department</th>
                            <th>Assets Count</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                       
                                    </div>
                                </div>
                            </td>
                        
                            <td>
                               
                            </td>
                            <td>{{ $user->region->name ?? 'N/A' }}</td>
                            <td>{{ $user->court->name ?? 'N/A' }}</td>
                            <td>{{ $user->office->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $user->assigned_assets_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('auditor.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportUsers() {
    // Implement export functionality
    alert('Export functionality to be implemented');
}
</script>
@endpush