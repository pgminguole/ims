@extends('layouts.app')

@section('title', $region->name . ' - Region Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('regions.index') }}" class="text-decoration-none text-muted">Regions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($region->name, 20) }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ $region->name }}</h4>
            <p class="text-tiny text-muted mb-0">{{ $region->code ?? 'No Code' }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                <i class="fas fa-plus me-1"></i> Assign Asset
            </button>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createAssetModal">
                <i class="fas fa-plus-circle me-1"></i> New Asset
            </button>
            <a href="{{ route('regions.edit', $region) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="printRegionDetails()">
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
                    <h6 class="card-title-small">Region Information</h6>
                </div>
                <div class="p-4 pt-1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Region Name</label>
                            <div class="text-small fw-medium text-dark">{{ $region->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Code</label>
                            <div class="text-small fw-medium text-dark">{{ $region->code ?? 'N/A' }}</div>
                        </div>
                         <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Status</label>
                            <div>
                                <span class="badge {{ $region->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border border-0">
                                    {{ $region->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Added By</label>
                            <div class="text-small fw-medium text-dark">{{ $region->creator->name ?? 'Superadmin' }}</div>
                            <div class="text-tiny text-muted">{{ $region->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-tiny text-muted fw-bold text-uppercase">Description</label>
                            <div class="text-small text-muted">{{ $region->description ?? 'No description provided.' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Statistics -->
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Region Snapshot</h6>
                </div>
                <div class="p-4 pt-1">
                     <div class="row g-3 text-center">
                        <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-light">
                                <div class="text-large fw-bold text-dark">{{ $assetStats['total'] }}</div>
                                <div class="text-tiny text-muted text-uppercase">Total Assets</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $assetStats['available'] ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Available</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $assetStats['assigned'] ?? 0 }}</div>
                                <div class="text-tiny text-muted text-uppercase">Assigned</div>
                            </div>
                        </div>
                         <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $region->courts()->count() }}</div>
                                <div class="text-tiny text-muted text-uppercase">Courts</div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="text-large fw-bold text-dark">{{ $region->locations()->count() }}</div>
                                <div class="text-tiny text-muted text-uppercase">Locations</div>
                            </div>
                        </div>
                     </div>

                    @if(count($assetStats['by_category'] ?? []) > 0)
                    <div class="mt-4">
                         <h6 class="text-tiny fw-bold text-uppercase mb-2 text-muted">Assets by Category</h6>
                         <div class="row g-2">
                            @foreach($assetStats['by_category'] as $category => $count)
                             <div class="col-auto">
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium">
                                    {{ $category }} <span class="ms-2 text-muted">{{ $count }}</span>
                                </span>
                             </div>
                            @endforeach
                         </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Locations List -->
            <div class="stunning-card mb-4">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">Locations</h6>
                    @if($locations->count() > 0)
                        <a href="{{ route('locations') }}?region={{ $region->id }}" class="text-tiny text-decoration-none fw-bold">View All</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                             <tr>
                                <th class="text-tiny text-muted text-uppercase ps-4">Name</th>
                                <th class="text-tiny text-muted text-uppercase">Code</th>
                                <th class="text-tiny text-muted text-uppercase text-center">Courts</th>
                                <th class="text-tiny text-muted text-uppercase text-center">Assets</th>
                                <th class="text-tiny text-muted text-uppercase text-end pe-4">Status</th>
                            </tr>
                        </thead>
                         <tbody>
                            @forelse($locations->take(5) as $location)
                            <tr>
                                <td class="ps-4">
                                    <div class="text-small fw-bold text-dark">{{ $location->name }}</div>
                                </td>
                                <td><div class="text-tiny font-monospace text-muted">{{ $location->code }}</div></td>
                                <td class="text-center"><div class="badge bg-light text-dark border rounded-pill">{{ $location->courts()->count() }}</div></td>
                                <td class="text-center"><div class="badge bg-light text-dark border rounded-pill">{{ $location->assets()->count() }}</div></td>
                                <td class="text-end pe-4">
                                     <span class="badge {{ $location->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border border-0 rounded-pill text-tiny">
                                        {{ $location->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted text-small">No locations found in this region.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Links -->
             <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Deep Dive</h6>
                </div>
                <div class="p-4 pt-1 d-flex flex-column gap-2">
                     <a href="{{ route('courts') }}?region={{ $region->id }}" class="btn btn-light border text-start d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-gavel me-2 text-muted"></i> View Courts</span>
                        <i class="fas fa-chevron-right fa-xs text-muted"></i>
                    </a>
                    <a href="{{ route('users') }}?region={{ $region->id }}" class="btn btn-light border text-start d-flex justify-content-between align-items-center">
                         <span><i class="fas fa-users me-2 text-muted"></i> View Users</span>
                         <i class="fas fa-chevron-right fa-xs text-muted"></i>
                    </a>
                     <a href="{{ route('locations') }}?region={{ $region->id }}" class="btn btn-light border text-start d-flex justify-content-between align-items-center">
                         <span><i class="fas fa-map-marker-alt me-2 text-muted"></i> View Locations</span>
                         <i class="fas fa-chevron-right fa-xs text-muted"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Assets -->
             <div class="stunning-card">
                 <div class="card-header-clean d-flex justify-content-between">
                    <h6 class="card-title-small">Recent Assets</h6>
                    @if($region->assets->count() > 0)
                        <button class="btn btn-sm btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#viewAssetsModal">View All</button>
                    @endif
                </div>
                <div class="p-4 pt-1">
                    @forelse($region->assets->take(5) as $asset)
                     <div class="mb-3 pb-3 border-bottom last-border-0">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                             <div class="text-small fw-bold text-dark">{{ $asset->asset_name }}</div>
                             <span class="badge bg-success-subtle text-success border border-0 text-tiny">{{ ucfirst($asset->status) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-tiny text-muted">{{ $asset->category->name ?? 'N/A' }}</span>
                             <form action="{{ route('regions.remove-asset', $region) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this asset from region?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                <button type="submit" class="btn btn-link p-0 text-danger" title="Remove">
                                    <i class="fas fa-times fa-xs"></i>
                                </button>
                            </form>
                        </div>
                     </div>
                    @empty
                        <p class="text-muted text-small text-center mb-0">No assets assigned.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Include Modals --}}
@include('regions.modals')

@endsection

@section('styles')
<style>
.last-border-0:last-child {
    border-bottom: 0 !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>
@endsection