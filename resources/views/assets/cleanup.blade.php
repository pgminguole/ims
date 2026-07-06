@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}" class="text-decoration-none text-muted">Assets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cleanup</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Asset Assignment Cleanup</h4>
            <p class="text-tiny text-muted mb-0">Assets with missing Assignment Type information.</p>
        </div>
        <div>
            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Assets
            </a>
        </div>
    </div>

    <!-- Alert -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
        <div class="d-flex">
            <div class="me-3 text-info">
                <i class="fas fa-info-circle fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Instructions</h6>
                <p class="text-small mb-0">
                    The following assets have an ambiguous assignment status. Please review the <strong>Comments</strong> column for assignment details 
                    and use the <strong>Edit</strong> button to set the correct User, Office, Court, or Region.
                </p>
            </div>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="stunning-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-uppercase text-tiny fw-bold text-muted" style="width: 50px;">#</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted">Asset Tag / Name</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted">Comments (Hint)</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted">Current Status</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr>
                            <td class="ps-4 text-small text-muted">{{ $asset->id }}</td>
                            <td>
                                <div class="fw-bold text-dark text-small">{{ $asset->asset_name }}</div>
                                <div class="text-tiny text-muted">{{ $asset->asset_tag }}</div>
                            </td>
                            <td class="text-break" style="max-width: 300px;">
                                @if($asset->comments)
                                    <div class="p-2 bg-warning bg-opacity-10 border border-warning rounded text-small text-dark">
                                        {{ $asset->comments }}
                                    </div>
                                @else
                                    <span class="text-muted text-tiny fst-italic">No comments available</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1 text-tiny">
                                    {{ ucfirst($asset->status) }}
                                </span>
                                <div class="text-tiny text-muted mt-1">Assigned Type: <span class="text-danger">NULL</span></div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('assets.edit', $asset->slug) }}" class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm" target="_blank">
                                    <i class="fas fa-edit me-1"></i> Edit & Fix
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i>
                                    <p class="fw-bold mb-0">No problematic assets found!</p>
                                    <p class="text-tiny mb-0">All assets have valid assignment types.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($assets->hasPages())
            <div class="p-3 border-top">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
