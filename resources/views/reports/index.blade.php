@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header Section -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Reports & Analytics</h4>
                <p class="text-muted text-small mb-0">Comprehensive asset management reports and insights.</p>
            </div>
            <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="window.print()">
                <i class="fas fa-print fa-xs me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Assets</div>
                <div class="metric-v2-value">{{ number_format($totalAssets ?? 0) }}</div>
                <div class="badge-gold-light mt-2">
                    <i class="fas fa-cubes"></i> Inventory
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
                <div class="metric-v2-label">Total Users</div>
                <div class="metric-v2-value">{{ number_format($totalUsers ?? 0) }}</div>
                <div class="text-tiny text-muted mt-2">Active Personnel</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Judges w/ Laptops</div>
                <div class="metric-v2-value">{{ number_format($judgesWithLaptops ?? 0) }}</div>
                <div class="badge-gold-light mt-2">
                    <i class="fas fa-laptop"></i> Assigned
                </div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Judges w/o Laptops</div>
                <div class="metric-v2-value">{{ number_format($judgesWithoutLaptops ?? 0) }}</div>
                <div class="text-tiny text-muted mt-2 text-danger">Pending Assignment</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-user-times"></i>
            </div>
        </div>
    </div>

    <!-- DTS Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total DTS Systems</div>
                <div class="metric-v2-value">{{ number_format($totalDts ?? 0) }}</div>
                <div class="badge-gold-light mt-2">
                    <i class="fas fa-satellite-dish"></i> Installed
                </div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-broadcast-tower"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Courts with DTS</div>
                <div class="metric-v2-value">{{ number_format($courtsWithDts ?? 0) }}</div>
                <div class="text-tiny text-muted mt-2">Operational</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Courts without DTS</div>
                <div class="metric-v2-value">{{ number_format($courtsWithoutDts ?? 0) }}</div>
                <div class="text-tiny text-muted mt-2 text-warning">Needs Attention</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <!-- Reports Cards Row -->
    <div class="col-12 mt-4">
        <h6 class="mb-3 fw-bold text-muted text-uppercase text-tiny ps-1">Available Reports</h6>
    </div>

    <!-- Assets Report -->
    <div class="col-md-4">
        <div class="stunning-card h-100">
            <div class="card-header-clean">
                <h6 class="card-title-small">Assets Report</h6>
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-chart-bar text-muted"></i>
                </div>
            </div>
            <p class="text-muted text-small mb-4">
                Detailed overview of asset categorization, status distribution, and regional analysis.
            </p>
            <div class="mb-4">
                <ul class="list-unstyled text-small text-muted">
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Status distribution</li>
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Category breakdown</li>
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Regional analysis</li>
                </ul>
            </div>
            <a href="{{ route('reports.assets') }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                <i class="fas fa-file-download me-2"></i>Generate Report
            </a>
        </div>
    </div>

    <!-- Users Report -->
    <div class="col-md-4">
        <div class="stunning-card h-100">
            <div class="card-header-clean">
                <h6 class="card-title-small">Users Report</h6>
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-users text-muted"></i>
                </div>
            </div>
            <p class="text-muted text-small mb-4">
                Analysis of users, roles, and equipment distribution across the organization.
            </p>
            <div class="mb-4">
                <ul class="list-unstyled text-small text-muted">
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Laptop assignments</li>
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Role distribution</li>
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Regional coverage</li>
                </ul>
            </div>
            <a href="{{ route('reports.users') }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                <i class="fas fa-file-download me-2"></i>Generate Report
            </a>
        </div>
    </div>

    <!-- Courts Report -->
    <div class="col-md-4">
        <div class="stunning-card h-100">
            <div class="card-header-clean">
                <h6 class="card-title-small">Courts Report</h6>
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-gavel text-muted"></i>
                </div>
            </div>
            <p class="text-muted text-small mb-4">
                Overview of courts, equipment distribution, and assignment status.
            </p>
            <div class="mb-4">
                <ul class="list-unstyled text-small text-muted">
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Equipment by court</li>
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Court type analysis</li>
                    <li class="mb-2"><i class="fas fa-check text-warning me-2 text-tiny"></i>Regional courts</li>
                </ul>
            </div>
            <a href="{{ route('reports.courts') }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                <i class="fas fa-file-download me-2"></i>Generate Report
            </a>
        </div>
    </div>
</div>
@endsection