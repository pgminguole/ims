@extends('layouts.app')

@section('title', 'Unit Analysis - ' . $department->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Premium Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="{{ route('auditor.departments.index') }}" class="text-decoration-none text-muted">Offices Audit</a></li>
                    <li class="breadcrumb-item active small text-primary" aria-current="page">Unit Assessment</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="fas fa-building me-3 text-primary"></i>{{ $department->name }}
                <span class="badge bg-light text-muted border ms-3 small fs-6 fw-normal">{{ $department->code }}</span>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-light-modern px-4">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            <button class="btn btn-primary-modern px-4 shadow-sm" onclick="exportDepartmentReport()">
                <i class="fas fa-file-pdf me-2"></i>Unit Report
            </button>
        </div>
    </div>

    <!-- Dept stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-primary border-4 bounce-in">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-primary-subtle text-primary rounded-circle"><i class="fas fa-calculator"></i></div>
                    <span class="text-muted small fw-bold">TOTAL STOCK</span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $ds->total() }}</h3>
                <p class="text-muted small mb-0 mt-1">Equipment items</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-success border-4 bounce-in" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-success-subtle text-success rounded-circle"><i class="fas fa-user-tag"></i></div>
                    <span class="text-muted small fw-bold">ALLOCATED</span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $assignedAssetsCount }}</h3>
                <p class="text-muted small mb-0 mt-1">Personnel assigned</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-info border-4 bounce-in" style="animation-delay: 0.2s">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-info-subtle text-info rounded-circle"><i class="fas fa-warehouse"></i></div>
                    <span class="text-muted small fw-bold">AVAILABLE</span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $availableAssetsCount }}</h3>
                <p class="text-muted small mb-0 mt-1">In storage/reserve</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-warning border-4 bounce-in" style="animation-delay: 0.3s">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-warning-subtle text-warning rounded-circle"><i class="fas fa-tools"></i></div>
                    <span class="text-muted small fw-bold">MAINTENANCE</span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $maintenanceAssetsCount }}</h3>
                <p class="text-muted small mb-0 mt-1">Items at workshop</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Unit Details -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bounce-in" style="animation-delay: 0.4s">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Unit Identification
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="list-group list-group-flush border-top-0">
                        <div class="list-group-item px-0 py-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Assigned Court</label>
                            <span class="fw-semibold text-dark">{{ $department->court->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Unit Manager</label>
                            <span class="fw-semibold text-dark">{{ $department->manager->full_name ?? 'Not Set' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Compliance Status</label>
                            <span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}-subtle text-{{ $department->is_active ? 'success' : 'danger' }} rounded-pill px-3">
                                {{ $department->is_active ? 'Fully Operations' : 'Suspended' }}
                            </span>
                        </div>
                        <div class="list-group-item px-0 py-3 border-bottom-0">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Description</label>
                            <p class="small text-muted mb-0">{{ $department->description ?? 'No specific unit description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Snapshot -->
            <div class="stunning-card p-4 shadow-gold bounce-in" style="animation-delay: 0.5s">
                <h6 class="text-dark fw-bold mb-3 d-flex align-items-center">
                    <i class="fas fa-heartbeat me-2 text-danger"></i>Condition Health
                </h6>
                @if($ds->total() > 0 && !empty($assetConditionCounts))
                    @foreach($assetConditionCounts as $condition => $count)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted text-uppercase fw-bold">{{ $condition }}</span>
                            <span class="small fw-bold">{{ round(($count / $ds->total()) * 100) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $condition === 'excellent' ? 'success' : ($condition === 'good' ? 'info' : 'warning') }}" 
                                 role="progressbar" 
                                 style="width: {{ ($count / $ds->total()) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted small text-center py-3">No conditioning data found.</p>
                @endif
            </div>
        </div>

        <!-- Unit Inventory -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bounce-in" style="animation-delay: 0.6s">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-boxes-stacked me-2 text-primary"></i>Departmental Equipment
                    </h5>
                    <a href="{{ route('auditor.assets.index') }}?office_id={{ $department->id }}" class="btn btn-sm btn-light-modern px-3">
                        Asset Archive
                    </a>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Item Name</th>
                                    <th>Category</th>
                                    <th>Assigned To</th>
                                    <th class="text-center">Condition</th>
                                    <th class="pe-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ds as $asset)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $asset->asset_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-muted border px-2">{{ $asset->category->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="small">{{ $asset->assignedUser->full_name ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : 'warning') }}-subtle text-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : 'warning') }} rounded-pill px-3">
                                            {{ ucfirst($asset->condition ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-center">
                                        <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-sm btn-light-gold rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">This unit does not have any assets registered.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Unit activity -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4 bounce-in" style="animation-delay: 0.7s">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-list-check me-2 text-primary"></i>Operational Events
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $activity)
                        <div class="list-group-item px-0 py-3 border-bottom-dashed">
                            <div class="d-flex justify-content-between align-items-start text-dark">
                                <div class="d-flex">
                                    <div class="icon-box-sm bg-light rounded-circle me-3"><i class="fas fa-tag text-muted small"></i></div>
                                    <div>
                                        <h6 class="mb-1 small fw-bold">{{ $activity->asset->asset_name ?? 'Unknown Asset' }}</h6>
                                        <p class="mb-0 small text-muted">{{ $activity->description }}</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $activity->action === 'assigned' ? 'success' : 'info' }}-subtle text-{{ $activity->action === 'assigned' ? 'success' : 'info' }} rounded-pill px-2 mb-1" style="font-size: 0.65rem;">
                                        {{ strtoupper($activity->action) }}
                                    </span>
                                    <br>
                                    <small class="text-muted-xs">{{ $activity->performed_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">Logs are clear for this unit.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stunning-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: transform 0.2s ease; }
    .stunning-card:hover { transform: translateY(-3px); }
    .icon-box-sm { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; }
    
    .btn-primary-modern {
        background: #1f2937;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-primary-modern:hover { background: #000; color: white; transform: translateY(-2px); }
    
    .btn-light-modern { background: #f3f4f6; color: #4b5563; border: none; border-radius: 12px; font-weight: 600; }
    .btn-light-gold { background-color: #fdf6e7; color: #dfa615; border: 1px solid #f9eecd; }
    .btn-light-gold:hover { background-color: #dfa615; color: white; }
    
    .shadow-gold { box-shadow: 0 10px 30px rgba(223, 166, 21, 0.08); }
    .border-bottom-dashed { border-bottom: 1px dashed #e5e7eb; }
    .text-muted-xs { font-size: 0.7rem; color: #9ca3af; }

    .table thead th { font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; padding: 1.25rem 0.75rem; }
    
    @keyframes bounceIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .bounce-in { animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
</style>

<script>
    function exportDepartmentReport() {
        alert('Compiling unit audit report...');
    }
</script>
@endsection