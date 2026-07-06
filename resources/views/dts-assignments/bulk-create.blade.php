@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-copy text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">Bulk Assign DTS Systems to Courts</h5>
                        <a href="{{ route('dts-assignments.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dts-assignments.store-bulk') }}">
                        @csrf

                        <div class="row g-4">
                            <!-- Court Selection -->
                            <div class="col-md-12">
                                <label class="form-label">Select Courts <span class="text-danger">*</span></label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row">
                                        @foreach($courts->chunk(ceil($courts->count() / 3)) as $courtChunk)
                                        <div class="col-md-4">
                                            @foreach($courtChunk as $court)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input court-checkbox" 
                                                       type="checkbox" 
                                                       name="targets[]" 
                                                       value="{{ $court->id }}" 
                                                       id="court_{{ $court->id }}"
                                                       {{ in_array($court->id, old('targets', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="court_{{ $court->id }}">
                                                    {{ $court->name }} - {{ $court->location->name ?? 'N/A' }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Select All Controls -->
                                    <div class="mt-3 pt-3 border-top">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                            <i class="fas fa-check-square me-1"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                                            <i class="fas fa-square me-1"></i> Deselect All
                                        </button>
                                        <span class="text-muted ms-2">
                                            <span id="selectedCount">0</span> courts selected
                                        </span>
                                    </div>
                                </div>
                                @error('targets')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- DTS Name -->
                            <div class="col-md-6">
                                <label for="dts_name" class="form-label">DTS System Name Template <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('dts_name') is-invalid @enderror" 
                                       id="dts_name" 
                                       name="dts_name" 
                                       value="{{ old('dts_name', 'DTS System') }}" 
                                       placeholder="e.g., Main Courtroom DTS"
                                       required>
                                <div class="form-text">
                                    This will be combined with court name: "Court Name - [Your Input]"
                                </div>
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
                                <h6 class="border-bottom pb-2 mb-3">DTS Components (Applied to All Selected Courts)</h6>
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
                                        Set as primary DTS for all selected courts
                                    </label>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Additional Notes (Applied to All)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Any additional notes or specifications for these DTS systems...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Summary Preview -->
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <strong>Summary:</strong> This will create <span id="previewCount">0</span> DTS system(s) 
                                            with the same configuration for all selected courts.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                        <i class="fas fa-copy me-2"></i>Create Bulk DTS Assignments
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
    // Court selection management
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.court-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('previewCount').textContent = selectedCount;
        
        // Disable submit button if no courts selected
        document.getElementById('submitBtn').disabled = selectedCount === 0;
    }

    // Select all courts
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.court-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });

    // Deselect all courts
    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.court-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    // Update count when checkboxes change
    document.querySelectorAll('.court-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Initialize count on page load
    document.addEventListener('DOMContentLoaded', updateSelectedCount);
</script>
@endpush
@endsection