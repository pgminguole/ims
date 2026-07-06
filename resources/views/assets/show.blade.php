@extends('layouts.app')

@section('title', $asset->asset_name . ' - Asset Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}" class="text-decoration-none text-muted">Assets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($asset->asset_name, 20) }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ $asset->asset_name }}</h4>
            <div class="d-flex align-items-center gap-2">
                 <p class="text-tiny text-muted mb-0">{{ $asset->asset_id }} • {{ $asset->asset_tag }}</p>
                 <span class="badge {{ $asset->status === 'available' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border border-0 text-tiny rounded-pill">
                    {{ ucfirst($asset->status) }}
                 </span>
            </div>
        </div>
        <div class="d-flex gap-2">
             @if($asset->status === 'available')
                @can('assign_assets')
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                    <i class="fas fa-user-plus me-1"></i> Assign
                </button>
                @endcan
            @elseif($asset->status === 'assigned')
                @can('assign_assets')
                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reassignAssetModal">
                    <i class="fas fa-exchange-alt me-1"></i> Reassign
                </button>
                @endcan
                @can('return_assets')
                <button type="button" class="btn btn-sm btn-info rounded-pill px-3 text-white" data-bs-toggle="modal" data-bs-target="#returnAssetModal">
                    <i class="fas fa-undo me-1"></i> Return
                </button>
                @endcan
            @endif
            @can('edit_assets')
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            @endcan
            @can('delete_assets')
            <form action="{{ route('assets.destroy', $asset) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="confirmDelete(event, 'Are you sure you want to delete this asset? This cannot be undone.', 'Yes, delete it!')">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
            @endcan
            <button type="button" class="btn btn-sm btn-light border rounded-circle" onclick="printAssetDetails()" title="Print">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Basic Information</h6>
                </div>
                <div class="p-4 pt-1">
                    <div class="row g-3">
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Serial Number</label>
                            <div class="text-small fw-medium text-dark">{{ $asset->serial_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Model</label>
                            <div class="text-small fw-medium text-dark">{{ $asset->model ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Brand</label>
                            <div class="text-small fw-medium text-dark">{{ $asset->brand ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Manufacturer</label>
                            <div class="text-small fw-medium text-dark">{{ $asset->manufacturer ?? 'N/A' }}</div>
                        </div>
                         <div class="col-12">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Description</label>
                            <div class="text-small text-muted">{{ $asset->description ?? 'No description provided.' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Comments</label>
                            <div class="text-small text-muted">{{ $asset->comments ?? 'No comments provided.' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classification & Assignment -->
             <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="stunning-card">
                         <div class="card-header-clean">
                            <h6 class="card-title-small">Classification</h6>
                        </div>
                        <div class="p-4 pt-1">
                             <div class="mb-3">
                                <label class="text-tiny text-muted fw-bold text-uppercase">Category</label>
                                <div><span class="badge bg-light text-dark border rounded-pill">{{ $asset->category->name ?? 'N/A' }}</span></div>
                            </div>
                            <div class="mb-3">
                                <label class="text-tiny text-muted fw-bold text-uppercase">Subcategory</label>
                                <div class="text-small fw-medium text-dark">{{ $asset->subcategory->name ?? 'N/A' }}</div>
                            </div>
                             <div class="mb-3">
                                <label class="text-tiny text-muted fw-bold text-uppercase">Region</label>
                                <div class="text-small fw-medium text-dark">{{ $asset->region->name ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-tiny text-muted fw-bold text-uppercase">Court</label>
                                <div class="text-small fw-medium text-dark">{{ $asset->court->name ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <label class="text-tiny text-muted fw-bold text-uppercase">Added By</label>
                                <div class="text-small fw-medium text-dark">{{ $asset->creator->name ?? 'Superadmin' }}</div>
                                <div class="text-tiny text-muted">{{ $asset->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stunning-card">
                        <div class="card-header-clean">
                            <h6 class="card-title-small">Assignment Details</h6>
                        </div>
                         <div class="p-4 pt-1">
                             <div class="mb-3">
                                <label class="text-tiny text-muted fw-bold text-uppercase">Assigned To</label>
                                 @php
                                    $assignedEntity = $asset->assigned_entity;
                                    $assignedType = strtolower($asset->assigned_type ?? '');
                                 @endphp
                                 
                                 @if($assignedEntity)
                                    <div class="d-flex align-items-center mt-1">
                                        @php
                                            $icon = 'fa-box';
                                            $bg = 'bg-secondary';
                                            if ($assignedType === 'user' || ($assignedEntity instanceof \App\Models\User)) {
                                                $icon = 'fa-user';
                                                $bg = 'bg-primary';
                                            } elseif ($assignedType === 'office' || ($assignedEntity instanceof \App\Models\Office)) {
                                                $icon = 'fa-building';
                                                $bg = 'bg-info';
                                            } elseif ($assignedType === 'court' || ($assignedEntity instanceof \App\Models\Court)) {
                                                $icon = 'fa-gavel';
                                                $bg = 'bg-warning';
                                            } elseif ($assignedType === 'region' || ($assignedEntity instanceof \App\Models\Region)) {
                                                $icon = 'fa-map-marker-alt';
                                                $bg = 'bg-success';
                                            }
                                        @endphp
                                        <div class="avatar-circle-sm {{ $bg }} text-white me-2">
                                            <i class="fas {{ $icon }} fa-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-small fw-bold text-dark">{{ $asset->assigned_entity_name }}</div>
                                            <div class="text-tiny text-muted">{{ ucfirst($assignedType ?: 'System') }} Assignment</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-small text-muted fst-italic mt-1">Currently Unassigned</div>
                                @endif
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Date Assigned</label>
                                    <div class="text-small fw-medium text-dark">{{ $asset->assigned_date?->format('M d, Y') ?? 'N/A' }}</div>
                                </div>
                                 <div class="col-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Condition</label>
                                    <div class="text-small fw-medium text-dark">{{ ucfirst($asset->condition) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>

            <!-- Technical & Financial (Tabs) -->
             <div class="stunning-card mb-4">
                 <div class="card-header-clean">
                     <ul class="nav nav-pills card-header-pills" id="assetTabs" role="tablist">
                         <li class="nav-item">
                             <button class="nav-link active text-tiny fw-bold text-uppercase py-1 px-3" data-bs-toggle="tab" data-bs-target="#financial" type="button">Financial</button>
                         </li>
                         <li class="nav-item">
                             <button class="nav-link text-tiny fw-bold text-uppercase py-1 px-3" data-bs-toggle="tab" data-bs-target="#warranty" type="button">Warranty</button>
                         </li>
                         <li class="nav-item">
                             <button class="nav-link text-tiny fw-bold text-uppercase py-1 px-3" data-bs-toggle="tab" data-bs-target="#technical" type="button">Technical</button>
                         </li>
                     </ul>
                 </div>
                 <div class="card-body p-4 pt-2">
                     <div class="tab-content">
                         <div class="tab-pane fade show active" id="financial">
                             <div class="row g-3">
                                 <div class="col-md-4">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Purchase Date</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->purchase_date?->format('M d, Y') ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-md-4">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Received Date</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->recieved_date?->format('M d, Y') ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-md-4">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Supplier</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->supplier ?? 'N/A' }}</div>
                                 </div>
                             </div>
                         </div>
                         <div class="tab-pane fade" id="warranty">
                              <div class="row g-3">
                                 <div class="col-md-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Warranty Period</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->warranty_period ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-md-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Expiry Date</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->warranty_expiry?->format('M d, Y') ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-md-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Last Maintenance</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->last_maintenance?->format('M d, Y') ?? 'N/A' }}</div>
                                 </div>
                                  <div class="col-md-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Next Maintenance</label>
                                     <div class="text-small fw-medium text-dark">{{ $asset->next_maintenance?->format('M d, Y') ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-12">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Maintenance Notes</label>
                                     <div class="text-small text-muted">{{ $asset->maintenance_notes ?? 'None' }}</div>
                                 </div>
                             </div>
                         </div>
                         <div class="tab-pane fade" id="technical">
                              <div class="row g-3">
                                 <div class="col-md-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">IP Address</label>
                                     <div class="text-small fw-medium text-dark font-monospace">{{ $asset->ip_address ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-md-6">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">MAC Address</label>
                                     <div class="text-small fw-medium text-dark font-monospace">{{ $asset->mac_address ?? 'N/A' }}</div>
                                 </div>
                                 <div class="col-12">
                                     <label class="text-tiny text-muted fw-bold text-uppercase">Specifications</label>
                                     <div class="text-small text-muted">{{ $asset->specifications ?? 'None' }}</div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
             <!-- Metadata -->
             <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Metadata</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-tiny text-muted">Created</span>
                        <span class="text-small fw-medium">{{ $asset->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-tiny text-muted">Last Updated</span>
                        <span class="text-small fw-medium">{{ $asset->updated_at->format('M d, Y') }}</span>
                    </div>
                     <div class="d-flex justify-content-between">
                        <span class="text-tiny text-muted">Registry</span>
                        <span class="text-small fw-medium">{{ $asset->registry->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
             <div class="stunning-card">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">History</h6>
                    <button class="btn btn-sm btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#historyModal">View All</button>
                </div>
                <div class="p-4 pt-1">
                    @forelse($asset->histories->take(5) as $history)
                        <div class="mb-3 pb-3 border-bottom last-border-0">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-light text-dark border border-0 text-tiny">{{ ucfirst(str_replace('_', ' ', $history->action)) }}</span>
                                <small class="text-tiny text-muted">{{ $history->performed_at->format('M d') }}</small>
                            </div>
                            <div class="text-small text-dark mb-1">{{ Str::limit($history->description, 50) }}</div>
                            <small class="text-tiny text-muted">By: {{ $history->performedBy->name ?? 'System' }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-small text-center mb-0">No history recorded.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Include Modals (Extracted to separate file for cleanliness) --}}
@include('assets.partials.modals')

@endsection

@section('styles')
<style>
.last-border-0:last-child {
    border-bottom: 0 !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
.avatar-circle-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
}
.nav-pills .nav-link {
    color: #6c757d;
    background: transparent;
    border-radius: 20px;
}
.nav-pills .nav-link.active {
    color: #000;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}
</style>
@endsection

@push('scripts')
<script>
function printAssetDetails() {
    window.print();
}
</script>
@endpush