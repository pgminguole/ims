@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">Regional ICT Administrators</h4>
            <p class="text-tiny text-muted mb-0">Manage ICT administrators for the 16 regions.</p>
        </div>
        <div>
            <a href="{{ route('regional-admins.create') }}" class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> Add ICT Administrator
            </a>
        </div>
    </div>

    <div class="stunning-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Administrator</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted">Region</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted">Contact</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted">Last Login</th>
                        <th class="text-uppercase text-tiny fw-bold text-muted text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark text-small">{{ $admin->name }}</div>
                                    <div class="text-tiny text-muted">ID: {{ $admin->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($admin->region)
                                <span class="badge bg-info-subtle text-info border border-info-subtle text-tiny">{{ $admin->region->name }}</span>
                            @else
                                <span class="text-muted text-tiny">No Region</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-small">{{ $admin->email }}</div>
                            <div class="text-tiny text-muted">{{ $admin->phone ?? '-' }}</div>
                        </td>
                         <td>
                            <div class="text-small">{{ $admin->login_at?->format('M d, Y H:i') ?? 'Never' }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('regional-admins.show', $admin) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> Manage
                                </a>
                                <form action="{{ route('regional-admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('Remove this administrator?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted mb-2"><i class="fas fa-user-shield fa-2x"></i></div>
                            <p class="text-muted mb-0">No Regional ICT Administrators found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
