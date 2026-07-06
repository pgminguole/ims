@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">Edit Region</h5>
                        <a href="{{ route('regions.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back to Regions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('regions.update', $region) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Region Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $region->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="code" class="form-label">Region Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $region->code) }}" 
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique code for the region</small>
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4">{{ old('description', $region->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $region->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Region
                                    </label>
                                </div>
                                <small class="text-muted">Inactive regions won't be available for new assignments</small>
                            </div>

                            <!-- Region Statistics -->
                            <div class="col-md-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Region Statistics</h6>
                                        <div class="row g-3">
                                            <div class="col-md-3 text-center">
                                                <div class="text-primary fw-bold h5 mb-1">{{ $region->courts->count() }}</div>
                                                <small class="text-muted">Courts</small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="text-primary fw-bold h5 mb-1">{{ $region->locations->count() }}</div>
                                                <small class="text-muted">Locations</small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="text-primary fw-bold h5 mb-1">{{ $region->assets->count() }}</div>
                                                <small class="text-muted">Assets</small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="text-primary fw-bold h5 mb-1">{{ $region->users->count() }}</div>
                                                <small class="text-muted">Users</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($region->courts->count() > 0 || $region->locations->count() > 0 || $region->assets->count() > 0)
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Important Note</h6>
                                    <p class="mb-0">
                                        This region has associated courts, locations, and/or assets. 
                                        Making this region inactive may affect these associations.
                                    </p>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Update Region
                                    </button>
                                    <a href="{{ route('regions.index') }}" class="btn btn-outline-secondary px-4">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection