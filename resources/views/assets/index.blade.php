@extends('layouts.app')

@section('content')


<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Assets Management</h4>
                <p class="text-muted text-small mb-0">Manage and track all organizational assets.</p>
            </div>
            <div class="d-flex gap-2">
                @can('delete_assets')
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 d-none" id="bulkDeleteBtn" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                    <i class="fas fa-trash-alt text-danger me-1"></i> Delete
                </button>
                @endcan

                @can('create_assets')
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-file-import me-1"></i> Import
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0">
                        <li><a class="dropdown-item text-small" href="{{ route('assets.import.form') }}"><i class="fas fa-file-upload me-2 text-muted"></i>Import Assets</a></li>
                        <li><a class="dropdown-item text-small" href="{{ route('assets.import.index') }}"><i class="fas fa-file-excel me-2 text-muted"></i>Import Excel</a></li>
                    </ul>
                </div>
                <a href="{{ route('assets.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> New Asset
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alert Messages (Compact) -->
    @if(session('success'))
    <div class="col-12">
        <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-0 rounded-3 border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <div class="text-small">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto small" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="col-12">
        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-0 rounded-3 border-0 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div class="text-small">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto small" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Metrics -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Assets</div>
                <div class="metric-v2-value">{{ number_format($totalAssets ?? 0) }}</div>
                <div class="text-tiny text-muted mt-2">Filtered: {{ $assets->total() }}</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Available</div>
                <div class="metric-v2-value">{{ number_format($availableCount ?? 0) }}</div>
                <div class="badge-gold-light mt-2">
                    {{ $totalAssets > 0 ? number_format(($availableCount/$totalAssets)*100, 0) : 0 }}% Stock
                </div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Assigned</div>
                <div class="metric-v2-value">{{ number_format($assignedCount ?? 0) }}</div>
                <div class="text-tiny text-muted mt-2">Active Use</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Maintenance</div>
                <div class="metric-v2-value">{{ number_format($maintenanceCount ?? 0) }}</div>
                <div class="text-tiny text-warning mt-2">Needs Action</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-tools"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Advanced Filters</h6>
                <i class="fas fa-chevron-down text-muted text-tiny"></i>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('assets.index') }}" id="filterForm">
                        <div class="row g-2">
                            <!-- Search -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Name, tag..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Category</label>
                                <select name="category_id" class="form-select form-select-sm text-small">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Region -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Region</label>
                                <select name="region_id" class="form-select form-select-sm text-small">
                                    @if(!auth()->user()->region_id || auth()->user()->hasRole('admin'))
                                        <option value="">All Regions</option>
                                    @endif
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Actions -->
                            <div class="col-lg-3 col-md-12 d-flex align-items-end">
                                <div class="d-flex w-100 gap-2">
                                    <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill">Apply</button>
                                    <a href="{{ route('assets.index') }}" class="btn btn-sm btn-light border w-100 rounded-pill">Reset</a>
                                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="exportData()" title="Export">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets List -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="40" class="ps-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Asset Name</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Code</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Category</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Region</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Status</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Added By</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Assigned Date</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td class="ps-4">
                                <div class="form-check">
                                    <input class="form-check-input asset-checkbox" type="checkbox" value="{{ $asset->id }}" data-asset-name="{{ $asset->asset_name }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="asset-icon-circle me-3 bg-light text-muted rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-box fa-xs"></i>
                                    </div>
                                    <span class="fw-semibold text-dark text-small">{{ $asset->asset_name }}</span>
                                </div>
                            </td>
                            <td class="text-small text-muted">{{ $asset->asset_code ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark border fw-normal text-tiny">{{ $asset->category->name ?? 'N/A' }}</span></td>
                            <td class="text-small text-muted">{{ $asset->region->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = match($asset->status) {
                                        'available' => 'bg-success-subtle text-success',
                                        'assigned' => 'bg-primary-subtle text-primary',
                                        'maintenance' => 'bg-warning-subtle text-warning',
                                        'retired' => 'bg-secondary-subtle text-secondary',
                                        default => 'bg-light text-muted'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} border-0 fw-medium text-tiny px-2 py-1 rounded-pill">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td class="text-small text-muted">
                                <div class="fw-bold">{{ $asset->creator->name ?? 'Superadmin' }}</div>
                                <div class="text-tiny">{{ $asset->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="text-small text-muted">{{ $asset->assigned_date ? $asset->assigned_date->format('M d, Y') : '-' }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" data-bs-toggle="tooltip" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    @can('edit_assets')
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    @endcan
                                    @can('delete_assets')
                                    <button class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" onclick="confirmAssetDelete({{ $asset->id }}, '{{ addslashes($asset->asset_name) }}')" data-bs-toggle="tooltip" title="Delete"><i class="fas fa-trash-alt fa-xs"></i></button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-box-open fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No assets found</h6>
                                    <p class="text-muted text-small mb-0">Try adjusting your filters or add a new asset.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($assets->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }}
                    </div>
                    <div>{{ $assets->links() }}</div>
                </div>
            </div>
            @endif
        </div>
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

    function confirmAssetDelete(assetId, assetName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete the asset "${assetName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/assets/' + assetId;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

@endsection