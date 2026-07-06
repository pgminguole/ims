@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                 <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('regional-admins.index') }}" class="text-decoration-none text-muted">Regional ICT Admins</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $admin->name }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ $admin->name }}</h4>
            <p class="text-tiny text-muted mb-0">ICT Administrator Details & Audit Log</p>
        </div>
        <div>
            <a href="{{ route('regional-admins.edit', $admin) }}" class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm">
                <i class="fas fa-edit me-1"></i> Edit Profile & Permissions
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="stunning-card mb-4">
                <div class="p-4 text-center border-bottom bg-light bg-opacity-10">
                    <div class="user-avatar-xl rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($admin->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $admin->name }}</h5>
                    <div class="badge bg-primary-subtle text-primary rounded-pill mb-2">Regional ICT Administrator</div>
                    <div class="text-small text-muted mb-0">{{ $admin->email }}</div>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                         <div class="col-12">
                            <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Assigned Region</div>
                             <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <span class="fw-semibold">{{ $admin->region->name ?? 'Unassigned' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Phone</div>
                             <div>{{ $admin->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Status</div>
                             <span class="badge bg-success-subtle text-success">Active</span>
                        </div>
                        <div class="col-12">
                             <div class="text-tiny fw-bold text-uppercase text-muted mb-2">Capabilities</div>
                             <div class="d-flex flex-wrap gap-1">
                                 @forelse($admin->permissions as $perm)
                                    <span class="badge bg-light text-dark border text-tiny">{{ $perm->name }}</span>
                                 @empty
                                    <span class="text-muted text-tiny">No special permissions</span>
                                 @endforelse
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Log -->
        <div class="col-lg-8">
            <div class="stunning-card">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h6 class="card-title-small">
                        <i class="fas fa-history text-muted me-2"></i>Activity Audit Log
                    </h6>
                    <span class="badge bg-light text-dark text-tiny border">{{ $activities->total() }} Events</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Activity</th>
                                <th class="text-uppercase text-tiny fw-bold text-muted">Asset</th>
                                <th class="text-uppercase text-tiny fw-bold text-muted">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                         @php
                                            $icon = match($activity->action) {
                                                'created' => 'fa-plus-circle text-success',
                                                'updated' => 'fa-edit text-primary',
                                                'deleted' => 'fa-trash text-danger',
                                                'assigned' => 'fa-user-check text-info',
                                                default => 'fa-circle text-secondary'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }} me-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark text-tiny text-uppercase">{{ $activity->action }}</div>
                                            <div class="text-muted text-tiny">{{ Str::limit($activity->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($activity->asset)
                                        <a href="{{ route('assets.show', $activity->asset) }}" class="text-decoration-none fw-semibold text-dark text-small">
                                            {{ $activity->asset->asset_name }}
                                        </a>
                                        <div class="text-tiny text-muted">{{ $activity->asset->asset_id }}</div>
                                    @else
                                        <span class="text-muted text-tiny">Asset Removed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-small">{{ $activity->performed_at?->format('M d, H:i') }}</div>
                                    <div class="text-tiny text-muted">{{ $activity->performed_at?->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <p class="text-muted mb-0">No activity recorded yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 @if($activities->hasPages())
                <div class="p-3 border-top">
                    {{ $activities->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
