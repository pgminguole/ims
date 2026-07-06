@extends('layouts.app')

@section('title', 'Obsolete Equipment')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
             <h4 class="mb-0 fw-bold">Obsolete Equipment</h4>
             <p class="text-muted text-small mb-0">Manage and track equipment written off or out of use.</p>
        </div>
        <a href="{{ route('obsolete-assets.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
            <i class="fas fa-plus me-1"></i> Record Obsolete Item
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4 bg-white stunning-card">
        <div class="card-body p-3">
            <form action="{{ route('obsolete-assets.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Search</label>
                        <div class="input-group input-group-sm">
                             <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                             <input type="text" name="search" class="form-control border-start-0 ps-0 text-small" placeholder="Search name, serial, brand..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-tiny text-uppercase fw-bold text-muted mb-1">Category</label>
                        <select name="category" class="form-select form-select-sm text-small">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill">Filter</button>
                    </div>
                    @if(request()->hasAny(['search', 'category']))
                    <div class="col-md-1">
                        <a href="{{ route('obsolete-assets.index') }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill">Clear</a>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm stunning-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Asset Details</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Obsolete Date</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Reason</th>
                             <th class="text-uppercase text-tiny fw-bold text-muted">Disposal</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($obsoleteAssets as $asset)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-archive fa-sm"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('obsolete-assets.show', $asset) }}" class="fw-bold text-small text-dark text-decoration-none d-block">
                                            {{ $asset->asset_name }}
                                        </a>
                                        <div class="text-tiny text-muted">
                                            {{ $asset->category }} 
                                            @if($asset->serial_number) • SN: {{ $asset->serial_number }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-small text-dark">{{ $asset->date_obsolete->format('M j, Y') }}</div>
                                <div class="text-tiny text-muted">Reported by {{ $asset->reported_by_name ?? 'System' }}</div>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate text-small text-muted" style="max-width: 200px;" title="{{ $asset->reason }}">
                                    {{ $asset->reason }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-tiny px-2 rounded-pill">
                                    {{ $asset->disposal_method ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v fa-xs"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                        <li><a class="dropdown-item text-small" href="{{ route('obsolete-assets.show', $asset) }}"><i class="fas fa-eye me-2 text-primary"></i>View Details</a></li>
                                        <li><a class="dropdown-item text-small" href="{{ route('obsolete-assets.edit', $asset) }}"><i class="fas fa-edit me-2 text-warning"></i>Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('obsolete-assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-small text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-archive fa-3x mb-3 text-light"></i>
                                <p class="mb-0">No obsolete equipment records found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($obsoleteAssets->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-tiny text-muted">
                        Showing {{ $obsoleteAssets->firstItem() }} - {{ $obsoleteAssets->lastItem() }} of {{ $obsoleteAssets->total() }} results
                    </div>
                    <div>{{ $obsoleteAssets->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
