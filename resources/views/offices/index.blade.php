@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Offices Management</h4>
                <p class="text-muted text-small mb-0">Manage system offices and their assignments.</p>
            </div>
            <div>
                <a href="{{ route('offices.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add Office
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Offices</div>
                <div class="metric-v2-value">{{ $totalOffices }}</div>
                <div class="text-tiny text-muted mt-2">All Registered</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-building"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Active Offices</div>
                <div class="metric-v2-value">{{ $activeOffices }}</div>
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
                <div class="metric-v2-label">With Assets</div>
                <div class="metric-v2-value">{{ $officesWithAssets }}</div>
                <div class="text-tiny text-muted mt-2">Equipment Assigned</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-laptop"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Inactive</div>
                <div class="metric-v2-value">{{ $totalOffices - $activeOffices }}</div>
                <div class="text-tiny text-danger mt-2">Not Operational</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-eye-slash"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="stunning-card mb-0 pb-3">
            <div class="card-header-clean cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <h6 class="card-title-small"><i class="fas fa-filter text-muted me-2"></i>Filter Offices</h6>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->hasAny(['search', 'region', 'status']))
                        <a href="{{ route('offices.index') }}" class="text-tiny text-danger text-decoration-none fw-bold"><i class="fas fa-times me-1"></i>CLEAR</a>
                    @endif
                    <i class="fas fa-chevron-down text-muted text-tiny"></i>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="pt-3">
                    <form method="GET" action="{{ route('offices.index') }}">
                        <div class="row g-2">
                            <div class="col-lg-5 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="Name, code..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Region</label>
                                <select name="region" class="form-select form-select-sm text-small">
                                    <option value="">All Regions</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm text-small">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Office Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Region & Contact</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Manager</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Assets</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Status</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Added By</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offices as $office)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-building fa-sm"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $office->name }}</div>
                                        <div class="text-tiny text-muted">{{ $office->code ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-small fw-medium">{{ $office->region->name ?? 'N/A' }}</div>
                                <div class="text-tiny text-muted">{{ $office->phone ?? 'N/A' }}</div>
                                @if($office->email)
                                    <div class="text-tiny text-muted">{{ $office->email }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-small">{{ $office->manager->full_name ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border-0 fw-bold rounded-pill text-tiny">{{ $office->assets_count ?? 0 }} assets</span>
                            </td>
                            <td class="text-center">
                                @if($office->is_active)
                                    <span class="badge bg-success-subtle text-success border-0 fw-bold rounded-pill text-tiny">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border-0 fw-bold rounded-pill text-tiny">Inactive</span>
                                @endif
                            </td>
                            <td class="text-small text-muted">
                                <div class="fw-bold">{{ $office->creator->name ?? 'Superadmin' }}</div>
                                <div class="text-tiny">{{ $office->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('offices.show', $office) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    <a href="{{ route('offices.edit', $office) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('offices.destroy', $office) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="confirmDelete(event, 'Delete this office?', 'Yes, delete it!')">
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
                                        <i class="fas fa-building fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No Offices Found</h6>
                                    <p class="text-muted text-small mb-0">Try adjusting your filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($offices->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $offices->firstItem() }} - {{ $offices->lastItem() }} of {{ $offices->total() }}
                    </div>
                    <div>{{ $offices->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection