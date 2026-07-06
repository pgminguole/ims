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
                    
                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-fill mb-4 border-bottom-0" id="assignmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-top fw-bold" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existing" type="button" role="tab" aria-controls="existing" aria-selected="true">
                                <i class="fas fa-hand-pointer me-2"></i>Assign Existing Asset
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-top fw-bold" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab" aria-controls="new" aria-selected="false">
                                <i class="fas fa-plus-circle me-2"></i>Create & Assign New Asset
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="assignmentTabsContent">
                        <!-- Assign Existing Asset Tab -->
                        <div class="tab-pane fade show active" id="existing" role="tabpanel" aria-labelledby="existing-tab">
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

                        <!-- Create & Assign New Asset Tab -->
                        <div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
                            <form action="{{ route('assignments.create-asset') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasAssignedRole('super_admin'))
                                     <div class="col-md-12">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Record Type <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-lg" name="record_type" required>
                                            <option value="assignment">Official Assignment</option>
                                            <option value="inventory">Inventory Collection</option>
                                        </select>
                                    </div>
                                    @endif

                                    <div class="col-md-6">
                                        <label for="assignment_target_new" class="form-label">Assignment Target <span class="text-danger">*</span></label>
                                        <select class="form-select @error('assignment_target') is-invalid @enderror" 
                                                id="assignment_target_new" 
                                                name="assignment_target" 
                                                required>
                                            <option value="">Select Target Type</option>
                                            <option value="user" {{ old('assignment_target') == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="court" {{ old('assignment_target') == 'court' ? 'selected' : '' }}>Court</option>
                                            <option value="office" {{ old('assignment_target') == 'office' ? 'selected' : '' }}>Office / Department</option>
                                            <option value="region" {{ old('assignment_target') == 'region' ? 'selected' : '' }}>Region</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="target_id_new" class="form-label">Select Target <span class="text-danger">*</span></label>
                                        <select class="form-select @error('target_id') is-invalid @enderror" 
                                                id="target_id_new" 
                                                name="target_id" 
                                                required disabled>
                                            <option value="">Select target type first...</option>
                                        </select>
                                        <div id="targetHelp_new" class="form-text text-muted"></div>
                                    </div>

                                     <div class="col-md-6">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="category_id" required>
                                            <option value="">Select Category...</option>
                                            @foreach($categories ?? [] as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Brand / Model</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="brand" placeholder="Brand (e.g. HP)">
                                            <input type="text" class="form-control" name="model" placeholder="Model (e.g. EliteBook)">
                                        </div>
                                    </div>
                                     <div class="col-md-4">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" value="1" min="1" max="50">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Condition</label>
                                         <select class="form-select" name="condition">
                                            <option value="excellent">Excellent</option>
                                            <option value="good">Good</option>
                                            <option value="fair">Fair</option>
                                            <option value="poor">Poor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Assigned Type</label>
                                        <select class="form-select" name="assigned_type" required>
                                            <option value="judge">Judge</option>
                                            <option value="staff">Staff</option>
                                            <option value="department">Department</option>
                                            <option value="court">Court</option>
                                            <option value="office">Office</option>
                                            <option value="region">Region</option>
                                        </select>
                                    </div>
                                     <div class="col-md-12">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Assigned Date</label>
                                        <input type="date" class="form-control" name="assigned_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-small fw-bold text-uppercase text-muted">Notes</label>
                                        <textarea class="form-control" name="comments" rows="2" placeholder="Start typing..."></textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="d-flex gap-2 pt-3">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fas fa-plus-circle me-2"></i>Create & Assign Asset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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

    // Target data
    const users = @json($users);
    const courts = @json($courts);
    const offices = @json($offices);
    const regions = @json($regions);

    // Target selection for new asset tab
    const assignmentTargetNew = document.getElementById('assignment_target_new');
    const targetIdNew = document.getElementById('target_id_new');
    const targetHelpNew = document.getElementById('targetHelp_new');

    if (assignmentTargetNew && targetIdNew) {
        assignmentTargetNew.addEventListener('change', function() {
            targetIdNew.innerHTML = '<option value="">Select target...</option>';
            targetIdNew.disabled = !this.value;
            targetHelpNew.textContent = '';

            switch(this.value) {
                case 'user':
                    users.forEach(u => {
                        targetIdNew.add(new Option(`${u.first_name} ${u.last_name} (${u.email})`, u.id));
                    });
                    targetHelpNew.textContent = 'Select the specific user receiving the asset';
                    break;
                case 'court':
                    courts.forEach(c => {
                        targetIdNew.add(new Option(c.name, c.id));
                    });
                    targetHelpNew.textContent = 'Select the court receiving the asset';
                    break;
                case 'office':
                    offices.forEach(o => {
                        targetIdNew.add(new Option(o.name, o.id));
                    });
                    targetHelpNew.textContent = 'Select the office or department receiving the asset';
                    break;
                case 'region':
                    regions.forEach(r => {
                        targetIdNew.add(new Option(r.name, r.id));
                    });
                    targetHelpNew.textContent = 'Select the region receiving the asset';
                    break;
            }
        });
    }

</script>
@endpush
@endsection