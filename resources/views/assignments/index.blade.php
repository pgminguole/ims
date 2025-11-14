@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Assignments Management</h2>
            <p class="text-muted mb-0">Manage asset assignments and tracking</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <a href="{{ route('assignments.history') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-history me-2"></i>Assignment History
                </a>
                <a href="{{ route('assignments.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus me-2"></i>New Assignment
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">Assignment Filters</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('assignments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Asset name, tag, or serial..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Assignment Type</label>
                        <select name="assigned_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="judge" {{ request('assigned_type') == 'judge' ? 'selected' : '' }}>Judge</option>
                            <option value="staff" {{ request('assigned_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="department" {{ request('assigned_type') == 'department' ? 'selected' : '' }}>Department</option>
                            <option value="court" {{ request('assigned_type') == 'court' ? 'selected' : '' }}>Court</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="assigned_date_from" class="form-control" value="{{ request('assigned_date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="assigned_date_to" class="form-control" value="{{ request('assigned_date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">ASSET</th>
                            <th class="py-3">ASSIGNED TO</th>
                            <th class="py-3">ASSIGNMENT TYPE</th>
                            <th class="py-3">ASSIGNED DATE</th>
                            <th class="py-3">CONDITION</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="asset-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-laptop"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $assignment->asset_name }}</div>
                                        <div class="text-muted small">{{ $assignment->asset_tag }}</div>
                                        <div class="text-muted small">{{ $assignment->serial_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($assignment->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="user-avatar-sm bg-primary text-white">
                                                {{ substr($assignment->assignedUser->full_name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-medium">{{ $assignment->assignedUser->full_name }}</div>
                                            <small class="text-muted">{{ $assignment->assignedUser->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge assignment-type-{{ $assignment->assigned_type }}">
                                    {{ ucfirst($assignment->assigned_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $assignment->assigned_date->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <span class="condition-badge condition-{{ $assignment->condition }}">
                                    {{ ucfirst($assignment->condition) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $assignment->status }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('assets.show', $assignment) }}" 
                                       class="action-btn view-btn" 
                                       title="View Asset">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="action-btn return-btn" 
                                            title="Return Asset"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#returnModal"
                                            data-asset-id="{{ $assignment->id }}"
                                            data-asset-name="{{ $assignment->asset_name }}">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <div class="dropdown d-inline-block">
                                        <button class="action-btn more-btn dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('assets.show', $assignment) }}">
                                                    <i class="fas fa-info-circle me-2 text-info"></i>Asset Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#returnModal"
                                                   data-asset-id="{{ $assignment->id }}"
                                                   data-asset-name="{{ $assignment->asset_name }}">
                                                    <i class="fas fa-undo me-2 text-warning"></i>Return Asset
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-history me-2 text-secondary"></i>Assignment History
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-user-check"></i>
                                    <h5>No assignments found</h5>
                                    <p>There are currently no assigned assets</p>
                                    <a href="{{ route('assignments.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Create Assignment
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assignments->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $assignments->firstItem() ?? 0 }} to {{ $assignments->lastItem() ?? 0 }} 
                    of {{ $assignments->total() }} entries
                </div>
                <div>
                    {{ $assignments->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Return Asset Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="returnForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="asset_id" id="returnAssetId">
                    <p>You are about to return: <strong id="returnAssetName"></strong></p>
                    
                    <div class="mb-3">
                        <label for="returned_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="returned_date" name="returned_date" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="returned_reason" class="form-label">Return Reason <span class="text-danger">*</span></label>
                        <select class="form-select" id="returned_reason" name="returned_reason" required>
                            <option value="">Select Reason</option>
                            <option value="End of assignment">End of assignment</option>
                            <option value="Asset upgrade">Asset upgrade</option>
                            <option value="User transfer">User transfer</option>
                            <option value="Asset maintenance">Asset maintenance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="returnee" class="form-label">Returned By <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="returnee" name="returnee" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="returned_to" class="form-label">Returned To</label>
                        <input type="text" class="form-control" id="returned_to" name="returned_to">
                    </div>
                    
                    <div class="mb-3">
                        <label for="condition" class="form-label">Current Condition <span class="text-danger">*</span></label>
                        <select class="form-select" id="condition" name="condition" required>
                            <option value="excellent">Excellent</option>
                            <option value="good" selected>Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                            <option value="broken">Broken</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comments" class="form-label">Additional Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Return Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.assignment-type-judge { background-color: #d1ecf1; color: #0c5460; }
.assignment-type-staff { background-color: #d4edda; color: #155724; }
.assignment-type-department { background-color: #fff3cd; color: #856404; }
.assignment-type-court { background-color: #f8d7da; color: #721c24; }
</style>

@push('scripts')
<script>
    // Return Modal Handler
    const returnModal = document.getElementById('returnModal');
    returnModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const assetId = button.getAttribute('data-asset-id');
        const assetName = button.getAttribute('data-asset-name');
        
        document.getElementById('returnAssetId').value = assetId;
        document.getElementById('returnAssetName').textContent = assetName;
        document.getElementById('returnForm').action = `/assets/${assetId}/return`;
    });

    // Set today's date as default for returned date
    document.getElementById('returned_date').value = new Date().toISOString().split('T')[0];
</script>
@endpush
@endsection