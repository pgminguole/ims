@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Add New Court</h1>
            <p class="text-muted">Register a new court in the system</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courts') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-arrow-left"></i> Back to Courts
            </a>
        </div>
    </div>

    <form action="{{ route('courts.store') }}" method="POST">
        @csrf
        
        <!-- Basic Information -->
        <div class="asset-form-section">
            <div class="section-header">Basic Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Court Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Court Code *</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                           name="code" value="{{ old('code') }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Court Type *</label>
                    <select class="form-select select2 @error('type') is-invalid @enderror" name="type" required>
                        <option value="">Select Court Type</option>
                         <option value="supreme_court" {{ old('type') == 'supreme_court' ? 'selected' : '' }}>Supreme Court</option>
                         <option value="appeal_court" {{ old('type') == 'appeal_court' ? 'selected' : '' }}>Court of Appeal</option>
                        <option value="high_court" {{ old('type') == 'high_court' ? 'selected' : '' }}>High Court</option>
                                  <option value="circuit_court" {{ old('type') == 'circuit_court' ? 'selected' : '' }}>Circuit Court</option>
                        <option value="district_court" {{ old('type') == 'district_court' ? 'selected' : '' }}>District Court</option>
                
                         <option value="probate_court" {{ old('type') == 'probate_court' ? 'selected' : '' }}>Probate Court</option>
                        <option value="magistrate_court" {{ old('type') == 'magistrate_court' ? 'selected' : '' }}>Magistrate Court</option>
                        <option value="special_court" {{ old('type') == 'special_court' ? 'selected' : '' }}>Special Court</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Region *</label>
                    <select class="form-select select2 @error('region_id') is-invalid @enderror" 
                            name="region_id" id="region_id" required>
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
            </div>
        </div>

        <!-- Location Information -->
        <div class="asset-form-section">
            <div class="section-header">Location Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <select class="form-select select2 @error('location_id') is-invalid @enderror" 
                            name="location_id" id="location_id">
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

                <div class="form-group form-group-full">
                    <label class="form-label">Address </label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              name="address" rows="3" >{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Personnel Assignment -->
        <div class="asset-form-section">
            <div class="section-header">Personnel Assignment</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Presiding Judge</label>
                    <select class="form-select select2 @error('presiding_judge') is-invalid @enderror" name="presiding_judge">
                        <option value="">Select Presiding Judge</option>
                        @foreach($judges as $judge)
                            <option value="{{ $judge->id }}" {{ old('presiding_judge') == $judge->id ? 'selected' : '' }}>
                                {{ $judge->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('presiding_judge')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Registry Officer</label>
                    <select class="form-select select2 @error('registry_officer') is-invalid @enderror" name="registry_officer">
                        <option value="">Select Registry Officer</option>
                        @foreach($registryOfficers as $officer)
                            <option value="{{ $officer->id }}" {{ old('registry_officer') == $officer->id ? 'selected' : '' }}>
                                {{ $officer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('registry_officer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="asset-form-section">
            <div class="section-header">Status</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Active Status</label>
                    <select class="form-select select2 @error('is_active') is-invalid @enderror" name="is_active">
                        <option value="1" {{ old('is_active', 1) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('courts') }}" class="btn btn-outline-secondary btn-compact">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-compact">
                <i class="fas fa-save"></i> Create Court
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const locationSelect = document.getElementById('location_id');
    const oldLocationId = "{{ old('location_id') }}";

    // Function to load locations based on region
    function loadLocations(regionId, selectedLocationId = null) {
        if (!regionId) {
            locationSelect.innerHTML = '<option value="">Select Location</option>';
            $(locationSelect).trigger('change.select2');
            return;
        }

        // Show loading state
        locationSelect.innerHTML = '<option value="">Loading locations...</option>';
        locationSelect.disabled = true;
        $(locationSelect).trigger('change.select2');

        // Fetch locations for the selected region
        fetch(`/api/regions/${regionId}/locations`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Clear and populate the location dropdown
                locationSelect.innerHTML = '<option value="">Select Location</option>';
                
                if (data.locations && data.locations.length > 0) {
                    data.locations.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.id;
                        option.textContent = location.name;
                        
                        // Pre-select if it matches the old value or selected value
                        if (selectedLocationId && location.id == selectedLocationId) {
                            option.selected = true;
                        }
                        
                        locationSelect.appendChild(option);
                    });
                } else {
                    locationSelect.innerHTML = '<option value="">No locations available</option>';
                }
                
                locationSelect.disabled = false;
                $(locationSelect).trigger('change.select2');
            })
            .catch(error => {
                console.error('Error loading locations:', error);
                locationSelect.innerHTML = '<option value="">Error loading locations</option>';
                locationSelect.disabled = false;
                $(locationSelect).trigger('change.select2');
            });
    }

    // Event listener for region change
    regionSelect.addEventListener('change', function() {
        const regionId = this.value;
        loadLocations(regionId);
    });

    // Load locations on page load if region is pre-selected
    if (regionSelect.value) {
        loadLocations(regionSelect.value, oldLocationId);
    }
});
</script>
@endpush
@endsection