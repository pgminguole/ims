@extends('layouts.app')

@section('title', 'Courts Audit')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-gavel me-2"></i>Courts Audit
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                <i class="fas fa-filter me-2"></i>Filters
            </button>
            <button class="btn btn-primary" onclick="exportCourts()">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-gavel fs-1 text-primary mb-2"></i>
                    <h3 class="fw-bold">{{ $totalCourts }}</h3>
                    <p class="text-muted mb-0">Total Courts</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-laptop fs-1 text-success mb-2"></i>
                    <h3 class="fw-bold">{{ $totalAssets }}</h3>
                    <p class="text-muted mb-0">Total Assets</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fs-1 text-info mb-2"></i>
                    <h3 class="fw-bold">{{ $totalUsers }}</h3>
                    <p class="text-muted mb-0">Court Staff</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-microphone fs-1 text-warning mb-2"></i>
                    <h3 class="fw-bold">{{ $totalDts }}</h3>
                    <p class="text-muted mb-0">DTS Systems</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fs-1 text-danger mb-2"></i>
                    <h3 class="fw-bold">{{ $activeRegions }}</h3>
                    <p class="text-muted mb-0">Active Regions</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-building fs-1 text-secondary mb-2"></i>
                    <h3 class="fw-bold">{{ $courtTypes->count() }}</h3>
                    <p class="text-muted mb-0">Court Types</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('auditor.courts.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Court Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($courtTypes as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Region</label>
                        <select name="region_id" class="form-select">
                            <option value="">All Regions</option>
                            @foreach($filterData['regions'] as $region)
                                <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search courts..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('auditor.courts.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courts Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Court Name</th>
                            <th>Type</th>
                            <th>Region</th>
                            <th>Location</th>
                            <th>Assets</th>
                            <th>Staff</th>
                            <th>DTS</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courts as $court)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-gavel text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $court->name }}</strong>
                                        <br><small class="text-muted">Code: {{ $court->code }}</small>
                                        @if($court->address)
                                        <br><small class="text-muted">{{ Str::limit($court->address, 30) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $court->type)) }}</span>
                            </td>
                            <td>{{ $court->region->name ?? 'N/A' }}</td>
                            <td>{{ $court->location->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $court->assets_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $court->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $court->dts_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $court->is_active ? 'success' : 'danger' }}">
                                    {{ $court->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('auditor.courts.show', $court) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $courts->firstItem() }} to {{ $courts->lastItem() }} of {{ $courts->total() }} results
                </div>
                {{ $courts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportCourts() {
    // Implement export functionality
    alert('Export functionality to be implemented');
}
</script>
@endpush