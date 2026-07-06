@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('offices.index') }}" class="text-decoration-none text-muted">Departments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create New</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Create New Department</h4>
            <p class="text-tiny text-muted mb-0">Add a new department/office to the system.</p>
        </div>
        <div>
            <a href="{{ route('offices.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('offices.store') }}">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Basic Information</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Department Name *</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Code</label>
                                <input type="text" class="form-control form-control-sm @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Description</label>
                                <textarea class="form-control form-control-sm" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                 <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Contact Information</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="row g-3">
                             <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Phone</label>
                                <input type="text" class="form-control form-control-sm" name="phone" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Email</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}">
                            </div>
                             <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Address</label>
                                <textarea class="form-control form-control-sm" name="address" rows="2">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <div class="col-lg-4">
                <!-- Location & Manager -->
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                        <h6 class="card-title-small">Organization</h6>
                    </div>
                    <div class="p-4 pt-1">
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Location</label>
                            <select class="form-select select2" name="location_id" id="location_id">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region</label>
                            <select class="form-select select2" name="region_id" id="region_id">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Head/Manager</label>
                            <select class="form-select select2" name="manager_id">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Capacity</label>
                            <input type="number" class="form-control form-control-sm" name="capacity" value="{{ old('capacity') }}" min="1">
                        </div>
                         <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label text-small">Active Department</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill shadow-sm">
                        <i class="fas fa-save me-1"></i> Create Department
                    </button>
                    <a href="{{ route('offices.index') }}" class="btn btn-light border rounded-pill">
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

    regionSelect.addEventListener('change', function() {
        const regionId = this.value;
        loadLocations(regionId);
    });

    function loadLocations(regionId, selectedLocationId = null) {
        if (!regionId) {
            locationSelect.innerHTML = '<option value="">Select Location</option>';
            $(locationSelect).trigger('change');
            return;
        }

        locationSelect.innerHTML = '<option value="">Loading locations...</option>';
        $(locationSelect).trigger('change');

        fetch(`/api/regions/${regionId}/locations`)
            .then(response => response.json())
            .then(data => {
                locationSelect.innerHTML = '<option value="">Select Location</option>';
                if (data.locations) {
                    data.locations.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.id;
                        option.textContent = location.name;
                        if (selectedLocationId && location.id == selectedLocationId) {
                            option.selected = true;
                        }
                        locationSelect.appendChild(option);
                    });
                }
                $(locationSelect).trigger('change');
            });
    }

    if (regionSelect.value) {
        loadLocations(regionSelect.value, "{{ old('location_id') }}");
    }
});
</script>
@endpush
@endsection