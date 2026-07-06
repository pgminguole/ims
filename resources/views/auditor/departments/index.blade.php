@extends('layouts.app')

@section('title', 'Departments Audit')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i>Departments Audit
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                <i class="fas fa-filter me-2"></i>Filters
            </button>
       
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-building text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Total Departments</h5>
                            <h2 class="fw-bold text-dark mb-0">{{ number_format($totalDepartments) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-laptop text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Department Assets</h5>
                            <h2 class="fw-bold text-dark mb-0">{{ number_format($totalAssets) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Department Staff</h5>
                            <h2 class="fw-bold text-dark mb-0">{{ number_format($totalStaff) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-map-marker-alt text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Active Regions</h5>
                            <h2 class="fw-bold text-dark mb-0">{{ number_format($activeRegions) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('auditor.departments.index') }}" method="GET">
                <div class="row g-3">
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
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Court</label>
                        <select name="court_id" class="form-select">
                            <option value="">All Courts</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search departments..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('auditor.departments.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Code</th>
                            <th>Region</th>
                            <th>Court</th>
                            <th>Manager</th>
                            <th>Staff Count</th>
                            <th>Assets Count</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $department->name }}</strong>
                                        @if($department->email)
                                        <br><small class="text-muted">{{ $department->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $department->code }}</code></td>
                            <td>{{ $department->region->name ?? 'N/A' }}</td>
                            <td>{{ $department->court->name ?? 'N/A' }}</td>
                            <td>{{ $department->manager->full_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $department->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $department->assets_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('auditor.departments.show', $department) }}" class="btn btn-sm btn-outline-primary">
                                    
                                    
                                    
                                    
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
                    Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} results
                </div>
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
