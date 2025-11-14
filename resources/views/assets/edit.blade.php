@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Edit Asset</h1>
            <p class="text-muted">Update asset information</p>
        </div>
        <div class="col-auto">
            <div class="btn-group-compact">
                <a href="{{ route('assets.show', $asset->slug) }}" class="btn btn-outline-secondary btn-compact">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('assets.update', $asset->slug) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="asset-form-section">
            <div class="section-header">Basic Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Asset Name *</label>
                    <input type="text" class="form-control @error('asset_name') is-invalid @enderror" 
                           name="asset_name" value="{{ old('asset_name', $asset->asset_name) }}" required>
                    @error('asset_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Asset Tag *</label>
                    <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" 
                           name="asset_tag" value="{{ old('asset_tag', $asset->asset_tag) }}" required>
                    @error('asset_tag')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Serial Number *</label>
                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                           name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" required>
                    @error('serial_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Model *</label>
                    <input type="text" class="form-control @error('model') is-invalid @enderror" 
                           name="model" value="{{ old('model', $asset->model) }}" required>
                    @error('model')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Brand *</label>
                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                           name="brand" value="{{ old('brand', $asset->brand) }}" required>
                    @error('brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" 
                           name="manufacturer" value="{{ old('manufacturer', $asset->manufacturer) }}">
                    @error('manufacturer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Classification -->
        <div class="asset-form-section">
            <div class="section-header">Classification</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Subcategory</label>
                    <select class="form-select @error('subcategory_id') is-invalid @enderror" name="subcategory_id">
                        <option value="">Select Subcategory</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $asset->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subcategory_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Region *</label>
                    <select class="form-select @error('region_id') is-invalid @enderror" name="region_id" required>
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id', $asset->region_id) == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Court</label>
                    <select class="form-select @error('court_id') is-invalid @enderror" name="court_id">
                        <option value="">Select Court</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ old('court_id', $asset->court_id) == $court->id ? 'selected' : '' }}>
                                {{ $court->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status & Condition -->
        <div class="asset-form-section">
            <div class="section-header">Status & Condition</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                        <option value="available" {{ old('status', $asset->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="assigned" {{ old('status', $asset->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="retired" {{ old('status', $asset->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                        <option value="lost" {{ old('status', $asset->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                        <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Condition *</label>
                    <select class="form-select @error('condition') is-invalid @enderror" name="condition" required>
                        <option value="excellent" {{ old('condition', $asset->condition) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition', $asset->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition', $asset->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="broken" {{ old('condition', $asset->condition) == 'broken' ? 'selected' : '' }}>Broken</option>
                    </select>
                    @error('condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Assigned To</label>
                    <select class="form-select @error('assigned_to') is-invalid @enderror" name="assigned_to">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $asset->assigned_to) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Assignment Type</label>
                    <select class="form-select @error('assigned_type') is-invalid @enderror" name="assigned_type">
                        <option value="">Select Type</option>
                        <option value="judge" {{ old('assigned_type', $asset->assigned_type) == 'judge' ? 'selected' : '' }}>Judge</option>
                        <option value="staff" {{ old('assigned_type', $asset->assigned_type) == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="department" {{ old('assigned_type', $asset->assigned_type) == 'department' ? 'selected' : '' }}>Department</option>
                        <option value="court" {{ old('assigned_type', $asset->assigned_type) == 'court' ? 'selected' : '' }}>Court</option>
                    </select>
                    @error('assigned_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Purchase & Financial -->
        <div class="asset-form-section">
            <div class="section-header">Purchase & Financial</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Purchase Date *</label>
                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                           name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" required>
                    @error('purchase_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Received Date</label>
                    <input type="date" class="form-control @error('recieved_date') is-invalid @enderror" 
                           name="recieved_date" value="{{ old('recieved_date', $asset->recieved_date?->format('Y-m-d')) }}">
                    @error('recieved_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Assigned Date</label>
                    <input type="date" class="form-control @error('assigned_date') is-invalid @enderror" 
                           name="assigned_date" value="{{ old('assigned_date', $asset->assigned_date?->format('Y-m-d')) }}">
                    @error('assigned_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Returned Date</label>
                    <input type="date" class="form-control @error('returned_date') is-invalid @enderror" 
                           name="returned_date" value="{{ old('returned_date', $asset->returned_date?->format('Y-m-d')) }}">
                    @error('returned_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Purchase Cost</label>
                    <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                           name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}">
                    @error('purchase_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Current Value</label>
                    <input type="number" step="0.01" class="form-control @error('current_value') is-invalid @enderror" 
                           name="current_value" value="{{ old('current_value', $asset->current_value) }}">
                    @error('current_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                           name="supplier" value="{{ old('supplier', $asset->supplier) }}">
                    @error('supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Warranty & Maintenance -->
        <div class="asset-form-section">
            <div class="section-header">Warranty & Maintenance</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Warranty Period</label>
                    <input type="text" class="form-control @error('warranty_period') is-invalid @enderror" 
                           name="warranty_period" value="{{ old('warranty_period', $asset->warranty_period) }}">
                    @error('warranty_period')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Warranty Expiry</label>
                    <input type="date" class="form-control @error('warranty_expiry') is-invalid @enderror" 
                           name="warranty_expiry" value="{{ old('warranty_expiry', $asset->warranty_expiry?->format('Y-m-d')) }}">
                    @error('warranty_expiry')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Last Maintenance</label>
                    <input type="date" class="form-control @error('last_maintenance') is-invalid @enderror" 
                           name="last_maintenance" value="{{ old('last_maintenance', $asset->last_maintenance?->format('Y-m-d')) }}">
                    @error('last_maintenance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Next Maintenance</label>
                    <input type="date" class="form-control @error('next_maintenance') is-invalid @enderror" 
                           name="next_maintenance" value="{{ old('next_maintenance', $asset->next_maintenance?->format('Y-m-d')) }}">
                    @error('next_maintenance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="asset-form-section">
            <div class="section-header">Additional Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">IP Address</label>
                    <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                           name="ip_address" value="{{ old('ip_address', $asset->ip_address) }}">
                    @error('ip_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">MAC Address</label>
                    <input type="text" class="form-control @error('mac_address') is-invalid @enderror" 
                           name="mac_address" value="{{ old('mac_address', $asset->mac_address) }}">
                    @error('mac_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Returned Reason</label>
                    <input type="text" class="form-control @error('returned_reason') is-invalid @enderror" 
                           name="returned_reason" value="{{ old('returned_reason', $asset->returned_reason) }}">
                    @error('returned_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Returnee</label>
                    <input type="text" class="form-control @error('returnee') is-invalid @enderror" 
                           name="returnee" value="{{ old('returnee', $asset->returnee) }}">
                    @error('returnee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              name="description" rows="3">{{ old('description', $asset->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Specifications</label>
                    <textarea class="form-control @error('specifications') is-invalid @enderror" 
                              name="specifications" rows="3">{{ old('specifications', $asset->specifications) }}</textarea>
                    @error('specifications')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Warranty Information</label>
                    <textarea class="form-control @error('warranty_information') is-invalid @enderror" 
                              name="warranty_information" rows="2">{{ old('warranty_information', $asset->warranty_information) }}</textarea>
                    @error('warranty_information')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Maintenance Notes</label>
                    <textarea class="form-control @error('maintenance_notes') is-invalid @enderror" 
                              name="maintenance_notes" rows="2">{{ old('maintenance_notes', $asset->maintenance_notes) }}</textarea>
                    @error('maintenance_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('assets.show', $asset->slug) }}" class="btn btn-outline-secondary btn-compact">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-compact">
                <i class="fas fa-save"></i> Update Asset
            </button>
        </div>
    </form>
</div>
@endsection