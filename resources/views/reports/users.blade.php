@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">Users Report</h4>
            <p class="text-tiny text-muted mb-0">Analysis of user assignments and status.</p>
        </div>
        <div>
            <button onclick="exportToExcel()" class="btn btn-sm btn-success rounded-pill px-3 text-white shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export to Excel
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    {{-- <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stunning-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-tiny fw-bold text-uppercase text-muted">Total Users</div>
                        <h4 class="mb-0 fw-bold">{{ $summary['total'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stunning-card p-3">
                 <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success rounded-circle p-2 me-3">
                        <i class="fas fa-laptop fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-tiny fw-bold text-uppercase text-muted">With Laptops</div>
                        <h4 class="mb-0 fw-bold">{{ $summary['with_laptops'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stunning-card p-3">
                 <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning rounded-circle p-2 me-3">
                        <i class="fas fa-user-slash fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-tiny fw-bold text-uppercase text-muted">No Laptops</div>
                        <h4 class="mb-0 fw-bold">{{ $summary['without_laptops'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
             <div class="stunning-card p-3">
                 <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info rounded-circle p-2 me-3">
                        <i class="fas fa-gavel fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-tiny fw-bold text-uppercase text-muted">Judges</div>
                        <h4 class="mb-0 fw-bold">{{ $summary['by_role']['Judge'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Filters -->
    <div class="stunning-card mb-4">
        <div class="p-3">
             <form method="GET" action="{{ route('reports.users') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label text-tiny fw-bold text-uppercase text-muted">Role</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region</label>
                        <select name="region_id" class="form-select form-select-sm">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                         <label class="form-label text-tiny fw-bold text-uppercase text-muted">Court</label>
                        <select name="court_id" class="form-select form-select-sm">
                            <option value="">All Courts</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                     <div class="col-md-2">
                        <label class="form-label text-tiny fw-bold text-uppercase text-muted">Has Laptop</label>
                        <select name="has_laptop" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <option value="yes" {{ request('has_laptop') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ request('has_laptop') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts -->
    {{-- <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stunning-card">
                 <div class="card-header-clean">
                    <h6 class="card-title-small">Role Distribution</h6>
                </div>
                <div class="p-3">
                    <canvas id="roleChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stunning-card">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Region Distribution</h6>
                </div>
                <div class="p-3">
                     <canvas id="regionChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Table -->
    <div class="stunning-card">
         <div class="card-header-clean">
            <h6 class="card-title-small">Detailed User List</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="bg-light">
                    <tr>
                         <th class="text-tiny fw-bold text-uppercase text-muted ps-4">Name</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Role</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Court</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Region</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Assets</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Laptop</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted pe-4">Status</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark text-small">{{ $user->name }}</div>
                            <div class="text-tiny text-muted">{{ $user->email }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border text-tiny">{{ $user->role->name ?? 'N/A' }}</span></td>
                        <td class="text-small">{{ $user->court->name ?? 'N/A' }}</td>
                        <td class="text-small">{{ $user->region->name ?? 'N/A' }}</td>
                        <td>
                            @if($user->assignedAssets->count() > 0)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-tiny">{{ $user->assignedAssets->count() }}</span>
                            @else
                                <span class="text-muted text-tiny">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->assignedAssets->contains(function($asset) {
                                return stripos($asset->category->name ?? '', 'laptop') !== false;
                            }))
                                <i class="fas fa-check text-success"></i>
                            @else
                                <span class="text-muted text-tiny">-</span>
                            @endif
                        </td>
                         <td class="pe-4">
                            <span class="badge {{ $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-tiny">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted text-small">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configs for cleaner charts
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#6c757d';
    
    // Role Chart
    const roleCtx = document.getElementById('roleChart').getContext('2d');
    new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($summary['by_role'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($summary['by_role'] ?? [])) !!},
                backgroundColor: [ #3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6b7280'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 6 } }
            },
            cutout: '70%'
        }
    });

    // Region Chart
    const regionCtx = document.getElementById('regionChart').getContext('2d');
    new Chart(regionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($summary['by_region'] ?? [])) !!},
            datasets: [{
                label: 'Users',
                data: {!! json_encode(array_values($summary['by_region'] ?? [])) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                x: { grid: { display: false } }
            }
        }
    });

    function exportToExcel() {
        const table = document.getElementById('usersTable');
        let csv = [];
        for (let i = 0; i < table.rows.length; i++) {
            let row = [], cols = table.rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) row.push('"' + cols[j].innerText.trim() + '"');
            csv.push(row.join(","));
        }
        const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        const downloadLink = document.createElement("a");
        downloadLink.download = "users_report.csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }
</script>
@endpush
@endsection