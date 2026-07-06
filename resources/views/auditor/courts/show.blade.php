@extends('layouts.app')

@section('title', 'Court Details - ' . $court->name)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-gavel me-2"></i>Court Audit Details
            </h1>
           <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('auditor.courts.index') }}">Courts Audit</a></li>
                <li class="breadcrumb-item active">{{ $court->name }}</li>
            </ol>
        </nav>
        
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('auditor.courts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            <button class="btn btn-primary" onclick="exportCourtReport()">
                <i class="fas fa-download me-2"></i>Export Report
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Court Information -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i>Court Information
                    </h5>
                </div>
                <div class="card-body">
                <div class="text-center mb-4">
    <div class="avatar-xl bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
         style="width: 80px; height: 80px;">
        <i class="fas fa-gavel text-primary fs-2"></i>
    </div>
    <h4 class="fw-bold">{{ $court->name }}</h4>
    <span class="badge bg-info fs-6">{{ ucfirst(str_replace('_', ' ', $court->type)) }}</span>
</div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Court Code</label>
                            <p class="mb-0"><code>{{ $court->code }}</code></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Region</label>
                            <p class="mb-0">{{ $court->region->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Location</label>
                            <p class="mb-0">{{ $court->location->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <p class="mb-0">{{ $court->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Presiding Judge</label>
                            <p class="mb-0">{{ $court->presidingJudge->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Registry Officer</label>
                            <p class="mb-0">{{ $court->registryOfficer->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $court->is_active ? 'success' : 'danger' }}">
                                    {{ $court->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-xl-8">
            <div class="row g-4">
                <!-- Asset Statistics -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-laptop me-2"></i>Asset Statistics
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-primary mb-1">{{ $court->assets_count }}</h4>
                                        <small class="text-muted">Total Assets</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-success mb-1">{{ $court->computers }}</h4>
                                        <small class="text-muted">Computers</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-info mb-1">{{ $court->laptops }}</h4>
                                        <small class="text-muted">Laptops</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-warning mb-1">{{ $court->printers }}</h4>
                                        <small class="text-muted">Printers</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DTS Statistics -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-microphone me-2"></i>DTS Statistics
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-primary mb-1">{{ $court->dts_count }}</h4>
                                        <small class="text-muted">DTS Systems</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-success mb-1">{{ $availableDtsCount }}</h4>
                                        <small class="text-muted">Available</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-info mb-1">{{ $court->dts_assets }}</h4>
                                        <small class="text-muted">DTS Assets</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-warning mb-1">{{ $completeDtsCount }}</h4>
                                        <small class="text-muted">Complete</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff Statistics -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-users me-2"></i>Staff Statistics
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-3">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-primary mb-1">{{ $court->users_count }}</h4>
                                        <small class="text-muted">Total Staff</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-success mb-1">{{ $judgesCount }}</h4>
                                        <small class="text-muted">Judges</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-info mb-1">{{ $staffCount }}</h4>
                                        <small class="text-muted">Staff</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-3">
                                        <h4 class="fw-bold text-warning mb-1">{{ $directorsCount }}</h4>
                                        <small class="text-muted">Directors</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assets -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-laptop me-2"></i>Recent Assets
                    </h5>
                    <a href="{{ route('auditor.assets.index') }}?court_id={{ $court->id }}" class="btn btn-sm btn-outline-primary">
                        View All Assets
                    </a>
                </div>
                <div class="card-body">
                    @if($recentAssets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Asset Name</th>
                                    <th>Category</th>
                                    <th>Asset Tag</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>Condition</th>
                                    <th>Assigned To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssets as $asset)
                                <tr>
                                    <td>
                                        <a href="{{ route('auditor.assets.show', $asset) }}" class="text-decoration-none">
                                            {{ $asset->asset_name }}
                                        </a>
                                    </td>
                                    <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                    <td><code>{{ $asset->asset_tag }}</code></td>
                                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $asset->status === 'available' ? 'success' : ($asset->status === 'assigned' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : ($asset->condition === 'fair' ? 'warning' : 'danger')) }}">
                                            {{ ucfirst($asset->condition) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($asset->assigned_type === 'user' && $asset->assignedUser)
                                            {{ $asset->assignedUser->full_name }}
                                        @elseif($asset->assigned_type === 'office' && $asset->office)
                                            {{ $asset->office->name }}
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-laptop fs-1 text-muted mb-3"></i>
                        <p class="text-muted">No assets found for this court.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DTS Systems -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-microphone me-2"></i>DTS Systems ({{ $dtsSystems->count() }})
                    </h5>
                    <a href="{{ route('auditor.dts.index') }}?court_id={{ $court->id }}" class="btn btn-sm btn-outline-primary">
                        View All DTS
                    </a>
                <div class="card-body">
                    @if($dtsSystems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>DTS Name</th>
                                    <th>Monitors</th>
                                    <th>Splitters</th>
                                    <th>HDMI Cables</th>
                                    <th>Extension Boards</th>
                                    <th>Trucking</th>
                                    <th>Sony Recorders</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dtsSystems as $dts)
                                <tr>
                                    <td>
                                        <a href="{{ route('auditor.dts.show', $dts) }}" class="text-decoration-none">
                                            {{ $dts->name }}
                                        </a>
                                    </td>
                                    <td>{{ $dts->monitors_count }}</td>
                                    <td>{{ $dts->splitters_count }}</td>
                                    <td>{{ $dts->hdmi_short_cables_count }} (5M) & {{ $dts->hdmi_long_cables_count }} (20M)</td>
                                    <td>{{ $dts->extension_boards_count }}</td>
                                    <td>{{ $dts->trucking_count }}</td>
                                    <td>{{ $dts->sony_recorders_count }}</td>
                                    <td>
                                        <span class="badge bg-{{ $dts->is_available ? 'success' : 'secondary' }}">
                                            {{ $dts->is_available ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('auditor.dts.show', $dts) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-microphone fs-1 text-muted mb-3"></i>
                        <p class="text-muted">No DTS systems found for this court.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportCourtReport() {
    // Implement export functionality
    alert('Export functionality to be implemented');
}
</script>
@endpush