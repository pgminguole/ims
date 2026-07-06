@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-desktop text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">Assign DTS System to Court</h5>
                        <a href="{{ route('dts-assignments.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dts-assignments.store') }}">
                        @csrf

                        <div class="row g-4">
                            <!-- Court Selection -->
                            <div class="col-md-6">
                                <label for="court_id" class="form-label">Select Court <span class="text-danger">*</span></label>
                                <select class="form-select @error('court_id') is-invalid @enderror" 
                                        id="court_id" 
                                        name="court_id" 
                                        required>
                                    <option value="">Choose a court...</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}
                                                data-region="{{ $court->region->name ?? 'N/A' }}"
                                                data-location="{{ $court->location->name ?? 'N/A' }}">
                                            {{ $court->name }} - {{ $court->location->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('court_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- Court Preview -->
                                <div id="courtPreview" class="mt-3 p-3 border rounded bg-light" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Region:</strong> <span id="previewRegion">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Location:</strong> <span id="previewLocation">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DTS Name -->
                            <div class="col-md-6">
                                <label for="dts_name" class="form-label">DTS System Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('dts_name') is-invalid @enderror" 
                                       id="dts_name" 
                                       name="dts_name" 
                                       value="{{ old('dts_name') }}" 
                                       placeholder="e.g., Main Courtroom DTS"
                                       required>
                                @error('dts_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Assignment Date -->
                            <div class="col-md-6">
                                <label for="assigned_date" class="form-label">Assignment Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('assigned_date') is-invalid @enderror" 
                                       id="assigned_date" 
                                       name="assigned_date" 
                                       value="{{ old('assigned_date', date('Y-m-d')) }}" 
                                       required>
                                @error('assigned_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Component Counts -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">DTS Components</h6>
                            </div>

                            <div class="col-md-4">
                                <label for="monitors_count" class="form-label">Monitors Count <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('monitors_count') is-invalid @enderror" 
                                       id="monitors_count" 
                                       name="monitors_count" 
                                       value="{{ old('monitors_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('monitors_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="splitters_count" class="form-label">Splitters Count <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('splitters_count') is-invalid @enderror" 
                                       id="splitters_count" 
                                       name="splitters_count" 
                                       value="{{ old('splitters_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('splitters_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="sony_recorders_count" class="form-label">Sony Recorders <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('sony_recorders_count') is-invalid @enderror" 
                                       id="sony_recorders_count" 
                                       name="sony_recorders_count" 
                                       value="{{ old('sony_recorders_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('sony_recorders_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="hdmi_short_cables_count" class="form-label">HDMI Short (5M) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('hdmi_short_cables_count') is-invalid @enderror" 
                                       id="hdmi_short_cables_count" 
                                       name="hdmi_short_cables_count" 
                                       value="{{ old('hdmi_short_cables_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('hdmi_short_cables_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="hdmi_long_cables_count" class="form-label">HDMI Long (20M) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('hdmi_long_cables_count') is-invalid @enderror" 
                                       id="hdmi_long_cables_count" 
                                       name="hdmi_long_cables_count" 
                                       value="{{ old('hdmi_long_cables_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('hdmi_long_cables_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="extension_boards_count" class="form-label">Extension Boards <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('extension_boards_count') is-invalid @enderror" 
                                       id="extension_boards_count" 
                                       name="extension_boards_count" 
                                       value="{{ old('extension_boards_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('extension_boards_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="trucking_count" class="form-label">Trucking Count <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('trucking_count') is-invalid @enderror" 
                                       id="trucking_count" 
                                       name="trucking_count" 
                                       value="{{ old('trucking_count', 0) }}" 
                                       min="0" 
                                       required>
                                @error('trucking_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Additional Options -->
                            <div class="col-md-6">
                                <div class="form-check mt-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="assign_as_primary" 
                                           name="assign_as_primary" 
                                           value="1"
                                           {{ old('assign_as_primary') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="assign_as_primary">
                                        Set as primary DTS for this court
                                    </label>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Any additional notes or specifications for this DTS system...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Assign DTS System
                                    </button>
                                    <a href="{{ route('dts-assignments.index') }}" class="btn btn-outline-secondary px-4">
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

@push('scripts')
<script>
    // Court preview functionality
    document.getElementById('court_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const preview = document.getElementById('courtPreview');
        
        if (this.value) {
            document.getElementById('previewRegion').textContent = selectedOption.getAttribute('data-region');
            document.getElementById('previewLocation').textContent = selectedOption.getAttribute('data-location');
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });

    // Auto-generate DTS name based on court selection
    document.getElementById('court_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const dtsNameInput = document.getElementById('dts_name');
        
        if (this.value && !dtsNameInput.value) {
            const courtName = selectedOption.text.split(' - ')[0];
            dtsNameInput.value = `${courtName} DTS System`;
        }
    });
</script>
@endpush
@endsection