@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
    <div class="asset-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-heading mb-1 text-white">{{ $asset->asset_name }}</h1>
                <p class="mb-0 text-white-80">{{ $asset->asset_id }} • {{ $asset->asset_tag }}</p>
            </div>
            <div class="col-auto">
                <div class="btn-group-compact">
                    @if($asset->status === 'available')
                        <button type="button" class="btn btn-success btn-compact" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                            <i class="fas fa-user-plus"></i> Assign Asset
                        </button>
                    @elseif($asset->status === 'assigned')
                        <button type="button" class="btn btn-warning btn-compact" data-bs-toggle="modal" data-bs-target="#reassignAssetModal">
                            <i class="fas fa-user-edit"></i> Reassign Asset
                        </button>
                        <button type="button" class="btn btn-info btn-compact" data-bs-toggle="modal" data-bs-target="#returnAssetModal">
                            <i class="fas fa-undo"></i> Return Asset
                        </button>
                    @endif
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-light btn-compact">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('assets.index') }}" class="btn btn-outline-light btn-compact">
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
                        <div class="detail-label">Serial Number</div>
                        <div class="detail-value">{{ $asset->serial_number }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Model</div>
                        <div class="detail-value">{{ $asset->model }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Brand</div>
                        <div class="detail-value">{{ $asset->brand }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Manufacturer</div>
                        <div class="detail-value">{{ $asset->manufacturer ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $asset->description ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Classification -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Classification</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Category</div>
                        <div class="detail-value">
                            <span class="category-badge">{{ $asset->category->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Subcategory</div>
                        <div class="detail-value">{{ $asset->subcategory->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Region</div>
                        <div class="detail-value">{{ $asset->region->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Court</div>
                        <div class="detail-value">{{ $asset->court->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Status & Assignment -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Status & Assignment</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge status-{{ $asset->status }}">
                                <span class="status-dot"></span>
                                {{ ucfirst($asset->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Condition</div>
                        <div class="detail-value">
                            <span class="condition-badge condition-{{ $asset->condition }}">
                                {{ ucfirst($asset->condition) }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Assigned To</div>
                        <div class="detail-value">
                            @if($asset->assignedUser)
                                <strong>{{ $asset->assignedUser->full_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $asset->assignedUser->email }}</small>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Assignment Type</div>
                        <div class="detail-value">{{ $asset->assigned_type ? ucfirst($asset->assigned_type) : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Assigned Date</div>
                        <div class="detail-value">{{ $asset->assigned_date?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Purchase & Financial -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Purchase & Financial</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Purchase Date</div>
                        <div class="detail-value">{{ $asset->purchase_date?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Received Date</div>
                        <div class="detail-value">{{ $asset->recieved_date?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Purchase Cost</div>
                        <div class="detail-value">{{ $asset->purchase_cost ? '$' . number_format($asset->purchase_cost, 2) : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Current Value</div>
                        <div class="detail-value">{{ $asset->current_value ? '$' . number_format($asset->current_value, 2) : 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Supplier</div>
                        <div class="detail-value">{{ $asset->supplier ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Warranty & Maintenance -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Warranty & Maintenance</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Warranty Period</div>
                        <div class="detail-value">{{ $asset->warranty_period ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Warranty Expiry</div>
                        <div class="detail-value">{{ $asset->warranty_expiry?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Last Maintenance</div>
                        <div class="detail-value">{{ $asset->last_maintenance?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Next Maintenance</div>
                        <div class="detail-value">{{ $asset->next_maintenance?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item form-group-full">
                        <div class="detail-label">Maintenance Notes</div>
                        <div class="detail-value">{{ $asset->maintenance_notes ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Technical Details</h5>
                <div class="asset-detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">IP Address</div>
                        <div class="detail-value">{{ $asset->ip_address ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">MAC Address</div>
                        <div class="detail-value">{{ $asset->mac_address ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item form-group-full">
                        <div class="detail-label">Specifications</div>
                        <div class="detail-value">{{ $asset->specifications ?? 'N/A' }}</div>
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
                    @if($asset->status === 'available')
                        <button type="button" class="btn btn-primary btn-compact" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                            <i class="fas fa-user-plus"></i> Assign Asset
                        </button>
                    @elseif($asset->status === 'assigned')
                        <button type="button" class="btn btn-warning btn-compact" data-bs-toggle="modal" data-bs-target="#reassignAssetModal">
                            <i class="fas fa-user-edit"></i> Reassign Asset
                        </button>
                        <button type="button" class="btn btn-info btn-compact" data-bs-toggle="modal" data-bs-target="#returnAssetModal">
                            <i class="fas fa-undo"></i> Return Asset
                        </button>
                    @endif
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-primary btn-compact">
                        <i class="fas fa-edit"></i> Edit Asset
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-compact">
                        <i class="fas fa-print"></i> Print Details
                    </a>
                    <a href="#" class="btn btn-outline-info btn-compact">
                        <i class="fas fa-history"></i> View History
                    </a>
                </div>
            </div>

            <!-- Asset Summary -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Asset Summary</h5>
                <div class="detail-item">
                    <div class="detail-label">Asset ID</div>
                    <div class="detail-value">{{ $asset->asset_id }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created</div>
                    <div class="detail-value">{{ $asset->created_at->format('M d, Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Last Updated</div>
                    <div class="detail-value">{{ $asset->updated_at->format('M d, Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Registry</div>
                    <div class="detail-value">{{ $asset->registry->name ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="asset-detail-card">
                <h5 class="section-header mb-3">Recent Activity</h5>
                @if($asset->histories->count() > 0)
                    @foreach($asset->histories->take(5) as $history)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="text-sm">{{ $history->action }}</strong>
                                <small class="text-muted">{{ $history->performed_at->format('M d') }}</small>
                            </div>
                            <p class="text-xs text-muted mb-1">{{ $history->description }}</p>
                            <small class="text-xs">By: {{ $history->performedBy->name ?? 'System' }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-sm">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('assets.assign', $asset->slug) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignAssetModalLabel">Assign Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assigned_type" class="form-label">Assignment Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="assigned_type" name="assigned_type" required>
                            <option value="">Select Type</option>
                            <option value="judge">Judge</option>
                            <option value="staff">Staff</option>
                            <option value="department">Department</option>
                            <option value="court">Court</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assignment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="assigned_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Optional notes about this assignment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reassign Asset Modal -->
<div class="modal fade" id="reassignAssetModal" tabindex="-1" aria-labelledby="reassignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('assets.assign', $asset->slug) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="reassignAssetModalLabel">Reassign Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Current Assignment:</strong><br>
                        Assigned to: {{ $asset->assignedUser->full_name ?? 'N/A' }}<br>
                        Type: {{ ucfirst($asset->assigned_type ?? 'N/A') }}<br>
                        Date: {{ $asset->assigned_date?->format('M d, Y') ?? 'N/A' }}
                    </div>
                    
                    <div class="mb-3">
                        <label for="reassign_to" class="form-label">Reassign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="reassign_to" name="assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ $asset->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reassign_type" class="form-label">Assignment Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="reassign_type" name="assigned_type" required>
                            <option value="">Select Type</option>
                            <option value="judge" {{ $asset->assigned_type == 'judge' ? 'selected' : '' }}>Judge</option>
                            <option value="staff" {{ $asset->assigned_type == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="department" {{ $asset->assigned_type == 'department' ? 'selected' : '' }}>Department</option>
                            <option value="court" {{ $asset->assigned_type == 'court' ? 'selected' : '' }}>Court</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reassign_date" class="form-label">Reassignment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="reassign_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reassign_comments" class="form-label">Reason for Reassignment</label>
                        <textarea class="form-control" id="reassign_comments" name="comments" rows="3" placeholder="Please provide a reason for reassignment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reassign Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Asset Modal -->
<div class="modal fade" id="returnAssetModal" tabindex="-1" aria-labelledby="returnAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('assets.return', $asset) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="returnAssetModalLabel">Return Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Current Assignment:</strong><br>
                        Assigned to: {{ $asset->assignedUser->full_name ?? 'N/A' }}<br>
                        Type: {{ ucfirst($asset->assigned_type ?? 'N/A') }}<br>
                        Assigned Date: {{ $asset->assigned_date?->format('M d, Y') ?? 'N/A' }}
                    </div>
                    
                    <div class="mb-3">
                        <label for="returned_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="returned_date" name="returned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="returnee" class="form-label">Returned By <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="returnee" name="returnee" placeholder="Name of person returning the asset" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="returned_to" class="form-label">Returned To</label>
                        <input type="text" class="form-control" id="returned_to" name="returned_to" placeholder="Name of person receiving the asset">
                    </div>
                    
                    <div class="mb-3">
                        <label for="condition" class="form-label">Asset Condition <span class="text-danger">*</span></label>
                        <select class="form-select" id="condition" name="condition" required>
                            <option value="">Select Condition</option>
                            <option value="excellent" {{ $asset->condition == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ $asset->condition == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ $asset->condition == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ $asset->condition == 'poor' ? 'selected' : '' }}>Poor</option>
                            <option value="broken" {{ $asset->condition == 'broken' ? 'selected' : '' }}>Broken</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="returned_reason" class="form-label">Reason for Return <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="returned_reason" name="returned_reason" rows="3" placeholder="Please provide a reason for returning the asset" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Return Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection