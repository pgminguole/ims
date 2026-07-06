@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">DTS Assignments</h4>
                <p class="text-muted text-small mb-0">Manage Digital Transcription Systems assigned to courts.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dts-assignments.bulk-create') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-layer-group me-1"></i> Bulk Assign
                </a>
                <a href="{{ route('dts-assignments.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Assign Single
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total DTS</div>
                <div class="metric-v2-value">{{ $dtsAssignments->total() }}</div>
                <div class="text-tiny text-muted mt-2">Systems Assigned</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-desktop"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Active Systems</div>
                <div class="metric-v2-value">{{ $dtsAssignments->where('is_available', 1)->count() }}</div>
                <div class="text-tiny text-success mt-2">Operational</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Components</div>
                <div class="metric-v2-value">{{ $dtsAssignments->sum('total_components') }}</div>
                <div class="text-tiny text-muted mt-2">Total Hardware</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Monitors</div>
                <div class="metric-v2-value">{{ $dtsAssignments->sum('monitors_count') }}</div>
                <div class="text-tiny text-muted mt-2">Screens Deployed</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-tv"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Filter DTS</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['search', 'court_id', 'region_id', 'is_available']))
                        <a href="{{ route('dts-assignments.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('dts-assignments.index') }}">
                        <div class="row g-2">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="DTS name..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Court</label>
                                <select name="court_id" class="form-select form-select-sm text-small">
                                    <option value="">All Courts</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Region</label>
                                <select name="region_id" class="form-select form-select-sm text-small">
                                    <option value="">All Regions</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="is_available" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Available</option>
                                    <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Not Available</option>
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

    <!-- Table -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">DTS System</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Court & Region</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Core Components</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Items</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Status</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dtsAssignments as $dts)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-desktop fa-sm"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $dts->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-small text-dark">{{ $dts->court->name }}</div>
                                <div class="text-tiny text-muted">{{ $dts->court->region->name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-light text-dark border text-tiny" title="Monitors"><i class="fas fa-tv me-1"></i>{{ $dts->monitors_count }}</span>
                                    <span class="badge bg-light text-dark border text-tiny" title="Splitters"><i class="fas fa-random me-1"></i>{{ $dts->splitters_count }}</span>
                                    <span class="badge bg-light text-dark border text-tiny" title="Cables"><i class="fas fa-plug me-1"></i>{{ $dts->hdmi_short_cables_count + $dts->hdmi_long_cables_count }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border-0 fw-bold rounded-pill text-tiny">{{ $dts->total_components }}</span>
                            </td>
                            <td class="text-center">
                                {!! $dts->status_badge !!}
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('courts.show', $dts->court) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    <a href="{{ route('courts.edit', $dts->court) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('dts-assignments.destroy', $dts) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="return confirm('Delete this assignment?')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-desktop fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No DTS Assignments</h6>
                                    <p class="text-muted text-small mb-0">Assign a DTS system to started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($dtsAssignments->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $dtsAssignments->firstItem() ?? 0 }} - {{ $dtsAssignments->lastItem() ?? 0 }} of {{ $dtsAssignments->total() }}
                    </div>
                    <div>{{ $dtsAssignments->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection