@extends('layouts.app')

@section('title', 'Regions Audit')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marker-alt me-2"></i>Regions Audit
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="exportRegions()">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-globe fs-1 text-primary mb-2"></i>
                    <h3 class="fw-bold">{{ $totalRegions }}</h3>
                    <p class="text-muted mb-0">Total Regions</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-gavel fs-1 text-success mb-2"></i>
                    <h3 class="fw-bold">{{ $totalCourts }}</h3>
                    <p class="text-muted mb-0">Total Courts</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-laptop fs-1 text-info mb-2"></i>
                    <h3 class="fw-bold">{{ $totalAssets }}</h3>
                    <p class="text-muted mb-0">Total Assets</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fs-1 text-warning mb-2"></i>
                    <h3 class="fw-bold">{{ $totalUsers }}</h3>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Regions Grid -->
    <div class="row g-4">
        @foreach($regions as $region)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title fw-bold">{{ $region->name }}</h5>
                            <p class="text-muted mb-0">Code: {{ $region->code }}</p>
                        </div>
                        <span class="badge bg-{{ $region->is_active ? 'success' : 'danger' }}">
                            {{ $region->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <p class="text-muted small mb-4">{{ $region->description ?? 'No description available.' }}</p>

                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h6 class="fw-bold text-primary mb-1">{{ $region->courts_count }}</h6>
                                <small class="text-muted">Courts</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h6 class="fw-bold text-success mb-1">{{ $region->assets_count }}</h6>
                                <small class="text-muted">Assets</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h6 class="fw-bold text-info mb-1">{{ $region->users_count }}</h6>
                                <small class="text-muted">Users</small>
                            </div>
                        </div>
                    </div>

                    <!-- Court Distribution -->
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Court Distribution</h6>
                        @if($region->courts_count > 0)
                            @php
                                $courtTypes = $region->courts->groupBy('type')->map->count();
                            @endphp
                            @foreach($courtTypes as $type => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                <span class="badge bg-secondary">{{ $count }}</span>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small mb-0">No courts in this region.</p>
                        @endif
                    </div>

                    <!-- Asset Status -->
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Asset Status</h6>
                        @if($region->assets_count > 0)
                            @php
                                $assetStatus = $region->assets->groupBy('status')->map->count();
                            $total = $region->assets_count;
                            @endphp
                            @foreach($assetStatus as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">{{ ucfirst($status) }}</span>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $status === 'available' ? 'success' : ($status === 'assigned' ? 'primary' : 'warning') }} me-2">
                                        {{ $count }}
                                    </span>
                                    <small class="text-muted">{{ round(($count / $total) * 100) }}%</small>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small mb-0">No assets in this region.</p>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-flex gap-2">
                        <a href="{{ route('auditor.assets.index') }}?region_id={{ $region->id }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-laptop me-1"></i>View Assets
                        </a>
                        <a href="{{ route('auditor.courts.index') }}?region_id={{ $region->id }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-gavel me-1"></i>View Courts
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($regions->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $regions->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportRegions() {
    // Implement export functionality
    alert('Export functionality to be implemented');
}
</script>
@endpush