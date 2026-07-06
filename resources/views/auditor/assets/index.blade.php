@extends('layouts.app')

@section('title', 'Assets - Auditor')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-laptop me-2"></i>Assets Audit
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                <i class="fas fa-filter me-2"></i>Filters
            </button>

        </div>
    </div>

    <!-- Stats Overview (Optional, adds premium feel) -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-white-50 text-uppercase fw-bold fs-7 mb-0">Total Assets</h6>
                        <i class="fas fa-laptop opacity-50"></i>
                    </div>
                    <h2 class="display-6 fw-bold mb-0">{{ $assets->total() }}</h2>
                </div>
            </div>
        </div>
        <!-- Add more summary cards here if data is available in controller, otherwise keep it simple -->
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4 bg-white rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-filter text-primary me-2"></i>Filter Options
            </h5>
            <i class="fas fa-chevron-down text-muted transition-icon"></i>
        </div>
        
        <div class="collapse show" id="filterCollapse">
            <div class="card-body pt-0">
                <form action="{{ route('auditor.assets.index') }}" method="GET">
                    <!-- Search Bar (Prominent) -->
                    <div class="mb-4">
                        <div class="input-group input-group-lg border rounded-3 overflow-hidden shadow-sm">
                            <span class="input-group-text bg-white border-0 ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-0 ps-2" placeholder="Search assets by name, tag, serial number, or model..." value="{{ request('search') }}">
                            <button class="btn btn-primary px-4 fw-semibold" type="submit">Search</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Primary Filters -->
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Region</label>
                            <select name="region_id" class="form-select select2" data-placeholder="Select Region">
                                <option value=""></option>
                                <option value="">All Regions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Court</label>
                            <select name="court_id" class="form-select select2" data-placeholder="Select Court">
                                <option value=""></option>
                                <option value="">All Courts</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                        {{ $court->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Office</label>
                            <select name="office_id" class="form-select select2" data-placeholder="Select Office">
                                <option value=""></option>
                                <option value="">All Offices</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Category</label>
                            <select name="category_id" class="form-select select2" data-placeholder="Select Category">
                                <option value=""></option>
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @if($category->children->count() > 0)
                                        @foreach($category->children as $subcategory)
                                            <option value="{{ $subcategory->id }}" {{ request('category_id') == $subcategory->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;&nbsp;└─ {{ $subcategory->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Secondary Filters (Toggleable or just in next row) -->
                        <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Status</label>
                            <select name="status" class="form-select select2" data-placeholder="Select Status">
                                <option value=""></option>
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Condition</label>
                            <select name="condition" class="form-select select2" data-placeholder="Select Condition">
                                <option value=""></option>
                                <option value="">All Conditions</option>
                                <option value="excellent" {{ request('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Year Assigned</label>
                            <select name="year" class="form-select select2" data-placeholder="Select Year">
                                <option value=""></option>
                                <option value="">All Years</option>
                                @forelse($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @empty
                                    <option value="{{ now()->year }}">{{ now()->year }}</option>
                                @endforelse
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Month</label>
                            <select name="month" class="form-select select2" data-placeholder="Select Month">
                                <option value=""></option>
                                <option value="">All Months</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                         <!-- Date Range (Less used, maybe keep separate or integrates) -->
                          <div class="col-md-6">
                            <label class="form-label text-muted fs-7 fw-bold text-uppercase">Date Range</label>
                            <div class="input-group">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                                <span class="input-group-text bg-light border-start-0 border-end-0">to</span>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <a href="{{ route('auditor.assets.index') }}" class="btn btn-light border me-2 hover-shadow">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                            <button type="submit" class="btn btn-primary px-4 hover-shadow">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['year', 'month', 'date_from', 'date_to', 'category_id', 'region_id', 'court_id', 'office_id', 'status', 'condition', 'search']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <small class="text-muted me-2">Active Filters:</small>
                
                @if(request('year'))
                    <span class="badge bg-primary">Year: {{ request('year') }}</span>
                @endif
                @if(request('month'))
                    <span class="badge bg-primary">Month: {{ date('F', mktime(0, 0, 0, request('month'), 1)) }}</span>
                @endif
                @if(request('date_from'))
                    <span class="badge bg-primary">From: {{ request('date_from') }}</span>
                @endif
                @if(request('date_to'))
                    <span class="badge bg-primary">To: {{ request('date_to') }}</span>
                @endif
                @if(request('category_id'))
                    @php
                        $category = $categories->flatten()->firstWhere('id', request('category_id'));
                    @endphp
                    <span class="badge bg-primary">Category: {{ $category->name ?? 'N/A' }}</span>
                @endif
                @if(request('region_id'))
                    <span class="badge bg-primary">Region: {{ $regions->firstWhere('id', request('region_id'))->name ?? 'N/A' }}</span>
                @endif
                @if(request('court_id'))
                    <span class="badge bg-primary">Court: {{ $courts->firstWhere('id', request('court_id'))->name ?? 'N/A' }}</span>
                @endif
                @if(request('office_id'))
                    <span class="badge bg-primary">Office: {{ $offices->firstWhere('id', request('office_id'))->name ?? 'N/A' }}</span>
                @endif
                @if(request('status'))
                    <span class="badge bg-primary">Status: {{ ucfirst(request('status')) }}</span>
                @endif
                @if(request('condition'))
                    <span class="badge bg-primary">Condition: {{ ucfirst(request('condition')) }}</span>
                @endif
                @if(request('search'))
                    <span class="badge bg-primary">Search: {{ request('search') }}</span>
                @endif
            </div>
        </div>
    </div>
    @endif

            <!-- Assets Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase fs-7 text-muted fw-bold">
                        <tr>
                            <th class="ps-4">Asset</th>
                            <th>Region</th>
                            <th>Assigned To</th>
                            <th>Assigned Date</th>
                            <th>Audit Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($assets as $asset)
                        <tr class="transition-hover">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center text-primary">
                                        <i class="fas fa-laptop"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $asset->category->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $asset->asset_number ?? $asset->asset_tag ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $asset->effective_region_name }}</span></td>
                            <td>
                                @php
                                    $assignedEntity = $asset->assigned_entity;
                                    $assignedName = $asset->assigned_entity_name;
                                @endphp
                                
                                <div class="d-flex align-items-center">
                                    @if($asset->assigned_type === 'user' && $assignedEntity)
                                        <div class="avatar avatar-xs bg-primary-subtle rounded-circle me-2 d-flex align-items-center justify-content-center text-primary fw-bold" style="width:24px;height:24px;font-size:10px;">
                                            {{ substr($assignedName, 0, 1) }}
                                        </div>
                                    @elseif($asset->assigned_type === 'court' || $asset->assigned_type === 'office')
                                        <div class="avatar avatar-xs bg-info-subtle rounded-circle me-2 d-flex align-items-center justify-content-center text-info" style="width:24px;height:24px;font-size:10px;">
                                            <i class="fas {{ $asset->assigned_type === 'court' ? 'fa-gavel' : 'fa-building' }} fa-xs"></i>
                                        </div>
                                    @endif
                                    
                                    <span class="{{ $assignedName === 'N/A' ? 'text-muted fst-italic' : '' }}">
                                        {{ $assignedName === 'N/A' ? 'Unassigned' : $assignedName }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($asset->assigned_date)
                                    <div class="text-muted small">
                                        {{ $asset->assigned_date->format('M j, Y') }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->is_audited)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-tiny">
                                        <i class="fas fa-check-circle me-1"></i>Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill text-tiny">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-sm btn-light border hover-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon bg-light rounded-circle shadow-sm mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-inbox fa-2x text-muted"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">No assets found</h5>
                                    <p class="text-muted mb-0">Try adjusting your filters or search criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    @if($assets->total() > 0)
                        Showing <strong>{{ $assets->firstItem() }}</strong> to <strong>{{ $assets->lastItem() }}</strong> of <strong>{{ $assets->total() }}</strong> results
                    @else
                        No results found
                    @endif
                </div>
                <div class="pagination-sm">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fs-7 { font-size: 0.85rem; }
    .bg-success-subtle { background-color: #d1e7dd; }
    .text-success { color: #0f5132; }
    .bg-primary-subtle { background-color: #cfe2ff; }
    .text-primary { color: #084298; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .text-warning { color: #664d03; }
    .bg-danger-subtle { background-color: #f8d7da; }
    .text-danger { color: #842029; }
    
    .indicator-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    
    .hover-shadow:hover {
        transform: translateY(-1px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .2s;
    }
    
    .cursor-pointer { cursor: pointer; }
    
    .transition-icon { transition: transform 0.3s ease; }
    [aria-expanded="true"] .transition-icon { transform: rotate(180deg); }
    
    .transition-hover:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #dee2e6;
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
    }
</style>
@endpush

@push('scripts')
<script>
function exportAssets() {
    // Get current filters
    const params = new URLSearchParams(window.location.search);
    
    // Implement export functionality - could redirect to an export endpoint
    const exportUrl = '{{ route("auditor.assets.index") }}' + '/export?' + params.toString();
    
    // For now, show alert
    alert('Export functionality to be implemented. Will export with current filters.');
    
    // When implemented, uncomment:
    // window.location.href = exportUrl;
}
</script>
@endpush