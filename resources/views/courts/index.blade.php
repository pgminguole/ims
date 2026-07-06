@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Courts Management</h4>
                <p class="text-muted text-small mb-0">Manage judiciary courts and allocations.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('courts.duplicates') }}" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-warning" title="Check for potential duplicate records">
                    <i class="fas fa-clone me-1"></i> Duplicates
                </a>
                <a href="{{ route('courts.import.form') }}" class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm text-dark">
                    <i class="fas fa-file-import me-1"></i> Import
                </a>
                <a href="{{ route('courts.create') }}" class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Add Court
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Courts</div>
                <div class="metric-v2-value">{{ $totalCourts }}</div>
                <div class="text-tiny text-muted mt-2">All Registered</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-gavel"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Active</div>
                <div class="metric-v2-value">{{ $activeCourts }}</div>
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
                <div class="metric-v2-label">High Courts</div>
                <div class="metric-v2-value">{{ $highCourts }}</div>
                <div class="text-tiny text-muted mt-2">Jurisdiction</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-balance-scale"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">District Courts</div>
                <div class="metric-v2-value">{{ $districtCourts }}</div>
                <div class="text-tiny text-muted mt-2">Jurisdiction</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-landmark"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Court Filters</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['search', 'region_id', 'type', 'is_active']))
                        <a href="{{ route('courts.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('courts') }}">
                        <div class="row g-2">
                            <!-- Search -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="Name, Code..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <!-- Region -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Region</label>
                                <select name="region_id" class="form-select form-select-sm text-small">
                                    <option value="">All Regions</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Type -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Type</label>
                                <select name="type" class="form-select form-select-sm text-small">
                                    <option value="">All Types</option>
                                    <option value="high_court" {{ request('type') == 'high_court' ? 'selected' : '' }}>High Court</option>
                                    <option value="district_court" {{ request('type') == 'district_court' ? 'selected' : '' }}>District Court</option>
                                    <option value="magistrate_court" {{ request('type') == 'magistrate_court' ? 'selected' : '' }}>Magistrate Court</option>
                                    <option value="special_court" {{ request('type') == 'special_court' ? 'selected' : '' }}>Special Court</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="is_active" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <!-- Submit -->
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
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Court Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Location</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Type</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Added By</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Assets</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courts as $court)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-small text-dark">{{ $court->name }}</div>
                                <div class="text-tiny text-muted">Code: {{ $court->code }}</div>
                            </td>
                            <td>
                                <div class="text-small">{{ $court->location->name ?? 'N/A' }}</div>
                                <div class="text-tiny text-muted text-truncate" style="max-width: 150px;">{{ $court->address }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-tiny text-capitalize">
                                    {{ str_replace('_', ' ', $court->type) }}
                                </span>
                            </td>
                            <td class="text-small text-muted">
                                <div class="fw-bold">{{ $court->creator->name ?? 'Superadmin' }}</div>
                                <div class="text-tiny">{{ $court->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border-0 fw-bold rounded-pill">{{ $court->totalAssets ?? '0' }}</span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('courts.show', $court) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    <a href="{{ route('courts.edit', $court) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('courts.destroy', $court) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="confirmDelete(event, 'Delete this court?', 'Yes, delete it!')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-gavel fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No Courts Found</h6>
                                    <p class="text-muted text-small mb-0">Try adjusting your filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($courts->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $courts->firstItem() }} - {{ $courts->lastItem() }} of {{ $courts->total() }}
                    </div>
                    <div>{{ $courts->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection