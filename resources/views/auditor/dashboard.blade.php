@extends('layouts.app')

@section('title', 'Auditor Dashboard')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12 mb-2">
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <div class="text-tiny text-uppercase fw-bold text-muted mb-1">Auditor Overview</div>
                <h3 class="fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->first_name ?? 'Auditor' }}</h3>
                <p class="text-small text-muted mb-0">Here's what's happening with asset compliance today.</p>
            </div>
            <div>
                <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 fw-medium">
                    <i class="fas fa-file-export me-1 text-muted"></i> Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Primary Stats Row (Compact) -->
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stunning-card metric-v2 p-3 shadow-sm border-0">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="metric-v2-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-laptop"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-v2-value h4 fw-bold mb-0 text-dark">{{ number_format($totalAssets ?? 0) }}</div>
                    <div class="text-tiny fw-bold text-muted text-uppercase">Total Assets</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stunning-card metric-v2 p-3 shadow-sm border-0">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="metric-v2-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-v2-value h4 fw-bold mb-0 text-dark">{{ number_format($totalUsers ?? 0) }}</div>
                    <div class="text-tiny fw-bold text-muted text-uppercase">Total Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stunning-card metric-v2 p-3 shadow-sm border-0">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="metric-v2-icon bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-gavel"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-v2-value h4 fw-bold mb-0 text-dark">{{ number_format($totalCourts ?? 0) }}</div>
                    <div class="text-tiny fw-bold text-muted text-uppercase">Courts</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stunning-card metric-v2 p-3 shadow-sm border-0">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="metric-v2-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-v2-value h4 fw-bold mb-0 text-dark">{{ number_format($totalRegions ?? 0) }}</div>
                    <div class="text-tiny fw-bold text-muted text-uppercase">Regions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stunning-card metric-v2 p-3 shadow-sm border-0">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="metric-v2-icon bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-v2-value h4 fw-bold mb-0 text-dark">{{ number_format($totalDepartments ?? 0) }}</div>
                    <div class="text-tiny fw-bold text-muted text-uppercase">Offices</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stunning-card metric-v2 p-3 shadow-sm border-0">
            <div class="d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="metric-v2-icon bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <i class="fas fa-microphone"></i>
                    </div>
                </div>
                <div>
                    <div class="metric-v2-value h4 fw-bold mb-0 text-dark">{{ number_format($totalDts ?? 0) }}</div>
                    <div class="text-tiny fw-bold text-muted text-uppercase">DTS Systems</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row (Reduced Height) -->
    <div class="col-xl-6">
        <div class="stunning-card shadow-sm border-0">
            <div class="card-header-clean py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title-small fw-bold">Asset Status</h6>
                <div class="text-tiny text-muted">Distribution</div>
            </div>
            <div class="p-3 d-flex align-items-center justify-content-center" style="position: relative; height: 180px;">
                <canvas id="assetStatusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="stunning-card shadow-sm border-0">
            <div class="card-header-clean py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title-small fw-bold">Top Categories</h6>
                <div class="text-tiny text-muted">By Volume</div>
            </div>
            <div class="p-3" style="position: relative; height: 180px;">
                <canvas id="assetCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Assets Table -->
    <div class="col-xl-8">
        <div class="stunning-card shadow-sm border-0">
            <div class="card-header-clean py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title-small fw-bold">Recent Assets</h6>
                    <div class="text-tiny text-muted mt-1">Latest additions to the registry</div>
                </div>
                <a href="{{ route('auditor.assets.index') }}" class="btn btn-xs btn-white border rounded-pill px-3 shadow-sm text-dark fw-medium">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-4 text-tiny text-uppercase fw-bold text-muted py-2 border-0">Asset Info</th>
                            <th class="text-tiny text-uppercase fw-bold text-muted py-2 border-0">Assigned To</th>
                            <th class="text-end pe-4 text-tiny text-uppercase fw-bold text-muted py-2 border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAssets ?? [] as $asset)
                        <tr>
                            <td class="ps-4 py-2">
                                <a href="{{ route('auditor.assets.show', $asset) }}" class="fw-bold text-small text-dark text-decoration-none d-block">
                                    {{ Str::limit($asset->asset_name, 30) }}
                                </a>
                                <span class="text-tiny text-muted">{{ $asset->category->name ?? 'N/A' }}</span>
                            </td>
                            <td class="py-2">
                                @if($asset->assigned_type === 'user' && $asset->assignedUser)
                                    <div class="text-tiny"><i class="fas fa-user-circle text-muted me-1"></i> {{ Str::limit($asset->assignedUser->full_name, 15) }}</div>
                                @elseif($asset->assigned_type === 'office' && $asset->office)
                                    <div class="text-tiny"><i class="fas fa-building text-muted me-1"></i> {{ Str::limit($asset->office->name, 15) }}</div>
                                @elseif($asset->court)
                                    <div class="text-tiny"><i class="fas fa-landmark text-muted me-1"></i> {{ Str::limit($asset->court->name, 15) }}</div>
                                @else
                                    <span class="text-tiny text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-2">
                                <span class="badge {{ $asset->status === 'available' ? 'bg-success-subtle text-success' : ($asset->status === 'assigned' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning') }} border-0 text-tiny px-2 rounded-pill">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted text-small">No recent assets found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stats Lists -->
    <div class="col-xl-4">
        <div class="stunning-card shadow-sm border-0">
             <div class="card-header-clean py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title-small fw-bold">Top Courts</h6>
                <a href="{{ route('auditor.courts.index') }}" class="btn btn-xs btn-white border rounded-pill px-3 shadow-sm text-dark fw-medium">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-4 text-tiny text-uppercase fw-bold text-muted py-2 border-0">Court</th>
                            <th class="text-end pe-4 text-tiny text-uppercase fw-bold text-muted py-2 border-0">Assets</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courtStats ?? [] as $court)
                        <tr>
                            <td class="ps-4 py-2">
                                <a href="{{ route('auditor.courts.show', $court) }}" class="fw-bold text-small text-dark text-decoration-none">
                                    {{ Str::limit($court->name, 25) }}
                                </a>
                            </td>
                            <td class="text-end pe-4 py-2 text-small fw-bold">{{ $court->assets_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-muted text-small">No court data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
             x: { grid: { display: false }, ticks: { font: { size: 10, family: "'Outfit', sans-serif" } }, border: { display: false } },
             y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f0f0f0' }, ticks: { font: { size: 10, family: "'Outfit', sans-serif" } }, border: { display: false } }
        },
        elements: {
            bar: { borderRadius: 4 },
            arc: { borderRadius: 4, borderWidth: 0 }
        }
    };

    // Asset Status Chart
    const statusCtx = document.getElementById('assetStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map('ucfirst', array_keys($assetStatusCounts->toArray()))) !!},
            datasets: [{
                data: {!! json_encode(array_values($assetStatusCounts->toArray())) !!},
                backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#dc3545', '#adb5bd'],
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 8, usePointStyle: true, font: { size: 11, family: "'Outfit', sans-serif" } } }
            }
        }
    });

    // Asset Category Chart
    const categoryCtx = document.getElementById('assetCategoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($assetsByCategoryData->toArray())) !!},
            datasets: [{
                data: {!! json_encode(array_values($assetsByCategoryData->toArray())) !!},
                backgroundColor: '#212529',
                barThickness: 16
            }]
        },
        options: commonOptions
    });
</script>
@endpush