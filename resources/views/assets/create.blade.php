@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Add New Asset</h1>
            <p class="text-muted">Register a new asset in the system</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-arrow-left"></i> Back to Assets
            </a>
        </div>
    </div>

    <form action="{{ route('assets.create') }}" method="POST">
        @csrf
        
        <!-- Basic Information -->
        <div class="asset-form-section">
            <div class="section-header">Basic Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Asset Name *</label>
                    <input type="text" class="form-control @error('asset_name') is-invalid @enderror" 
                           name="asset_name" value="{{ old('asset_name') }}" required>
                    @error('asset_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Asset Tag *</label>
                    <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" 
                           name="asset_tag" value="{{ old('asset_tag') }}" required>
                    @error('asset_tag')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Serial Number </label>
                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                           name="serial_number" value="{{ old('serial_number') }}">
                    @error('serial_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

               <div class="form-group">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                    name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                <small class="form-text text-muted">Number of assets to create with same specifications</small>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

                <div class="form-group">
                    <label class="form-label">Model *</label>
                    <input type="text" class="form-control @error('model') is-invalid @enderror" 
                           name="model" value="{{ old('model') }}" >
                    @error('model')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Brand *</label>
                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                           name="brand" value="{{ old('brand') }}" >
                    @error('brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" 
                           name="manufacturer" value="{{ old('manufacturer') }}">
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
                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" >
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            <option value="{{ $subcategory->id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
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
                    <select class="form-select @error('region_id') is-invalid @enderror" name="region_id">
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
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
                            <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                {{ $court->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                  <div class="form-group">
                    <label class="form-label">Location </label>
                    <select class="form-select @error('location_id') is-invalid @enderror" name="location_id">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
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
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Condition *</label>
                    <select class="form-select @error('condition') is-invalid @enderror" name="condition" required>
                        <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="broken" {{ old('condition') == 'broken' ? 'selected' : '' }}>Broken</option>
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
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
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
                        <option value="judge" {{ old('assigned_type') == 'judge' ? 'selected' : '' }}>Judge</option>
                        <option value="staff" {{ old('assigned_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="department" {{ old('assigned_type') == 'department' ? 'selected' : '' }}>Department</option>
                        <option value="court" {{ old('assigned_type') == 'court' ? 'selected' : '' }}>Court</option>
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
                           name="purchase_date" value="{{ old('purchase_date') }}" >
                    @error('purchase_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Received Date</label>
                    <input type="date" class="form-control @error('recieved_date') is-invalid @enderror" 
                           name="recieved_date" value="{{ old('recieved_date') }}">
                    @error('recieved_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Assigned Date</label>
                    <input type="date" class="form-control @error('assigned_date') is-invalid @enderror" 
                           name="assigned_date" value="{{ old('assigned_date') }}">
                    @error('assigned_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Purchase Cost</label>
                    <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                           name="purchase_cost" value="{{ old('purchase_cost') }}">
                    @error('purchase_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Current Value</label>
                    <input type="number" step="0.01" class="form-control @error('current_value') is-invalid @enderror" 
                           name="current_value" value="{{ old('current_value') }}">
                    @error('current_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                           name="supplier" value="{{ old('supplier') }}">
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
                           name="warranty_period" value="{{ old('warranty_period') }}">
                    @error('warranty_period')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Warranty Expiry</label>
                    <input type="date" class="form-control @error('warranty_expiry') is-invalid @enderror" 
                           name="warranty_expiry" value="{{ old('warranty_expiry') }}">
                    @error('warranty_expiry')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Last Maintenance</label>
                    <input type="date" class="form-control @error('last_maintenance') is-invalid @enderror" 
                           name="last_maintenance" value="{{ old('last_maintenance') }}">
                    @error('last_maintenance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Next Maintenance</label>
                    <input type="date" class="form-control @error('next_maintenance') is-invalid @enderror" 
                           name="next_maintenance" value="{{ old('next_maintenance') }}">
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
                           name="ip_address" value="{{ old('ip_address') }}">
                    @error('ip_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">MAC Address</label>
                    <input type="text" class="form-control @error('mac_address') is-invalid @enderror" 
                           name="mac_address" value="{{ old('mac_address') }}">
                    @error('mac_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Specifications</label>
                    <textarea class="form-control @error('specifications') is-invalid @enderror" 
                              name="specifications" rows="3">{{ old('specifications') }}</textarea>
                    @error('specifications')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Warranty Information</label>
                    <textarea class="form-control @error('warranty_information') is-invalid @enderror" 
                              name="warranty_information" rows="2">{{ old('warranty_information') }}</textarea>
                    @error('warranty_information')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-compact">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-compact">
                <i class="fas fa-save"></i> Create Asset
            </button>
        </div>
    </form>
</div>
@endsection