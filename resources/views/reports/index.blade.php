@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Reports & Analytics</h2>
            <p class="text-muted mb-0">Comprehensive asset management reports and insights</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper primary-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Assets</h6>
                            <h3 class="mb-0">{{ $totalAssets ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper success-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Users</h6>
                            <h3 class="mb-0">{{ $totalUsers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper info-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Judges with Laptops</h6>
                            <h3 class="mb-0">{{ $judgesWithLaptops ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper warning-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Judges without Laptops</h6>
                            <h3 class="mb-0">{{ $judgesWithoutLaptops ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row g-4">
        <!-- Assets Report Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">Assets Report</h5>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Comprehensive overview of all assets with detailed categorization, status distribution, and regional analysis.
                    </p>
                    <div class="mb-3">
                        <h6>Report Includes:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Asset status distribution</li>
                            <li><i class="fas fa-check text-success me-2"></i>Category-wise breakdown</li>
                            <li><i class="fas fa-check text-success me-2"></i>Regional analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Condition assessment</li>
                            <li><i class="fas fa-check text-success me-2"></i>Assignment status</li>
                        </ul>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.assets') }}" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Generate Assets Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Report Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users text-success me-2"></i>
                        <h5 class="mb-0 fw-semibold">Users Report</h5>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Detailed analysis of users, their roles, assigned assets, and equipment distribution across the organization.
                    </p>
                    <div class="mb-3">
                        <h6>Report Includes:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Judges with/without laptops</li>
                            <li><i class="fas fa-check text-success me-2"></i>Role-wise user distribution</li>
                            <li><i class="fas fa-check text-success me-2"></i>Regional user analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Asset assignment status</li>
                            <li><i class="fas fa-check text-success me-2"></i>Court-wise user distribution</li>
                        </ul>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.users') }}" class="btn btn-success w-100">
                            <i class="fas fa-download me-2"></i>Generate Users Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courts Report Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-gavel text-info me-2"></i>
                        <h5 class="mb-0 fw-semibold">Courts Report</h5>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Comprehensive overview of courts, their assets, equipment distribution, and user assignments.
                    </p>
                    <div class="mb-3">
                        <h6>Report Includes:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Courts with laptops/computers</li>
                            <li><i class="fas fa-check text-success me-2"></i>Court type analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i>Regional court distribution</li>
                            <li><i class="fas fa-check text-success me-2"></i>Asset inventory by court</li>
                            <li><i class="fas fa-check text-success me-2"></i>User distribution by court</li>
                        </ul>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.courts') }}" class="btn btn-info w-100">
                            <i class="fas fa-download me-2"></i>Generate Courts Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection