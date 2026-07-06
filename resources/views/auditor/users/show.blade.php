@extends('layouts.app')

@section('title', 'User Profile - ' . $user->full_name)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Premium Header -->
    <!-- Premium Header -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-dark text-white position-relative">
        <!-- Abstract Background -->
        <div class="position-absolute top-0 end-0 w-50 h-100 bg-gradient-primary opacity-10" style="clip-path: polygon(20% 0%, 100% 0, 100% 100%, 0% 100%);"></div>

        <div class="card-body p-4 position-relative">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="me-4 position-relative">
                            <div class="icon-box-xl bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-white border border-white border-opacity-25" style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-2 border-dark rounded-circle"></span>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 letter-spacing-tight">{{ $user->name }}</h2>
                            <div class="d-flex align-items-center gap-3 text-white-50 small fw-bold text-uppercase">
                                <span class="badge bg-white bg-opacity-10 border border-white border-opacity-10 text-white fw-semibold rounded-pill px-3 py-1">
                                    N/A
                                </span>
                                <span><i class="fas fa-barcode me-1"></i> ID: #{{ $user->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <a href="#" onclick="history.back(); return false;" class="btn btn-outline-light border-white border-opacity-25 text-white px-4 rounded-3 fw-semibold hover-bg-light hover-text-dark">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button class="btn btn-primary px-4 rounded-3 fw-semibold shadow-lg border-0 bg-gradient-to-r from-blue-500 to-blue-600" onclick="exportUserReport()">
                            <i class="fas fa-file-pdf me-2"></i>Export Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bounce-in">
                <div class="card-body p-4">
                     <h6 class="text-muted small fw-bold text-uppercase mb-3 d-flex align-items-center">
                        <i class="fas fa-id-card me-2 text-primary"></i>Personnel Information
                    </h6>
                    <div class="list-group list-group-flush border-top-0">
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Email Address</span>
                            <span class="fw-semibold text-dark">N/A</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Contact Phone</span>
                            <span class="fw-semibold text-dark">{{ $user->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Region</span>
                            <span class="fw-semibold text-dark">{{ $user->region->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Court Center</span>
                            <span class="fw-semibold text-dark">{{ $user->court->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom-0">
                            <span class="text-muted small">Employment Status</span>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}-subtle text-{{ $user->is_active ? 'success' : 'danger' }} rounded-pill px-3">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Summary Card -->
            <div class="stunning-card p-4 border-start border-primary border-4 bounce-in" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-0">Hardware Status</h6>
                    <div class="icon-box-sm bg-primary-subtle text-primary rounded-circle"><i class="fas fa-laptop-medical"></i></div>
                </div>
                <div class="row text-center g-0">
                    <div class="col-6 border-end">
                        <h3 class="fw-bold mb-0 text-dark">{{ $assignedAssets->count() }}</h3>
                        <small class="text-muted">Currently Assigned</small>
                    </div>
                    <div class="col-6">
                        <h3 class="fw-bold mb-0 text-dark">{{ $assetHistory->where('action', 'returned')->count() }}</h3>
                        <small class="text-muted">Items Returned</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Equipment -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bounce-in" style="animation-delay: 0.2s">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-keyboard me-2 text-primary"></i>Currently Assigned Equipment
                    </h5>
                </div>
                <div class="card-body pt-0">
                    @if($assignedAssets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Equipment Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Condition</th>
                                    <th>Assigned Date</th>
                                    <th class="pe-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedAssets as $asset)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $asset->asset_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-muted border px-2">{{ $asset->category->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : 'warning') }}-subtle text-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : 'warning') }} rounded-pill px-3">
                                            {{ ucfirst($asset->condition) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $asset->assigned_date ? $asset->assigned_date->format('M j, Y') : 'N/A' }}</td>
                                    <td class="pe-3 text-center">
                                        <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-sm btn-light-gold rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="icon-box-lg bg-light text-muted rounded-circle mx-auto mb-3">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h6 class="text-muted fw-bold">No assets currently assigned to this user.</h6>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Profile History -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4 bounce-in" style="animation-delay: 0.3s">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-clock-rotate-left me-2 text-primary"></i>Equipment Transfer History
                    </h5>
                </div>
                <div class="card-body pt-0">
                    @if($assetHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Asset Tag</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th class="pe-3">Performed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assetHistory->take(10) as $history)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $history->performed_at->format('M j, Y g:i A') }}</td>
                                    <td><code class="text-primary">{{ $history->asset->asset_tag }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $history->action === 'assigned' ? 'success' : ($history->action === 'returned' ? 'warning' : 'info') }} rounded-pill px-2">
                                            {{ ucfirst($history->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $history->description }}</td>
                                    <td class="pe-3">{{ $history->performedBy->full_name ?? 'System' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted small">No historical data available.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stunning-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    .icon-box-sm { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; }
    .icon-box-lg { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    
    .btn-primary-modern {
        background: #1f2937;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary-modern:hover { background: #000; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    
    .btn-light-gold {
        background-color: #fdf6e7;
        color: #dfa615;
        border: 1px solid #f9eecd;
    }
    .btn-light-gold:hover { background-color: #dfa615; color: white; }

    .table thead th { font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; padding: 1.25rem 0.75rem; }
    
    @keyframes bounceIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .bounce-in { animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    
    .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.3); }
</style>

<script>
    function exportUserReport() {
        alert('Compiling user asset profile for export...');
    }
</script>
@endsection