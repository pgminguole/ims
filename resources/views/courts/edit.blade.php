@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('courts') }}" class="text-decoration-none text-muted">Courts</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courts.show', $court) }}" class="text-decoration-none text-muted">{{ Str::limit($court->name, 20) }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Edit Court</h4>
            <p class="text-tiny text-muted mb-0">Update information for {{ $court->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('courts.show', $court) }}" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-dark">
                <i class="fas fa-eye me-1"></i> View
            </a>
            <a href="{{ route('courts') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('courts.update', $court) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Court Name *</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $court->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Court Code *</label>
                                <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code', $court->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                             <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Type *</label>
                                <select class="form-select form-select-sm @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">Select Court Type</option>
                                    <option value="high_court" {{ old('type', $court->type) == 'high_court' ? 'selected' : '' }}>High Court</option>
                                    <option value="district_court" {{ old('type', $court->type) == 'district_court' ? 'selected' : '' }}>District Court</option>
                                    <option value="magistrate_court" {{ old('type', $court->type) == 'magistrate_court' ? 'selected' : '' }}>Magistrate Court</option>
                                    <option value="special_court" {{ old('type', $court->type) == 'special_court' ? 'selected' : '' }}>Special Court</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region *</label>
                                <select class="form-select form-select-sm @error('region_id') is-invalid @enderror" name="region_id" id="region_id" required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $court->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Address *</label>
                                <textarea class="form-control form-control-sm @error('address') is-invalid @enderror" 
                                          name="address" rows="2" required>{{ old('address', $court->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personnel -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Personnel</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Presiding Judge</label>
                                <select class="form-select form-select-sm @error('presiding_judge') is-invalid @enderror" name="presiding_judge">
                                    <option value="">Select Presiding Judge</option>
                                    @foreach($judges as $judge)
                                        <option value="{{ $judge->id }}" {{ old('presiding_judge', $court->presiding_judge) == $judge->id ? 'selected' : '' }}>
                                            {{ $judge->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Registry Officer</label>
                                <select class="form-select form-select-sm @error('registry_officer') is-invalid @enderror" name="registry_officer">
                                    <option value="">Select Registry Officer</option>
                                    @foreach($registryOfficers as $officer)
                                        <option value="{{ $officer->id }}" {{ old('registry_officer', $court->registry_officer) == $officer->id ? 'selected' : '' }}>
                                            {{ $officer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Status</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Active Status</label>
                            <select class="form-select form-select-sm @error('is_active') is-invalid @enderror" name="is_active">
                                <option value="1" {{ old('is_active', $court->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $court->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                             <label class="form-label text-tiny fw-bold text-uppercase text-muted">Location</label>
                            <select class="form-select form-select-sm @error('location_id') is-invalid @enderror" name="location_id" id="location_id">
                                <option value="">Select Location</option>
                                <!-- Locations populated via JS -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Court
                    </button>
                    <a href="{{ route('courts.show', $court) }}" class="btn btn-light border rounded-pill">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const locationSelect = document.getElementById('location_id');
    const currentLocationId = "{{ $court->location_id }}";

    regionSelect.addEventListener('change', function() {
        loadLocations(this.value);
    });

    function loadLocations(regionId, selectedLocationId = null) {
        if (!regionId) {
            locationSelect.innerHTML = '<option value="">Select Location</option>';
            return;
        }

        locationSelect.innerHTML = '<option value="">Loading locations...</option>';

        fetch(`/api/regions/${regionId}/locations`)
            .then(response => response.json())
            .then(data => {
                locationSelect.innerHTML = '<option value="">Select Location</option>';
                if (data.locations) {
                    data.locations.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.id;
                        option.textContent = location.name;
                        if ((selectedLocationId && location.id == selectedLocationId) || 
                            (!selectedLocationId && location.id == currentLocationId)) {
                            option.selected = true;
                        }
                        locationSelect.appendChild(option);
                    });
                }
            });
    }

    if (regionSelect.value) {
        loadLocations(regionSelect.value, "{{ old('location_id') }}");
    }
});
</script>
@endpush
@endsection