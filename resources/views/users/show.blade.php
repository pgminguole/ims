@extends('layouts.app')

@section('title', $user->name . ' - User Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('users') }}" class="text-decoration-none text-muted">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($user->name, 20) }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ $user->name }}</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="text-tiny text-muted">{{ $user->email ?? 'No Email' }}</span>
                <span class="text-muted">•</span>
                {{-- <span class="badge bg-light text-dark border rounded-pill">{{ $user->role->name ?? 'No Role' }}</span> --}}
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                <i class="fas fa-plus me-1"></i> Assign Asset
            </button>
            <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-success" data-bs-toggle="modal" data-bs-target="#createAssetModal">
                <i class="fas fa-plus-circle me-1"></i> New Asset
            </button>
            <div class="btn-group">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-white border rounded-start-pill px-3 shadow-sm text-dark">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-white border rounded-end-pill px-3 shadow-sm text-dark" onclick="printUserDetails()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Asset Summary -->
            <div class="row g-3 mb-4">
                 <div class="col-md-3 col-6">
                    <div class="stunning-card metric-v2 p-3 shadow-sm border-0 h-100">
                        <div class="d-flex flex-column justify-content-center">
                             <div class="text-tiny text-muted fw-bold text-uppercase mb-1">Total Assets</div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="h3 fw-bold text-dark mb-0">{{ $user->assignedAssets->count() }}</div>
                                <div class="metric-v2-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                                    <i class="fas fa-cubes"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="col-md-3 col-6">
                    <div class="stunning-card metric-v2 p-3 shadow-sm border-0 h-100">
                         <div class="d-flex flex-column justify-content-center">
                             <div class="text-tiny text-muted fw-bold text-uppercase mb-1">Laptops</div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="h3 fw-bold text-dark mb-0">{{ $user->laptops }}</div>
                                <div class="metric-v2-icon bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-laptop"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stunning-card metric-v2 p-3 shadow-sm border-0 h-100">
                         <div class="d-flex flex-column justify-content-center">
                             <div class="text-tiny text-muted fw-bold text-uppercase mb-1">Printers</div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="h3 fw-bold text-dark mb-0">{{ $user->printers }}</div>
                                <div class="metric-v2-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-print"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="col-md-3 col-6">
                    <div class="stunning-card metric-v2 p-3 shadow-sm border-0 h-100">
                         <div class="d-flex flex-column justify-content-center">
                             <div class="text-tiny text-muted fw-bold text-uppercase mb-1">UPS</div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="h3 fw-bold text-dark mb-0">{{ $user->ups }}</div>
                                <div class="metric-v2-icon bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fas fa-plug"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Basic Information</h6>
                </div>
                <div class="p-4 pt-1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Full Name</label>
                            <div class="text-small fw-medium text-dark">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Email</label>
                            <div class="text-small fw-medium text-dark">{{ $user->email ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Phone</label>
                            <div class="text-small fw-medium text-dark">{{ $user->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">User ID</label>
                            <div class="text-small fw-medium text-dark">#{{ $user->id }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Added By</label>
                            <div class="text-small fw-medium text-dark">{{ $user->creator->name ?? 'Superadmin' }}</div>
                            <div class="text-tiny text-muted">{{ $user->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role & Access Information -->
            {{-- <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Role & Access</h6>
                </div>
                <div class="p-4 pt-1">
                    <div class="row g-3">
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Primary Role</label>
                            <div>
                                <span class="badge bg-light text-dark border">{{ $user->role->name ?? 'No Role' }}</span>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Status</label>
                            <div>
                                <span class="badge {{ $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border border-0">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Account Approved</label>
                            <div class="text-small fw-medium text-dark">{{ $user->is_approved ? 'Yes' : 'Pending' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Pass. Reset Required</label>
                            <div class="text-small fw-medium text-dark">{{ $user->require_password_reset ? 'Yes' : 'No' }}</div>
                        </div>
                    </div>
                </div>
            </div> --}}

            @php
                $officialAssignments = $user->assignedAssets->where('record_type', 'assignment');
                $inventoryAssets = $user->assignedAssets->where('record_type', 'inventory');
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
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-icon btn-sm btn-light border" title="View">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                     <button class="btn btn-icon btn-sm btn-light border text-danger remove-asset-modal" 
                                            data-asset-id="{{ $asset->id }}"
                                            data-user-id="{{ $user->id }}"
                                            data-asset-name="{{ $asset->asset_name }}"
                                            title="Unassign">
                                        <i class="fas fa-times fa-xs"></i>
                                    </button>
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
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-icon btn-sm btn-light border" title="View">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                     <button class="btn btn-icon btn-sm btn-light border text-danger remove-asset-modal" 
                                            data-asset-id="{{ $asset->id }}"
                                            data-user-id="{{ $user->id }}"
                                            data-asset-name="{{ $asset->asset_name }}"
                                            title="Unassign">
                                        <i class="fas fa-times fa-xs"></i>
                                    </button>
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
            <!-- Location Info -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Location Details</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Court</label>
                        <div class="text-small fw-medium text-dark">{{ $user->court->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Region</label>
                        <div class="text-small fw-medium text-dark">{{ $user->court->region->name ?? 'N/A' }}</div>
                    </div>
                     <div class="mb-3">
                        <label class="text-tiny text-muted fw-bold text-uppercase">Office/Location</label>
                        <div class="text-small fw-medium text-dark">{{ $user->location->name ?? 'N/A' }}</div>
                    </div>
                     <div>
                        <label class="text-tiny text-muted fw-bold text-uppercase">Registry</label>
                        <div class="text-small fw-medium text-dark">{{ $user->registry->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

             <!-- Timeline -->
            <div class="stunning-card">
                 <div class="card-header-clean">
                    <h6 class="card-title-small">Timeline</h6>
                </div>
                 <div class="p-4 pt-1">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 position-relative ps-4">
                             <div class="position-absolute top-0 start-0 border-start border-2 h-100" style="left: 6px;"></div>
                             <div class="position-absolute top-0 start-0 bg-primary rounded-circle" style="width: 10px; height: 10px; left: 2px; top: 5px;"></div>
                            <div class="text-tiny text-muted text-uppercase mb-1">Last Login</div>
                            <div class="text-small fw-medium text-dark">{{ $user->login_at?->format('M d, Y H:i') ?? 'Never' }}</div>
                        </li>
                         <li class="mb-3 position-relative ps-4">
                             <div class="position-absolute top-0 start-0 border-start border-2 h-100" style="left: 6px;"></div>
                             <div class="position-absolute top-0 start-0 bg-secondary rounded-circle" style="width: 10px; height: 10px; left: 2px; top: 5px;"></div>
                            <div class="text-tiny text-muted text-uppercase mb-1">Updated</div>
                            <div class="text-small fw-medium text-dark">{{ $user->updated_at->format('M d, Y') }}</div>
                        </li>
                         <li class="position-relative ps-4">
                             <div class="position-absolute top-0 start-0 bg-success rounded-circle" style="width: 10px; height: 10px; left: 2px; top: 5px;"></div>
                            <div class="text-tiny text-muted text-uppercase mb-1">Created</div>
                            <div class="text-small fw-medium text-dark">{{ $user->created_at->format('M d, Y') }}</div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Include Modals --}}
@include('users.modals')

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