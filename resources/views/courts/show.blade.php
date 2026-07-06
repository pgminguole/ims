@extends('layouts.app')

@section('title', $court->name . ' - Court Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('courts') }}" class="text-decoration-none text-muted">Courts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($court->name, 20) }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ $court->name }}</h4>
            <p class="text-tiny text-muted mb-0">{{ $court->code ?? 'No Code' }} • {{ isset($court->type) ? str_replace('_', ' ', ucfirst($court->type)) : 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                <i class="fas fa-plus me-1"></i> Assign Asset
            </button>
            <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createAssetModal">
                <i class="fas fa-plus-circle me-1"></i> New Asset
            </button>
            <a href="{{ route('courts.edit', $court) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="printAssetDetails()">
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
                            <label class="text-tiny text-muted fw-bold text-uppercase">Court Name</label>
                            <div class="text-small fw-medium text-dark">{{ $court->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Code</label>
                            <div class="text-small fw-medium text-dark">{{ $court->code ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Location</label>
                            <div class="text-small fw-medium text-dark">{{ $court->location->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Type</label>
                            <div>
                                @if(isset($court->type))
                                    <span class="badge bg-light text-dark border border-0 fw-medium">
                                        {{ str_replace('_', ' ', ucfirst($court->type)) }}
                                    </span>
                                @else
                                    <span class="text-small text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Region</label>
                            <div class="text-small fw-medium text-dark">{{ $court->region->name ?? 'N/A' }}</div>
                        </div>
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Status</label>
                             <div>
                                <span class="badge {{ isset($court->is_active) && $court->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border border-0">
                                    {{ isset($court->is_active) && $court->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Added By</label>
                            <div class="text-small fw-medium text-dark">{{ $court->creator->name ?? 'Superadmin' }}</div>
                            <div class="text-tiny text-muted">{{ $court->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Summary -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Asset Snapshot</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="row g-3 text-center">
                        <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-light">
                                <div class="text-large fw-bold text-dark">{{ $court->total_assets ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Total</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->computers ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Computers</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->laptops ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Laptops</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->printers ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Printers</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->scanners ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Scanners</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->photocopiers ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Copiers</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->ups ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">UPS</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-3">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $court->dts_count ?? $court->dts->count() ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">DTS</div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            <!-- DTS Information -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h6 class="card-title-small">Direct Transcription System (DTS)</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addDtsModal">
                        <i class="fas fa-plus me-1"></i> Add DTS
                    </button>
                </div>
                 <div class="p-4 pt-1">
                    @forelse($court->dts as $index => $dts)
                        <div class="dts-item mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-small fw-bold text-primary mb-0">
                                    DTS #{{ $index + 1 }}{{ isset($dts->name) && $dts->name ? ' - ' . $dts->name : '' }}
                                </h6>
                                <span class="badge {{ isset($dts->is_available) && $dts->is_available ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border border-0 text-tiny">
                                    {{ isset($dts->is_available) && $dts->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-3 col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="text-tiny text-uppercase text-muted">Monitors</div>
                                        <div class="fw-bold">{{ $dts->monitors_count ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                         <div class="text-tiny text-uppercase text-muted">Splitters</div>
                                        <div class="fw-bold">{{ $dts->splitters_count ?? 0 }}</div>
                                    </div>
                                </div>
                                 <div class="col-md-3 col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                         <div class="text-tiny text-uppercase text-muted">HDMI (5M/20M)</div>
                                        <div class="fw-bold">{{ $dts->hdmi_short_cables_count ?? 0 }} / {{ $dts->hdmi_long_cables_count ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                         <div class="text-tiny text-uppercase text-muted">Trucking</div>
                                        <div class="fw-bold">{{ $dts->trucking_count ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                 <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editDtsDateModal"
                                        data-dts-id="{{ $dts->id }}"
                                        data-dts-name="{{ $dts->name }}"
                                        data-date-assigned="{{ $dts->date_assigned }}">
                                    <i class="fas fa-calendar me-1"></i> Date
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editDtsModal"
                                        data-dts-id="{{ $dts->id }}"
                                        data-dts-name="{{ $dts->name }}"
                                        data-monitors="{{ $dts->monitors_count }}"
                                        data-splitters="{{ $dts->splitters_count }}"
                                        data-hdmi-short="{{ $dts->hdmi_short_cables_count }}"
                                        data-hdmi-long="{{ $dts->hdmi_long_cables_count }}"
                                        data-extension-boards="{{ $dts->extension_boards_count }}"
                                        data-trucking="{{ $dts->trucking_count }}"
                                        data-sony-recorders="{{ $dts->sony_recorders_count }}"
                                        data-date-assigned="{{ $dts->date_assigned }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <form action="{{ route('courts.remove-dts', $court) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="dts_id" value="{{ $dts->id }}">
                                    <button type="submit" class="btn btn-xs btn-outline-danger rounded-pill" onclick="confirmDelete(event, 'Remove this DTS?', 'Yes, remove it!')">
                                        <i class="fas fa-trash me-1"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                         <div class="text-center py-4 bg-light rounded">
                            <p class="text-muted text-small mb-0">No DTS assigned to this court.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Personnel Info -->
             <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Personnel</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Presiding Judge</label>
                        <div class="text-small fw-medium text-dark">{{ $court->presidingJudge->full_name ?? 'N/A' }}</div>
                    </div>
                     <div>
                        <label class="text-tiny text-muted fw-bold text-uppercase">Registry Officer</label>
                        <div class="text-small fw-medium text-dark">{{ $court->registryOfficer->full_name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

             <!-- Address Info -->
             <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Address</h6>
                </div>
                <div class="p-4 pt-1">
                     <div>
                        <div class="text-small fw-medium text-dark">{{ $court->address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            @php
                $officialAssignments = isset($court->assets) ? $court->assets->where('record_type', 'assignment') : collect();
                $inventoryAssets = isset($court->assets) ? $court->assets->where('record_type', 'inventory') : collect();
            @endphp

            <!-- Official Assignments -->
             <div class="stunning-card mb-4">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">Official Assignments</h6>
                </div>
                <div class="p-4 pt-1">
                    @if($officialAssignments->count() > 0)
                        @foreach($officialAssignments->take(5) as $asset)
                             <div class="mb-3 pb-3 border-bottom last-border-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                     <div class="text-small fw-bold text-dark">{{ $asset->asset_name ?? 'N/A' }}</div>
                                     <span class="badge bg-success-subtle text-success border border-0 text-tiny">{{ isset($asset->status) ? ucfirst($asset->status) : 'Unknown' }}</span>
                                </div>
                                <div class="text-tiny text-muted mb-2">{{ $asset->asset_tag ?? 'N/A' }}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-xs btn-light border" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAssetDateModal"
                                            data-asset-id="{{ $asset->id }}"
                                              data-asset-slug="{{ $asset->slug }}"
                                            data-asset-name="{{ $asset->asset_name }}"
                                            data-assigned-date="{{ $asset->assigned_date }}">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </button>
                                     <form action="{{ route('courts.remove-asset', $court) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                        <button type="submit" class="btn btn-link p-0 text-danger" title="Remove" onclick="confirmDelete(event, 'Remove this asset from court?', 'Yes, remove it!')">
                                            <i class="fas fa-times fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                             </div>
                        @endforeach
                    @else
                        <p class="text-muted text-small text-center mb-0">No official assignments</p>
                    @endif
                </div>
            </div>

            <!-- Inventory Collection -->
             <div class="stunning-card">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">Inventory Collection</h6>
                </div>
                <div class="p-4 pt-1">
                    @if($inventoryAssets->count() > 0)
                        @foreach($inventoryAssets->take(5) as $asset)
                             <div class="mb-3 pb-3 border-bottom last-border-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                     <div class="text-small fw-bold text-dark">{{ $asset->asset_name ?? 'N/A' }}</div>
                                     <span class="badge bg-success-subtle text-success border border-0 text-tiny">{{ isset($asset->status) ? ucfirst($asset->status) : 'Unknown' }}</span>
                                </div>
                                <div class="text-tiny text-muted mb-2">{{ $asset->asset_tag ?? 'N/A' }}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-xs btn-light border" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAssetDateModal"
                                            data-asset-id="{{ $asset->id }}"
                                              data-asset-slug="{{ $asset->slug }}"
                                            data-asset-name="{{ $asset->asset_name }}"
                                            data-assigned-date="{{ $asset->assigned_date }}">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </button>
                                     <form action="{{ route('courts.remove-asset', $court) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                        <button type="submit" class="btn btn-link p-0 text-danger" title="Remove" onclick="confirmDelete(event, 'Remove this asset from court?', 'Yes, remove it!')">
                                            <i class="fas fa-times fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                             </div>
                        @endforeach
                    @else
                        <p class="text-muted text-small text-center mb-0">No inventory assets</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modals -->
@include('courts.partials.asset-modals')
@include('courts.partials.dts-modals')
@include('courts.partials.date-edit-modals')
@include('courts.partials.create-asset-modal')

@endsection

@section('styles')
<style>
.last-border-0:last-child {
    border-bottom: 0 !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
.btn-xs {
    padding: 0.1rem 0.4rem;
    font-size: 0.75rem;
}
</style>
@endsection

@push('scripts')
<script>
function printAssetDetails() {
    // Basic print functionality
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    // Edit DTS Modal Handler
    const editDtsModal = document.getElementById('editDtsModal');
    if (editDtsModal) {
        editDtsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const dtsId = button.getAttribute('data-dts-id');
            const dtsName = button.getAttribute('data-dts-name');
            const monitors = button.getAttribute('data-monitors');
            const splitters = button.getAttribute('data-splitters');
            const hdmiShort = button.getAttribute('data-hdmi-short');
            const hdmiLong = button.getAttribute('data-hdmi-long');
            const extensionBoards = button.getAttribute('data-extension-boards');
            const trucking = button.getAttribute('data-trucking');
            const sonyRecorders = button.getAttribute('data-sony-recorders');
            const dateAssigned = button.getAttribute('data-date-assigned');

            const modal = this;
            modal.querySelector('#edit_dts_id').value = dtsId;
            modal.querySelector('#edit_dts_name').value = dtsName || '';
            modal.querySelector('#edit_monitors_count').value = monitors || 0;
            modal.querySelector('#edit_splitters_count').value = splitters || 0;
            modal.querySelector('#edit_hdmi_short_cables_count').value = hdmiShort || 0;
            modal.querySelector('#edit_hdmi_long_cables_count').value = hdmiLong || 0;
            modal.querySelector('#edit_extension_boards_count').value = extensionBoards || 0;
            modal.querySelector('#edit_trucking_count').value = trucking || 0;
            modal.querySelector('#edit_sony_recorders_count').value = sonyRecorders || 0;
            if (dateAssigned) {
                modal.querySelector('#edit_date_assigned').value = dateAssigned.split(' ')[0];
            }
        });
    }

    // Edit DTS Date Modal Handler
    const editDtsDateModal = document.getElementById('editDtsDateModal');
    if (editDtsDateModal) {
        editDtsDateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const dtsId = button.getAttribute('data-dts-id');
            const dtsName = button.getAttribute('data-dts-name');
            const dateAssigned = button.getAttribute('data-date-assigned');

            const modal = this;
            modal.querySelector('#edit_dts_date_id').value = dtsId;
            modal.querySelector('#dts_name_display').textContent = dtsName || 'DTS System';
            if (dateAssigned) {
                modal.querySelector('#edit_dts_date_assigned').value = dateAssigned.split(' ')[0];
            }
        });
    }

    // Edit Asset Date Modal Handler
    const editAssetDateModal = document.getElementById('editAssetDateModal');
    if (editAssetDateModal) {
        editAssetDateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const assetId = button.getAttribute('data-asset-id');
            const assetName = button.getAttribute('data-asset-name');
            const assignedDate = button.getAttribute('data-assigned-date');

            const modal = this;
            const form = modal.querySelector('#editAssetDateForm');
            
            modal.querySelector('#edit_asset_date_id').value = assetId;
            modal.querySelector('#asset_name_display').textContent = assetName || 'Asset';
            if (assignedDate) {
                modal.querySelector('#edit_asset_date_assigned').value = assignedDate.split(' ')[0];
            }
             form.action = `/assets/${assetId}/change-assigned-date`;
        });
    }
});
</script>
@endpush