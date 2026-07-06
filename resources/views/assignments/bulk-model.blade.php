@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
<!-- Modern Page Header -->
<!-- Modern Page Header -->
<div class="page-header-wrapper">
    <div class="row align-items-center mb-4">
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
            <div class="header-content">
                <div class="header-badge mb-2">
                    <i class="fas fa-layer-group me-2"></i>
                    <span>Bulk Operations</span>
                </div>
                <h2 class="page-title mb-2">Bulk Asset Assignment</h2>
                <p class="page-subtitle mb-0" style="font-size: 0.875rem;">
                    <i class="fas fa-info-circle me-2"></i>
                    Assign multiple assets to users or offices at once
                </p>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12">
            <div class="header-actions">
                <a href="{{ route('assignments.index') }}" class="btn btn-action btn-outline btn-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span class="btn-text">Back to Assignments</span>
                </a>
                
                <a href="{{ route('assignments.create') }}" class="btn btn-action btn-outline btn-sm">
                    <i class="fas fa-user-plus"></i>
                    <span class="btn-text">Single Assignment</span>
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

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-laptop-house me-2 text-primary"></i>
                        Bulk Asset Assignment
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="bulkAssignmentForm" method="POST" action="{{ route('assignments.bulk-model.store') }}">
                        @csrf

                        <!-- Device Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-semibold mb-0">Device Items <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm btn-primary" id="addDeviceBtn">
                                        <i class="fas fa-plus me-1"></i>Add Device
                                    </button>
                                </div>
                                
                                <div id="deviceItemsContainer">
                                    <!-- Device items will be added here -->
                                </div>

                                <div id="noDevicesMessage" class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Click "Add Device" to start adding devices to this bulk assignment.
                                </div>
                            </div>
                        </div>

                        <!-- Targets Selection Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-3">Select Targets <span class="text-danger">*</span></label>
                                
                                <!-- Target Type Tabs -->
                                <ul class="nav nav-tabs mb-3" id="targetTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" 
                                                data-bs-target="#users-panel" type="button" role="tab">
                                            <i class="fas fa-users me-2"></i>Users
                                            <span class="badge bg-primary ms-2" id="users_badge">0</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="courts-tab" data-bs-toggle="tab" 
                                                data-bs-target="#courts-panel" type="button" role="tab">
                                            <i class="fas fa-gavel me-2"></i>Courts
                                            <span class="badge bg-primary ms-2" id="courts_badge">0</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="offices-tab" data-bs-toggle="tab" 
                                                data-bs-target="#offices-panel" type="button" role="tab">
                                            <i class="fas fa-building me-2"></i>Offices
                                            <span class="badge bg-primary ms-2" id="offices_badge">0</span>
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="targetTabContent">
                                    <!-- Users Panel -->
                                    <div class="tab-pane fade show active" id="users-panel" role="tabpanel">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" id="user_search" class="form-control form-control-sm" 
                                                               placeholder="Search users...">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select id="user_region_filter" class="form-select form-select-sm">
                                                            <option value="">All Regions</option>
                                                            @foreach($regions as $region)
                                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select id="user_role_filter" class="form-select form-select-sm">
                                                            <option value="">All Roles</option>
                                                            <option value="judge">Judge</option>
                                                            <option value="staff">Staff</option>
                                                            <option value="admin">Admin</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 text-end">
                                                        <button type="button" class="btn btn-sm btn-primary" id="select_all_users">
                                                            <i class="fas fa-check-double"></i> All
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clear_all_users">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body target-selection-body">
                                                <div id="user_no_results" class="text-center text-muted py-3" style="display: none;">
                                                    <i class="fas fa-search fa-2x mb-2"></i>
                                                    <p>No users found matching your filters</p>
                                                </div>
                                                <div class="row g-3" id="users_list">
                                                    @foreach($users as $user)
                                                        <div class="col-md-6 col-lg-4 user-item" 
                                                             data-region="{{ $user->region_id ?? '' }}"
                                                             data-role="{{ strtolower($user->role->name ?? '') }}"
                                                             data-name="{{ strtolower($user->name) }}"
                                                             data-email="{{ strtolower($user->email) }}">
                                                            <div class="form-check p-3 border rounded hover-item">
                                                                <input class="form-check-input target-checkbox user-checkbox" 
                                                                       type="checkbox" name="targets[users][]" 
                                                                       value="{{ $user->id }}" id="user_{{ $user->id }}"
                                                                       {{ in_array($user->id, old('targets.users', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label w-100" for="user_{{ $user->id }}">
                                                                    <strong>{{ $user->name }}</strong>
                                                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                                                    <div class="mt-1">
                                                                        @if($user->region)
                                                                            <span class="badge bg-secondary">{{ $user->region->name }}</span>
                                                                        @endif
                                                                        @if($user->role)
                                                                            <span class="badge bg-info">{{ $user->role->name }}</span>
                                                                        @endif
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Courts Panel -->
                                    <div class="tab-pane fade" id="courts-panel" role="tabpanel">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-5">
                                                        <input type="text" id="court_search" class="form-control form-control-sm" 
                                                               placeholder="Search courts...">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select id="court_region_filter" class="form-select form-select-sm">
                                                            <option value="">All Regions</option>
                                                            @foreach($regions as $region)
                                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-primary w-100" id="select_all_courts">
                                                            <i class="fas fa-check-double"></i> All
                                                        </button>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="clear_all_courts">
                                                            <i class="fas fa-times"></i> Clear
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body target-selection-body">
                                                <div id="court_no_results" class="text-center text-muted py-3" style="display: none;">
                                                    <i class="fas fa-search fa-2x mb-2"></i>
                                                    <p>No courts found matching your filters</p>
                                                </div>
                                                <div class="row g-3" id="courts_list">
                                                    @foreach($courts as $court)
                                                        <div class="col-md-6 col-lg-4 court-item" 
                                                             data-region="{{ $court->region_id ?? '' }}"
                                                             data-name="{{ strtolower($court->name) }}">
                                                            <div class="form-check p-3 border rounded hover-item">
                                                                <input class="form-check-input target-checkbox court-checkbox" 
                                                                       type="checkbox" name="targets[courts][]" 
                                                                       value="{{ $court->id }}" id="court_{{ $court->id }}"
                                                                       {{ in_array($court->id, old('targets.courts', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label w-100" for="court_{{ $court->id }}">
                                                                    <strong>{{ $court->name }}</strong>
                                                                    <small class="text-muted d-block">{{ $court->location->name ?? 'N/A' }}</small>
                                                                    @if($court->region)
                                                                        <span class="badge bg-secondary mt-1">{{ $court->region->name }}</span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Offices Panel -->
                                    <div class="tab-pane fade" id="offices-panel" role="tabpanel">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <div class="row align-items-center g-2">
                                                    <div class="col-md-6">
                                                        <input type="text" id="office_search" class="form-control form-control-sm" 
                                                               placeholder="Search offices...">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select id="office_region_filter" class="form-select form-select-sm">
                                                            <option value="">All Regions</option>
                                                            @foreach($regions as $region)
                                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 d-flex gap-1">
                                                        <button type="button" class="btn btn-sm btn-primary flex-fill" id="select_all_offices">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" id="clear_all_offices">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body target-selection-body">
                                                <div id="office_no_results" class="text-center text-muted py-3" style="display: none;">
                                                    <i class="fas fa-search fa-2x mb-2"></i>
                                                    <p>No offices found matching your filters</p>
                                                </div>
                                                <div class="row g-3" id="offices_list">
                                                    @foreach($offices as $office)
                                                        <div class="col-md-6 col-lg-4 office-item" 
                                                             data-region="{{ $office->region_id ?? '' }}"
                                                             data-name="{{ strtolower($office->name) }}">
                                                            <div class="form-check p-3 border rounded hover-item">
                                                                <input class="form-check-input target-checkbox office-checkbox" 
                                                                       type="checkbox" name="targets[offices][]" 
                                                                       value="{{ $office->id }}" id="office_{{ $office->id }}"
                                                                       {{ in_array($office->id, old('targets.offices', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label w-100" for="office_{{ $office->id }}">
                                                                    <strong>{{ $office->name }}</strong>
                                                                    <small class="text-muted d-block">{{ $office->location->name ?? 'N/A' }}</small>
                                                                    @if($office->region)
                                                                        <span class="badge bg-secondary mt-1">{{ $office->region->name }}</span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="selection_summary" class="mt-3" style="display: none;">
                                    <div class="alert alert-info mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>
                                                    <span id="total_targets">0</span> total target(s):
                                                </strong>
                                                <span id="target_breakdown" class="ms-2"></span>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <strong><span id="total_device_count">0</span> device(s)</strong>
                                                <span class="mx-2">=</span>
                                                <strong class="text-primary fs-5"><span id="total_assets">0</span> assets</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Assigned Date <span class="text-danger">*</span></label>
                                <input type="date" name="assigned_date" class="form-control" 
                                       value="{{ old('assigned_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                                <select name="condition" class="form-select" required>
                                    <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('condition', 'good') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Warranty (Months)</label>
                                <input type="number" name="warranty_months" class="form-control" 
                                       value="{{ old('warranty_months') }}" placeholder="e.g., 24" min="0">
                            </div>
                        </div>

                        <!-- Purchase Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date Purchased</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Purchase Price (Per Asset)</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control" 
                                       value="{{ old('purchase_price') }}" placeholder="0.00" min="0">
                            </div>
                        </div>

                        <!-- Comments -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Comments</label>
                                <textarea name="comments" class="form-control" rows="3" 
                                          placeholder="Any additional comments about this bulk assignment...">{{ old('comments') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="reset" class="btn btn-outline-secondary me-2" id="resetBtn">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                    <i class="fas fa-plus me-2"></i>Create Bulk Assignment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Device Item Template -->
<template id="deviceItemTemplate">
    <div class="card mb-3 device-item" data-device-index="">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0 fw-semibold text-primary">
                    <i class="fas fa-laptop me-2"></i>Device <span class="device-number"></span>
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-device-btn">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select class="form-select device-category device-field" data-field="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Brand</label>
                    <input type="text" class="form-control device-field" data-field="brand"
                           placeholder="e.g., Dell, HP (optional)">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Model</label>
                    <input type="text" class="form-control device-field" data-field="model"
                           placeholder="e.g., Latitude 5420 (optional)">
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control device-quantity device-field" data-field="quantity"
                           min="1" max="100" value="1" required>
                    <small class="text-muted">Number of assets per target for this category</small>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.target-selection-body {
    max-height: 500px;
    overflow-y: auto;
}
.form-check-label {
    cursor: pointer;
    user-select: none;
}
.hover-item {
    transition: all 0.2s ease;
}
.hover-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.hover-item:has(.form-check-input:checked) {
    background-color: #e7f3ff;
    border-color: #007bff !important;
}
.device-item {
    border-left: 3px solid #007bff;
}
.nav-tabs .nav-link {
    color: #495057;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}
.badge {
    font-size: 0.7rem;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');
    const totalTargets = document.getElementById('total_targets');
    const targetBreakdown = document.getElementById('target_breakdown');
    const totalDeviceCount = document.getElementById('total_device_count');
    const totalAssets = document.getElementById('total_assets');
    const selectionSummary = document.getElementById('selection_summary');
    const deviceItemsContainer = document.getElementById('deviceItemsContainer');
    const noDevicesMessage = document.getElementById('noDevicesMessage');
    const addDeviceBtn = document.getElementById('addDeviceBtn');
    const deviceTemplate = document.getElementById('deviceItemTemplate');
    const form = document.getElementById('bulkAssignmentForm');
    
    let deviceCounter = 0;

    // Add device item
    addDeviceBtn.addEventListener('click', function() {
        const index = deviceCounter;
        deviceCounter++;
        
        const clone = deviceTemplate.content.cloneNode(true);
        
        const deviceCard = clone.querySelector('.device-item');
        deviceCard.dataset.deviceIndex = index;
        
        // Update device number
        clone.querySelector('.device-number').textContent = deviceCounter;
        
        // Update name attributes for all fields BEFORE appending to DOM
        const fields = clone.querySelectorAll('.device-field');
        fields.forEach(field => {
            const fieldName = field.dataset.field;
            field.name = `devices[${index}][${fieldName}]`;
        });
        
        // Append to container first
        deviceItemsContainer.appendChild(clone);
        
        // Then add event listeners (after DOM insertion)
        const addedDevice = deviceItemsContainer.querySelector(`[data-device-index="${index}"]`);
        const removeBtn = addedDevice.querySelector('.remove-device-btn');
        removeBtn.addEventListener('click', removeDevice);
        
        const quantityInput = addedDevice.querySelector('.device-quantity');
        quantityInput.addEventListener('input', updateSelectionSummary);
        
        noDevicesMessage.style.display = 'none';
        updateSelectionSummary();
    });

    // Remove device item
    function removeDevice(e) {
        e.target.closest('.device-item').remove();
        reindexDevices();
        
        if (deviceItemsContainer.children.length === 0) {
            noDevicesMessage.style.display = 'block';
            deviceCounter = 0;
        }
        updateSelectionSummary();
    }

    // Reindex devices after removal to ensure continuous array indices
    function reindexDevices() {
        const devices = deviceItemsContainer.querySelectorAll('.device-item');
        devices.forEach((device, index) => {
            device.querySelector('.device-number').textContent = index + 1;
            device.dataset.deviceIndex = index;
            
            // Update all field names with new index
            const fields = device.querySelectorAll('.device-field');
            fields.forEach(field => {
                const fieldName = field.dataset.field;
                field.name = `devices[${index}][${fieldName}]`;
            });
        });
        deviceCounter = devices.length;
    }

    // Reset form
    resetBtn.addEventListener('click', function() {
        setTimeout(() => {
            deviceItemsContainer.innerHTML = '';
            deviceCounter = 0;
            noDevicesMessage.style.display = 'block';
            
            // Uncheck all targets
            document.querySelectorAll('.target-checkbox').forEach(cb => cb.checked = false);
            updateSelectionSummary();
            updateAllBadges();
        }, 0);
    });

    // Calculate total devices across all device items
    function getTotalDeviceCount() {
        const deviceItems = deviceItemsContainer.querySelectorAll('.device-item');
        let total = 0;
        
        deviceItems.forEach(item => {
            const quantityInput = item.querySelector('.device-quantity');
            total += parseInt(quantityInput.value) || 0;
        });
        
        return total;
    }

    // Count selected targets by type
    function getSelectedTargetCounts() {
        return {
            users: document.querySelectorAll('.user-checkbox:checked').length,
            courts: document.querySelectorAll('.court-checkbox:checked').length,
            offices: document.querySelectorAll('.office-checkbox:checked').length
        };
    }

    // Update all badge counts
    function updateAllBadges() {
        const counts = getSelectedTargetCounts();
        document.getElementById('users_badge').textContent = counts.users;
        document.getElementById('courts_badge').textContent = counts.courts;
        document.getElementById('offices_badge').textContent = counts.offices;
    }

    // Update selection summary
    function updateSelectionSummary() {
        const counts = getSelectedTargetCounts();
        const total = counts.users + counts.courts + counts.offices;
        const totalDevices = getTotalDeviceCount();
        const deviceItemCount = deviceItemsContainer.querySelectorAll('.device-item').length;
        
        totalTargets.textContent = total;
        totalDeviceCount.textContent = totalDevices;
        totalAssets.textContent = total * totalDevices;
        
        // Build breakdown text
        const breakdownParts = [];
        if (counts.users > 0) breakdownParts.push(`${counts.users} user(s)`);
        if (counts.courts > 0) breakdownParts.push(`${counts.courts} court(s)`);
        if (counts.offices > 0) breakdownParts.push(`${counts.offices} office(s)`);
        targetBreakdown.textContent = breakdownParts.join(', ');
        
        selectionSummary.style.display = total > 0 && deviceItemCount > 0 ? 'block' : 'none';
        submitBtn.disabled = total === 0 || deviceItemCount === 0;
        
        updateAllBadges();
    }

    // Generic filter function
    function filterItems(containerSelector, itemSelector, searchInput, regionFilter, additionalFilter = null) {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const regionId = regionFilter.value;
        const additionalValue = additionalFilter ? additionalFilter.value.toLowerCase() : '';
        
        const items = document.querySelectorAll(itemSelector);
        let visibleCount = 0;
        
        items.forEach(item => {
            const name = item.dataset.name || '';
            const email = item.dataset.email || '';
            const region = item.dataset.region || '';
            const additionalAttr = additionalFilter ? (item.dataset[additionalFilter.dataset.filterAttr] || '') : '';
            
            const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRegion = !regionId || region === regionId;
            const matchesAdditional = !additionalFilter || !additionalValue || additionalAttr === additionalValue;
            
            if (matchesSearch && matchesRegion && matchesAdditional) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        const noResultsId = containerSelector.replace('_list', '_no_results');
        const noResults = document.getElementById(noResultsId);
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Users filtering
    const userSearch = document.getElementById('user_search');
    const userRegionFilter = document.getElementById('user_region_filter');
    const userRoleFilter = document.getElementById('user_role_filter');
    
    if (userSearch && userRegionFilter && userRoleFilter) {
        userRoleFilter.dataset.filterAttr = 'role';
        
        function filterUsers() {
            filterItems('users_list', '.user-item', userSearch, userRegionFilter, userRoleFilter);
        }
        
        userSearch.addEventListener('input', filterUsers);
        userRegionFilter.addEventListener('change', filterUsers);
        userRoleFilter.addEventListener('change', filterUsers);
        
        // Select all visible users
        document.getElementById('select_all_users')?.addEventListener('click', function() {
            document.querySelectorAll('.user-item:not([style*="display: none"]) .user-checkbox').forEach(cb => {
                cb.checked = true;
            });
            updateSelectionSummary();
        });
        
        // Clear all users
        document.getElementById('clear_all_users')?.addEventListener('click', function() {
            document.querySelectorAll('.user-checkbox').forEach(cb => {
                cb.checked = false;
            });
            updateSelectionSummary();
        });
    }

    // Courts filtering
    const courtSearch = document.getElementById('court_search');
    const courtRegionFilter = document.getElementById('court_region_filter');
    
    if (courtSearch && courtRegionFilter) {
        function filterCourts() {
            filterItems('courts_list', '.court-item', courtSearch, courtRegionFilter);
        }
        
        courtSearch.addEventListener('input', filterCourts);
        courtRegionFilter.addEventListener('change', filterCourts);
        
        document.getElementById('select_all_courts')?.addEventListener('click', function() {
            document.querySelectorAll('.court-item:not([style*="display: none"]) .court-checkbox').forEach(cb => {
                cb.checked = true;
            });
            updateSelectionSummary();
        });
        
        document.getElementById('clear_all_courts')?.addEventListener('click', function() {
            document.querySelectorAll('.court-checkbox').forEach(cb => {
                cb.checked = false;
            });
            updateSelectionSummary();
        });
    }

    // Offices filtering
    const officeSearch = document.getElementById('office_search');
    const officeRegionFilter = document.getElementById('office_region_filter');
    
    if (officeSearch && officeRegionFilter) {
        function filterOffices() {
            filterItems('offices_list', '.office-item', officeSearch, officeRegionFilter);
        }
        
        officeSearch.addEventListener('input', filterOffices);
        officeRegionFilter.addEventListener('change', filterOffices);
        
        document.getElementById('select_all_offices')?.addEventListener('click', function() {
            document.querySelectorAll('.office-item:not([style*="display: none"]) .office-checkbox').forEach(cb => {
                cb.checked = true;
            });
            updateSelectionSummary();
        });
        
        document.getElementById('clear_all_offices')?.addEventListener('click', function() {
            document.querySelectorAll('.office-checkbox').forEach(cb => {
                cb.checked = false;
            });
            updateSelectionSummary();
        });
    }

    // Update summary on checkbox change
    document.querySelectorAll('.target-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionSummary);
    });

    // Initialize
    updateSelectionSummary();
});
</script>
@endpush
@endsection