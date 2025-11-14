@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Locations Management</h2>
            <p class="text-muted mb-0">Manage physical locations and buildings</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('locations.create') }}" class="btn btn-primary px-4">
                <i class="fas fa-plus me-2"></i>Add New Location
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper primary-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Locations</h6>
                            <h3 class="mb-0">{{ $locations->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Active Locations</h6>
                            <h3 class="mb-0">{{ $locations->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper warning-icon">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Buildings</h6>
                            <h3 class="mb-0">{{ $locations->unique('building')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper info-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Assets in Locations</h6>
                            <h3 class="mb-0">{{ $locations->sum(function($location) { return $location->assets->count(); }) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">Location Filters</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('locations') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Location name, building, or room..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Region</label>
                        <select name="region_id" class="form-select">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
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
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">LOCATION NAME</th>
                              <th class="py-3">REGION</th>
                            <th class="py-3">ASSETS COUNT</th>
                            <th class="py-3">COURTS COUNT</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="location-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $location->name }}</div>
                                        <small class="text-muted">{{ Str::limit($location->description, 30) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $location->region->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $location->assets->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info rounded-pill">{{ $location->courts->count() }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $location->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $location->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('locations.edit', $location) }}" 
                                       class="action-btn edit-btn" 
                                       title="Edit Location">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" 
                                                title="Delete Location"
                                                onclick="return confirm('Are you sure you want to delete this location?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h5>No locations found</h5>
                                    <p>Get started by creating your first location</p>
                                    <a href="{{ route('locations.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Create Location
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($locations->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $locations->firstItem() ?? 0 }} to {{ $locations->lastItem() ?? 0 }} 
                    of {{ $locations->total() }} entries
                </div>
                <div>
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.location-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection