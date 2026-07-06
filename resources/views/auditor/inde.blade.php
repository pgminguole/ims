@extends('layouts.app')

@section('title', 'Assets - Auditor')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Assets Audit</h4>
                <p class="text-muted text-small mb-0">Audit and verify assets across regions and courts.</p>
            </div>
            <div>
                <button class="btn btn-sm btn-dark rounded-pill px-3" onclick="exportAssets()">
                    <i class="fas fa-download me-1"></i> Export Data
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Filter Assets</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['year', 'region_id', 'status', 'search']))
                        <a href="{{ route('auditor.assets.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form action="{{ route('auditor.assets.index') }}" method="GET">
                        <div class="row g-2">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="Search assets..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Year</label>
                                <select name="year" class="form-select form-select-sm text-small">
                                    <option value="">All Years</option>
                                    @foreach($filterData['years'] as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Region</label>
                                <select name="region_id" class="form-select form-select-sm text-small">
                                    <option value="">All Regions</option>
                                    @foreach($filterData['regions'] as $region)
                                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Asset</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Location</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Assigned To</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Status</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Last Audit</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-cube fa-sm"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('auditor.assets.show', $asset) }}" class="fw-bold text-small text-dark text-decoration-none d-block">
                                            {{ $asset->asset_name }}
                                        </a>
                                        <div class="text-tiny text-muted">{{ $asset->asset_tag }} @if($asset->serial_number) • SN: {{ $asset->serial_number }} @endif</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-small text-muted">{{ $asset->region->name ?? 'N/A' }}</div>
                                <div class="text-tiny text-muted">{{ $asset->category->name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @if($asset->assigned_type === 'user' && $asset->assignedUser)
                                    <div class="text-small"><i class="fas fa-user text-primary me-1"></i>{{ $asset->assignedUser->full_name }}</div>
                                @elseif($asset->assigned_type === 'office' && $asset->office)
                                    <div class="text-small"><i class="fas fa-building text-info me-1"></i>{{ $asset->office->name }}</div>
                                @else
                                    <span class="text-muted text-small">Not Assigned</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $asset->status === 'available' ? 'bg-success-subtle text-success' : ($asset->status === 'assigned' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning') }} border border-0 text-tiny px-2 rounded-pill">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($asset->last_audited_at)
                                    <div class="text-small text-dark">{{ $asset->last_audited_at->format('M j, Y') }}</div>
                                @else
                                    <span class="text-muted text-tiny">Never</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="View">
                                    <i class="fas fa-eye fa-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assets->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $assets->firstItem() }} - {{ $assets->lastItem() }} of {{ $assets->total() }} results
                    </div>
                    <div>{{ $assets->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportAssets() {
    alert('Export functionality to be implemented');
}
</script>
@endpush