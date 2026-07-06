@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Assignment Control</h4>
                <p class="text-muted text-small mb-0">Manage asset assignments and user allocations.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('assignments.history') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                    <i class="fas fa-history me-1"></i> History
                </a>
                <a href="{{ route('assignments.bulk-model') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-layer-group me-1"></i> Bulk Assign
                </a>
                <a href="{{ route('assignments.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> New Assignment
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="col-12">
        <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-0 rounded-3 border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i>
            <div class="text-small">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto small" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="col-12">
        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-0 rounded-3 border-0 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div class="text-small">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto small" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Assignment Filters</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['search', 'assignment_target', 'category_id', 'assigned_date_from', 'assigned_date_to']))
                        <a href="{{ route('assignments.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('assignments.index') }}" id="filterForm">
                        <div class="row g-2">
                            <!-- Search -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Asset name, tag..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <!-- Category -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Device Type</label>
                                <select name="category_id" class="form-select form-select-sm text-small">
                                    <option value="">All Types</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Target -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Assigned To</label>
                                <select name="assignment_target" class="form-select form-select-sm text-small">
                                    <option value="">All Targets</option>
                                    <option value="user" {{ request('assignment_target') == 'user' ? 'selected' : '' }}>Users</option>
                                    <option value="court" {{ request('assignment_target') == 'court' ? 'selected' : '' }}>Courts</option>
                                    <option value="office" {{ request('assignment_target') == 'office' ? 'selected' : '' }}>Offices</option>
                                    <option value="region" {{ request('assignment_target') == 'region' ? 'selected' : '' }}>Regions</option>
                                </select>
                            </div>

                            <!-- Dates -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">From</label>
                                <input type="date" name="assigned_date_from" class="form-control form-control-sm text-small" value="{{ request('assigned_date_from') }}">
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">To</label>
                                <input type="date" name="assigned_date_to" class="form-control form-control-sm text-small" value="{{ request('assigned_date_to') }}">
                            </div>

                            <!-- Submit -->
                            <div class="col-lg-1 col-md-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Asset Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Assigned To</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Date</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Condition</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted" style="width: 36px; height: 36px;">
                                            <i class="fas fa-laptop fa-xs"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $assignment->asset_name ?? $assignment->model }}</div>
                                        <div class="text-tiny text-muted">{{ $assignment->asset_tag }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($assignment->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 fw-bold text-tiny" style="width: 28px; height: 28px;">
                                            {{ substr($assignment->assignedUser->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-small fw-medium">{{ $assignment->assignedUser->name }}</div>
                                            <div class="text-tiny text-muted">User</div>
                                        </div>
                                    </div>
                                @elseif($assignment->court)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center me-2 fw-bold text-tiny" style="width: 28px; height: 28px;">
                                            <i class="fas fa-landmark fa-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-small fw-medium">{{ $assignment->court->name }}</div>
                                            <div class="text-tiny text-muted">Court</div>
                                        </div>
                                    </div>
                                @elseif($assignment->office)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-2 fw-bold text-tiny" style="width: 28px; height: 28px;">
                                            <i class="fas fa-building fa-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-small fw-medium">{{ $assignment->office->name }}</div>
                                            <div class="text-tiny text-muted">Office</div>
                                        </div>
                                    </div>
                                @elseif($assignment->region)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-2 fw-bold text-tiny" style="width: 28px; height: 28px;">
                                            <i class="fas fa-map-marked-alt fa-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-small fw-medium">{{ $assignment->region->name }}</div>
                                            <div class="text-tiny text-muted">Region</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-tiny text-muted fst-italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-small text-muted">{{ optional($assignment->assigned_date)->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-tiny px-2 py-1 rounded-pill">
                                    {{ ucfirst($assignment->condition) }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('assets.show', $assignment) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" data-bs-toggle="tooltip" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    
                                    <button class="btn btn-icon btn-sm btn-warning-subtle border border-warning-subtle rounded-circle text-warning" 
                                            title="Return Asset"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#returnModal"
                                            data-asset="{{ $assignment->getRouteKey() }}"
                                            data-asset-name="{{ $assignment->asset_name ?? $assignment->model }}">
                                        <i class="fas fa-undo fa-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-users-slash fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No assignments found</h6>
                                    <p class="text-muted text-small mb-0">There are currently no assigned assets matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }}
                    </div>
                    <div>{{ $assignments->appends(request()->query())->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Return Asset Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-bottom-0 bg-light pb-0">
                <h5 class="modal-title fw-bold">Return Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="returnForm" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <input type="hidden" name="asset_id" id="returnAssetId">
                    <div class="alert alert-warning border-0 d-flex align-items-center mb-4">
                        <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                        <div>
                            <div class="text-tiny text-uppercase fw-bold opacity-75">Returning Asset</div>
                            <strong id="returnAssetName" class="d-block"></strong>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Return Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control text-small" id="returned_date" name="returned_date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Condition <span class="text-danger">*</span></label>
                            <select class="form-select text-small" id="condition" name="condition" required>
                                <option value="excellent">Excellent</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="broken">Broken</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Return Reason <span class="text-danger">*</span></label>
                            <select class="form-select text-small" id="returned_reason" name="returned_reason" required>
                                <option value="">Select Reason...</option>
                                <option value="End of assignment">End of assignment</option>
                                <option value="Asset upgrade">Asset upgrade</option>
                                <option value="User transfer">User transfer</option>
                                <option value="Asset maintenance">Asset maintenance</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Returned By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-small" id="returnee" name="returnee" placeholder="Name of returnee" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Returned To</label>
                            <input type="text" class="form-control text-small" id="returned_to" name="returned_to" placeholder="Received by">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-tiny fw-bold text-uppercase text-muted">Additional Comments</label>
                            <textarea class="form-control text-small" id="comments" name="comments" rows="3" placeholder="Any check-in notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const returnModal = document.getElementById('returnModal');
if (returnModal) {
    returnModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const asset = button.getAttribute('data-asset');
        const assetName = button.getAttribute('data-asset-name');
        
        document.getElementById('returnAssetName').textContent = assetName;
        
        const returnForm = document.getElementById('returnForm');
        returnForm.action = `/assets/${asset}/return`;
    });
}

const dateInput = document.getElementById('returned_date');
if (dateInput) {
    dateInput.value = new Date().toISOString().split('T')[0];
}
</script>
@endpush
@endsection