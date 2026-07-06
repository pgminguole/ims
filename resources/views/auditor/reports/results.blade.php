@extends('layouts.app')

@section('title', 'Report Results')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4 no-print">
        <div>
            <h1 class="h3 mb-0 text-dark fw-bold d-flex align-items-center">
                <i class="fas fa-chart-pie text-primary me-3"></i>Report Results
            </h1>
            <div class="mt-1">
                <span class="badge badge-gold-light text-uppercase fs-7 fw-bold">{{ $reportType }}</span>
                <span class="text-muted small ms-2">Judicial ICT Asset Management</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('auditor.reports.index') }}" class="btn btn-light-modern px-3 fs-7">
                <i class="fas fa-arrow-left me-2"></i>New Report
            </a>
            <button type="button" onclick="printReport()" class="btn btn-light-modern px-3 fs-7">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <div class="dropdown">
                <button class="btn btn-primary-modern dropdown-toggle px-4 fs-7 shadow-gold" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-2"></i>Export Results
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="exportDropdown">
                    <li>
                        <button type="button" class="dropdown-item py-2 px-3 d-flex align-items-center" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>PDF Document
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item py-2 px-3 d-flex align-items-center" onclick="exportReport('excel')">
                            <i class="fas fa-file-excel me-2 text-success"></i>Excel Spreadsheet
                        </button>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <button type="button" class="dropdown-item py-2 px-3 d-flex align-items-center" onclick="exportReport('csv')">
                            <i class="fas fa-file-csv me-2 text-info"></i>CSV File
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Report Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="fw-bold text-primary">{{ ucfirst($reportType) }} Audit Report</h4>
                    <p class="text-muted mb-0">
                        Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}
                        @if(isset($filters['start_date']) && isset($filters['end_date']))
                            | Period: {{ \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') }}
                        @endif
                        @if(isset($filters['year']))
                            | Year: {{ $filters['year'] }}
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="mb-2">
                    <p class="text-muted mb-0">Judicial Service of Ghana</p>
                    <p class="text-muted mb-0">ICT Assets Management System</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unified Search & Filter Bar -->
<div class="row mb-4 no-print">
    <div class="col-12">
        <div class="filter-bar d-flex flex-wrap align-items-center gap-3">
            @if($reportType !== 'summary')
            <form action="{{ route('auditor.reports.generate') }}" method="POST" class="d-flex flex-grow-1 align-items-center gap-2">
                @csrf
                @foreach($filters as $key => $value)
                    @if($key !== 'search' && $key !== '_token' && !is_array($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div class="search-input-group">
                    <i class="fas fa-search fas-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search within these results..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <button type="submit" class="btn btn-primary-modern px-4">Search</button>
            </form>
            @else
            <div class="flex-grow-1">
                <span class="text-muted small fw-bold text-uppercase">Summary View: Apply advanced filters below to narrow down scope</span>
            </div>
            @endif
            
            <div class="d-flex gap-2">
                <button class="btn btn-light-modern px-3" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                    <i class="fas fa-sliders-h me-2"></i>Advanced Filters
                </button>
                @if(collect($filters)->except(['report_type', '_token', 'format', 'search'])->filter()->isNotEmpty())
                <a href="{{ route('auditor.reports.generate', ['report_type' => $reportType]) }}" onclick="event.preventDefault(); document.getElementById('clearFiltersForm').submit();" class="btn btn-outline-danger btn-sm border-0 rounded-pill px-3">
                    <i class="fas fa-times-circle me-1"></i>Clear Filters
                </a>
                <form id="clearFiltersForm" action="{{ route('auditor.reports.generate') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                </form>
                @endif
            </div>
        </div>

        <!-- Active Filters Display -->
        @php
            $activeFilters = collect($filters)->except(['report_type', '_token', 'format', 'search', 'include_charts'])->filter();
        @endphp
        @if($activeFilters->isNotEmpty() || !empty($filters['search']))
        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted small fw-bold text-uppercase me-2"><i class="fas fa-tag me-1"></i>Active:</span>
            
            @if(!empty($filters['search']))
                <span class="filter-badge">
                    Search: "{{ $filters['search'] }}"
                </span>
            @endif

            @if(isset($filters['region_id']) && $filters['region_id'])
                @php $region = $filterData['regions']->firstWhere('id', $filters['region_id']); @endphp
                <span class="filter-badge">
                    Region: {{ $region->name ?? 'N/A' }}
                </span>
            @endif

            @if(isset($filters['asset_status']) && $filters['asset_status'])
                <span class="filter-badge text-capitalize">
                    Status: {{ $filters['asset_status'] }}
                </span>
            @endif

            @if(isset($filters['category_id']) && $filters['category_id'])
                @php $cat = $filterData['categories']->firstWhere('id', $filters['category_id']); @endphp
                <span class="filter-badge">
                    Category: {{ $cat->name ?? 'N/A' }}
                </span>
            @endif

            @if(isset($filters['court_type']) && $filters['court_type'])
                <span class="filter-badge text-capitalize">
                    Court: {{ str_replace('_', ' ', $filters['court_type']) }}
                </span>
            @endif

            @if(isset($filters['user_type']) && $filters['user_type'])
                <span class="filter-badge text-capitalize">
                    User Type: {{ $filters['user_type'] }}
                </span>
            @endif

            @if(isset($filters['start_date']) && $filters['start_date'])
                <span class="filter-badge">
                    From: {{ \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') }}
                </span>
            @endif

            @if(isset($filters['end_date']) && $filters['end_date'])
                <span class="filter-badge">
                    To: {{ \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') }}
                </span>
            @endif
        </div>
        @endif

        <!-- Advanced Filters Collapse -->
        <div class="collapse mt-3" id="advancedFilters">
            <div class="card advanced-filters-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('auditor.reports.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="report_type" value="{{ $reportType }}">
                        <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                        
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">Region</label>
                                <select name="region_id" class="form-select rounded-3">
                                    <option value="">All Regions</option>
                                    @foreach($filterData['regions'] as $region)
                                        <option value="{{ $region->id }}" {{ (isset($filters['region_id']) && $filters['region_id'] == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @if($reportType === 'assets')
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">Asset Status</label>
                                <select name="asset_status" class="form-select rounded-3">
                                    <option value="">All Statuses</option>
                                    <option value="available" {{ (isset($filters['asset_status']) && $filters['asset_status'] == 'available') ? 'selected' : '' }}>Available</option>
                                    <option value="assigned" {{ (isset($filters['asset_status']) && $filters['asset_status'] == 'assigned') ? 'selected' : '' }}>Assigned</option>
                                    <option value="maintenance" {{ (isset($filters['asset_status']) && $filters['asset_status'] == 'maintenance') ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">Category</label>
                                <select name="category_id" class="form-select rounded-3">
                                    <option value="">All Categories</option>
                                    @foreach($filterData['categories'] as $category)
                                        <option value="{{ $category->id }}" {{ (isset($filters['category_id']) && $filters['category_id'] == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if($reportType === 'courts')
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">Court Type</label>
                                <select name="court_type" class="form-select rounded-3">
                                    <option value="">All Types</option>
                                    @foreach($filterData['courtTypes'] as $type)
                                        <option value="{{ $type }}" {{ (isset($filters['court_type']) && $filters['court_type'] == $type) ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                             @if($reportType === 'users')
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">User Type</label>
                                <select name="user_type" class="form-select rounded-3">
                                    <option value="">All Users</option>
                                    @foreach($filterData['userTypes'] as $type)
                                        <option value="{{ $type }}" {{ (isset($filters['user_type']) && $filters['user_type'] == $type) ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">Date From</label>
                                <input type="date" name="start_date" class="form-control rounded-3" value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-bold text-dark-soft">Date To</label>
                                <input type="date" name="end_date" class="form-control rounded-3" value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                            
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary-modern px-5 shadow-gold">Apply Advanced Filters</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    @if($reportType === 'assets')
    <!-- Enhanced Entity Context Header -->
    @if(isset($context))
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-9 p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box-xl bg-primary-subtle text-primary rounded-4 me-4 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            @if($context['type'] === 'court')
                                <i class="fas fa-university fa-2x"></i>
                            @elseif($context['type'] === 'office')
                                <i class="fas fa-building fa-2x"></i>
                            @else
                                <i class="fas fa-user-circle fa-3x"></i>
                            @endif
                        </div>
                        <div>
                            <h2 class="fw-bold text-dark mb-1">{{ $context['data']->name }}</h2>
                             @if($context['type'] === 'court')
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">{{ ucfirst($context['data']->type) }}</span>
                            @elseif($context['type'] === 'user')
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                                    <i class="fas fa-shield-halved me-1"></i> {{ $context['data']->role->name ?? 'N/A' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-4 pt-2">
                         @if($context['type'] === 'court')
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Region</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-location-dot me-1 text-primary"></i>{{ $context['data']->region->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Presiding Judge</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-gavel me-1 text-primary"></i>{{ $context['data']->presidingJudge->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Registry Officer</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-user-pen me-1 text-primary"></i>{{ $context['data']->registryOfficer->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @elseif($context['type'] === 'office')
                             <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Region</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-location-dot me-1 text-primary"></i>{{ $context['data']->region->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Associated Court</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-university me-1 text-primary"></i>{{ $context['data']->court->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                             <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Unit Manager</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-user-tie me-1 text-primary"></i>{{ $context['data']->manager->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @elseif($context['type'] === 'user')
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Email Address</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-envelope me-1 text-primary"></i>{{ $context['data']->email }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Phone Number</span>
                                    <span class="text-dark fw-bold"><i class="fas fa-phone me-1 text-primary"></i>{{ $context['data']->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Status</span>
                                    <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 bg-primary bg-gradient p-4 text-white d-flex flex-column justify-content-center align-items-center text-center">
                    <div class="p-3 bg-white bg-opacity-10 rounded-circle mb-3">
                         <i class="fas fa-laptop-code fa-2x text-white"></i>
                    </div>
                    <div class="display-4 fw-bold mb-0">{{ $summary['total_assets'] }}</div>
                    <div class="small text-white-50 fw-bold text-uppercase letter-spacing-1">Asset Density</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Assets Report -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="fas fa-laptop fa-4x text-primary"></i>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <h2 class="display-6 fw-bold mb-1 text-dark">{{ $summary['total_assets'] }}</h2>
                    <div class="text-muted small fw-bold text-uppercase letter-spacing-1">Total Assets</div>
                </div>
            </div>
        </div>
        @foreach($summary['by_status'] as $status => $count)
        @php
            $variant = $status === 'available' ? 'success' : ($status === 'assigned' ? 'primary' : 'warning');
            $icon = $status === 'available' ? 'check-circle' : ($status === 'assigned' ? 'user-tag' : 'triangle-exclamation');
        @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="fas fa-{{ $icon }} fa-4x text-{{ $variant }}"></i>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-md bg-{{ $variant }}-subtle text-{{ $variant }} rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-tag"></i>
                        </div>
                    </div>
                    <h2 class="display-6 fw-bold mb-1 text-dark">{{ $count }}</h2>
                    <div class="text-muted small fw-bold text-uppercase letter-spacing-1">{{ $status }} status</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-list-ul me-2 text-primary"></i>Detailed Asset Listing ({{ $assets->count() }})
            </h5>
        </div>
        <div class="card-body pt-0">

            <div class="table-responsive rounded-3">
                <table class="table table-hover align-middle mb-0" id="assetsTable">
                    <thead class="bg-light border-top border-bottom">
                        <tr>
                            <th class="ps-3">Asset Category</th>
                            <th>Current Court</th>
                            <th>Status Badge</th>
                            <th>Device Condition</th>
                            <th>Assigned Entity</th>
                            <th class="pe-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                      
                            <td>{{ $asset->category->name ?? 'N/A' }}</td>
                            
                            <td>{{ $asset->court->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $asset->status === 'available' ? 'success' : ($asset->status === 'assigned' ? 'primary' : 'warning') }}">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : ($asset->condition === 'fair' ? 'warning' : 'danger')) }}">
                                    {{ ucfirst($asset->condition) }}
                                </span>
                            </td>
                             <td>
                                {{ $asset->assigned_entity_name }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('auditor.assets.show', $asset) }}" class="btn btn-sm btn-light-gold rounded-pill shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No assets found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'dts')
    <!-- DTS Report -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
             <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="fas fa-microphone fa-4x text-primary"></i>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <h2 class="display-6 fw-bold mb-1 text-dark">{{ $summary['total_dts'] }}</h2>
                    <div class="text-muted small fw-bold text-uppercase letter-spacing-1">Total Systems</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
             <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="fas fa-check fa-4x text-success"></i>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-md bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <h2 class="display-6 fw-bold mb-1 text-dark">{{ $summary['available_dts'] }}</h2>
                    <div class="text-muted small fw-bold text-uppercase letter-spacing-1">Available</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
             <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="fas fa-box fa-4x text-info"></i>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-md bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                    <h2 class="display-6 fw-bold mb-1 text-dark">{{ $summary['complete_systems'] }}</h2>
                    <div class="text-muted small fw-bold text-uppercase letter-spacing-1">Complete</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
             <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="fas fa-university fa-4x text-warning"></i>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-md bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>
                    <h2 class="display-6 fw-bold mb-1 text-dark">{{ $summary['by_court']->count() }}</h2>
                    <div class="text-muted small fw-bold text-uppercase letter-spacing-1">Court Coverage</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-list-check me-2 text-primary"></i>DTS Systems Inventory
            </h5>
        </div>
        <div class="card-body pt-0">

            <!-- DTS Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dtsTable">
                    <thead class="table-light">
                        <tr>
                            <th>DTS Name</th>
                            <th>Court</th>
                            <th>Region</th>
                            <th>Monitors</th>
                            <th>Splitters</th>
                            <th>HDMI Cables</th>
                            <th>Extension Boards</th>
                            <th>Trucking</th>
                            <th>Sony Recorders</th>
                  
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dtsSystems as $dts)
                        <tr>
                            <td><strong>{{ $dts->name }}</strong></td>
                            <td>{{ $dts->court->name }}</td>
                            <td>{{ $dts->court->region->name ?? 'N/A' }}</td>
                            <td>{{ $dts->monitors_count }}</td>
                            <td>{{ $dts->splitters_count }}</td>
                            <td>{{ $dts->hdmi_short_cables_count }} (5M) & {{ $dts->hdmi_long_cables_count }} (20M)</td>
                            <td>{{ $dts->extension_boards_count }}</td>
                            <td>{{ $dts->trucking_count }}</td>
                            <td>{{ $dts->sony_recorders_count }}</td>
                          
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No DTS systems found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'users')
    <!-- Users Report -->
    <!-- Users Report -->
    {{-- <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stunning-card h-100 p-4 border-start border-primary border-4">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Total Users</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-bold mb-0 text-primary">{{ $summary['total_users'] }}</h2>
                    <div class="bg-primary-subtle p-2 rounded-3 text-primary"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card h-100 p-4 border-start border-success border-4">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Active Users</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-bold mb-0 text-success">{{ $summary['active_users'] }}</h2>
                    <div class="bg-success-subtle p-2 rounded-3 text-success"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>
        @foreach($summary['by_role'] as $role => $count)
        <div class="col-md-3">
            <div class="stunning-card h-100 p-4 border-start border-info border-4">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">{{ $role ?: 'No Role' }}</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-bold mb-0 text-info">{{ $count }}</h2>
                    <div class="bg-info-subtle p-2 rounded-3 text-info"><i class="fas fa-user-tag"></i></div>
                </div>
            </div>
        </div>
        @endforeach
    </div> --}}

    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-list me-2 text-primary"></i>System User Directory ({{ $users->count() }})
            </h5>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive rounded-3">
                <table class="table table-hover align-middle mb-0" id="usersTable">
                    <thead class="bg-light border-top border-bottom">
                        <tr>
                             <th class="ps-3">Full Name</th>
                            {{-- <th>Email Address</th>
                            <th>Phone</th>
                            <th>System Role</th>
                            <th>Jurisdiction</th> --}}
                            <th class="text-center">Equipment count</th>
                            <th class="pe-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            {{-- <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>{{ $user->role->name ?? 'N/A' }}</td>
                            <td>{{ $user->region->name ?? 'N/A' }}</td> --}}
                             <td class="text-center">
                                <a href="{{ route('auditor.reports.quick-generate', ['report_type' => 'assets', 'assigned_to' => $user->id, 'assigned_type' => 'user']) }}" class="text-decoration-none">
                                    <span class="badge bg-info hover-shadow-sm">{{ $user->assignedAssets->count() }}</span>
                                </a>
                            </td>
                             <td class="text-center">
                                <a href="{{ route('auditor.users.show', $user) }}" class="btn btn-sm btn-light-gold rounded-pill shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'courts')
    <!-- Courts Report -->
    <!-- Courts Report -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-5 g-3 mb-4">
        <div class="col">
             <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-3 position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="icon-box-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <i class="fas fa-university fa-2x text-primary opacity-25"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $summary['total_courts'] }}</h3>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Total Courts</div>
                </div>
            </div>
        </div>
        <div class="col">
             <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-3 position-relative">
                     <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="icon-box-sm bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <i class="fas fa-check-double fa-2x text-success opacity-25"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $summary['active_courts'] }}</h3>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Active Courts</div>
                </div>
            </div>
        </div>
        @foreach($summary['by_type'] as $type => $count)
        <div class="col">
             <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-3 position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="icon-box-sm bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-scale-balanced"></i>
                        </div>
                         <i class="fas fa-gavel fa-2x text-info opacity-25"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $count }}</h3>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">{{ ucfirst($type) }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-map-location-dot me-2 text-primary"></i>Regional Court Distribution ({{ $courts->count() }})
            </h5>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive rounded-3">
                <table class="table table-hover align-middle mb-0" id="courtsTable">
                    <thead class="bg-light border-top border-bottom">
                        <tr>
                             <th class="ps-3">Court Name</th>
                            <th>Type</th>
                            <th>Region</th>
                            <th>Location</th>
                            <th class="text-center">Assets</th>
                            <th class="text-center">DTS</th>
                            <th class="pe-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courts as $court)
                        <tr>
                            <td>{{ $court->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($court->type) }}</span>
                            </td>
                            <td>{{ $court->region->name ?? 'N/A' }}</td>
                            <td>{{ $court->location->name ?? 'N/A' }}</td>
                             <td class="text-center">
                                <a href="{{ route('auditor.reports.quick-generate', ['report_type' => 'assets', 'court_id' => $court->id]) }}" class="text-decoration-none">
                                    <span class="badge bg-primary hover-shadow-sm">{{ $court->assets->count() }}</span>
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $court->dts->count() }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('auditor.courts.show', $court) }}" class="btn btn-sm btn-light-gold rounded-pill shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No courts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'offices')
    <!-- Offices Report -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stunning-card h-100 p-4 border-start border-primary border-4">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Total Offices</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-bold mb-0 text-primary">{{ $summary['total_offices'] }}</h2>
                    <div class="bg-primary-subtle p-2 rounded-3 text-primary"><i class="fas fa-building"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card h-100 p-4 border-start border-success border-4">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Active Units</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-bold mb-0 text-success">{{ $summary['active_offices'] }}</h2>
                    <div class="bg-success-subtle p-2 rounded-3 text-success"><i class="fas fa-toggle-on"></i></div>
                </div>
            </div>
        </div>
        @foreach($summary['by_region'] as $region => $count)
        <div class="col-md-3">
            <div class="stunning-card h-100 p-4 border-start border-info border-4">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">{{ $region ?: 'No Region' }}</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="fw-bold mb-0 text-info">{{ $count }}</h2>
                    <div class="bg-info-subtle p-2 rounded-3 text-info"><i class="fas fa-location-dot"></i></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-sitemap me-2 text-primary"></i>Administrative Office Units
            </h5>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive rounded-3">
                <table class="table table-hover align-middle mb-0" id="officesTable">
                    <thead class="bg-light border-top border-bottom">
                        <tr>
                             <th class="ps-3">Office Unit Name</th>
                            <th>Jurisdictional Region</th>
                            <th>Associated Court</th>
                            <th>Unit Manager</th>
                            <th class="text-center">Assets Count</th>
                            <th class="pe-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offices as $office)
                        <tr>
                            <td>{{ $office->name }}</td>
                           
                            <td>{{ $office->region->name ?? 'N/A' }}</td>
                            <td>{{ $office->court->name ?? 'N/A' }}</td>
                            <td>{{ $office->manager->name ?? 'N/A' }}</td>
                             <td class="text-center">
                                <a href="{{ route('auditor.reports.quick-generate', ['report_type' => 'assets', 'office_id' => $office->id]) }}" class="text-decoration-none">
                                    <span class="badge bg-primary hover-shadow-sm">{{ $office->assets->count() }}</span>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('auditor.departments.show', $office) }}" class="btn btn-sm btn-light-gold rounded-pill shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No offices found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($reportType === 'summary')
    <!-- Summary Report -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-primary border-4 bounce-in">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="icon-box-sm bg-primary-subtle text-primary rounded-circle"><i class="fas fa-laptop"></i></div>
                    <span class="text-muted small fw-bold">ASSETS</span>
                </div>
                <h2 class="fw-bold mb-0">{{ $totalAssets }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-success border-4 bounce-in" style="animation-delay: 0.1s">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="icon-box-sm bg-success-subtle text-success rounded-circle"><i class="fas fa-users"></i></div>
                    <span class="text-muted small fw-bold">USERS</span>
                </div>
                <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-info border-4 bounce-in" style="animation-delay: 0.2s">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="icon-box-sm bg-info-subtle text-info rounded-circle"><i class="fas fa-university"></i></div>
                    <span class="text-muted small fw-bold">COURTS</span>
                </div>
                <h2 class="fw-bold mb-0">{{ $totalCourts }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stunning-card p-4 border-start border-warning border-4 bounce-in" style="animation-delay: 0.3s">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="icon-box-sm bg-warning-subtle text-warning rounded-circle"><i class="fas fa-microphone"></i></div>
                    <span class="text-muted small fw-bold">DTS</span>
                </div>
                <h2 class="fw-bold mb-0">{{ $totalDts }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Detailed Breakdowns -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-chart-simple me-2 text-primary"></i>Asset Portfolio Status
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="list-group list-group-flush border-top-0">
                        @foreach($assetsByStatus as $status => $count)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="status-dot-lg me-3 bg-{{ $status === 'available' ? 'success' : ($status === 'assigned' ? 'primary' : 'warning') }}"></div>
                                <span class="text-capitalize fw-semibold text-dark-soft">{{ $status }}</span>
                            </div>
                             <a href="{{ route('auditor.reports.quick-generate', ['report_type' => 'assets', 'status' => $status]) }}" class="text-decoration-none">
                                <span class="badge badge-gold-light rounded-pill px-3">{{ $count }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-heart-pulse me-2 text-danger"></i>Condition Assessment
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="list-group list-group-flush border-top-0">
                        @foreach($assetsByCondition as $condition => $count)
                        @php
                            $variant = $condition === 'excellent' ? 'success' : ($condition === 'good' ? 'info' : ($condition === 'fair' ? 'warning' : 'danger'));
                        @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="status-dot-lg me-3 bg-{{ $variant }}"></div>
                                <span class="text-capitalize fw-semibold text-dark-soft">{{ $condition }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="progress me-3" style="width: 100px; height: 6px;">
                                    <div class="progress-bar bg-{{ $variant }}" role="progressbar" style="width: {{ $totalAssets > 0 ? ($count / $totalAssets) * 100 : 0 }}%"></div>
                                </div>
                                 <a href="{{ route('auditor.reports.quick-generate', ['report_type' => 'assets', 'condition' => $condition]) }}" class="text-decoration-none">
                                    <span class="badge bg-light text-dark rounded-pill px-2 small">{{ $count }}</span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Report Footer -->
    <div class="card border-0 bg-light">
        <div class="card-body text-center">
            <p class="text-muted mb-0">
                This report was generated by the ICT Assets Management System on behalf of the Judicial Service of Ghana.
            </p>
            <p class="text-muted mb-0">
                For any queries, please contact the ICT Department.
            </p>
        </div>
    </div>
</div>

<!-- Hidden Export Form -->
<form id="exportForm" method="POST" action="{{ route('auditor.reports.export') }}" style="display: none;">
    @csrf
    <input type="hidden" name="format" id="exportFormat">
    <input type="hidden" name="report_type" value="{{ $reportType }}">
    @foreach($filters as $key => $value)
        @if($value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
</form>
@endsection

@push('styles')
<style>
    /* Styling for the report view */
    .stunning-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: transform 0.2s ease;
    }
    
    .stunning-card:hover {
        transform: translateY(-3px);
    }
    
    .icon-box-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .icon-box-lg {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .status-dot-lg {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    
    .badge-gold-light {
        background-color: #fdf6e7;
        color: #dfa615;
    }
    
    .text-dark-soft { color: #4b5563; }
    
    /* Buttons */
    .btn-primary-modern {
        background-color: var(--text-dark, #1f2937);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .btn-primary-modern:hover {
        background-color: #000;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-light-modern {
        background-color: #f3f4f6;
        color: #4b5563;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .btn-light-modern:hover {
        background-color: #e5e7eb;
    }
    
    .shadow-gold {
        box-shadow: 0 4px 6px -1px rgba(255, 210, 93, 0.1);
    }

    .btn-light-gold {
        background-color: #fdf6e7;
        color: #dfa615;
        border: 1px solid #f9eecd;
    }

    .btn-light-gold:hover {
        background-color: #dfa615;
        color: white;
    }
    
    /* Table Styling */
    .table thead th {
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        padding: 1rem 0.75rem;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        color: #374151;
        font-size: 0.875rem;
        border-bottom-color: #f3f4f6;
    }
    
    /* Animations */
    @keyframes bounceIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .bounce-in {
        animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    
    /* Search & Filter Bar */
    .filter-bar {
        background: white;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        border: 1px solid #f3f4f6;
    }

    .search-input-group {
        position: relative;
        flex-grow: 1;
    }

    .search-input-group .form-control {
        padding-left: 2.5rem;
        border-radius: 10px;
        border-color: #e5e7eb;
        height: 45px;
    }

    .search-input-group .fas-search {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        z-index: 4;
    }

    .filter-badge {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        background: #f3f4f6;
        color: #4b5563;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #e5e7eb;
    }

    .filter-badge .btn-close {
        font-size: 0.5rem;
        padding: 0;
        margin: 0;
    }

    .advanced-filters-card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #fdfdfb;
    }

    @media print {
        .no-print, .btn, .dropdown, .no-print *, .filter-bar, .advanced-filters-card {
            display: none !important;
        }
        
        body {
            background-color: white !important;
            padding: 0 !important;
        }
        
        .stunning-card {
            box-shadow: none !important;
            border: 1px solid #eee !important;
        }
        
        .card {
            border: 1px solid #eee !important;
            box-shadow: none !important;
        }
        
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function exportReport(format) {
    document.getElementById('exportFormat').value = format;
    document.getElementById('exportForm').submit();
}

function printReport() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Report Results Modernized View Ready');
});
</script>
@endpush