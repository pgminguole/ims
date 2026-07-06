@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-{{ isset($location) ? 'edit' : 'plus' }} text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">
                            {{ isset($location) ? 'Edit Location' : 'Create New Location' }}
                        </h5>
                        <a href="{{ route('locations') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="{{ isset($location) ? route('locations.update', $location) : route('locations.create') }}">
                        @csrf
                        @if(isset($location))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Location Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $location->name ?? '') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="building" class="form-label">Building </label>
                                <input type="text" 
                                       class="form-control @error('building') is-invalid @enderror" 
                                       id="building" 
                                       name="building" 
                                       value="{{ old('building', $location->building ?? '') }}" 
                                       >
                                @error('building')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="floor" class="form-label">Floor</label>
                                <input type="text" 
                                       class="form-control @error('floor') is-invalid @enderror" 
                                       id="floor" 
                                       name="floor" 
                                       value="{{ old('floor', $location->floor ?? '') }}">
                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="room" class="form-label">Room</label>
                                <input type="text" 
                                       class="form-control @error('room') is-invalid @enderror" 
                                       id="room" 
                                       name="room" 
                                       value="{{ old('room', $location->room ?? '') }}">
                                @error('room')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="region_id" class="form-label">Region <span class="text-danger">*</span></label>
                                <select class="form-select @error('region_id') is-invalid @enderror" 
                                        id="region_id" 
                                        name="region_id" 
                                        required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" 
                                                {{ old('region_id', $location->region_id ?? '') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3">{{ old('description', $location->description ?? '') }}</textarea>
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
                                           {{ old('is_active', isset($location) ? $location->is_active : true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Location
                                    </label>
                                </div>
                                <small class="text-muted">Inactive locations won't be available for new assignments</small>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>
                                        {{ isset($location) ? 'Update Location' : 'Create Location' }}
                                    </button>
                                    <a href="{{ route('locations') }}" class="btn btn-outline-secondary px-4">
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