@extends('layouts.app')

@section('title', 'DTS Systems Audit')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-microphone me-2"></i>DTS Systems Audit
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                <i class="fas fa-filter me-2"></i>Filters
            </button>
          
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-microphone fs-1 text-primary mb-2"></i>
                    <h3 class="fw-bold">{{ $totalDts }}</h3>
                    <p class="text-muted mb-0">Total DTS Systems</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fs-1 text-success mb-2"></i>
                    <h3 class="fw-bold">{{ $availableDts }}</h3>
                    <p class="text-muted mb-0">Available Systems</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-cogs fs-1 text-info mb-2"></i>
                    <h3 class="fw-bold">{{ $completeSystems }}</h3>
                    <p class="text-muted mb-0">Complete Systems</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-gavel fs-1 text-warning mb-2"></i>
                    <h3 class="fw-bold">{{ $courtsWithDts }}</h3>
                    <p class="text-muted mb-0">Courts with DTS</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('auditor.dts.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Court</label>
                        <select name="court_id" class="form-select">
                            <option value="">All Courts</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Availability</label>
                        <select name="is_available" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('is_available') == '1' ? 'selected' : '' }}>Available</option>
                            <option value="0" {{ request('is_available') == '0' ? 'selected' : '' }}>Unavailable</option>
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
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search DTS systems..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('auditor.dts.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DTS Systems Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>DTS Name</th>
                            <th>Court</th>
                            <th>Region</th>
                            <th>Components</th>
                            <th>Total Items</th>
                        
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dtsSystems as $dts)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-microphone text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $dts->name }}</strong>
                                        @if($dts->notes)
                                        <br><small class="text-muted">{{ Str::limit($dts->notes, 30) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('auditor.courts.show', $dts->court) }}" class="text-decoration-none">
                                    {{ $dts->court->name }}
                                </a>
                            </td>
                            <td>{{ $dts->court->region->name ?? 'N/A' }}</td>
                            <td>
                                <div class="small">
                                    <div>Monitors: {{ $dts->monitors_count }}</div>
                                    <div>Splitters: {{ $dts->splitters_count }}</div>
                                    <div>HDMI: {{ $dts->hdmi_short_cables_count }}/{{ $dts->hdmi_long_cables_count }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $dts->total_components }}</span>
                            </td>
                           
                            <td>
                                <span class="badge bg-{{ $dts->is_available ? 'success' : 'secondary' }}">
                                    {{ $dts->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td>{{ $dts->updated_at->format('M j, Y') }}</td>
                            <td>
                                <a href="{{ route('auditor.dts.show', $dts) }}" class="btn btn-sm btn-outline-primary">
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
                    Showing {{ $dtsSystems->firstItem() }} to {{ $dtsSystems->lastItem() }} of {{ $dtsSystems->total() }} results
                </div>
                {{ $dtsSystems->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportDts() {
    // Implement export functionality
    alert('Export functionality to be implemented');
}
</script>
@endpush