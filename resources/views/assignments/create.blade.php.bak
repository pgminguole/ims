@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">Assign Asset</h5>
                        <a href="{{ route('assignments.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('assignments.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="asset_id" class="form-label">Select Asset <span class="text-danger">*</span></label>
                                <select class="form-select @error('asset_id') is-invalid @enderror" 
                                        id="asset_id" 
                                        name="asset_id" 
                                        required>
                                    <option value="">Choose an asset to assign...</option>
                                    @foreach($availableAssets as $asset)
                                        <option value="{{ $asset->id }}" 
                                                {{ old('asset_id') == $asset->id ? 'selected' : '' }}
                                                data-category="{{ $asset->category->name ?? 'N/A' }}"
                                                data-region="{{ $asset->region->name ?? 'N/A' }}"
                                                data-condition="{{ $asset->condition }}">
                                            {{ $asset->asset_name }} ({{ $asset->asset_tag }}) - {{ $asset->category->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- Asset Preview -->
                                <div id="assetPreview" class="mt-3 p-3 border rounded bg-light" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Category:</strong> <span id="previewCategory">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Region:</strong> <span id="previewRegion">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Condition:</strong> <span id="previewCondition">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Status:</strong> <span class="badge bg-success">Available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                        id="assigned_to" 
                                        name="assigned_to" 
                                        required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_type" class="form-label">Assignment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('assigned_type') is-invalid @enderror" 
                                        id="assigned_type" 
                                        name="assigned_type" 
                                        required>
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

                            <div class="col-md-6">
                                <label for="assigned_date" class="form-label">Assignment Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('assigned_date') is-invalid @enderror" 
                                       id="assigned_date" 
                                       name="assigned_date" 
                                       value="{{ old('assigned_date', now()->format('Y-m-d')) }}" 
                                       required>
                                @error('assigned_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="expected_return_date" class="form-label">Expected Return Date</label>
                                <input type="date" 
                                       class="form-control @error('expected_return_date') is-invalid @enderror" 
                                       id="expected_return_date" 
                                       name="expected_return_date" 
                                       value="{{ old('expected_return_date') }}">
                                @error('expected_return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="comments" class="form-label">Assignment Notes</label>
                                <textarea class="form-control @error('comments') is-invalid @enderror" 
                                          id="comments" 
                                          name="comments" 
                                          rows="3" 
                                          placeholder="Any special instructions or notes for this assignment...">{{ old('comments') }}</textarea>
                                @error('comments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1" checked>
                                    <label class="form-check-label" for="send_notification">
                                        Send notification email to the assigned user
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-user-check me-2"></i>Assign Asset
                                    </button>
                                    <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary px-4">
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
    // Asset preview functionality
    document.getElementById('asset_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const preview = document.getElementById('assetPreview');
        
        if (this.value) {
            document.getElementById('previewCategory').textContent = selectedOption.getAttribute('data-category');
            document.getElementById('previewRegion').textContent = selectedOption.getAttribute('data-region');
            document.getElementById('previewCondition').textContent = selectedOption.getAttribute('data-condition');
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });

    // Set today's date as default for assignment date
    document.getElementById('assigned_date').value = new Date().toISOString().split('T')[0];
    
    // Set default expected return date to 1 year from now
    const nextYear = new Date();
    nextYear.setFullYear(nextYear.getFullYear() + 1);
    document.getElementById('expected_return_date').value = nextYear.toISOString().split('T')[0];
</script>
@endpush
@endsection