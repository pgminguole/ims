@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Regions Management</h2>
            <p class="text-muted mb-0">Manage geographical regions and their associations</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('regions.create') }}" class="btn btn-primary px-4">
                <i class="fas fa-plus me-2"></i>Add New Region
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
                                <i class="fas fa-globe"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Regions</h6>
                            <h3 class="mb-0">{{ $regions->total() }}</h3>
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
                            <h6 class="mb-1">Active Regions</h6>
                            <h3 class="mb-0">{{ $regions->where('is_active', true)->count() }}</h3>
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
                                <i class="fas fa-gavel"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Courts</h6>
                            <h3 class="mb-0">{{ $regions->sum(function($region) { return $region->courts->count(); }) }}</h3>
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
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Assets</h6>
                            <h3 class="mb-0">{{ $regions->sum(function($region) { return $region->assets->count(); }) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Regions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-list text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">All Regions</h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">REGION NAME</th>
                            <th class="py-3">CODE</th>
                            <th class="py-3">DESCRIPTION</th>
                            <th class="py-3">COURTS</th>
                            <th class="py-3">LOCATIONS</th>
                            <th class="py-3">ASSETS</th>
                            <th class="py-3">USERS</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regions as $region)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="region-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $region->name }}</div>
                                        <small class="text-muted">Created {{ $region->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $region->code }}</span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ Str::limit($region->description, 50) ?: 'No description' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info rounded-pill">{{ $region->courts->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $region->locations->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning rounded-pill">{{ $region->assets->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success rounded-pill">{{ $region->users->count() }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $region->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $region->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('regions.edit', $region) }}" 
                                       class="action-btn edit-btn" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Region">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('regions.destroy', $region) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" 
                                                data-bs-toggle="tooltip" 
                                                title="Delete Region"
                                                onclick="return confirm('Are you sure you want to delete this region? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-globe"></i>
                                    <h5>No regions found</h5>
                                    <p>Get started by creating your first region</p>
                                    <a href="{{ route('regions.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Create Region
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($regions->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $regions->firstItem() ?? 0 }} to {{ $regions->lastItem() ?? 0 }} 
                    of {{ $regions->total() }} entries
                </div>
                <div>
                    {{ $regions->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.region-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection