@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Assets Report</h2>
            <p class="text-muted mb-0">Comprehensive assets analysis and statistics</p>
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
            <form method="GET" action="{{ route('reports.assets') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
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
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Condition</label>
                        <select name="condition" class="form-select">
                            <option value="">All Conditions</option>
                            <option value="excellent" {{ request('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Purchase Year</label>
                        <select name="purchase_year" class="form-select">
                            <option value="">All Years</option>
                            @for($year = date('Y'); $year >= 2000; $year--)
                                <option value="{{ $year }}" {{ request('purchase_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <a href="{{ route('reports.assets') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <p class="text-muted mb-0">Total Assets</p>
                </div>
            </div>
        </div>
       
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $summary['by_status']['assigned'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Assigned Assets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $summary['by_status']['maintenance'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Under Maintenance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Assets by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Assets by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Assets Table -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">Assets Details</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="assetsTable">
                    <thead>
                        <tr>
                            <th>Asset Name</th>
                            <th>Category</th>
                            <th>Region</th>
                            <th>Status</th>
                            <th>Condition</th>
                            <th>Purchase Date</th>
                      
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $asset->asset_name }}</div>
                                <small class="text-muted">{{ $asset->asset_tag }}</small>
                            </td>
                            <td>{{ $asset->category->name ?? 'N/A' }}</td>
                            <td>{{ $asset->region->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $asset->status }}">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="condition-badge condition-{{ $asset->condition }}">
                                    {{ ucfirst($asset->condition) }}
                                </span>
                            </td>
                            <td>{{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'N/A' }}</td>
                           
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No assets found matching the criteria
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
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($summary['by_status'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($summary['by_status'] ?? [])) !!},
                backgroundColor: [
                    '#28a745', // Available - Green
                    '#007bff', // Assigned - Blue
                    '#ffc107', // Maintenance - Yellow
                    '#6c757d', // Retired - Gray
                    '#dc3545'  // Lost/Disposed - Red
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

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($summary['by_category'] ?? [])) !!},
            datasets: [{
                label: 'Assets by Category',
                data: {!! json_encode(array_values($summary['by_category'] ?? [])) !!},
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
        // Simple table export implementation
        const table = document.getElementById('assetsTable');
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
        link.setAttribute("download", "assets_report.csv");
        document.body.appendChild(link);
        link.click();
    }
</script>
@endpush
@endsection