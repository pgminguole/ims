@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tools text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">Maintenance Details</h5>
                        <div class="ms-auto">
                            <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Asset Information</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="asset-icon-wrapper bg-primary text-white">
                                        <i class="fas fa-laptop"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $maintenance->asset->asset_name }}</div>
                                    <div class="text-muted small">{{ $maintenance->asset->asset_tag }}</div>
                                    <div class="text-muted small">{{ $maintenance->asset->serial_number }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Maintenance Details</h6>
                            <div class="mb-2">
                                <strong>Type:</strong>
                                <span class="badge maintenance-type-{{ $maintenance->type }} ms-2">
                                    {{ ucfirst($maintenance->type) }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <strong>Date:</strong>
                                <span class="ms-2">{{ $maintenance->maintenance_date->format('M d, Y') }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Technician:</strong>
                                <span class="ms-2">{{ $maintenance->technician }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Cost:</strong>
                                <span class="ms-2">
                                    @if($maintenance->cost)
                                        ${{ number_format($maintenance->cost, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Scheduling</h6>
                            <div class="mb-2">
                                <strong>Next Maintenance:</strong>
                                <span class="ms-2">
                                    @if($maintenance->next_maintenance_date)
                                        {{ $maintenance->next_maintenance_date->format('M d, Y') }}
                                        @if($maintenance->next_maintenance_date < now())
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @elseif($maintenance->next_maintenance_date <= now()->addDays(30))
                                            <span class="badge bg-warning ms-2">Due Soon</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </span>
                            </div>
                            <div class="mb-2">
                                <strong>Performed By:</strong>
                                <span class="ms-2">{{ $maintenance->performedBy->full_name ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="text-muted mb-2">Description</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $maintenance->description }}
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="text-muted mb-2">Actions Taken</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $maintenance->actions_taken }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Asset Quick Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">Asset Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Category</small>
                        <div>{{ $maintenance->asset->category->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Region</small>
                        <div>{{ $maintenance->asset->region->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Status</small>
                        <div>
                            <span class="status-badge status-{{ $maintenance->asset->status }}">
                                {{ ucfirst($maintenance->asset->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Condition</small>
                        <div>
                            <span class="condition-badge condition-{{ $maintenance->asset->condition }}">
                                {{ ucfirst($maintenance->asset->condition) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('assets.show', $maintenance->asset) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt me-1"></i> View Asset Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection