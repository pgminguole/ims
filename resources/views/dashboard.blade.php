@extends('layouts.app')

@section('title', 'Dashboard - Asset Management System')

@section('content')
@section('content')
<div class="row g-3">
    <!-- Header Section -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Dashboard</h4>
                <p class="text-muted text-small mb-0">Overview of judicial assets and distribution.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-tiny text-muted">Updated: {{ now()->format('h:i A') }}</span>
                <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt fa-xs me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Assets</div>
                <div class="metric-v2-value">{{ number_format($totalAssets) }}</div>
                <div class="badge-gold-light mt-2">
                    <i class="fas fa-database"></i> All Inventory
                </div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Assigned</div>
                <div class="metric-v2-value">{{ number_format($assignedAssets) }}</div>
                <div class="text-tiny text-muted mt-2">
                    {{ $totalAssets > 0 ? number_format(($assignedAssets/$totalAssets)*100, 1) : 0 }}% deployed
                </div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Available</div>
                <div class="metric-v2-value">{{ number_format($availableAssets) }}</div>
                <div class="badge-gold-light mt-2">
                    <i class="fas fa-check"></i> Ready
                </div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Retired</div>
                <div class="metric-v2-value">{{ number_format($retiredAssets) }}</div>
                <div class="text-tiny text-muted mt-2">Out of Service</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-archive"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="col-xl-8 col-lg-7">
        <!-- Category Chart -->
        <div class="stunning-card mb-3">
            <div class="card-header-clean">
                <h6 class="card-title-small">Assets by Category</h6>
                <button class="btn btn-link btn-sm text-muted p-0"><i class="fas fa-ellipsis-h"></i></button>
            </div>
            <div style="height: 250px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Court Distribution -->
        <div class="stunning-card">
            <div class="card-header-clean">
                <h6 class="card-title-small">Device Distribution</h6>
                <span class="text-tiny text-muted">Top Courts</span>
            </div>
            <div style="height: 300px;">
                <canvas id="courtDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <!-- Status Donut -->
        <div class="stunning-card mb-3">
            <div class="card-header-clean">
                <h6 class="card-title-small">Status Overview</h6>
            </div>
            <div style="height: 200px;">
                <canvas id="statusDistributionChart"></canvas>
            </div>
            <div class="mt-3">
                @foreach($assetsByStatus->take(3) as $status)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-tiny text-muted d-flex align-items-center gap-2">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $status['color'] }}"></span>
                        {{ $status['status'] }}
                    </span>
                    <span class="text-tiny fw-bold">{{ $status['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="stunning-card">
            <div class="card-header-clean">
                <h6 class="card-title-small">Recent Activity</h6>
                <a href="#" class="text-tiny text-muted text-decoration-none">View All</a>
            </div>
            <div class="activity-list">
                @forelse($recentActivities->take(5) as $activity)
                <div class="d-flex gap-3 mb-3">
                    <div class="mt-1">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            <i class="fas fa-history text-muted" style="font-size: 0.6rem;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-small fw-medium">{{ $activity->action }}</div>
                        <div class="text-tiny text-muted">
                            {{ $activity->asset->name ?? 'Asset' }} • {{ $activity->performed_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted text-small">No recent activity</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Configuration
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = '#6b7280';
Chart.defaults.font.size = 12;

// Common chart options
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                padding: 15,
                usePointStyle: true,
                font: {
                    size: 12,
                    weight: 500
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.9)',
            padding: 12,
            cornerRadius: 8,
            titleFont: {
                size: 13,
                weight: 600
            },
            bodyFont: {
                size: 12
            }
        }
    }
};

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
            hoverOffset: 10
        }]
    },
    options: {
        ...commonOptions,
        cutout: '65%',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                ...commonOptions.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.parsed;
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Category Chart - Horizontal Bar
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
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        ...commonOptions,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            },
            tooltip: commonOptions.plugins.tooltip
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    precision: 0
                }
            },
            y: {
                grid: {
                    display: false
                },
                border: {
                    display: false
                }
            }
        }
    }
});

// Regional Distribution Chart
const regionCtx = document.getElementById('regionChart');
new Chart(regionCtx, {
    type: 'polarArea',
    data: {
        labels: {!! json_encode($assetsByRegion->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($assetsByRegion->pluck('count')) !!},
            backgroundColor: {!! json_encode($assetsByRegion->pluck('color')->map(function($color) {
                return $color . '80';
            })) !!},
            borderColor: {!! json_encode($assetsByRegion->pluck('color')) !!},
            borderWidth: 2
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 11
                    }
                }
            },
            tooltip: commonOptions.plugins.tooltip
        },
        scales: {
            r: {
                ticks: {
                    display: false
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
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
            backgroundColor: [
                '#10b981',
                '#3b82f6',
                '#f59e0b',
                '#ef4444'
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                ...commonOptions.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.parsed;
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Court Distribution Chart - Stacked Bar
const courtData = {!! json_encode($deviceDistributionByCourt) !!};
const courtLabels = courtData.map(c => c.name);
const laptopsData = courtData.map(c => c.laptops_count);
const computersData = courtData.map(c => c.computers_count);
const printersData = courtData.map(c => c.printers_count);

const courtCtx = document.getElementById('courtDistributionChart');
new Chart(courtCtx, {
    type: 'bar',
    data: {
        labels: courtLabels,
        datasets: [
            {
                label: 'Laptops',
                data: laptopsData,
                backgroundColor: '#3b82f6',
                borderRadius: 6,
                borderSkipped: false
            },
            {
                label: 'Computers',
                data: computersData,
                backgroundColor: '#06b6d4',
                borderRadius: 6,
                borderSkipped: false
            },
            {
                label: 'Printers',
                data: printersData,
                backgroundColor: '#f59e0b',
                borderRadius: 6,
                borderSkipped: false
            }
        ]
    },
    options: {
        ...commonOptions,
        scales: {
            x: {
                stacked: true,
                grid: {
                    display: false
                },
                border: {
                    display: false
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                border: {
                    display: false
                },
                ticks: {
                    precision: 0
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 12,
                        weight: 500
                    }
                }
            },
            tooltip: commonOptions.plugins.tooltip
        }
    }
});
</script>
@endsection