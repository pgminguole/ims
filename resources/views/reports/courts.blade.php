@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Courts Report</h2>
            <p class="text-muted mb-0">Comprehensive courts analysis and asset distribution</p>
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
            <form method="GET" action="{{ route('reports.courts') }}">
                <div class="row g-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">Court Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="high court" {{ request('type') == 'high court' ? 'selected' : '' }}>High Court</option>
                            <option value="circuit court" {{ request('type') == 'circuit court' ? 'selected' : '' }}>Circuit Court</option>
                            <option value="district court" {{ request('type') == 'district court' ? 'selected' : '' }}>District Court</option>
                            <option value="supreme court" {{ request('type') == 'supreme court' ? 'selected' : '' }}>Supreme Court</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
                        <a href="{{ route('reports.courts') }}" class="btn btn-outline-secondary mt-4">Reset</a>
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
                    <p class="text-muted mb-0">Total Courts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $summary['courts_with_laptops'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Courts with Laptops</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $summary['courts_with_computers'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Courts with Computers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $summary['total_assets'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Assets</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Courts by Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Courts by Region</h6>
                </div>
                <div class="card-body">
                    <canvas id="courtRegionChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Courts Table -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">Courts Details</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="courtsTable">
                    <thead>
                        <tr>
                            <th>Court Name</th>
                            <th>Type</th>
                            <th>Region</th>
                            <th>Total Assets</th>
                            <th>Total Users</th>
                            <th>Has Laptops</th>
                            <th>Has Computers</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courts as $court)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $court->name }}</div>
                                <small class="text-muted">{{ $court->code }}</small>
                            </td>
                            <td>{{ ucfirst($court->type) }}</td>
                            <td>{{ $court->region->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $court->assets->count() }} assets</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $court->users->count() }} users</span>
                            </td>
                            <td>
                                @if($court->assets->contains(function($asset) {
                                    return stripos($asset->category->name ?? '', 'laptop') !== false;
                                }))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </td>
                            <td>
                                @if($court->assets->contains(function($asset) {
                                    return stripos($asset->category->name ?? '', 'computer') !== false || 
                                           stripos($asset->category->name ?? '', 'desktop') !== false;
                                }))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $court->is_active ? 'success' : 'secondary' }}">
                                    {{ $court->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No courts found matching the criteria
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
    // Type Distribution Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($summary['by_type'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($summary['by_type'] ?? [])) !!},
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

    // Court Region Distribution Chart
    const courtRegionCtx = document.getElementById('courtRegionChart').getContext('2d');
    const courtRegionChart = new Chart(courtRegionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($summary['by_region'] ?? [])) !!},
            datasets: [{
                label: 'Courts by Region',
                data: {!! json_encode(array_values($summary['by_region'] ?? [])) !!},
                backgroundColor: '#007bff'
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
        const table = document.getElementById('courtsTable');
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
        link.setAttribute("download", "courts_report.csv");
        document.body.appendChild(link);
        link.click();
    }
</script>
@endpush
@endsection