@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}" class="text-decoration-none text-muted">Assets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create New</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Add New Asset</h4>
            <p class="text-tiny text-muted mb-0">Register a new asset in the system.</p>
        </div>
        <div>
            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-dark">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('assets.create') }}" method="POST">
        @csrf
        
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
                                       name="asset_name" value="{{ old('asset_name') }}" required>
                                @error('asset_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Asset Tag *</label>
                                <input type="text" class="form-control form-control-sm @error('asset_tag') is-invalid @enderror" 
                                       name="asset_tag" value="{{ old('asset_tag') }}" required>
                                @error('asset_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Serial Number</label>
                                <input type="text" class="form-control form-control-sm @error('serial_number') is-invalid @enderror" 
                                       name="serial_number" value="{{ old('serial_number') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Brand *</label>
                                <input type="text" class="form-control form-control-sm @error('brand') is-invalid @enderror" 
                                       name="brand" value="{{ old('brand') }}" >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Model *</label>
                                <input type="text" class="form-control form-control-sm @error('model') is-invalid @enderror" 
                                       name="model" value="{{ old('model') }}" >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Manufacturer</label>
                                <input type="text" class="form-control form-control-sm @error('manufacturer') is-invalid @enderror" 
                                       name="manufacturer" value="{{ old('manufacturer') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Quantity *</label>
                                <input type="number" class="form-control form-control-sm @error('quantity') is-invalid @enderror" 
                                    name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                                <div class="form-text text-tiny">Number of duplicates to create.</div>
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
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Purchase Date *</label>
                                <input type="date" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror" 
                                       name="purchase_date" value="{{ old('purchase_date') }}" >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Received Date</label>
                                <input type="date" class="form-control form-control-sm @error('recieved_date') is-invalid @enderror" 
                                       name="recieved_date" value="{{ old('recieved_date') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Purchase Cost</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">GHS</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm border-start-0 ps-0 @error('purchase_cost') is-invalid @enderror" 
                                           name="purchase_cost" value="{{ old('purchase_cost') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Current Value</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">GHS</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm border-start-0 ps-0 @error('current_value') is-invalid @enderror" 
                                           name="current_value" value="{{ old('current_value') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Supplier</label>
                                <input type="text" class="form-control form-control-sm @error('supplier') is-invalid @enderror" 
                                       name="supplier" value="{{ old('supplier') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Warranty Info</label>
                                <textarea class="form-control form-control-sm @error('warranty_information') is-invalid @enderror" 
                                          name="warranty_information" rows="2">{{ old('warranty_information') }}</textarea>
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
                                          name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Specifications</label>
                                <textarea class="form-control form-control-sm @error('specifications') is-invalid @enderror" 
                                          name="specifications" rows="3">{{ old('specifications') }}</textarea>
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
                                <option value="assignment" {{ old('record_type') == 'assignment' ? 'selected' : '' }}>Official Assignment</option>
                                <option value="inventory" {{ old('record_type') == 'inventory' ? 'selected' : '' }}>Inventory Collection</option>
                            </select>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Category *</label>
                            <select class="form-select form-select-sm select2 @error('category_id') is-invalid @enderror" name="category_id" >
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $subcategory->id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
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
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Condition *</label>
                            <select class="form-select form-select-sm select2 @error('condition') is-invalid @enderror" name="condition" required>
                                <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                <option value="broken" {{ old('condition') == 'broken' ? 'selected' : '' }}>Broken</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Assignment (Optional) -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Initial Assignment</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Assign To Type</label>
                            <select class="form-select form-select-sm" id="assign_type" name="assigned_type">
                                <option value="">Select Type...</option>
                                <option value="user" {{ old('assigned_type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="office" {{ old('assigned_type') == 'office' ? 'selected' : '' }}>Office</option>
                                <option value="court" {{ old('assigned_type') == 'court' ? 'selected' : '' }}>Court</option>
                                <option value="region" {{ old('assigned_type') == 'region' ? 'selected' : '' }}>Region</option>
                            </select>
                        </div>

                        <div id="user_section" style="display: {{ old('assigned_type') == 'user' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Assigned User</label>
                                <select class="form-select form-select-sm select2" name="assigned_to" id="user_select" {{ old('assigned_type') == 'user' ? '' : 'disabled' }}>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="office_section" style="display: {{ old('assigned_type') == 'office' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Office</label>
                                <select class="form-select form-select-sm select2" name="office_id" id="office_select" {{ old('assigned_type') == 'office' ? '' : 'disabled' }}>
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                            {{ $office->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="court_section" style="display: {{ old('assigned_type') == 'court' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Court</label>
                                <select class="form-select form-select-sm select2" name="court_id" id="court_select" {{ old('assigned_type') == 'court' ? '' : 'disabled' }}>
                                    <option value="">Select Court</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                            {{ $court->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="region_section" style="display: {{ old('assigned_type') == 'region' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region</label>
                                <select class="form-select form-select-sm select2" name="region_id" id="region_select" {{ old('assigned_type') == 'region' ? '' : 'disabled' }}>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                         <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Date Assigned</label>
                            <input type="date" class="form-control form-control-sm @error('assigned_date') is-invalid @enderror" 
                                   name="assigned_date" value="{{ old('assigned_date', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill shadow-sm">
                        <i class="fas fa-save me-1"></i> Create Asset
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-light border rounded-pill">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('assign_type');
    const sections = {
        user: document.getElementById('user_section'),
        office: document.getElementById('office_section'),
        court: document.getElementById('court_section'),
        region: document.getElementById('region_section')
    };
    const selects = {
        user: document.getElementById('user_select'),
        office: document.getElementById('office_select'),
        court: document.getElementById('court_select'),
        region: document.getElementById('region_select')
    };

    function toggleSections() {
        const selectedType = typeSelect.value;
        
        Object.keys(sections).forEach(type => {
            if (type === selectedType) {
                sections[type].style.display = 'block';
                if (selects[type]) selects[type].disabled = false;
            } else {
                sections[type].style.display = 'none';
                if (selects[type]) selects[type].disabled = true;
            }
        });
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleSections);
    }
});
</script>
@endpush