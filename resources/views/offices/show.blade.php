@extends('layouts.app')

@section('title', $office->name . ' - Office Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('offices.index') }}" class="text-decoration-none text-muted">Offices</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($office->name, 20) }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ $office->name }}</h4>
            <p class="text-tiny text-muted mb-0">{{ $office->code ?? 'No Code' }} • {{ $office->location->name ?? 'No Location' }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                <i class="fas fa-plus me-1"></i> Assign Asset
            </button>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createAssetModal">
                <i class="fas fa-plus-circle me-1"></i> New Asset
            </button>
            <a href="{{ route('offices.edit', $office) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="printOfficeDetails()">
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
                    <h6 class="card-title-small">Office Information</h6>
                </div>
                <div class="p-4 pt-1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Office Name</label>
                            <div class="text-small fw-medium text-dark">{{ $office->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Code</label>
                            <div class="text-small fw-medium text-dark">{{ $office->code ?? 'N/A' }}</div>
                        </div>
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Region</label>
                            <div class="text-small fw-medium text-dark">{{ $office->region->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Status</label>
                            <div>
                                <span class="badge {{ $office->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border border-0">
                                    {{ $office->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Added By</label>
                            <div class="text-small fw-medium text-dark">{{ $office->creator->name ?? 'Superadmin' }}</div>
                            <div class="text-tiny text-muted">{{ $office->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Description</label>
                            <div class="text-small text-muted">{{ $office->description ?? 'No description provided.' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Statistics -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Asset Statistics</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="row g-3 text-center">
                        <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-light">
                                <div class="text-large fw-bold text-dark">{{ $office->assets->count() }}</div>
                                <div class="text-tiny text-muted text-uppercase">Total</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $office->computers }}</div>
                                <div class="text-tiny text-muted text-uppercase">PCs</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $office->laptops }}</div>
                                <div class="text-tiny text-muted text-uppercase">Laptops</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $office->printers }}</div>
                                <div class="text-tiny text-muted text-uppercase">Printers</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $office->ups }}</div>
                                <div class="text-tiny text-muted text-uppercase">UPS</div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            @php
                $officialAssignments = $office->assets->where('record_type', 'assignment');
                $inventoryAssets = $office->assets->where('record_type', 'inventory');
            @endphp

            <!-- Official Assignments -->
             <div class="stunning-card mb-4">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">Official Assignments</h6>
                    @if($officialAssignments->count() > 0)
                        <span class="badge bg-primary rounded-pill">{{ $officialAssignments->count() }}</span>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-tiny text-muted text-uppercase ps-4">Asset Name</th>
                                <th class="text-tiny text-muted text-uppercase">Tag</th>
                                <th class="text-tiny text-muted text-uppercase">Category</th>
                                <th class="text-tiny text-muted text-uppercase">Status</th>
                                <th class="text-tiny text-muted text-uppercase">Assigned</th>
                                <th class="text-tiny text-muted text-uppercase pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                         <tbody>
                            @forelse($officialAssignments as $asset)
                            <tr>
                                <td class="ps-4">
                                    <div class="text-small fw-bold text-dark">{{ $asset->asset_name }}</div>
                                </td>
                                <td><div class="text-tiny font-monospace text-muted">{{ $asset->asset_tag }}</div></td>
                                <td><div class="text-tiny text-dark">{{ $asset->category->name ?? '-' }}</div></td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-tiny">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td><div class="text-tiny text-muted">{{ $asset->assigned_date?->format('M d, Y') ?? '-' }}</div></td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-icon btn-sm btn-light border" title="View">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        <form action="{{ route('offices.remove-asset', $office) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                            <button type="submit" class="btn btn-icon btn-sm btn-light border text-danger" title="Remove" onclick="confirmDelete(event, 'Remove this asset from office?', 'Yes, remove it!')">
                                                <i class="fas fa-times fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted text-small">No official assignments yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Inventory Collection -->
             <div class="stunning-card">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">Inventory Collection</h6>
                    @if($inventoryAssets->count() > 0)
                        <span class="badge bg-secondary rounded-pill">{{ $inventoryAssets->count() }}</span>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-tiny text-muted text-uppercase ps-4">Asset Name</th>
                                <th class="text-tiny text-muted text-uppercase">Tag</th>
                                <th class="text-tiny text-muted text-uppercase">Category</th>
                                <th class="text-tiny text-muted text-uppercase">Status</th>
                                <th class="text-tiny text-muted text-uppercase">Assigned</th>
                                <th class="text-tiny text-muted text-uppercase pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                         <tbody>
                            @forelse($inventoryAssets as $asset)
                            <tr>
                                <td class="ps-4">
                                    <div class="text-small fw-bold text-dark">{{ $asset->asset_name }}</div>
                                </td>
                                <td><div class="text-tiny font-monospace text-muted">{{ $asset->asset_tag }}</div></td>
                                <td><div class="text-tiny text-dark">{{ $asset->category->name ?? '-' }}</div></td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-tiny">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td><div class="text-tiny text-muted">{{ $asset->assigned_date?->format('M d, Y') ?? '-' }}</div></td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-icon btn-sm btn-light border" title="View">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        <form action="{{ route('offices.remove-asset', $office) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                            <button type="submit" class="btn btn-icon btn-sm btn-light border text-danger" title="Remove" onclick="confirmDelete(event, 'Remove this asset from office?', 'Yes, remove it!')">
                                                <i class="fas fa-times fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted text-small">No inventory collection assets yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Info -->
             <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Contact Details</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Phone</label>
                        <div class="text-small fw-medium text-dark">{{ $office->phone ?? 'N/A' }}</div>
                    </div>
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Email</label>
                        <div class="text-small fw-medium text-dark">{{ $office->email ?? 'N/A' }}</div>
                    </div>
                     <div>
                        <label class="text-tiny text-muted fw-bold text-uppercase">Address</label>
                        <div class="text-small fw-medium text-dark">{{ $office->address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Manager Info -->
             <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Manager</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Name</label>
                        <div class="text-small fw-medium text-dark">{{ $office->manager->name ?? 'Not Assigned' }}</div>
                    </div>
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Phone</label>
                        <div class="text-small fw-medium text-dark">{{ $office->manager->phone ?? 'N/A' }}</div>
                    </div>
                     <div>
                        <label class="text-tiny text-muted fw-bold text-uppercase">Email</label>
                        <div class="text-small fw-medium text-dark">{{ $office->manager->email ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

             <!-- Summary -->
             <div class="stunning-card">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Summary</h6>
                </div>
                <div class="p-4 pt-1">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-small text-muted">Created</span>
                        <span class="text-small fw-medium">{{ $office->created_at->format('M d, Y') }}</span>
                    </div>
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-small text-muted">Updated</span>
                        <span class="text-small fw-medium">{{ $office->updated_at->format('M d, Y') }}</span>
                    </div>
                     <div class="d-flex justify-content-between">
                        <span class="text-small text-muted">Capacity</span>
                        <span class="text-small fw-medium">{{ $office->capacity ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Include Modals --}}
@include('offices.modals')

@endsection

@section('styles')
<style>
.btn-icon {
    width: 28px;
    height: 28px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>
@endsection