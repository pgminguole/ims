@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Maintenance Management</h4>
                <p class="text-muted text-small mb-0">Track and manage asset maintenance activities.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('maintenance.scheduled') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-calendar-alt me-1"></i> Scheduled
                </a>
                <a href="{{ route('maintenance.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Log Maintenance
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Maintenance</div>
                <div class="metric-v2-value">{{ $totalMaintenance ?? 0 }}</div>
                <div class="text-tiny text-muted mt-2">All Records</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-tools"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Upcoming</div>
                <div class="metric-v2-value">{{ $upcomingMaintenance ?? 0 }}</div>
                <div class="text-tiny text-warning mt-2">Scheduled</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Overdue</div>
                <div class="metric-v2-value">{{ $overdueMaintenance ?? 0 }}</div>
                <div class="text-tiny text-danger mt-2">Action Required</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Cost</div>
                <div class="metric-v2-value">${{ number_format($maintenanceLogs->sum('cost') ?? 0, 2) }}</div>
                <div class="text-tiny text-muted mt-2">Expenses</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Filter Maintenance</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('maintenance.index') }}">
                        <div class="row g-2">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="Asset name, tag..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Type</label>
                                <select name="type" class="form-select form-select-sm text-small">
                                    <option value="">All Types</option>
                                    <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                                    <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                                    <option value="routine" {{ request('type') == 'routine' ? 'selected' : '' }}>Routine</option>
                                    <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm text-small" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm text-small" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-lg-1 col-md-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Asset Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Date & Type</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Technician</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Cost</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Next Due</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceLogs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-laptop fa-sm"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $log->asset->asset_name ?? 'Unknown Asset' }}</div>
                                        <div class="text-tiny text-muted">{{ $log->asset->asset_tag ?? 'NO TAG' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-small">{{ $log->maintenance_date->format('M d, Y') }}</div>
                                <span class="badge maintenance-type-{{ $log->type }} border fw-normal text-tiny text-uppercase">{{ $log->type }}</span>
                            </td>
                            <td>
                                <div class="text-small text-muted">{{ $log->technician }}</div>
                            </td>
                            <td class="text-center">
                                @if($log->cost)
                                    <span class="fw-bold text-small">${{ number_format($log->cost, 2) }}</span>
                                @else
                                    <span class="text-muted text-tiny">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($log->next_maintenance_date)
                                    @if($log->next_maintenance_date < now())
                                        <div class="text-danger text-tiny fw-bold">
                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $log->next_maintenance_date->format('M d, Y') }}
                                        </div>
                                    @elseif($log->next_maintenance_date <= now()->addDays(30))
                                        <div class="text-warning text-tiny fw-bold">
                                            <i class="fas fa-clock me-1"></i>{{ $log->next_maintenance_date->format('M d, Y') }}
                                        </div>
                                    @else
                                        <div class="text-success text-tiny fw-bold">
                                            {{ $log->next_maintenance_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted text-tiny">—</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('maintenance.edit', $log) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('maintenance.destroy', $log) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="return confirm('Delete this log?')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-tools fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No Maintenance Found</h6>
                                    <p class="text-muted text-small mb-0">Log a maintenance activity to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($maintenanceLogs->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $maintenanceLogs->firstItem() ?? 0 }} - {{ $maintenanceLogs->lastItem() ?? 0 }} of {{ $maintenanceLogs->total() }}
                    </div>
                    <div>{{ $maintenanceLogs->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.maintenance-type-preventive { background-color: #e0f2f1; color: #00695c; }
.maintenance-type-corrective { background-color: #ffebee; color: #c62828; }
.maintenance-type-routine { background-color: #f3e5f5; color: #6a1b9a; }
.maintenance-type-emergency { background-color: #fff8e1; color: #ff8f00; }
</style>
@endsection