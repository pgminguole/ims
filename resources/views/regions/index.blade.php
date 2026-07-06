@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Regions Management</h4>
                <p class="text-muted text-small mb-0">Manage geographical regions and their associations.</p>
            </div>
            <div>
                <a href="{{ route('regions.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add Region
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Regions</div>
                <div class="metric-v2-value">{{ $regions->total() }}</div>
                <div class="text-tiny text-muted mt-2">All Registered</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-globe"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Active Regions</div>
                <div class="metric-v2-value">{{ $regions->where('is_active', true)->count() }}</div>
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
                <div class="metric-v2-label">Total Courts</div>
                <div class="metric-v2-value">{{ $regions->sum(fn($region) => $region->courts->count()) }}</div>
                <div class="text-tiny text-muted mt-2">Jurisdiction</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-gavel"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Assets</div>
                <div class="metric-v2-value">{{ $regions->sum(fn($region) => $region->assets->count()) }}</div>
                <div class="text-tiny text-muted mt-2">Allocated</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-boxes"></i>
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
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Region Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Code</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Description</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Courts</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Locations</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Assets</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Status</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regions as $region)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-globe fa-sm"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $region->name }}</div>
                                        <div class="text-tiny text-muted">Added By: {{ $region->creator->name ?? 'Superadmin' }}</div>
                                        <div class="text-tiny text-muted">{{ $region->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-tiny">{{ $region->code }}</span>
                            </td>
                            <td>
                                <div class="text-tiny text-muted text-truncate" style="max-width: 200px;">
                                    {{ Str::limit($region->description, 50) ?: 'No description' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info border-0 fw-bold rounded-pill text-tiny">{{ $region->courts->count() }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border-0 fw-bold rounded-pill text-tiny">{{ $region->locations->count() }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning border-0 fw-bold rounded-pill text-tiny">{{ $region->assets->count() }}</span>
                            </td>
                            <td class="text-center">
                                @if($region->is_active)
                                    <span class="badge bg-success-subtle text-success border-0 fw-bold rounded-pill text-tiny">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border-0 fw-bold rounded-pill text-tiny">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('regions.show', $region) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="View"><i class="fas fa-eye fa-xs"></i></a>
                                    <a href="{{ route('regions.edit', $region) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('regions.destroy', $region) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="return confirm('Delete this region?')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-globe fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No Regions Found</h6>
                                    <p class="text-muted text-small mb-0">Create a region to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($regions->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $regions->firstItem() ?? 0 }} - {{ $regions->lastItem() ?? 0 }} of {{ $regions->total() }}
                    </div>
                    <div>{{ $regions->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection