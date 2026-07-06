@extends('layouts.app')

@section('title', 'DTS Details - ' . $dts->name)

@section('content')
<!-- Header Section -->
<div class="header-section py-4">
    <div class="d-flex justify-content-between align-items-start">
        <div class="header-content">
            <div class="d-flex align-items-center mb-2">
               
                <div>
                    <h1 class="header-title mb-1">DTS System Audit Details</h1>
                    <div class="system-info">
                        <span class="text-muted">System:</span>
                        <span class="fw-semibold text-dark ms-1">{{ $dts->name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <div class="d-flex gap-2">
                <a href="{{ route('auditor.dts.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
                <button class="btn btn-primary btn-lg" onclick="exportDtsReport()">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
            </div>
        </div>
    </div>


    <div class="row g-4">
        <!-- DTS Information -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>DTS Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="position-relative mx-auto mb-3" style="width: 80px; height: 80px;">
                            <div class="avatar-circle bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center w-100 h-100">
                                <i class="fas fa-microphone text-primary fs-4"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold">{{ $dts->name }}</h4>
                        <span class="badge bg-{{ $dts->is_available ? 'success' : 'secondary' }} fs-6">
                            {{ $dts->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Court</label>
                            <p class="mb-0">
                                <a href="{{ route('auditor.courts.show', $dts->court) }}" class="text-decoration-none">
                                    {{ $dts->court->name }}
                                </a>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Region</label>
                            <p class="mb-0">{{ $dts->court->region->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Date Assigned</label>
                            <p class="mb-0">{{ $dts->date_assigned ? $dts->date_assigned->format('M j, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">System Completeness</label>
                            <p class="mb-0">
                                @if($dts->isComplete())
                                    <span class="badge bg-success">Complete</span>
                                @else
                                    <span class="badge bg-warning">Incomplete</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Asset Linking</label>
                            <p class="mb-0">
                                @if($dts->has_detailed_assets)
                                    <span class="badge bg-success">Fully Linked</span>
                                @else
                                    <span class="badge bg-warning">Partially Linked</span>
                                @endif
                            </p>
                        </div>
                        @if($dts->notes)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <p class="mb-0 text-muted">{{ $dts->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Components Overview -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-cogs me-2"></i>Components Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Monitors -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-desktop fs-1 text-primary mb-3"></i>
                                    <h4 class="fw-bold">{{ $dts->monitors_count }}</h4>
                                    <p class="text-muted mb-2">Monitors</p>
                                    @if($dts->monitorAsset)
                                    <small class="text-success">
                                        <i class="fas fa-link me-1"></i>Asset: {{ $dts->monitorAsset->asset_tag }}
                                    </small>
                                    @else
                                    <small class="text-warning">No asset linked</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Splitters -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-code-branch fs-1 text-info mb-3"></i>
                                    <h4 class="fw-bold">{{ $dts->splitters_count }}</h4>
                                    <p class="text-muted mb-2">Splitters</p>
                                    @if($dts->splitterAsset)
                                    <small class="text-success">
                                        <i class="fas fa-link me-1"></i>Asset: {{ $dts->splitterAsset->asset_tag }}
                                    </small>
                                    @else
                                    <small class="text-warning">No asset linked</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- HDMI Cables -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-plug fs-1 text-success mb-3"></i>
                                    <h4 class="fw-bold">{{ $dts->hdmi_short_cables_count + $dts->hdmi_long_cables_count }}</h4>
                                    <p class="text-muted mb-2">HDMI Cables</p>
                                    <small>
                                        {{ $dts->hdmi_short_cables_count }} (5M) + {{ $dts->hdmi_long_cables_count }} (20M)
                                    </small>
                                    @if($dts->hdmiShortCableAsset || $dts->hdmiLongCableAsset)
                                    <small class="text-success d-block mt-1">
                                        <i class="fas fa-link me-1"></i>Assets Linked
                                    </small>
                                    @else
                                    <small class="text-warning d-block mt-1">No assets linked</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Extension Boards -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-plug fs-1 text-warning mb-3"></i>
                                    <h4 class="fw-bold">{{ $dts->extension_boards_count }}</h4>
                                    <p class="text-muted mb-2">Extension Boards</p>
                                    @if($dts->extensionBoardAsset)
                                    <small class="text-success">
                                        <i class="fas fa-link me-1"></i>Asset: {{ $dts->extensionBoardAsset->asset_tag }}
                                    </small>
                                    @else
                                    <small class="text-warning">No asset linked</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Trucking -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-truck-loading fs-1 text-secondary mb-3"></i>
                                    <h4 class="fw-bold">{{ $dts->trucking_count }}</h4>
                                    <p class="text-muted mb-2">Trucking</p>
                                    @if($dts->truckingAsset)
                                    <small class="text-success">
                                        <i class="fas fa-link me-1"></i>Asset: {{ $dts->truckingAsset->asset_tag }}
                                    </small>
                                    @else
                                    <small class="text-warning">No asset linked</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sony Recorders -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-record-vinyl fs-1 text-danger mb-3"></i>
                                    <h4 class="fw-bold">{{ $dts->sony_recorders_count }}</h4>
                                    <p class="text-muted mb-2">Sony Recorders</p>
                                    @if($dts->sonyRecorderAsset)
                                    <small class="text-success">
                                        <i class="fas fa-link me-1"></i>Asset: {{ $dts->sonyRecorderAsset->asset_tag }}
                                    </small>
                                    @else
                                    <small class="text-warning">No asset linked</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h5 class="fw-bold mb-2">Total Components: {{ $dts->total_components }}</h5>
                                    <p class="text-muted mb-0">
                                        System Status: 
                                        <span class="badge bg-{{ $dts->isComplete() ? 'success' : 'warning' }}">
                                            {{ $dts->isComplete() ? 'Complete System' : 'Incomplete System' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Linked Assets Details -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-link me-2"></i>Linked Assets Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Component Type</th>
                                    <th>Asset Tag</th>
                                    <th>Asset Name</th>
                                    <th>Serial Number</th>
                                    <th>Category</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dts->all_assets as $componentType => $asset)
                                    @if($asset)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $componentType)) }}</span>
                                        </td>
                                        <td><code>{{ $asset->asset_tag }}</code></td>
                                        <td>{{ $asset->asset_name }}</td>
                                        <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                                        <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : ($asset->condition === 'fair' ? 'warning' : 'danger')) }}">
                                                {{ ucfirst($asset->condition) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status === 'available' ? 'success' : ($asset->status === 'assigned' ? 'primary' : 'warning') }}">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                @if($dts->all_assets->count() == 0)
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-unlink fs-1 text-muted mb-3"></i>
                                        <p class="text-muted">No assets linked to this DTS system.</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    aspect-ratio: 1 / 1;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
function exportDtsReport() {
    // Implement export functionality
    alert('Export functionality to be implemented');
}
</script>
@endpush