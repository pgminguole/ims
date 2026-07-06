@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                 <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('users') }}" class="text-decoration-none text-muted">Users</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.duplicates') }}" class="text-decoration-none text-muted">Duplicates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Merge</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Merge Users Preview</h4>
            <p class="text-tiny text-muted mb-0">Review and confirm the merge operation.</p>
        </div>
        <div>
            <a href="{{ route('users.duplicates') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Warning -->
    <div class="alert alert-warning border-warning border-opacity-25 rounded-4 d-flex align-items-center mb-4">
        <i class="fas fa-exclamation-triangle text-warning me-3 fa-lg"></i>
        <div>
            <h6 class="fw-bold mb-0 text-dark">Important: Review Before Merging</h6>
             <p class="mb-0 text-small text-muted">Select the primary user. All data will be transferred to them, and others will be deleted. This cannot be undone.</p>
        </div>
    </div>

    <form action="{{ route('users.merge') }}" method="POST" id="mergeForm">
        @csrf
        
        <div class="row g-4">
            <!-- Left: User Selection -->
            <div class="col-lg-8">
                <div class="stunning-card mb-4">
                    <div class="card-header-clean">
                         <h6 class="card-title-small">Select Primary User</h6>
                    </div>
                    <div class="p-4">
                        @foreach($users as $user)
                        <div class="user-selection-card p-3 rounded-3 border mb-3 {{ $user->id === $suggestedPrimary->id ? 'bg-success-subtle border-success' : 'bg-white border-light' }}" 
                             style="cursor: pointer; transition: all 0.2s;"
                             onclick="document.getElementById('primary_{{ $user->id }}').click()">
                            
                            <div class="d-flex">
                                <div class="me-3 pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="primary_user_id" 
                                               id="primary_{{ $user->id }}" value="{{ $user->id }}"
                                               {{ $user->id === $suggestedPrimary->id ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center mb-2">
                                             <div class="user-avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-small">{{ $user->name }}</div>
                                                <div class="text-tiny text-muted">ID: {{ $user->id }}</div>
                                            </div>
                                            @if($user->id === $suggestedPrimary->id)
                                                <span class="badge bg-success text-white text-tiny ms-2">Recommended</span>
                                            @endif
                                        </div>
                                        <span class="badge {{ $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-tiny">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>

                                    <div class="row g-2 text-small text-muted">
                                        <div class="col-md-4">
                                            <i class="fas fa-envelope me-1 text-tiny"></i> {{ $user->email ?? '-' }}
                                        </div>
                                        <div class="col-md-4">
                                            <i class="fas fa-briefcase me-1 text-tiny"></i> {{ $user->role->name ?? '-' }}
                                        </div>
                                         <div class="col-md-4">
                                            <i class="fas fa-laptop me-1 text-tiny"></i> {{ $user->assignedAssets->count() }} Assets
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="user_ids[]" value="{{ $user->id }}">
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="col-lg-4">
                <div class="stunning-card sticky-top" style="top: 20px;">
                    <div class="card-header-clean">
                         <h6 class="card-title-small">Merge Summary</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                    <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $users->sum(fn($u) => $u->assignedAssets->count()) }}</h5>
                                        <div class="text-tiny text-uppercase fw-bold text-muted">Total Assets to Consolidate</div>
                                    </div>
                                </div>
                            </div>
                             <div class="col-12">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                    <div class="bg-info-subtle text-info rounded-circle p-2 me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $users->count() }}</h5>
                                        <div class="text-tiny text-uppercase fw-bold text-muted">Users Involved</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger rounded-pill shadow-sm" id="confirmMergeBtn">
                                <i class="fas fa-code-branch me-2"></i> Confirm Merge
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Style update on selection
    const cards = document.querySelectorAll('.user-selection-card');
    const radios = document.querySelectorAll('input[name="primary_user_id"]');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            cards.forEach(c => {
                 c.classList.remove('bg-success-subtle', 'border-success');
                 c.classList.add('bg-white', 'border-light');
            });
            const selectedCard = this.closest('.user-selection-card');
            selectedCard.classList.remove('bg-white', 'border-light');
            selectedCard.classList.add('bg-success-subtle', 'border-success');
        });
    });

    // Confirm submit
    document.getElementById('mergeForm').addEventListener('submit', function(e) {
        if (!confirm('Are you definitely sure? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endsection