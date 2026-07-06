@extends('layouts.app')

@section('title', 'Asset Portfolio - ' . $asset->asset_name)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Premium Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="{{ route('auditor.reports.index') }}" class="text-decoration-none text-muted">Auditor Reports</a></li>
                    <li class="breadcrumb-item active small text-primary" aria-current="page">Asset Details</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="fas fa-microchip me-3 text-primary"></i>{{ $asset->asset_name }}
                <span class="badge bg-light text-muted border ms-3 small fs-6 fw-normal">{{ $asset->asset_tag }}</span>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-light-modern px-4">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            @if(!$asset->is_audited)
                <form action="{{ route('auditor.assets.verify', $asset) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success px-4 shadow-sm" onclick="return confirm('Are you sure you want to mark this assignment as verified?')">
                        <i class="fas fa-check-circle me-2"></i>Mark as Verified
                    </button>
                </form>
            @else
                <span class="btn btn-outline-success px-4 disabled">
                    <i class="fas fa-check-double me-2"></i>Verified
                </span>
            @endif
            <button class="btn btn-primary-modern px-4 shadow-sm" onclick="exportAssetReport()">
                <i class="fas fa-file-pdf me-2"></i>Export Details
            </button>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-primary border-4 bounce-in">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-primary-subtle text-primary rounded-circle"><i class="fas fa-tag"></i></div>
                    @php
                        $statusVariant = $asset->status === 'available' ? 'success' : ($asset->status === 'assigned' ? 'primary' : 'warning');
                    @endphp
                    <span class="badge bg-{{ $statusVariant }}-subtle text-{{ $statusVariant }} rounded-pill px-3">{{ ucfirst($asset->status) }}</span>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Status</h6>
                <h4 class="fw-bold mb-0 text-dark">Asset Lifecycle</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : 'warning') }} border-4 bounce-in" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-info-subtle text-info rounded-circle"><i class="fas fa-heart-pulse"></i></div>
                    <span class="badge bg-light text-dark border rounded-pill px-3">{{ ucfirst($asset->condition) }}</span>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Condition</h6>
                <h4 class="fw-bold mb-0 text-dark">Device Health</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-info border-4 bounce-in" style="animation-delay: 0.2s">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-info-subtle text-info rounded-circle"><i class="fas fa-university"></i></div>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Assigned Court</h6>
                <h4 class="fw-bold mb-0 text-dark text-truncate">{{ $asset->assigned_entity_name }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-warning border-4 bounce-in" style="animation-delay: 0.3s">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="icon-box-sm bg-warning-subtle text-warning rounded-circle"><i class="fas fa-calendar-check"></i></div>
                </div>
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Last Audited</h6>
                <h4 class="fw-bold mb-0 text-dark">{{ $asset->last_audited_at ? $asset->last_audited_at->format('M j, Y') : 'Pending' }}</h4>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Technical Specifications
                            </h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Manufacturer</label>
                                    <span class="fw-semibold text-dark">{{ $asset->manufacturer ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Model Name</label>
                                    <span class="fw-semibold text-dark">{{ $asset->model ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Brand</label>
                                    <span class="fw-semibold text-dark">{{ $asset->brand ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Serial Number</label>
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $asset->serial_number ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">IP Address</label>
                                    <span class="fw-semibold text-dark">{{ $asset->ip_address ?? 'Not assigned' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">MAC Address</label>
                                    <span class="fw-semibold text-dark">{{ $asset->mac_address ?? 'Not available' }}</span>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Full Specifications</label>
                                    <div class="bg-light p-3 rounded-3 mt-1">
                                        <p class="mb-0 text-dark-soft">{{ $asset->specifications ?? 'No detailed specifications provided.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-history me-2 text-primary"></i>Asset History & Timeline
                            </h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Date</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th class="pe-3">Performed By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($asset->histories->sortByDesc('performed_at')->take(10) as $history)
                                        <tr>
                                            <td class="ps-3 small fw-medium">{{ $history->performed_at->format('M j, Y g:i A') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $history->action === 'assigned' ? 'success' : ($history->action === 'returned' ? 'warning' : 'info') }}-subtle text-{{ $history->action === 'assigned' ? 'success' : ($history->action === 'returned' ? 'warning' : 'info') }} rounded-pill px-3">
                                                    {{ ucfirst($history->action) }}
                                                </span>
                                            </td>
                                            <td class="small">{{ $history->description }}</td>
                                            <td class="pe-3 small text-dark-soft">{{ $history->performedBy->full_name ?? 'System' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">No history records found for this asset.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Assignment Card -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-user-tie me-2 text-primary"></i>Current Assignment
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            @if($asset->assigned_type)
                            @php
                                $assignedEntity = $asset->assigned_entity;
                                $assignedName = $asset->assigned_entity_name;
                            @endphp
                            <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4">
                                <div class="icon-box-lg bg-primary text-white rounded-circle me-3 shadow-sm">
                                    <i class="fas {{ $asset->assigned_type === 'user' ? 'fa-user' : ($asset->assigned_type === 'court' ? 'fa-gavel' : 'fa-building') }}"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        {{ $assignedName }}
                                    </h6>
                                    <span class="text-muted small text-uppercase fw-bold">{{ $asset->assigned_type }} assignment</span>
                                </div>
                            </div>
                            <div class="px-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small fw-bold text-uppercase">Assigned On</span>
                                    <span class="fw-semibold text-dark">{{ $asset->assigned_date ? $asset->assigned_date->format('M j, Y') : 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small fw-bold text-uppercase">Region</span>
                                    <span class="fw-semibold text-dark">{{ $asset->effective_region_name }}</span>
                                </div>
                                @if($asset->office)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small fw-bold text-uppercase">Office Unit</span>
                                    <span class="fw-semibold text-dark">{{ $asset->office->name }}</span>
                                </div>
                                @elseif($asset->court)
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small fw-bold text-uppercase">Court Details</span>
                                    <span class="fw-semibold text-dark">{{ $asset->court->name }}</span>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="text-center py-4 bg-light rounded-4">
                                <div class="icon-box-lg bg-warning-subtle text-warning rounded-circle mx-auto mb-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <p class="text-muted small fw-bold mb-0">NOT CURRENTLY ASSIGNED</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Financial & Warranty -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-shield-halved me-2 text-primary"></i>Assurance & Value
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="list-group list-group-flush border-top-0">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <span class="text-muted small fw-bold text-uppercase">Purchase Date</span>
                                    <span class="fw-semibold text-dark">{{ $asset->purchase_date ? $asset->purchase_date->format('M j, Y') : 'N/A' }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <span class="text-muted small fw-bold text-uppercase">Current Value</span>
                                    <span class="fw-bold text-primary">GHS {{ number_format($asset->current_value ?? 0, 2) }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <span class="text-muted small fw-bold text-uppercase">Warranty Expiry</span>
                                    @if($asset->warranty_expiry)
                                        <span class="badge {{ $asset->warranty_expiry->isPast() ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3">
                                            {{ $asset->warranty_expiry->format('M j, Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted small text-uppercase fw-bold">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Overview -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden shadow-gold">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-tools me-2 text-primary"></i>Service Status
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="p-3 bg-primary-subtle rounded-4 mb-3 text-center">
                                <h6 class="text-primary small fw-bold text-uppercase mb-1">Last Maintenance</h6>
                                <h5 class="fw-bold text-primary mb-0">{{ $asset->last_maintenance ? $asset->last_maintenance->format('M j, Y') : 'Never serviced' }}</h5>
                            </div>
                            <div class="p-3 bg-light rounded-4 text-center">
                                <h6 class="text-muted small fw-bold text-uppercase mb-1">Next Scheduled</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $asset->next_maintenance ? $asset->next_maintenance->format('M j, Y') : 'Contact Support' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for the Asset Detailed View */
    .stunning-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: transform 0.2s ease;
    }
    
    .stunning-card:hover { transform: translateY(-3px); }
    
    .icon-box-sm { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; }
    .icon-box-lg { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    
    .btn-primary-modern {
        background: #1f2937;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-primary-modern:hover { background: #000; color: white; transform: translateY(-2px); }
    
    .btn-light-modern {
        background: #f3f4f6;
        color: #4b5563;
        border: none;
        border-radius: 12px;
        font-weight: 600;
    }
    
    .text-dark-soft { color: #4b5563; }
    .shadow-gold { box-shadow: 0 10px 30px rgba(223, 166, 21, 0.08); }
    
    .table thead th {
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        padding: 1.25rem 0.75rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before { color: #d1d5db; }
    
    @keyframes bounceIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .bounce-in { animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
</style>

<script>
    function exportAssetReport() {
        const assetId = {{ $asset->id }};
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("auditor.reports.export") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const reportType = document.createElement('input');
        reportType.type = 'hidden';
        reportType.name = 'report_type';
        reportType.value = 'asset_details';
        form.appendChild(reportType);
        
        const assetInput = document.createElement('input');
        assetInput.type = 'hidden';
        assetInput.name = 'asset_id';
        assetInput.value = assetId;
        form.appendChild(assetInput);
        
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = 'pdf';
        form.appendChild(formatInput);
        
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection