@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('users') }}" class="text-decoration-none text-muted">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Duplicates</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Duplicate Users Detection</h4>
            <p class="text-tiny text-muted mb-0">Find and merge users with similar names.</p>
        </div>
        <div>
            <a href="{{ route('users') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stunning-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning rounded-circle p-2 me-3">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-tiny fw-bold text-uppercase text-muted">Duplicate Groups</div>
                        <h4 class="mb-0 fw-bold">{{ $duplicateGroups->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stunning-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-danger-subtle text-danger rounded-circle p-2 me-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-tiny fw-bold text-uppercase text-muted">Total Duplicate Users</div>
                        <h4 class="mb-0 fw-bold">{{ $duplicateGroups->sum('count') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="stunning-card mb-4">
        <div class="p-4 pt-1">
             <form method="GET" action="{{ route('users.duplicates') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                         <label class="form-label text-tiny fw-bold text-uppercase text-muted">Similarity Threshold (%)</label>
                        <input type="number" class="form-control form-control-sm" name="threshold" 
                               value="{{ $threshold }}" min="50" max="100" step="5">
                        <div class="form-text text-tiny">Higher values = stricter matching (85% recommended)</div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-dark w-100 rounded-pill">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Duplicate Groups -->
    @if($duplicateGroups->isEmpty())
        <div class="stunning-card text-center py-5">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 64px; height: 64px;">
                     <i class="fas fa-check fa-2x"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark">No Duplicates Found</h5>
            <p class="text-muted text-small mb-4">All user names are unique at the current similarity threshold.</p>
        </div>
    @else
        @foreach($duplicateGroups as $group)
        <div class="stunning-card mb-4">
            <div class="card-header-clean d-flex justify-content-between align-items-center">
                 <div>
                    <h6 class="card-title-small text-warning">
                        <i class="fas fa-user-friends me-2"></i>Similar to: "{{ $group['name'] }}"
                    </h6>
                </div>
                <span class="badge bg-warning-subtle text-warning text-tiny rounded-pill">{{ $group['count'] }} Matches</span>
            </div>
            <div class="p-0">
                <form action="{{ route('users.merge-preview') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="ps-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input select-all-group" data-group="{{ $group['key'] }}">
                                        </div>
                                    </th>
                                    <th class="text-tiny fw-bold text-uppercase text-muted">User Details</th>
                                    <th class="text-tiny fw-bold text-uppercase text-muted">Contact</th>
                                    <th class="text-tiny fw-bold text-uppercase text-muted">Role/Location</th>
                                    <th class="text-tiny fw-bold text-uppercase text-muted">Status</th>
                                    <th class="text-tiny fw-bold text-uppercase text-muted pe-4">Assets</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['users'] as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input user-checkbox" 
                                                   name="user_ids[]" value="{{ $user->id }}" 
                                                   data-group="{{ $group['key'] }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-small">{{ $user->name }}</div>
                                                <div class="text-tiny text-muted">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-small">{{ $user->email ?? 'N/A' }}</div>
                                        <div class="text-tiny text-muted">{{ $user->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-tiny mb-1">{{ $user->role->name ?? 'N/A' }}</span>
                                        <div class="text-tiny text-muted">{{ $user->court->name ?? $user->location->name ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                         <span class="badge {{ $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-tiny">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                        <div class="text-tiny text-muted mt-1">
                                            Last: {{ $user->login_at?->format('M d, Y') ?? 'Never' }}
                                        </div>
                                    </td>
                                    <td class="pe-4">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle text-tiny">
                                            {{ $user->assignedAssets->count() }} assets
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted text-tiny">
                                <i class="fas fa-info-circle me-1"></i> Select users to merge.
                            </small>
                            <button type="submit" class="btn btn-sm btn-warning rounded-pill shadow-sm btn-merge-group" 
                                    data-group="{{ $group['key'] }}" disabled>
                                <i class="fas fa-code-branch me-1"></i> Preview Merge
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All
    document.querySelectorAll('.select-all-group').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            document.querySelectorAll(`.user-checkbox[data-group="${group}"]`).forEach(cb => cb.checked = this.checked);
            updateMergeButton(group);
        });
    });

    // Individual checkboxes
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateMergeButton(this.dataset.group);
        });
    });

    function updateMergeButton(group) {
        const checked = document.querySelectorAll(`.user-checkbox[data-group="${group}"]:checked`);
        const btn = document.querySelector(`.btn-merge-group[data-group="${group}"]`);
        
        if (checked.length >= 2) {
            btn.disabled = false;
            btn.classList.replace('btn-warning', 'btn-success');
            btn.classList.replace('btn-sm', 'btn-sm'); // ensure size stays
        } else {
            btn.disabled = true;
            btn.classList.replace('btn-success', 'btn-warning');
        }
    }
});
</script>
@endpush
@endsection