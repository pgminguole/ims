@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Assigned Assets</h2>
            <p class="text-muted mb-0">Track all assigned organizational assets</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <a href="{{ route('assets.index') }}" class="btn btn-outline-primary px-4">
                    <i class="fas fa-boxes me-2"></i>All Assets
                </a>
                <a href="{{ route('assets.available') }}" class="btn btn-outline-success px-4">
                    <i class="fas fa-check-circle me-2"></i>Available
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper info-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Assigned</h6>
                            <h3 class="mb-0">{{ $totalAssigned ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category Summary -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Assigned Assets by Category</h6>
                    <div class="row g-2">
                        @forelse($categorySummary->take(4) as $summary)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <span class="fw-medium">{{ $summary['category'] ?? 'Uncategorized' }}</span>
                                <span class="badge bg-primary">{{ $summary['count'] }} assigned</span>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No assigned assets found</p>
                        </div>
                        @endforelse
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
                <h5 class="mb-0 fw-semibold">Assignment Filters</h5>
                <button class="btn btn-sm btn-link ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('assets.assigned') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" 
                                       placeholder="Asset name, tag, or serial..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Region -->
                        <div class="col-md-2">
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

                        <!-- Court -->
                        <div class="col-md-2">
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

                        <!-- Category -->
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assigned Date Range -->
                        <div class="col-md-3">
                            <label class="form-label">Assigned Date From</label>
                            <input type="date" name="assigned_date_from" class="form-control" 
                                   value="{{ request('assigned_date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">To</label>
                            <input type="date" name="assigned_date_to" class="form-control" 
                                   value="{{ request('assigned_date_to') }}">
                        </div>

                        <!-- Filter Actions -->
                        <div class="col-md-12">
                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-filter me-2"></i>Apply Filters
                                </button>
                                <a href="{{ route('assets.assigned') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assigned Assets Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">ASSET NAME</th>
                            <th class="py-3">CATEGORY</th>
                            <th class="py-3">ASSIGNED TO</th>
                            <th class="py-3">REGION</th>
                            <th class="py-3">ASSIGNED DATE</th>
                            <th class="py-3">CONDITION</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        @php
                                            $gradients = ['gradient-1', 'gradient-2', 'gradient-3', 'gradient-4', 'gradient-5'];
                                            $gradientClass = $gradients[$index % 5];
                                        @endphp
                                        <div class="asset-icon-wrapper {{ $gradientClass }}">
                                            <i class="fas fa-laptop"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="asset-name-text">{{ $asset->asset_name }}</div>
                                        <div class="asset-brand-text">{{ $asset->brand ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $asset->asset_tag }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge">
                                    {{ $asset->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($asset->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="user-avatar-sm">
                                                {{ substr($asset->assignedUser->full_name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-medium">{{ $asset->assignedUser->full_name }}</div>
                                            <small class="text-muted text-capitalize">{{ $asset->assigned_type }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>{{ $asset->region->name ?? 'N/A' }}</td>
                            <td>
                                <span class="date-text">
                                    {{ $asset->assigned_date ? $asset->assigned_date->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="condition-badge condition-{{ $asset->condition }}">
                                    {{ ucfirst($asset->condition) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('assets.show', $asset) }}" 
                                       class="action-btn view-btn" 
                                       data-bs-toggle="tooltip" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset) }}" 
                                       class="action-btn edit-btn" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Asset">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="dropdown d-inline-block">
                                        <button class="action-btn more-btn dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('assets.show', $asset->slug) }}">
                                                    <i class="fas fa-info-circle me-2 text-info"></i>Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-undo me-2 text-warning"></i>Return Asset
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-history me-2 text-secondary"></i>History
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-user-check"></i>
                                    <h5>No assigned assets found</h5>
                                    <p>Try adjusting your filters or check available assets</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assets->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $assets->firstItem() ?? 0 }} to {{ $assets->lastItem() ?? 0 }} 
                    of {{ $assets->total() }} entries
                </div>
                <div>
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@push('styles')
<style>
.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}
</style>
@endpush
@endsection