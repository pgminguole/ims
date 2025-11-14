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
                    <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                        <option value="">Select Court Type</option>
                        <option value="high_court" {{ old('type') == 'high_court' ? 'selected' : '' }}>High Court</option>
                        <option value="district_court" {{ old('type') == 'district_court' ? 'selected' : '' }}>District Court</option>
                        <option value="magistrate_court" {{ old('type') == 'magistrate_court' ? 'selected' : '' }}>Magistrate Court</option>
                        <option value="special_court" {{ old('type') == 'special_court' ? 'selected' : '' }}>Special Court</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Region *</label>
                    <select class="form-select @error('region_id') is-invalid @enderror" name="region_id" required>
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
                    <select class="form-select @error('location_id') is-invalid @enderror" name="location_id">
                        <option value="">Select Location</option>
                        <!-- Locations will be populated based on selected region -->
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              name="address" rows="3" required>{{ old('address') }}</textarea>
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
                    <select class="form-select @error('presiding_judge') is-invalid @enderror" name="presiding_judge">
                        <option value="">Select Presiding Judge</option>
                        @foreach($judges as $judge)
                            <option value="{{ $judge->id }}" {{ old('presiding_judge') == $judge->id ? 'selected' : '' }}>
                                {{ $judge->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('presiding_judge')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Registry Officer</label>
                    <select class="form-select @error('registry_officer') is-invalid @enderror" name="registry_officer">
                        <option value="">Select Registry Officer</option>
                        @foreach($registryOfficers as $officer)
                            <option value="{{ $officer->id }}" {{ old('registry_officer') == $officer->id ? 'selected' : '' }}>
                                {{ $officer->full_name }}
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
                    <select class="form-select @error('is_active') is-invalid @enderror" name="is_active">
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
@endsection