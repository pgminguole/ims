@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
 <!-- Modern Page Header -->
<div class="page-header-wrapper">
    <div class="row align-items-center mb-4">
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
            <div class="header-content">
                <div class="header-badge mb-2">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>Asset Availability</span>
                </div>
                <h1 class="page-title mb-2">Available Assets</h1>
                <p class="page-subtitle mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Manage available assets ready for assignment
                </p>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12">
            <div class="header-actions">
                <a href="{{ route('assets.index') }}" class="btn btn-action btn-outline">
                    <i class="fas fa-boxes"></i>
                    <span class="btn-text">All Assets</span>
                </a>
                
                <a href="{{ route('assets.assigned') }}" class="btn btn-action btn-outline">
                    <i class="fas fa-user-check"></i>
                    <span class="btn-text">Assigned</span>
                </a>
                
                <a href="{{ route('assets.create') }}" class="btn btn-action btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    <span class="btn-text">Add New Asset</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-modern alert-success alert-dismissible fade show" role="alert">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-content">
            <strong>Success!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-modern alert-danger alert-dismissible fade show" role="alert">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-content">
            <strong>Error!</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Available</h6>
                            <h3 class="mb-0">{{ $totalAvailable ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category Summary -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Available Assets by Category</h6>
                    <div class="row g-2">
                        @forelse($categorySummary->take(4) as $summary)
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <span class="fw-medium">{{ $summary['category'] ?? 'Uncategorized' }}</span>
                                <span class="badge bg-success">{{ $summary['count'] }} available</span>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No available assets found</p>
                        </div>
                        @endforelse
                    </div>
                    @if($categorySummary->count() > 4)
                    <div class="mt-2 text-center">
                        <small class="text-muted">+{{ $categorySummary->count() - 4 }} more categories</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">Availability Filters</h5>
                <button class="btn btn-sm btn-link ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('assets.available') }}" id="filterForm">
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

                        <!-- Condition -->
                        <div class="col-md-2">
                            <label class="form-label">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="">All Conditions</option>
                                <option value="excellent" {{ request('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                <option value="broken" {{ request('condition') == 'broken' ? 'selected' : '' }}>Broken</option>
                            </select>
                        </div>

                        <!-- Filter Actions -->
                        <div class="col-md-12">
                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-filter me-2"></i>Apply Filters
                                </button>
                                <a href="{{ route('assets.available') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Available Assets Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="py-3">ASSET NAME</th>
                            <th class="py-3">CATEGORY</th>
                            <th class="py-3">CONDITION</th>
                            <th class="py-3">PURCHASE DATE</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input asset-checkbox" value="{{ $asset->id }}">
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
                                    <button class="action-btn assign-btn" 
                                            data-bs-toggle="tooltip" 
                                            title="Assign Asset"
                                            onclick="openAssignModal('{{ $asset->slug }}', '{{ addslashes($asset->asset_name) }}')">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
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
                                                <a class="dropdown-item" href="#" onclick="openAssignModal('{{ $asset->slug }}', '{{ addslashes($asset->asset_name) }}')">
                                                    <i class="fas fa-user-plus me-2 text-success"></i>Assign
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-qrcode me-2 text-dark"></i>QR Code
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
                                    <i class="fas fa-check-circle"></i>
                                    <h5>No available assets found</h5>
                                    <p>All assets might be assigned or try adjusting your filters</p>
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

<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assignAssetForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title" id="assignAssetModalLabel">Assign Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Asset</label>
                        <p class="form-control-plaintext fw-bold" id="modalAssetName"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assign_type" class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="assign_type" name="assigned_type" required>
                            <option value="">Select Assignment Type</option>
                            <option value="user">User</option>
                            <option value="office">Office</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="assign_user_section" style="display: none;">
                        <label for="assign_user_select" class="form-label">Select User <span class="text-danger">*</span></label>
                        <select class="form-select" id="assign_user_select" name="assigned_to" disabled>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="assign_office_section" style="display: none;">
                        <label for="assign_office_select" class="form-label">Select Office <span class="text-danger">*</span></label>
                        <select class="form-select" id="assign_office_select" name="assigned_to" disabled>
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }} ({{ $office->code ?? 'No Code' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assign_date" class="form-label">Assignment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="assign_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assign_comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="assign_comments" name="comments" rows="3" placeholder="Optional notes about this assignment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });

    // Handle Assign Modal Type Change
    document.getElementById('assign_type').addEventListener('change', function() {
        const type = this.value;
        const userSection = document.getElementById('assign_user_section');
        const officeSection = document.getElementById('assign_office_section');
        const userSelect = document.getElementById('assign_user_select');
        const officeSelect = document.getElementById('assign_office_select');
        
        if (type === 'user') {
            userSection.style.display = 'block';
            officeSection.style.display = 'none';
            userSelect.disabled = false;
            officeSelect.disabled = true;
            officeSelect.value = '';
        } else if (type === 'office') {
            userSection.style.display = 'none';
            officeSection.style.display = 'block';
            userSelect.disabled = true;
            officeSelect.disabled = false;
            userSelect.value = '';
        } else {
            userSection.style.display = 'none';
            officeSection.style.display = 'none';
            userSelect.disabled = true;
            officeSelect.disabled = true;
            userSelect.value = '';
            officeSelect.value = '';
        }
    });

    // Open Assign Modal
    function openAssignModal(assetSlug, assetName) {
        // Set the asset name in the modal
        document.getElementById('modalAssetName').textContent = assetName;
        
        // Set the form action using the correct route with slug
        const form = document.getElementById('assignAssetForm');
        const baseUrl = '{{ url("/") }}';
        form.action = `${baseUrl}/assets/${assetSlug}/assign`;
        
        console.log('Form action set to:', form.action); // For debugging
        
        // Reset form fields
        form.reset();
        document.getElementById('assign_date').value = '{{ date('Y-m-d') }}';
        document.getElementById('assign_type').value = '';
        document.getElementById('assign_user_section').style.display = 'none';
        document.getElementById('assign_office_section').style.display = 'none';
        document.getElementById('assign_user_select').disabled = true;
        document.getElementById('assign_office_select').disabled = true;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('assignAssetModal'));
        modal.show();
    }

    function assignMultiple() {
        const selectedAssets = Array.from(document.querySelectorAll('.asset-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedAssets.length === 0) {
            alert('Please select at least one asset to assign');
            return;
        }
        
        // Implement bulk assignment
        alert('Bulk assign assets: ' + selectedAssets.join(', '));
    }

    function exportData() {
        alert('Export functionality would be implemented here');
    }

    // Initialize modals on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Users loaded:', {{ $users->count() ?? 0 }});
        console.log('Offices loaded:', {{ $offices->count() ?? 0 }});
    });
</script>
@endpush
@endsection