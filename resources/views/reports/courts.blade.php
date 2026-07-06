@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">Courts Report</h4>
            <p class="text-tiny text-muted mb-0">Analysis of courts, DTS status, and asset assignments.</p>
        </div>
        <div>
             <button onclick="exportToExcel()" class="btn btn-sm btn-success rounded-pill px-3 text-white shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export to Excel
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="stunning-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Total Courts</div>
                <h4 class="mb-0 fw-bold text-primary">{{ $summary['total'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stunning-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                <div class="text-tiny fw-bold text-uppercase text-muted mb-1">With DTS</div>
                <h4 class="mb-0 fw-bold text-success">{{ $summary['courts_with_dts'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stunning-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Without DTS</div>
                <h4 class="mb-0 fw-bold text-danger">{{ $summary['courts_without_dts'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stunning-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                 <div class="text-tiny fw-bold text-uppercase text-muted mb-1">With PC</div>
                <h4 class="mb-0 fw-bold text-info">{{ $summary['courts_with_computers'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-2">
             <div class="stunning-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                 <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Total Assets</div>
                <h4 class="mb-0 fw-bold text-warning">{{ $summary['total_assets'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-lg-2">
             <div class="stunning-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                 <div class="text-tiny fw-bold text-uppercase text-muted mb-1">Total Users</div>
                <h4 class="mb-0 fw-bold text-secondary">{{ $summary['total_users'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
     <div class="stunning-card mb-4">
        <div class="p-3">
             <form method="GET" action="{{ route('reports.courts') }}">
                <div class="row g-2 align-items-end">
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
                        <label class="form-label text-tiny fw-bold text-uppercase text-muted">Court Type</label>
                        <select name="type" class="form-select form-select-sm">
                             <option value="">All Types</option>
                            <option value="high court" {{ request('type') == 'high court' ? 'selected' : '' }}>High Court</option>
                            <option value="circuit court" {{ request('type') == 'circuit court' ? 'selected' : '' }}>Circuit Court</option>
                            <option value="district court" {{ request('type') == 'district court' ? 'selected' : '' }}>District Court</option>
                            <option value="supreme court" {{ request('type') == 'supreme court' ? 'selected' : '' }}>Supreme Court</option>
                        </select>
                    </div>
                     <div class="col-md-3">
                        <label class="form-label text-tiny fw-bold text-uppercase text-muted">DTS Status</label>
                        <select name="dts_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="with_dts" {{ request('dts_status') == 'with_dts' ? 'selected' : '' }}>With DTS</option>
                            <option value="without_dts" {{ request('dts_status') == 'without_dts' ? 'selected' : '' }}>Without DTS</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stunning-card h-100">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Courts by Type</h6>
                </div>
                <div class="p-3">
                    <canvas id="typeChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
         <div class="col-md-4">
            <div class="stunning-card h-100">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Courts by Region</h6>
                </div>
                <div class="p-3">
                    <canvas id="courtRegionChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
         <div class="col-md-4">
            <div class="stunning-card h-100">
                <div class="card-header-clean">
                    <h6 class="card-title-small">DTS Distribution</h6>
                </div>
                <div class="p-3">
                    <canvas id="dtsChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- DTS Summary Lists -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stunning-card h-100">
                <div class="card-header-clean">
                    <h6 class="card-title-small text-success">Courts With DTS</h6>
                </div>
                <div class="p-0" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        @forelse($courts->where('has_dts', true) as $court)
                        <li class="list-group-item d-flex justify-content-between align-items-center text-small px-4">
                            {{ $court->name }}
                            <span class="badge bg-success-subtle text-success rounded-pill">{{ $court->dts_count ?? 0 }} units</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center py-3 text-small">No courts with DTS found</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stunning-card h-100">
                <div class="card-header-clean">
                    <h6 class="card-title-small text-danger">Courts Without DTS</h6>
                </div>
                <div class="p-0" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                         @forelse($courts->where('has_dts', false) as $court)
                        <li class="list-group-item d-flex justify-content-between align-items-center text-small px-4">
                            {{ $court->name }}
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Needs DTS</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center py-3 text-small">All courts have DTS!</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="stunning-card">
         <div class="card-header-clean">
            <h6 class="card-title-small">Detailed Court List</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="courtsTable">
                <thead class="bg-light">
                    <tr>
                         <th class="text-tiny fw-bold text-uppercase text-muted ps-4">Court Name</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Type</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Region</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">DTS Status</th>
                        <th class="text-tiny fw-bold text-uppercase text-muted">Assets/Users</th>
                         <th class="text-tiny fw-bold text-uppercase text-muted pe-4">Status</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($courts as $court)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark text-small">{{ $court->name }}</div>
                            <div class="text-tiny text-muted">{{ $court->code }}</div>
                        </td>
                        <td class="text-small">{{ ucfirst($court->type) }}</td>
                        <td class="text-small">{{ $court->region->name ?? 'N/A' }}</td>
                        <td>
                             @if($court->has_dts)
                                <span class="badge bg-success-subtle text-success text-tiny">With DTS ({{ $court->dts_count }})</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger text-tiny">No DTS</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <span class="badge bg-light text-dark border text-tiny" title="Assets">{{ $court->assets->count() }} Assets</span>
                                <span class="badge bg-light text-dark border text-tiny" title="Users">{{ $court->users->count() }} Users</span>
                                 @if($court->assets->contains(function($asset) {
                                    return stripos($asset->category->name ?? '', 'computer') !== false || 
                                           stripos($asset->category->name ?? '', 'desktop') !== false;
                                }))
                                    <i class="fas fa-desktop text-primary" title="Has Computers"></i>
                                @endif
                            </div>
                        </td>
                         <td class="pe-4">
                            <span class="badge {{ $court->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-tiny">
                                {{ $court->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                     <tr>
                        <td colspan="6" class="text-center py-4 text-muted text-small">
                            No courts found.
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
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#6c757d';

    // Type Char
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($summary['by_type'] ?? [])) !!},
            datasets: [{
                data: {!! json_encode(array_values($summary['by_type'] ?? [])) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6b7280'],
                borderWidth:0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 6 } } }
        }
    });

    // Region Chart
    const courtRegionCtx = document.getElementById('courtRegionChart').getContext('2d');
    new Chart(courtRegionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($summary['by_region'] ?? [])) !!},
            datasets: [{
                label: 'Courts',
                data: {!! json_encode(array_values($summary['by_region'] ?? [])) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                x: { grid: { display: false } }
            }
        }
    });

    // DTS Chart
    const dtsCtx = document.getElementById('dtsChart').getContext('2d');
    new Chart(dtsCtx, {
        type: 'doughnut',
        data: {
            labels: ['With DTS', 'Without DTS'],
            datasets: [{
                data: [{{ $summary['courts_with_dts'] ?? 0 }}, {{ $summary['courts_without_dts'] ?? 0 }}],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 6 } } },
            cutout: '70%'
        }
    });

    function exportToExcel() {
        const table = document.getElementById('courtsTable');
        let csv = [];
        for (let i = 0; i < table.rows.length; i++) {
            let row = [], cols = table.rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) row.push('"' + cols[j].innerText.trim() + '"');
            csv.push(row.join(","));
        }
        const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        const downloadLink = document.createElement("a");
        downloadLink.download = "courts_report.csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }
</script>
@endpush
@endsection