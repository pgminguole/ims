@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('courts') }}" class="text-decoration-none text-muted">Courts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Duplicates</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Potential Duplicates</h4>
            <p class="text-tiny text-muted mb-0">Records with similar names or codes.</p>
        </div>
        <div>
            <a href="{{ route('courts') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if($duplicates->count() > 0)
        <!-- Stats Row -->
        <div class="row g-3 mb-4">
             <div class="col-md-12">
                <div class="stunning-card p-3 d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning rounded-circle p-2 me-3">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                    <div>
                         <h6 class="fw-bold mb-0 text-dark">Found {{ $duplicates->sum('count') }} Potential Duplicates</h6>
                         <p class="mb-0 text-tiny text-muted">These courts share the same name but have different IDs.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="stunning-card">
             <div class="card-header-clean">
                <h6 class="card-title-small">Duplicate Groups</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Court Name</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Count</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">IDs</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($duplicates as $duplicate)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark text-small">{{ $duplicate->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning border-0 rounded-pill px-3">{{ $duplicate->count }} records</span>
                            </td>
                            <td>
                                <div class="text-tiny font-monospace text-muted">{{ $duplicate->ids }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('courts', ['search' => $duplicate->name]) }}" class="btn btn-sm btn-white border rounded-pill shadow-sm text-dark font-small" target="_blank">
                                    <i class="fas fa-search me-1"></i> Review Matches
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="stunning-card text-center py-5">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 64px; height: 64px;">
                    <i class="fas fa-check fa-2x"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark">No Duplicates Found</h5>
            <p class="text-muted text-small mb-4">Your court records appear to be clean.</p>
            <a href="{{ route('courts') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">Return to Courts</a>
        </div>
    @endif
</div>
@endsection
