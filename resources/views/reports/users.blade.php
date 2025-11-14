@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Users Report</h2>
            <p class="text-muted mb-0">Comprehensive users analysis and asset assignment statistics</p>
        </div>
        <div class="col-md-6 text-end">
            <button onclick="exportToExcel()" class="btn btn-success px-4">
                <i class="fas fa-file-excel me-2"></i>Export to Excel
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.users') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Region</label>
                        <select name="region_id" class="form-select">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Court</label>
                        <select name="court_id" class="form-select">
                            <option value="">All Courts</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Has Laptop</label>
                        <select name="has_laptop" class="form-select">
                            <option value="">All Users</option>
                            <option value="yes" {{ request('has_laptop') == 'yes' ? 'selected' : '' }}>With Laptop</option>
                            <option value="no" {{ request('has_laptop') == 'no' ? 'selected' : '' }}>Without Laptop</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <a href="{{ route('reports.users') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $summary['total'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $summary['with_laptops'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Users with Laptops</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $summary['without_laptops'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Users without Laptops</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $summary['by_role']['Judge'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Judges</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Users by Role</h6>
                </div>
                <div class="card-body">
                    <canvas id="roleChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Users by Region</h6>
                </div>
                <div class="card-body">
                    <canvas id="regionChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Users Table -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">Users Details</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Role</th>
                            <th>Court</th>
                            <th>Region</th>
                            <th>Assigned Assets</th>
                            <th>Has Laptop</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $user->full_name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </td>
                            <td>{{ $user->role->name ?? 'N/A' }}</td>
                            <td>{{ $user->court->name ?? 'N/A' }}</td>
                            <td>{{ $user->region->name ?? 'N/A' }}</td>
                            <td>
                                @if($user->assignedAssets->count() > 0)
                                    <span class="badge bg-primary">{{ $user->assignedAssets->count() }} assets</span>
                                @else
                                    <span class="text-muted">No assets</span>
                                @endif
                            </td>
                            <td>
                                @if($user->assignedAssets->contains(function($asset) {
                                    return stripos($asset->category->name ?? '', 'laptop') !== false;
                                }))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No users found matching the criteria
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Role Distribution Chart
    const roleCtx = document.getElementById('roleChart').getContext('2d');
    const roleChart = new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($summary['by_role'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($summary['by_role'] ?? [])) !!},
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                    '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Region Distribution Chart
    const regionCtx = document.getElementById('regionChart').getContext('2d');
    const regionChart = new Chart(regionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($summary['by_region'] ?? [])) !!},
            datasets: [{
                label: 'Users by Region',
                data: {!! json_encode(array_values($summary['by_region'] ?? [])) !!},
                backgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function exportToExcel() {
        const table = document.getElementById('usersTable');
        let csv = [];
        
        // Headers
        let headers = [];
        for (let i = 0; i < table.rows[0].cells.length; i++) {
            headers.push(table.rows[0].cells[i].innerText);
        }
        csv.push(headers.join(','));
        
        // Data rows
        for (let i = 1; i < table.rows.length; i++) {
            let row = [];
            for (let j = 0; j < table.rows[i].cells.length; j++) {
                row.push(table.rows[i].cells[j].innerText);
            }
            csv.push(row.join(','));
        }
        
        // Download CSV
        const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "users_report.csv");
        document.body.appendChild(link);
        link.click();
    }
</script>
@endpush
@endsection