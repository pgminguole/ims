@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <!-- Replace the Page Header section in your assets index view with this -->
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark mb-1">Assets Management</h2>
        <p class="text-muted mb-0">Manage and track all organizational assets</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-danger px-4 d-none" id="bulkDeleteBtn" 
                    data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                <i class="fas fa-trash me-2"></i>Delete Selected
            </button>
            <a href="{{ route('assets.import.form') }}" class="btn btn-outline-primary px-4">
                <i class="fas fa-upload me-2"></i>Import Assets
            </a>
            <a href="{{ route('assets.create') }}" class="btn btn-primary px-4">
                <i class="fas fa-plus me-2"></i>Add New Asset
            </a>
        </div>
    </div>
</div>

<!-- Add success/error messages after the header, before Summary Cards -->
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
    <div style="max-height: 200px; overflow-y: auto;">
        <ul class="mb-0 small">
            @foreach(session('errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper primary-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Assets</h6>
                            <h3 class="mb-0">{{ $totalAssets ?? 0 }}</h3>
                            <small class="text-muted">Filtered: {{ $assets->total() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Available</h6>
                            <h3 class="mb-0">{{ $availableCount ?? 0 }}</h3>
                            <small class="text-muted">{{ $totalAssets > 0 ? number_format(($availableCount/$totalAssets)*100, 1) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper info-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Assigned</h6>
                            <h3 class="mb-0">{{ $assignedCount ?? 0 }}</h3>
                            <small class="text-muted">{{ $totalAssets > 0 ? number_format(($assignedCount/$totalAssets)*100, 1) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper warning-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Maintenance</h6>
                            <h3 class="mb-0">{{ $maintenanceCount ?? 0 }}</h3>
                            <small class="text-muted">{{ $totalAssets > 0 ? number_format(($maintenanceCount/$totalAssets)*100, 1) : 0 }}%</small>
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
                <h5 class="mb-0 fw-semibold">Advanced Filters</h5>
                <button class="btn btn-sm btn-link ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('assets.index') }}" id="filterForm">
                    <div class="row g-2">
                        <!-- Search -->
                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small fw-semibold mb-1">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted small"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" 
                                       placeholder="Name, tag, serial..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                            </select>
                        </div>

                        <!-- Condition -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Condition</label>
                            <select name="condition" class="form-select form-select-sm">
                                <option value="">All Conditions</option>
                                <option value="excellent" {{ request('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                <option value="broken" {{ request('condition') == 'broken' ? 'selected' : '' }}>Broken</option>
                            </select>
                        </div>

                        <!-- Region -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Region</label>
                            <select name="region_id" class="form-select form-select-sm">
                                <option value="">All Regions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Category</label>
                            <select name="category_id" class="form-select form-select-sm" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Court -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Court</label>
                            <select name="court_id" class="form-select form-select-sm">
                                <option value="">All Courts</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                        {{ $court->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assigned Type -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Assigned To</label>
                            <select name="assigned_type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                <option value="judge" {{ request('assigned_type') == 'judge' ? 'selected' : '' }}>Judge</option>
                                <option value="staff" {{ request('assigned_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="director" {{ request('assigned_type') == 'director' ? 'selected' : '' }}>Director</option>
                                <option value="reg_admin" {{ request('assigned_type') == 'reg_admin' ? 'selected' : '' }}>Regional Admin</option>
                                <option value="registry" {{ request('assigned_type') == 'registry' ? 'selected' : '' }}>Registry</option>
                                <option value="admin" {{ request('assigned_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <!-- Purchase Year -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Purchase Year</label>
                            <select name="purchase_year" class="form-select form-select-sm">
                                <option value="">All Years</option>
                                @for($year = date('Y'); $year >= 2000; $year--)
                                    <option value="{{ $year }}" {{ request('purchase_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Purchase Date From -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Purchase From</label>
                            <input type="date" name="purchase_date_from" class="form-control form-control-sm" 
                                   value="{{ request('purchase_date_from') }}">
                        </div>

                        <!-- Purchase Date To -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" name="purchase_date_to" class="form-control form-control-sm" 
                                   value="{{ request('purchase_date_to') }}">
                        </div>

                        <!-- Assigned Date From -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">Assigned From</label>
                            <input type="date" name="assigned_date_from" class="form-control form-control-sm" 
                                   value="{{ request('assigned_date_from') }}">
                        </div>

                        <!-- Assigned Date To -->
                        <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" name="assigned_date_to" class="form-control form-control-sm" 
                                   value="{{ request('assigned_date_to') }}">
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm px-3">
                                    <i class="fas fa-filter me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="fas fa-redo me-1"></i>Reset
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-sm px-3 ms-auto" onclick="exportData()">
                                    <i class="fas fa-download me-1"></i>Export
                                </button>
                                <div class="text-muted small ms-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Showing filtered results
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-4" width="50">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="py-3">ASSET NAME</th>
                            <th class="py-3">CATEGORY</th>
                    
                            <th class="py-3">REGION</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3">CONDITION</th>
                            <th class="py-3">PURCHASE DATE</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                        <tr>
                            <td class="ps-4">
                                <div class="form-check">
                                    <input class="form-check-input asset-checkbox" type="checkbox" 
                                           value="{{ $asset->id }}" data-asset-name="{{ $asset->asset_name }}">
                                </div>
                            </td>
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
                                        <div class="asset-name-text fw-semibold">{{ $asset->asset_name }}</div>
                                        <div class="asset-meta-text">
                                            <small class="text-muted">{{ $asset->asset_tag }}</small>
                                            @if($asset->serial_number)
                                            <small class="text-muted ms-2">• {{ $asset->serial_number }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge">
                                    {{ $asset->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            
                            <td>
                                <span class="region-text">{{ $asset->region->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $asset->status }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="condition-badge condition-{{ $asset->condition }}">
                                    {{ ucfirst($asset->condition) }}
                                </span>
                            </td>
                            <td>
                                <span class="date-text">
                                    {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'N/A' }}
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
                                    <button class="action-btn delete-btn" 
                                            data-bs-toggle="tooltip" 
                                            title="Delete Asset"
                                            onclick="confirmDelete({{ $asset->id }}, '{{ addslashes($asset->asset_name) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No assets found</h5>
                                    <p class="text-muted mb-0">Try adjusting your filters or add a new asset</p>
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
                    of {{ $assets->total() }} entries (Filtered from {{ $totalAssets }} total assets)
                </div>
                <div>
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Confirm Bulk Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected assets? This action cannot be undone.</p>
                <div id="selectedAssetsList" class="mt-3 small text-muted"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDelete">
                    <i class="fas fa-trash me-2"></i>Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Single Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the asset "<span id="assetName" class="fw-semibold"></span>"? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>


// Export functionality
function handleExport(exportUrl) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.classList.add('btn-export-loading');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
    btn.disabled = true;
    
    // Create a temporary iframe for download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);
    
    iframe.onload = function() {
        // Clean up
        setTimeout(() => {
            document.body.removeChild(iframe);
            btn.classList.remove('btn-export-loading');
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            // Close modal if open
            const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
            if (exportModal) {
                exportModal.hide();
            }
        }, 1000);
    };
    
    iframe.src = exportUrl;
}

// Add click handlers for export buttons
document.addEventListener('DOMContentLoaded', function() {
    // Filtered export
    const filteredExportBtn = document.querySelector('a[href*="assets.export"]');
    if (filteredExportBtn) {
        filteredExportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleExport(this.href);
        });
    }
    
    // All assets export
    const allExportBtn = document.querySelector('a[href*="assets.export.all"]');
    if (allExportBtn) {
        allExportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleExport(this.href);
        });
    }
});


    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Bulk selection functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const confirmBulkDelete = document.getElementById('confirmBulkDelete');
        const selectedAssetsList = document.getElementById('selectedAssetsList');

        // Select all functionality
        selectAll.addEventListener('change', function() {
            assetCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            toggleBulkDeleteButton();
        });

        // Individual checkbox change
        assetCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllCheckbox();
                toggleBulkDeleteButton();
            });
        });

        function updateSelectAllCheckbox() {
            const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
            selectAll.checked = checkedCount === assetCheckboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < assetCheckboxes.length;
        }

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkDeleteBtn.classList.remove('d-none');
                updateSelectedAssetsList();
            } else {
                bulkDeleteBtn.classList.add('d-none');
            }
        }

        function updateSelectedAssetsList() {
            const selectedAssets = Array.from(document.querySelectorAll('.asset-checkbox:checked'))
                .map(checkbox => checkbox.getAttribute('data-asset-name'));
            
            if (selectedAssets.length > 0) {
                let html = '<strong>Selected assets (' + selectedAssets.length + '):</strong><br>';
                selectedAssets.slice(0, 5).forEach(asset => {
                    html += '• ' + asset + '<br>';
                });
                if (selectedAssets.length > 5) {
                    html += '• ... and ' + (selectedAssets.length - 5) + ' more';
                }
                selectedAssetsList.innerHTML = html;
            }
        }

        // Bulk delete functionality
        confirmBulkDelete.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.asset-checkbox:checked'))
                .map(checkbox => checkbox.value);

            if (selectedIds.length > 0) {
                // Show loading state
                const originalText = confirmBulkDelete.innerHTML;
                confirmBulkDelete.disabled = true;
                confirmBulkDelete.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';

                // Send bulk delete request
                fetch('{{ route("assets.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting assets: ' + data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting assets');
                    location.reload();
                })
                .finally(() => {
                    confirmBulkDelete.disabled = false;
                    confirmBulkDelete.innerHTML = originalText;
                });
            }
        });

        // Dynamic subcategory filtering
        const categoryFilter = document.getElementById('categoryFilter');
        const subcategoryFilter = document.getElementById('subcategoryFilter');
        
        if (categoryFilter && subcategoryFilter) {
            categoryFilter.addEventListener('change', function() {
                const categoryId = this.value;
                
                // Reset subcategory when category changes
                subcategoryFilter.innerHTML = '<option value="">All Subcategories</option>';
                
                if (categoryId) {
                    // You could implement AJAX to load subcategories based on category
                    console.log('Category selected:', categoryId);
                    // Example: fetchSubcategories(categoryId);
                }
            });
        }
    });

    function exportData() {
        // Get current filter parameters
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData).toString();
        
        // Redirect to export route with current filters
        window.location.href = '{{ route("assets.export") }}?' + params;
    }

    function confirmDelete(assetId, assetName) {
        document.getElementById('assetName').textContent = assetName;
        document.getElementById('deleteForm').action = '/assets/' + assetId;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush

<style>
/* Compact filter styles */
.form-label.small {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}

.form-select-sm, .form-control-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.input-group-sm > .form-control,
.input-group-sm > .form-select,
.input-group-sm > .input-group-text {
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
}

/* Action buttons */
.action-btn-group {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.action-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.view-btn {
    background: #e8f4fd;
    color: #0d6efd;
}

.edit-btn {
    background: #f0f7f0;
    color: #198754;
}

.delete-btn {
    background: #fdf2f2;
    color: #dc3545;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.view-btn:hover {
    background: #0d6efd;
    color: white;
}

.edit-btn:hover {
    background: #198754;
    color: white;
}

.delete-btn:hover {
    background: #dc3545;
    color: white;
}

/* Table styles */
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
    border-bottom: 2px solid #e9ecef;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.status-available {
    background: #f0f7f0;
    color: #198754;
}

.status-available .status-dot {
    background: #198754;
}

.status-assigned {
    background: #e8f4fd;
    color: #0d6efd;
}

.status-assigned .status-dot {
    background: #0d6efd;
}

.status-maintenance {
    background: #fff3e0;
    color: #fd7e14;
}

.status-maintenance .status-dot {
    background: #fd7e14;
}

.status-retired, .status-lost, .status-disposed {
    background: #f8f9fa;
    color: #6c757d;
}

.status-retired .status-dot, .status-lost .status-dot, .status-disposed .status-dot {
    background: #6c757d;
}

/* Condition badges */
.condition-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.condition-excellent {
    background: #f0f7f0;
    color: #198754;
}

.condition-good {
    background: #e8f4fd;
    color: #0d6efd;
}

.condition-fair {
    background: #fff3e0;
    color: #fd7e14;
}

.condition-poor {
    background: #ffe6e6;
    color: #dc3545;
}

.condition-broken {
    background: #f8f9fa;
    color: #6c757d;
}

/* Category badges */
.category-badge, .subcategory-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    background: #f8f9fa;
    color: #495057;
}

/* Asset icon */
.asset-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.gradient-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.gradient-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.gradient-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

.asset-name-text {
    font-size: 0.9rem;
    font-weight: 500;
    color: #212529;
}

.asset-meta-text {
    font-size: 0.75rem;
}

/* Empty state */
.empty-state {
    opacity: 0.7;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .action-btn-group {
        gap: 0.25rem;
    }
    
    .action-btn {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
    }
}
</style>
@endsection