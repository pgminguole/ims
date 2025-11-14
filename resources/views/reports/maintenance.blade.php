@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Maintenance Report</h2>
            <p class="text-muted mb-0">Maintenance activities analysis and cost tracking</p>
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
            <form method="GET" action="{{ route('reports.maintenance') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Maintenance Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                            <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                            <option value="routine" {{ request('type') == 'routine' ? 'selected' : '' }}>Routine</option>
                            <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
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
                    <p class="text-muted mb-0">Total Maintenance</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success">${{ number_format($summary['total_cost'] ?? 0, 2) }}</h3>
                    <p class="text-muted mb-0">Total Cost</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info">${{ number_format($summary['average_cost'] ?? 0, 2) }}</h3>
                    <p class="text-muted mb-0">Average Cost</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $upcomingMaintenance->count() }}</h3>
                    <p class="text-muted mb-0">Upcoming Maintenance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Maintenance by Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Maintenance Cost by Month</h6>
                </div>
                <div class="card-body">
                    <canvas id="costChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Logs -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">Maintenance Activities</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="maintenanceTable">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Maintenance Date</th>
                            <th>Type</th>
                            <th>Technician</th>
                            <th>Cost</th>
                            <th>Next Maintenance</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceLogs as $log)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $log->asset->asset_name }}</div>
                                <small class="text-muted">{{ $log->asset->asset_tag }}</small>
                            </td>
                            <td>{{ $log->maintenance_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge maintenance-type-{{ $log->type }}">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td>{{ $log->technician }}</td>
                            <td>
                                @if($log->cost)
                                    <span class="fw-medium">${{ number_format($log->cost, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($log->next_maintenance_date)
                                    {{ $log->next_maintenance_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($log->description, 50) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No maintenance records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Maintenance -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">Upcoming Maintenance (Next 30 Days)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Category</th>
                            <th>Last Maintenance</th>
                            <th>Next Maintenance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingMaintenance as $asset)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $asset->asset_name }}</div>
                                <small class="text-muted">{{ $asset->asset_tag }}</small>
                            </td>
                            <td>{{ $asset->category->name ?? 'N/A' }}</td>
                            <td>
                                @if($asset->last_maintenance)
                                    {{ $asset->last_maintenance->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->next_maintenance)
                                    @if($asset->next_maintenance < now())
                                        <span class="text-danger fw-medium">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $asset->next_maintenance->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-warning fw-medium">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $asset->next_maintenance->format('M d, Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->next_maintenance && $asset->next_maintenance < now())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-warning">Due Soon</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No upcoming maintenance scheduled
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
    // Maintenance Type Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($summary['by_type'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($summary['by_type'] ?? [])) !!},
                backgroundColor: [
                    '#d1ecf1', // Preventive
                    '#f8d7da', // Corrective
                    '#d4edda', // Routine
                    '#fff3cd'  // Emergency
                ],
                borderColor: [
                    '#0c5460',
                    '#721c24',
                    '#155724',
                    '#856404'
                ],
                borderWidth: 1
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

    // Cost by Month Chart
    const costCtx = document.getElementById('costChart').getContext('2d');
    const costChart = new Chart(costCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($summary['by_month'] ?? [])) !!},
            datasets: [{
                label: 'Maintenance Count',
                data: {!! json_encode(array_values($summary['by_month'] ?? [])) !!},
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
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
        const table = document.getElementById('maintenanceTable');
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
        link.setAttribute("download", "maintenance_report.csv");
        document.body.appendChild(link);
        link.click();
    }
</script>
@endpush
@endsection