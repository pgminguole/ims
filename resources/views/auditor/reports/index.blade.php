@extends('layouts.app')

@section('title', 'Audit Reports')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center py-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar me-2"></i>Audit Reports
        </h1>
    </div>

    <div class="row g-4">
        <!-- Report Types Grid -->
        <div class="col-xl-9">
            <!-- Generate Report Section -->
            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-file-alt text-primary me-2"></i>Generate Detailed Audit Report
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Select a report type and apply filters to generate comprehensive audit data.</p>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('auditor.reports.generate') }}" method="POST" id="reportForm">
                        @csrf
                        
                        <!-- Report Type Selection -->
                        <div class="mb-5">
                            <label class="form-label text-uppercase fs-7 fw-bold text-muted mb-3 d-block">1. Select Report Type</label>
                            <div class="row row-cols-2 row-cols-md-3 g-4">
                                <div class="col">
                                    <label class="report-card-label" for="assets_report">
                                        <input class="report-card-input" type="radio" name="report_type" id="assets_report" value="assets" checked>
                                        <div class="report-card stunning-card">
                                            <div class="report-card-icon bg-primary-subtle text-primary">
                                                <i class="fas fa-laptop"></i>
                                            </div>
                                            <div class="report-card-title">Assets</div>
                                            <div class="report-card-check"><i class="fas fa-check-circle"></i></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col">
                                    <label class="report-card-label" for="users_report">
                                        <input class="report-card-input" type="radio" name="report_type" id="users_report" value="users">
                                        <div class="report-card stunning-card">
                                            <div class="report-card-icon bg-success-subtle text-success">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="report-card-title">Users</div>
                                            <div class="report-card-check"><i class="fas fa-check-circle"></i></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col">
                                    <label class="report-card-label" for="courts_report">
                                        <input class="report-card-input" type="radio" name="report_type" id="courts_report" value="courts">
                                        <div class="report-card stunning-card">
                                            <div class="report-card-icon bg-info-subtle text-info">
                                                <i class="fas fa-gavel"></i>
                                            </div>
                                            <div class="report-card-title">Courts</div>
                                            <div class="report-card-check"><i class="fas fa-check-circle"></i></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col">
                                    <label class="report-card-label" for="dts_report">
                                        <input class="report-card-input" type="radio" name="report_type" id="dts_report" value="dts">
                                        <div class="report-card stunning-card">
                                            <div class="report-card-icon bg-warning-subtle text-warning">
                                                <i class="fas fa-microphone"></i>
                                            </div>
                                            <div class="report-card-title">DTS</div>
                                            <div class="report-card-check"><i class="fas fa-check-circle"></i></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col">
                                    <label class="report-card-label" for="offices_report">
                                        <input class="report-card-input" type="radio" name="report_type" id="offices_report" value="offices">
                                        <div class="report-card stunning-card">
                                            <div class="report-card-icon bg-dark-subtle text-dark">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div class="report-card-title">Offices</div>
                                            <div class="report-card-check"><i class="fas fa-check-circle"></i></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col">
                                    <label class="report-card-label" for="summary_report">
                                        <input class="report-card-input" type="radio" name="report_type" id="summary_report" value="summary">
                                        <div class="report-card stunning-card">
                                            <div class="report-card-icon bg-danger-subtle text-danger">
                                                <i class="fas fa-chart-pie"></i>
                                            </div>
                                            <div class="report-card-title">Summary</div>
                                            <div class="report-card-check"><i class="fas fa-check-circle"></i></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="bg-warm-light rounded-4 p-4 mb-4 border border-faint">
                             <h6 class="text-uppercase fs-7 fw-bold text-muted mb-4 d-flex align-items-center">
                                 <i class="fas fa-filter me-2 text-tiny"></i> 2. Report Filters
                             </h6>
                             <div class="row g-4">
                                 <div class="col-lg-5">
                                     <label class="form-label fs-7 fw-bold text-dark-soft">Search by Name</label>
                                     <input type="text" name="search" class="form-control rounded-3 border-faint" placeholder="Search...">
                                 </div>
                                 <div class="col-lg-7">
                                     <label class="form-label fs-7 fw-bold text-dark-soft">Date Range</label>
                                     <div class="range-picker d-flex align-items-center bg-white rounded-3 border px-2 py-1">
                                         <input type="date" name="start_date" class="form-control border-0 bg-transparent text-small px-2">
                                         <span class="px-2 text-muted text-tiny">to</span>
                                         <input type="date" name="end_date" class="form-control border-0 bg-transparent text-small px-2">
                                     </div>
                                 </div>
                                 
                                 
                                 <div class="col-lg-5 col-md-6">
                                     <label class="form-label fs-7 fw-bold text-dark-soft">Region</label>
                                     <select name="region_id" class="form-select select2-modern" data-placeholder="Select Region">
                                         <option value=""></option>
                                         <option value="">All Regions</option>
                                         @foreach($filterData['regions'] as $region)
                                             <option value="{{ $region->id }}">{{ $region->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div class="col-lg-4 col-md-6">
                                     <label class="form-label fs-7 fw-bold text-dark-soft">Court Type</label>
                                     <select name="court_type" class="form-select select2-modern" data-placeholder="All Types">
                                         <option value=""></option>
                                         <option value="">All Court Types</option>
                                         @foreach($filterData['courtTypes'] as $type)
                                             <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div class="col-lg-4 col-md-6">
                                     <label class="form-label fs-7 fw-bold text-dark-soft">User Type</label>
                                     <select name="user_type" class="form-select select2-modern" data-placeholder="All Users">
                                          <option value=""></option>
                                         <option value="">All User Types</option>
                                         @foreach($filterData['userTypes'] as $type)
                                             <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div class="col-lg-4 col-md-6">
                                     <label class="form-label fs-7 fw-bold text-dark-soft">Asset Status</label>
                                     <select name="asset_status" class="form-select select2-modern" data-placeholder="All Status">
                                         <option value=""></option>
                                         <option value="">All Status</option>
                                         <option value="available">Available</option>
                                         <option value="assigned">Assigned</option>
                                         <option value="maintenance">Maintenance</option>
                                     </select>
                                 </div>
                             </div>
                        </div>

                        <!-- Report Options & Actions -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-4 mt-4 bg-light rounded-4 p-4 border border-faint">
                            <div class="row g-3 flex-grow-1">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold text-muted text-uppercase mb-2">3. Output Format</label>
                                    <div class="d-flex gap-2">
                                        <div class="format-option flex-grow-1">
                                            <input type="radio" name="format" id="format_html" value="html" checked class="btn-check">
                                            <label class="btn btn-outline-light-custom text-small px-3 py-2 rounded-3 w-100 h-100 d-flex align-items-center justify-content-center" for="format_html">
                                                <i class="fas fa-globe me-2"></i>Web
                                            </label>
                                        </div>
                                        <div class="format-option flex-grow-1">
                                            <input type="radio" name="format" id="format_pdf" value="pdf" class="btn-check">
                                            <label class="btn btn-outline-light-custom text-small px-3 py-2 rounded-3 w-100 h-100 d-flex align-items-center justify-content-center" for="format_pdf">
                                                <i class="fas fa-file-pdf me-2"></i>PDF
                                            </label>
                                        </div>
                                        <div class="format-option flex-grow-1">
                                            <input type="radio" name="format" id="format_excel" value="excel" class="btn-check">
                                            <label class="btn btn-outline-light-custom text-small px-3 py-2 rounded-3 w-100 h-100 d-flex align-items-center justify-content-center" for="format_excel">
                                                <i class="fas fa-file-excel me-2"></i>Excel
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold text-muted text-uppercase mb-2">Charts & Visuals</label>
                                    <select name="include_charts" class="form-select select2-modern-simple">
                                        <option value="1">Include Statistical Charts</option>
                                        <option value="0">Data Tables Only</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 align-self-end">
                                <button type="reset" class="btn btn-light-modern btn-lg px-4 fs-7">Reset All</button>
                                <button type="submit" class="btn btn-primary-modern btn-lg px-5 shadow-gold">
                                    <i class="fas fa-wand-magic-sparkles me-2"></i>Generate Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark text-uppercase fs-7 d-flex align-items-center">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-grid gap-3">
                        <form action="{{ route('auditor.reports.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_type" value="assets">
                            <input type="hidden" name="format" value="html">
                            <button type="submit" class="quick-action-card stunning-card w-100 text-start p-3 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="qa-icon bg-primary-subtle text-primary">
                                        <i class="fas fa-laptop"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="qa-title">Assets Snapshot</div>
                                        <div class="qa-desc">Real-time inventory list</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted text-tiny"></i>
                                </div>
                            </button>
                        </form>
                        
                        <form action="{{ route('auditor.reports.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_type" value="summary">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="quick-action-card stunning-card w-100 text-start p-3 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="qa-icon bg-success-subtle text-success">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="qa-title">Monthly Summary</div>
                                        <div class="qa-desc">Current month overview</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted text-tiny"></i>
                                </div>
                            </button>
                        </form>
                        
                        <form action="{{ route('auditor.reports.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_type" value="dts">
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="quick-action-card stunning-card w-100 text-start p-3 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="qa-icon bg-warning-subtle text-warning">
                                        <i class="fas fa-microphone"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="qa-title">DTS Distribution</div>
                                        <div class="qa-desc">Systems across courts</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted text-tiny"></i>
                                </div>
                            </button>
                        </form>
                        
                        <form action="{{ route('auditor.reports.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_type" value="users">
                            <input type="hidden" name="format" value="html">
                            <button type="submit" class="quick-action-card stunning-card w-100 text-start p-3 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="qa-icon bg-info-subtle text-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="qa-title">User Assignments</div>
                                        <div class="qa-desc">Assigned assets lookup</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted text-tiny"></i>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Report Type Cards */
.report-card-label {
    width: 100%;
    cursor: pointer;
    margin-bottom: 0;
}

.report-card-input {
    display: none !important;
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.report-card {
    position: relative;
    text-align: center;
    padding: 1.25rem 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 2px solid transparent !important;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.report-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
    transition: transform 0.3s ease;
}

.report-card-title {
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--text-dark);
}

.report-card-check {
    position: absolute;
    top: 10px;
    right: 10px;
    color: var(--primary-gold);
    font-size: 0.9rem;
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.2s ease;
}

/* Selected State */
.report-card-input:checked + .report-card {
    border-color: var(--primary-gold) !important;
    background-color: #fdfaf0;
    box-shadow: 0 10px 25px rgba(255, 210, 93, 0.15) !important;
    transform: translateY(-5px);
}

.report-card-input:checked + .report-card .report-card-icon {
    transform: scale(1.1);
}

.report-card-input:checked + .report-card .report-card-check {
    opacity: 1;
    transform: scale(1);
}

/* Hover State */
.report-card-label:hover .report-card:not(.report-card-input:checked + .report-card) {
    transform: translateY(-3px);
    border-color: rgba(255, 210, 93, 0.3) !important;
}

/* Filter Section */
.bg-warm-light { background-color: #fdfdfb; }
.border-faint { border-color: rgba(0,0,0,0.04) !important; }
.text-dark-soft { color: #4b5563; }

.range-picker {
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb !important;
}

.range-picker:focus-within {
    border-color: var(--primary-gold) !important;
    box-shadow: 0 0 0 3px rgba(255, 210, 93, 0.1);
}

.range-picker .form-control {
    min-width: 140px;
    flex: 1;
}

/* Buttons */
.btn-primary-modern {
    background-color: var(--text-dark);
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    box-shadow: 0 4px 6px -1px rgba(255, 210, 93, 0.1), 0 2px 4px -1px rgba(255, 210, 93, 0.06);
}

/* Quick Action Cards */
.quick-action-card {
    transition: all 0.2s ease;
}

.quick-action-card:hover {
    transform: translateX(5px) !important;
    background-color: #fcfbf8 !important;
}

.qa-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.qa-title {
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--text-dark);
    line-height: 1.2;
}

.qa-desc {
    font-size: 0.7rem;
    color: var(--text-muted);
}

/* Format Radios */
.btn-outline-light-custom {
    border: 1px solid #e5e7eb;
    background: white;
    color: #4b5563;
    font-weight: 500;
}

.btn-check:checked + .btn-outline-light-custom {
    border-color: var(--primary-gold);
    background-color: #fdfaf0;
    color: #dfa615;
    font-weight: 700;
}

/* Modern Select2 */
.select2-modern + .select2-container--bootstrap-5 .select2-selection {
    border-radius: 10px !important;
    border-color: #e5e7eb !important;
    height: 44px !important;
    display: flex;
    align-items: center;
}

.select2-modern-simple + .select2-container--bootstrap-5 .select2-selection {
    border-radius: 10px !important;
    border: 1px solid #e5e7eb !important;
    height: 44px !important;
    background-color: white !important;
}

.fs-7 { font-size: 0.8rem; }

/* Subtle Animations */
@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

#reportForm {
    animation: fadeInScale 0.4s ease-out;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range to current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    document.querySelector('input[name="start_date"]').value = firstDay.toISOString().split('T')[0];
    document.querySelector('input[name="end_date"]').value = lastDay.toISOString().split('T')[0];
    
    // Set current year as default
    document.querySelector('select[name="year"]').value = today.getFullYear();
});
</script>
@endpush