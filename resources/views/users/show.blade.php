@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
    <div class="asset-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-heading mb-1 text-white">{{ $user->full_name }}</h1>
                <p class="mb-0 text-white-80">{{ $user->username }} • {{ $user->access_type }}</p>
            </div>
            <div class="col-auto">
                <div class="btn-group-compact">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-light btn-compact">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('users') }}" class="btn btn-outline-light btn-compact">
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
                        <div class="detail-label">First Name</div>
                        <div class="detail-value">{{ $user->first_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Last Name</div>
                        <div class="detail-value">{{ $user->last_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Username</div>
                        <div class="detail-value">@{{ $user->username }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $user->email }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Phone</div>
                        <div class="detail-value">{{ $user->phone ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Access & Roles -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Access & Roles</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Access Type</div>
                        <div class="detail-value">
                            <span class="role-badge {{ $user->access_type }}">
                                {{ ucfirst($user->access_type) }}
                            </span>
                        </div>
                    </div>
                   
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge-user {{ $user->status }}">
                                <span class="status-dot"></span>
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location & Assignment -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Location & Assignment</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Court</div>
                        <div class="detail-value">{{ $user->court->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Region</div>
                        <div class="detail-value">{{ $user->court->region->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">{{ $user->location->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Account Information</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Account Created</div>
                        <div class="detail-value">{{ $user->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">{{ $user->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Last Login</div>
                        <div class="detail-value">{{ $user->login_at?->format('M d, Y H:i') ?? 'Never' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Account Expiry</div>
                        <div class="detail-value">{{ $user->expire_date?->format('M d, Y') ?? 'Never' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Password Reset Required</div>
                        <div class="detail-value">{{ $user->require_password_reset ? 'Yes' : 'No' }}</div>
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
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary btn-compact">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <button class="btn btn-outline-secondary btn-compact">
                        <i class="fas fa-envelope"></i> Send Message
                    </button>
                    <button class="btn btn-outline-info btn-compact">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </div>
            </div>

            <!-- User Summary -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">User Summary</h5>
                <div class="detail-item">
                    <div class="detail-label">User ID</div>
                    <div class="detail-value">#{{ $user->id }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Approval Status</div>
                    <div class="detail-value">
                        @if($user->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Online Status</div>
                    <div class="detail-value">
                        @if($user->is_online)
                            <span class="badge bg-success">Online</span>
                        @else
                            <span class="badge bg-secondary">Offline</span>
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Invited By</div>
                    <div class="detail-value">{{ $user->invitedBy->full_name ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Assigned Assets -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Assigned Assets</h5>
                @if($user->assignedAssets->count() > 0)
                    @foreach($user->assignedAssets->take(5) as $asset)
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
                            <small class="text-xs">Assigned: {{ $asset->assigned_date?->format('M d, Y') ?? 'N/A' }}</small>
                        </div>
                    </div>
                    @endforeach
                    @if($user->assignedAssets->count() > 5)
                    <div class="text-center mt-2">
                        <a href="#" class="text-sm text-primary">View all {{ $user->assignedAssets->count() }} assets</a>
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