@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Maintenance Management</h2>
            <p class="text-muted mb-0">Track and manage asset maintenance activities</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <a href="{{ route('maintenance.scheduled') }}" class="btn btn-outline-warning px-4">
                    <i class="fas fa-calendar-alt me-2"></i>Scheduled
                </a>
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus me-2"></i>Log Maintenance
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper primary-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Maintenance</h6>
                            <h3 class="mb-0">{{ $totalMaintenance ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper warning-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Upcoming</h6>
                            <h3 class="mb-0">{{ $upcomingMaintenance ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper danger-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Overdue</h6>
                            <h3 class="mb-0">{{ $overdueMaintenance ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper success-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Cost</h6>
                            <h3 class="mb-0">${{ number_format($maintenanceLogs->sum('cost') ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">Maintenance Filters</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('maintenance.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Asset name or tag..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                            <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                            <option value="routine" {{ request('type') == 'routine' ? 'selected' : '' }}>Routine</option>
                            <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Maintenance Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">ASSET</th>
                            <th class="py-3">MAINTENANCE DATE</th>
                            <th class="py-3">TYPE</th>
                            <th class="py-3">TECHNICIAN</th>
                            <th class="py-3">COST</th>
                            <th class="py-3">NEXT MAINTENANCE</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceLogs as $log)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="asset-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-laptop"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $log->asset->asset_name }}</div>
                                        <small class="text-muted">{{ $log->asset->asset_tag }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $log->maintenance_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge maintenance-type-{{ $log->type }}">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td>{{ $log->technician }}</td>
                            <td>
                                @if($log->cost)
                                    <span class="fw-medium">${{ number_format($log->cost, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($log->next_maintenance_date)
                                    @if($log->next_maintenance_date < now())
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $log->next_maintenance_date->format('M d, Y') }}
                                        </span>
                                    @elseif($log->next_maintenance_date <= now()->addDays(30))
                                        <span class="text-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $log->next_maintenance_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-success">
                                            {{ $log->next_maintenance_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('maintenance.show', $log) }}" 
                                       class="action-btn view-btn" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('maintenance.edit', $log) }}" 
                                       class="action-btn edit-btn" 
                                       title="Edit Maintenance">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('maintenance.destroy', $log) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" 
                                                title="Delete Maintenance"
                                                onclick="return confirm('Are you sure you want to delete this maintenance record?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-tools"></i>
                                    <h5>No maintenance records found</h5>
                                    <p>Start by logging your first maintenance activity</p>
                                    <a href="{{ route('maintenance.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Log Maintenance
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($maintenanceLogs->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $maintenanceLogs->firstItem() ?? 0 }} to {{ $maintenanceLogs->lastItem() ?? 0 }} 
                    of {{ $maintenanceLogs->total() }} entries
                </div>
                <div>
                    {{ $maintenanceLogs->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.maintenance-type-preventive { background-color: #d1ecf1; color: #0c5460; }
.maintenance-type-corrective { background-color: #f8d7da; color: #721c24; }
.maintenance-type-routine { background-color: #d4edda; color: #155724; }
.maintenance-type-emergency { background-color: #fff3cd; color: #856404; }
</style>
@endsection