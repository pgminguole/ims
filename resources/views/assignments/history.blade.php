@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Assignment History</h2>
            <p class="text-muted mb-0">Complete history of all asset assignments and returns</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <a href="{{ route('assignments.index') }}" class="btn btn-outline-primary px-4">
                    <i class="fas fa-list me-2"></i>Current Assignments
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
                <h5 class="mb-0 fw-semibold">History Filters</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('assignments.history') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Action Type</label>
                        <select name="action" class="form-select">
                            <option value="">All Actions</option>
                            <option value="assigned" {{ request('action') == 'assigned' ? 'selected' : '' }}>Assignments</option>
                            <option value="returned" {{ request('action') == 'returned' ? 'selected' : '' }}>Returns</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter History</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignment History Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">ASSET</th>
                            <th class="py-3">ACTION</th>
                            <th class="py-3">USER</th>
                            <th class="py-3">DETAILS</th>
                            <th class="py-3">PERFORMED BY</th>
                            <th class="py-3">DATE & TIME</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $record)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="asset-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-laptop"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $record->asset->asset_name }}</div>
                                        <div class="text-muted small">{{ $record->asset->asset_tag }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($record->action === 'assigned')
                                    <span class="badge bg-success">
                                        <i class="fas fa-user-plus me-1"></i>Assigned
                                    </span>
                                @elseif($record->action === 'returned')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-undo me-1"></i>Returned
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($record->action) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($record->asset->assignedUser && $record->action === 'assigned')
                                    <div class="fw-medium">{{ $record->asset->assignedUser->full_name }}</div>
                                    <small class="text-muted">{{ $record->asset->assigned_type }}</small>
                                @elseif($record->action === 'returned')
                                    <span class="text-muted">—</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-muted small">
                                    {{ $record->description }}
                                </div>
                                @if($record->action === 'returned' && $record->asset->returned_reason)
                                    <div class="mt-1">
                                        <small class="badge bg-light text-dark">
                                            Reason: {{ $record->asset->returned_reason }}
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="user-avatar-sm bg-info text-white">
                                            {{ substr($record->performedBy->full_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <div class="fw-medium">{{ $record->performedBy->full_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    {{ $record->performed_at->format('M d, Y') }}<br>
                                    <small>{{ $record->performed_at->format('h:i A') }}</small>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-history"></i>
                                    <h5>No assignment history found</h5>
                                    <p>Assignment history will appear here as assets are assigned and returned</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($history->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} 
                    of {{ $history->total() }} entries
                </div>
                <div>
                    {{ $history->links() }}
                </div>
            </div>
        </div>
        @endif
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

.asset-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection