@extends('layouts.app')

@section('title', 'Dashboard - Asset Management System')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="dashboard-title mb-1">Dashboard</h1>
            <p class="dashboard-subtitle mb-0">Overview of ICT Equipments across the</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="last-updated">
                <small class="text-muted d-block">Last Updated</small>
                <span class="fw-semibold">{{ now()->format('M d, h:i A') }}</span>
            </div>
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-sync-alt fa-sm"></i>
                <span class="d-none d-md-inline">Refresh</span>
            </button>
        </div>
    </div>

  <!-- Device Distribution by Court -->
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-card-header">
                <h6 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Device Distribution by Court
                </h6>
            </div>
            <div class="chart-card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Court</th>
                                <th>Region</th>
                                <th><i class="fas fa-laptop text-primary"></i> Laptops</th>
                                <th><i class="fas fa-desktop text-info"></i> Computers</th>
                                <th><i class="fas fa-print text-warning"></i> Printers</th>
                                <th>Total Devices</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deviceDistributionByCourt as $court)
                            <tr>
                                <td>{{ $court->name }}</td>
                                <td>{{ $court->region->name ?? 'N/A' }}</td>
                                <td>{{ $court->laptops_count }}</td>
                                <td>{{ $court->computers_count }}</td>
                                <td>{{ $court->printers_count }}</td>
                                <td><strong>{{ $court->laptops_count + $court->computers_count + $court->printers_count }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Charts Row 1 -->
    <div class="row g-3 mb-3">
        <!-- Status Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-title">
                        <i class="fas fa-chart-doughnut"></i>
                        Status Distribution
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-wrapper" style="height: 200px;">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                    <div class="status-legend">
                        @foreach($assetsByStatus as $status)
                        <div class="legend-item">
                            <span class="legend-dot" style="background-color: {{ $status['color'] }}"></span>
                            <span class="legend-label">{{ $status['status'] }}</span>
                            <span class="legend-value">{{ $status['count'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Acquisition Trend -->
        <div class="col-xl-8 col-lg-7">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Asset Acquisition Trend
                    </h6>
                    <span class="period-badge">Last 12 Months</span>
                </div>
                <div class="chart-card-body">
                    <div class="chart-wrapper" style="height: 280px;">
                        <canvas id="acquisitionTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-3">
        <!-- Category Distribution -->
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-title">
                        <i class="fas fa-chart-bar"></i>
                        Assets by Category
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-wrapper" style="height: 280px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

      
    </div>

   

    <!-- Bottom Row -->
    <div class="row g-3">
        <!-- Asset Age -->
        <div class="col-xl-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-title">
                        <i class="fas fa-clock"></i>
                        Asset Age Distribution
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="chart-wrapper" style="height: 220px;">
                        <canvas id="assetAgeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Courts -->
        <div class="col-xl-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6 class="chart-title">
                        <i class="fas fa-building"></i>
                        Top Courts by Assets
                    </h6>
                </div>
                <div class="chart-card-body">
                    <div class="courts-list">
                        @foreach($topCourts as $court)
                        <div class="court-item">
                            <div class="court-info">
                                <h6 class="court-name">{{ $court->name }}</h6>
                                <small class="court-region">{{ $court->region->name ?? 'N/A' }}</small>
                            </div>
                            <span class="court-count">{{ $court->assets_count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

      
    </div>
</div>

<style>
:root {
    --primary: #3b82f6;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Dashboard Header */
.dashboard-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
}

.dashboard-subtitle {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.last-updated {
    text-align: right;
}

.last-updated small {
    font-size: 0.75rem;
    line-height: 1.2;
}

.last-updated .fw-semibold {
    font-size: 0.813rem;
    color: var(--gray-700);
}

/* Metric Cards */
.metric-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}


.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.metric-card-body {
    padding: 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.metric-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.125rem;
}

.metric-icon-primary {
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
}

.metric-icon-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.metric-icon-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.metric-icon-info {
    background: rgba(6, 182, 212, 0.1);
    color: var(--info);
}

.metric-content {
    flex: 1;
    min-width: 0;
}

.metric-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.25rem;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.metric-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.625rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.metric-badge i {
    font-size: 0.625rem;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.metric-info {
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.metric-info i {
    font-size: 0.625rem;
}

.metric-progress {
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.metric-progress-bar {
    height: 100%;
    background: var(--info);
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* Chart Cards */
.chart-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.chart-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chart-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-title i {
    font-size: 0.813rem;
    color: var(--gray-400);
}

.period-badge {
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
    padding: 0.25rem 0.625rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.alert-badge {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
    padding: 0.25rem 0.625rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.chart-card-body {
    padding: 1.25rem;
    flex: 1;
}

.chart-wrapper {
    position: relative;
}

/* Status Legend */
.status-legend {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.813rem;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.legend-label {
    flex: 1;
    color: var(--gray-600);
}

.legend-value {
    font-weight: 600;
    color: var(--gray-700);
}

/* Alert List */
.alert-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.alert-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem;
    background: var(--gray-50);
    border-radius: 8px;
    gap: 1rem;
}

.alert-item-content {
    flex: 1;
    min-width: 0;
}

.alert-item-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 0.25rem;
}

.alert-item-category {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-bottom: 0.375rem;
}

.alert-item-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 10px;
    font-size: 0.688rem;
    font-weight: 500;
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.alert-item-days {
    text-align: center;
    flex-shrink: 0;
}

.days-count {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--warning);
    line-height: 1;
}

.alert-item-days small {
    font-size: 0.688rem;
    color: var(--gray-500);
    display: block;
    margin-top: 0.125rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-state i {
    font-size: 2.5rem;
    color: var(--success);
    margin-bottom: 0.75rem;
}

.empty-state p {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin: 0;
}

/* Activity Timeline */
.activity-timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.25rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 1.25rem;
    bottom: -0.5rem;
    width: 2px;
    background: var(--gray-200);
    margin-left: 7px;
}

.timeline-dot {
    position: absolute;
    left: -1.5rem;
    top: 0.25rem;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-dot-primary {
    color: var(--primary);
    background: var(--primary);
}

.timeline-dot-success {
    color: var(--success);
    background: var(--success);
}

.timeline-dot-warning {
    color: var(--warning);
    background: var(--warning);
}

.timeline-dot-info {
    color: var(--info);
    background: var(--info);
}

.timeline-content {
    background: var(--gray-50);
    padding: 0.75rem;
    border-radius: 8px;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.375rem;
    gap: 0.5rem;
}

.timeline-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
}

.timeline-time {
    font-size: 0.688rem;
    color: var(--gray-500);
    white-space: nowrap;
}

.timeline-description {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-bottom: 0.375rem;
}

.timeline-user {
    font-size: 0.688rem;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.timeline-user i {
    font-size: 0.625rem;
}

/* Courts List */
.courts-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.court-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 8px;
}

.court-info {
    flex: 1;
}

.court-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 0.125rem;
}

.court-region {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.court-count {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(59, 130, 246, 0.1);
    color: var(--primary);
    font-weight: 700;
    font-size: 0.875rem;
    border-radius: 8px;
}

/* Warranty Summary */
.warranty-summary {
    text-align: center;
}

.warranty-total {
    padding: 1.5rem 0;
    border-bottom: 1px solid var(--gray-100);
    margin-bottom: 1.25rem;
}

.warranty-count {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--danger);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.warranty-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin: 0;
}

.warranty-stats {
    display: flex;
    align-items: stretch;
    justify-content: space-around;
}

.warranty-stat {
    flex: 1;
    padding: 0.5rem;
}

.warranty-stat-divider {
    width: 1px;
    background: var(--gray-200);
}

.warranty-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.375rem;
}

.warranty-stat small {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* Utilities */
.gap-3 {
    gap: 1rem !important;
}

.text-danger {
    color: var(--danger) !important;
}

.text-warning {
    color: var(--warning) !important;
}

.text-success {
    color: var(--success) !important;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Configuration
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = '#6b7280';
Chart.defaults.font.size = 11;

// Status Distribution Chart
const statusCtx = document.getElementById('statusDistributionChart');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($assetsByStatus->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($assetsByStatus->pluck('count')) !!},
            backgroundColor: {!! json_encode($assetsByStatus->pluck('color')) !!},
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 6,
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.parsed;
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Acquisition Trend Chart
const acquisitionCtx = document.getElementById('acquisitionTrendChart');
new Chart(acquisitionCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($assetAcquisitionTrend->pluck('month')) !!},
        datasets: [{
            label: 'Assets Acquired',
            data: {!! json_encode($assetAcquisitionTrend->pluck('count')) !!},
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.08)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointHoverBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 6
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.04)',
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    precision: 0,
                    padding: 8
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    padding: 8
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart');
new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($assetsByCategory->pluck('name')) !!},
        datasets: [{
            label: 'Assets',
            data: {!! json_encode($assetsByCategory->pluck('count')) !!},
            backgroundColor: {!! json_encode($assetsByCategory->pluck('color')) !!},
            borderWidth: 0,
            borderRadius: 6,
            borderSkipped: false,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 6
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.04)',
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    precision: 0,
                    padding: 8
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    padding: 8
                }
            }
        }
    }
});



// Asset Age Chart
const assetAgeCtx = document.getElementById('assetAgeChart');
new Chart(assetAgeCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($assetAgeDistribution->keys()) !!},
        datasets: [{
            data: {!! json_encode($assetAgeDistribution->values()) !!},
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 12,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    font: {
                        size: 11
                    },
                    boxWidth: 8,
                    boxHeight: 8
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 6,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.parsed;
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>
@endsection