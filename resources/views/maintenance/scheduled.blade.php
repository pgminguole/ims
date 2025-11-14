@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Scheduled Maintenance</h2>
            <p class="text-muted mb-0">Upcoming and overdue maintenance schedules</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <a href="{{ route('maintenance.index') }}" class="btn btn-outline-primary px-4">
                    <i class="fas fa-history me-2"></i>Maintenance History
                </a>
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary px-4">
                    <i class="fas fa-plus me-2"></i>Log Maintenance
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper primary-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Scheduled</h6>
                            <h3 class="mb-0">{{ $scheduledAssets->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper warning-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Due Soon (30 days)</h6>
                            <h3 class="mb-0">{{ $upcomingCount ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
                            <h3 class="mb-0">{{ $dueCount ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Maintenance Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">ASSET</th>
                            <th class="py-3">CATEGORY</th>
                            < class="py-3">REGION</th>
                            <th class="py-3">LAST MAINTENANCE</th>
                            <th class="py-3">NEXT MAINTENANCE</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scheduledAssets as $asset)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="asset-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-laptop"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $asset->asset_name }}</div>
                                        <small class="text-muted">{{ $asset->asset_tag }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $asset->category->name ?? 'N/A' }}</td>
                            <td>{{ $asset->region->name ?? 'N/A' }}</td>
                            <td>
                                @if($asset->last_maintenance)
                                    {{ $asset->last_maintenance->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->next_maintenance)
                                    @if($asset->next_maintenance < now())
                                        <span class="text-danger fw-medium">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $asset->next_maintenance->format('M d, Y') }}
                                        </span>
                                    @elseif($asset->next_maintenance <= now()->addDays(30))
                                        <span class="text-warning fw-medium">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $asset->next_maintenance->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-success">
                                            {{ $asset->next_maintenance->format('M d, Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->next_maintenance)
                                    @if($asset->next_maintenance < now())
                                        <span class="badge bg-danger">Overdue</span>
                                    @elseif($asset->next_maintenance <= now()->addDays(7))
                                        <span class="badge bg-warning">Due This Week</span>
                                    @elseif($asset->next_maintenance <= now()->addDays(30))
                                        <span class="badge bg-info">Due Soon</span>
                                    @else
                                        <span class="badge bg-success">Scheduled</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Not Scheduled</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('assets.show', $asset) }}" 
                                       class="action-btn view-btn" 
                                       title="View Asset">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('maintenance.create') }}?asset_id={{ $asset->id }}" 
                                       class="action-btn success-btn" 
                                       title="Log Maintenance">
                                        <i class="fas fa-tools"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-calendar-check"></i>
                                    <h5>No scheduled maintenance</h5>
                                    <p>All assets are up to date with their maintenance schedules</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($scheduledAssets->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $scheduledAssets->firstItem() ?? 0 }} to {{ $scheduledAssets->lastItem() ?? 0 }} 
                    of {{ $scheduledAssets->total() }} entries
                </div>
                <div>
                    {{ $scheduledAssets->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection