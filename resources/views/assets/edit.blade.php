@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}" class="text-decoration-none text-muted">Assets</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assets.show', $asset->slug) }}" class="text-decoration-none text-muted">{{ Str::limit($asset->asset_name, 20) }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Edit Asset</h4>
            <p class="text-tiny text-muted mb-0">Update information for {{ $asset->asset_tag }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('assets.show', $asset->slug) }}" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-dark">
                <i class="fas fa-eye me-1"></i> View
            </a>
            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('assets.update', $asset) }}" method="POST">
        @csrf
        @method('POST')
        
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Basic Information</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Asset Name *</label>
                                <input type="text" class="form-control form-control-sm @error('asset_name') is-invalid @enderror" 
                                       name="asset_name" value="{{ old('asset_name', $asset->asset_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Asset Tag</label>
                                <input type="text" class="form-control form-control-sm @error('asset_tag') is-invalid @enderror" 
                                       name="asset_tag" value="{{ old('asset_tag', $asset->asset_tag) }}" >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Serial Number</label>
                                <input type="text" class="form-control form-control-sm @error('serial_number') is-invalid @enderror" 
                                       name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Brand</label>
                                <input type="text" class="form-control form-control-sm @error('brand') is-invalid @enderror" 
                                       name="brand" value="{{ old('brand', $asset->brand) }}" >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Model</label>
                                <input type="text" class="form-control form-control-sm @error('model') is-invalid @enderror" 
                                       name="model" value="{{ old('model', $asset->model) }}" >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Manufacturer</label>
                                <input type="text" class="form-control form-control-sm @error('manufacturer') is-invalid @enderror" 
                                       name="manufacturer" value="{{ old('manufacturer', $asset->manufacturer) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase & Financial -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Purchase & Financial</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Purchase Date</label>
                                <input type="date" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror" 
                                       name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Received Date</label>
                                <input type="date" class="form-control form-control-sm @error('recieved_date') is-invalid @enderror" 
                                       name="recieved_date" value="{{ old('recieved_date', $asset->recieved_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Purchase Cost</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">GHS</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm border-start-0 ps-0 @error('purchase_cost') is-invalid @enderror" 
                                           name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Current Value</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">GHS</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm border-start-0 ps-0 @error('current_value') is-invalid @enderror" 
                                           name="current_value" value="{{ old('current_value', $asset->current_value) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Supplier</label>
                                <input type="text" class="form-control form-control-sm @error('supplier') is-invalid @enderror" 
                                       name="supplier" value="{{ old('supplier', $asset->supplier) }}">
                            </div>

                             <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Warranty Info</label>
                                <textarea class="form-control form-control-sm @error('warranty_information') is-invalid @enderror" 
                                          name="warranty_information" rows="2">{{ old('warranty_information', $asset->warranty_information) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                 <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Additional Notes</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="row g-3">
                             <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Description</label>
                                <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                          name="description" rows="3">{{ old('description', $asset->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Comments</label>
                                <textarea class="form-control form-control-sm @error('comments') is-invalid @enderror" 
                                          name="comments" rows="2" placeholder="Assignment-specific comments or notes">{{ old('comments', $asset->comments) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Specifications</label>
                                <textarea class="form-control form-control-sm @error('specifications') is-invalid @enderror" 
                                          name="specifications" rows="3">{{ old('specifications', $asset->specifications) }}</textarea>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Classification -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Classification</h6>
                    </div>
                    <div class="p-4 pt-1">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasAssignedRole('super_admin'))
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Record Type *</label>
                            <select class="form-select form-select-sm" name="record_type" required>
                                <option value="assignment" {{ old('record_type', $asset->record_type) == 'assignment' ? 'selected' : '' }}>Official Assignment</option>
                                <option value="inventory" {{ old('record_type', $asset->record_type) == 'inventory' ? 'selected' : '' }}>Inventory Collection</option>
                            </select>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Category *</label>
                            <select class="form-select form-select-sm select2 @error('category_id') is-invalid @enderror" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Subcategory</label>
                            <select class="form-select form-select-sm select2 @error('subcategory_id') is-invalid @enderror" name="subcategory_id">
                                <option value="">Select Subcategory</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $asset->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                 <!-- Status & Condition -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Status & Condition</h6>
                    </div>
                    <div class="p-4 pt-1">
                         <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Status *</label>
                            <select class="form-select form-select-sm select2 @error('status') is-invalid @enderror" name="status" required>
                                <option value="available" {{ old('status', $asset->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="assigned" {{ old('status', $asset->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ old('status', $asset->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                <option value="lost" {{ old('status', $asset->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Condition *</label>
                            <select class="form-select form-select-sm select2 @error('condition') is-invalid @enderror" name="condition" required>
                                <option value="excellent" {{ old('condition', $asset->condition) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ old('condition', $asset->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('condition', $asset->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                                <option value="broken" {{ old('condition', $asset->condition) == 'broken' ? 'selected' : '' }}>Broken</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Assignment -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Assignment Info</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <!-- Hidden fields for polymorphic assignment -->
                        <input type="hidden" name="assigned_to" id="assigned_to" value="{{ old('assigned_to', $asset->assigned_to) }}">
                        <input type="hidden" name="assigned_type" id="assigned_type" value="{{ old('assigned_type', $asset->assigned_type) }}">

                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region</label>
                            <select class="form-select form-select-sm select2 assignment-target @error('region_id') is-invalid @enderror" 
                                    name="region_id" id="assignment_region" data-type="region">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ (old('region_id', $asset->region_id) == $region->id || ($asset->assigned_type === 'region' && $asset->assigned_to == $region->id)) ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Court</label>
                            <select class="form-select form-select-sm select2 assignment-target @error('court_id') is-invalid @enderror" 
                                    name="court_id" id="assignment_court" data-type="court">
                                <option value="">Select Court</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->id }}" {{ (old('court_id', $asset->court_id) == $court->id || ($asset->assigned_type === 'court' && $asset->assigned_to == $court->id)) ? 'selected' : '' }}>
                                        {{ $court->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Office Unit</label>
                            <select class="form-select form-select-sm select2 assignment-target @error('office_id') is-invalid @enderror" 
                                    name="office_id" id="assignment_office" data-type="office">
                                <option value="">Select Office</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ (old('office_id', $asset->office_id) == $office->id || ($asset->assigned_type === 'office' && $asset->assigned_to == $office->id)) ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Location</label>
                            <select class="form-select form-select-sm select2 @error('location_id') is-invalid @enderror" name="location_id">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Assigned User</label>
                            <select class="form-select form-select-sm select2 assignment-target @error('assigned_to_user') is-invalid @enderror" 
                                    id="assignment_user" data-type="user">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (old('assigned_to', $asset->assigned_to) == $user->id && (old('assigned_type', $asset->assigned_type) === 'user' || $asset->assigned_type === 'user')) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Date Assigned</label>
                            <input type="date" class="form-control form-control-sm @error('assigned_date') is-invalid @enderror" 
                                   name="assigned_date" value="{{ old('assigned_date', $asset->assigned_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targets = document.querySelectorAll('.assignment-target');
                        const assignedTo = document.getElementById('assigned_to');
                        const assignedType = document.getElementById('assigned_type');

                        function updateAssignment(element) {
                            const val = element.value;
                            const type = element.dataset.type;

                            if (val) {
                                assignedTo.value = val;
                                assignedType.value = type;
                                
                                // Optional: Clear others to avoid confusion, though controller handles priority
                                /*
                                targets.forEach(t => {
                                    if (t !== element) $(t).val(null).trigger('change.select2');
                                });
                                */
                            } else {
                                // If current type element is cleared, check others or clear main fields
                                if (assignedType.value === type) {
                                    assignedTo.value = '';
                                    assignedType.value = '';
                                }
                            }
                        }

                        // Use jQuery for Select2 compatibility if present
                        if (typeof $ !== 'undefined') {
                            $('.assignment-target').on('change', function() {
                                updateAssignment(this);
                            });
                        } else {
                            targets.forEach(t => t.addEventListener('change', () => updateAssignment(t)));
                        }
                    });
                </script>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Asset
                    </button>
                    <a href="{{ route('assets.show', $asset->slug) }}" class="btn btn-light border rounded-pill">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection