@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-{{ isset($maintenance) ? 'edit' : 'plus' }} text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">
                            {{ isset($maintenance) ? 'Edit Maintenance Record' : 'Log New Maintenance' }}
                        </h5>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="{{ isset($maintenance) ? route('maintenance.update', $maintenance) : route('maintenance.store') }}">
                        @csrf
                        @if(isset($maintenance))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                <select class="form-select @error('asset_id') is-invalid @enderror" 
                                        id="asset_id" 
                                        name="asset_id" 
                                        required>
                                    <option value="">Select Asset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" 
                                                {{ old('asset_id', $maintenance->asset_id ?? '') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->asset_name }} ({{ $asset->asset_tag }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="maintenance_date" class="form-label">Maintenance Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('maintenance_date') is-invalid @enderror" 
                                       id="maintenance_date" 
                                       name="maintenance_date" 
                                       value="{{ old('maintenance_date', isset($maintenance) ? $maintenance->maintenance_date->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                                       required>
                                @error('maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Select Type</option>
                                    <option value="preventive" {{ old('type', $maintenance->type ?? '') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                                    <option value="corrective" {{ old('type', $maintenance->type ?? '') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                                    <option value="routine" {{ old('type', $maintenance->type ?? '') == 'routine' ? 'selected' : '' }}>Routine</option>
                                    <option value="emergency" {{ old('type', $maintenance->type ?? '') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="technician" class="form-label">Technician <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('technician') is-invalid @enderror" 
                                       id="technician" 
                                       name="technician" 
                                       value="{{ old('technician', $maintenance->technician ?? '') }}" 
                                       required>
                                @error('technician')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cost" class="form-label">Cost ($)</label>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('cost') is-invalid @enderror" 
                                       id="cost" 
                                       name="cost" 
                                       value="{{ old('cost', $maintenance->cost ?? '') }}">
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="next_maintenance_date" class="form-label">Next Maintenance Date</label>
                                <input type="date" 
                                       class="form-control @error('next_maintenance_date') is-invalid @enderror" 
                                       id="next_maintenance_date" 
                                       name="next_maintenance_date" 
                                       value="{{ old('next_maintenance_date', isset($maintenance) && $maintenance->next_maintenance_date ? $maintenance->next_maintenance_date->format('Y-m-d') : '') }}">
                                @error('next_maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          required>{{ old('description', $maintenance->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="actions_taken" class="form-label">Actions Taken <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('actions_taken') is-invalid @enderror" 
                                          id="actions_taken" 
                                          name="actions_taken" 
                                          rows="3" 
                                          required>{{ old('actions_taken', $maintenance->actions_taken ?? '') }}</textarea>
                                @error('actions_taken')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="performed_by" class="form-label">Performed By <span class="text-danger">*</span></label>
                                <select class="form-select @error('performed_by') is-invalid @enderror" 
                                        id="performed_by" 
                                        name="performed_by" 
                                        required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ old('performed_by', $maintenance->performed_by ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('performed_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>
                                        {{ isset($maintenance) ? 'Update Maintenance' : 'Log Maintenance' }}
                                    </button>
                                    <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary px-4">
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