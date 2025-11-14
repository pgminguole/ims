@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
    <div class="asset-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-heading mb-1 text-white">{{ $court->name }}</h1>
                <p class="mb-0 text-white-80">{{ $court->code }} • {{ str_replace('_', ' ', ucfirst($court->type)) }}</p>
            </div>
            <div class="col-auto">
                <div class="btn-group-compact">
                    <a href="{{ route('courts.edit', $court) }}" class="btn btn-light btn-compact">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('courts') }}" class="btn btn-outline-light btn-compact">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Basic Information</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Court Name</div>
                        <div class="detail-value">{{ $court->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Court Code</div>
                        <div class="detail-value">{{ $court->code }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Court Type</div>
                        <div class="detail-value">
                            <span class="court-type-badge {{ $court->type }}">
                                {{ str_replace('_', ' ', ucfirst($court->type)) }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Region</div>
                        <div class="detail-value">{{ $court->region->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Location Information</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">{{ $court->location->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item form-group-full">
                        <div class="detail-label">Address</div>
                        <div class="detail-value">{{ $court->address }}</div>
                    </div>
                </div>
            </div>

            <!-- Personnel Information -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Personnel Information</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Presiding Judge</div>
                        <div class="detail-value">{{ $court->presidingJudge->full_name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Registry Officer</div>
                        <div class="detail-value">{{ $court->registryOfficer->full_name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Court Statistics</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Total Users</div>
                        <div class="detail-value">{{ $court->users->count() }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Assets</div>
                        <div class="detail-value">{{ $court->assets->count() }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Active Users</div>
                        <div class="detail-value">{{ $court->users->where('status', 'active')->count() }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Available Assets</div>
                        <div class="detail-value">{{ $court->assets->where('status', 'available')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('courts.edit', $court) }}" class="btn btn-outline-primary btn-compact">
                        <i class="fas fa-edit"></i> Edit Court
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-compact">
                        <i class="fas fa-users"></i> View Users
                    </a>
                    <a href="#" class="btn btn-outline-info btn-compact">
                        <i class="fas fa-laptop"></i> View Assets
                    </a>
                </div>
            </div>

            <!-- Court Summary -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Court Summary</h5>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        @if($court->is_active)
                            <span class="active-toggle active">Active</span>
                        @else
                            <span class="active-toggle inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created</div>
                    <div class="detail-value">{{ $court->created_at->format('M d, Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Last Updated</div>
                    <div class="detail-value">{{ $court->updated_at->format('M d, Y') }}</div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Recent Users</h5>
                @if($court->users->count() > 0)
                    @foreach($court->users->take(5) as $user)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="text-sm">{{ $user->full_name }}</strong>
                                <span class="status-badge-user {{ $user->status }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-muted mb-1">{{ $user->access_type }}</p>
                            <small class="text-xs">Last login: {{ $user->login_at?->format('M d, Y') ?? 'Never' }}</small>
                        </div>
                    </div>
                    @endforeach
                    @if($court->users->count() > 5)
                    <div class="text-center mt-2">
                        <a href="#" class="text-sm text-primary">View all {{ $court->users->count() }} users</a>
                    </div>
                    @endif
                @else
                    <p class="text-muted text-sm">No users assigned</p>
                @endif
            </div>

            <!-- Recent Assets -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Recent Assets</h5>
                @if($court->assets->count() > 0)
                    @foreach($court->assets->take(5) as $asset)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="text-sm">{{ $asset->asset_name }}</strong>
                                <span class="status-badge status-{{ $asset->status }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-muted mb-1">{{ $asset->asset_tag }}</p>
                            <small class="text-xs">Condition: {{ ucfirst($asset->condition) }}</small>
                        </div>
                    </div>
                    @endforeach
                    @if($court->assets->count() > 5)
                    <div class="text-center mt-2">
                        <a href="#" class="text-sm text-primary">View all {{ $court->assets->count() }} assets</a>
                    </div>
                    @endif
                @else
                    <p class="text-muted text-sm">No assets assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection