@extends('layouts.app')

@section('title', 'Obsolete Asset Details')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center py-4">
                <div>
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('obsolete-assets.index') }}" class="text-decoration-none">Obsolete Equipment</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($obsoleteAsset->asset_name, 20) }}</li>
                        </ol>
                    </nav>
                     <h4 class="mb-0 fw-bold">Asset Details</h4>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('obsolete-assets.edit', $obsoleteAsset) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm stunning-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                            <i class="fas fa-archive fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $obsoleteAsset->asset_name }}</h5>
                            <div class="text-muted small">
                                Category: {{ $obsoleteAsset->category ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-tiny fw-bold text-muted border-bottom pb-2 mb-3">Asset Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted text-small w-40">Serial Number:</td>
                                    <td class="text-dark fw-medium text-small">{{ $obsoleteAsset->serial_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-small">Brand/Make:</td>
                                    <td class="text-dark fw-medium text-small">{{ $obsoleteAsset->brand ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-small">Model:</td>
                                    <td class="text-dark fw-medium text-small">{{ $obsoleteAsset->model ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-small">Date Acquired:</td>
                                    <td class="text-dark fw-medium text-small">{{ $obsoleteAsset->date_acquired ? $obsoleteAsset->date_acquired->format('M j, Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-tiny fw-bold text-muted border-bottom pb-2 mb-3">Obsolescence Record</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted text-small w-40">Date Obsolete:</td>
                                    <td class="text-dark fw-bold text-small">{{ $obsoleteAsset->date_obsolete->format('M j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-small">Reported By:</td>
                                    <td class="text-dark fw-medium text-small">{{ $obsoleteAsset->reported_by_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-small">Disposal Method:</td>
                                    <td class="text-dark fw-medium text-small">
                                        <span class="badge bg-light text-dark border text-tiny px-2 rounded-pill">
                                            {{ $obsoleteAsset->disposal_method ?? 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12">
                             <h6 class="text-uppercase text-tiny fw-bold text-muted border-bottom pb-2 mb-3">Reason / Notes</h6>
                             <div class="bg-light p-3 rounded text-small text-dark">
                                 {{ $obsoleteAsset->reason }}
                             </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 p-4 pt-0">
                    <div class="d-flex justify-content-between align-items-center">
                         <div class="text-tiny text-muted">
                            Record created {{ $obsoleteAsset->created_at->diffForHumans() }}
                        </div>
                        <form action="{{ route('obsolete-assets.destroy', $obsoleteAsset) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                <i class="fas fa-trash me-1"></i> Delete Record
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
