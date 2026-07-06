@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1">Import Assets</h2>
            <p class="text-muted mb-0">Upload an Excel file to import multiple assets at once</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Assets
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('errors') && is_array(session('errors')))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Import Warnings</h5>
        <div style="max-height: 300px; overflow-y: auto;">
            <ul class="mb-0">
                @foreach(session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Import Form -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Upload Excel File</h5>
                    
                    <form action="{{ route('assets.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="file" class="form-label">Select Excel File</label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Accepted formats: .xlsx, .xls, .csv (Max size: 10MB)
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Import Tips:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Duplicate asset tags will be skipped</li>
                                <li>Category, region, and court must exist</li>
                                <li>Default status is "available"</li>
                                <li>Default condition is "good"</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Import Assets
                            </button>
                            <a href="{{ route('assets.import.template') }}" class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Download Sample Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Import Instructions</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-primary">Required Columns:</h6>
                                <ul class="list-unstyled ms-3">
                                    <li><i class="fas fa-check text-success me-2"></i><strong>asset_name</strong> - Asset name</li>
                                    <li><i class="fas fa-check text-success me-2"></i><strong>asset_tag</strong> - Unique asset tag/code</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-primary">Asset Details:</h6>
                                <ul class="list-unstyled ms-3 small">
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>serial_number</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>model</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>brand</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>manufacturer</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>description</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>specifications</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-primary">Location & Category:</h6>
                                <ul class="list-unstyled ms-3 small">
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>category (must exist)</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>subcategory</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>region (name or code)</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>court (name or code)</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>location</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-primary">Financial & Dates:</h6>
                                <ul class="list-unstyled ms-3 small">
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>purchase_cost</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>current_value</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>purchase_date</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>received_date</li>
                                    <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.4rem;"></i>supplier</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">Status & Condition:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small><strong>Status options:</strong></small>
                                <div class="small text-muted">available, assigned, maintenance, retired, lost, disposed</div>
                            </div>
                            <div class="col-6">
                                <small><strong>Condition options:</strong></small>
                                <div class="small text-muted">excellent, good, fair, poor, broken</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important Notes:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>First row must contain column headers (exactly as shown)</li>
                            <li>Asset tags must be unique across all assets</li>
                            <li>Categories, regions, and courts must exist in the system</li>
                            <li>Date format: YYYY-MM-DD or Excel date format</li>
                            <li>Duplicate asset tags will be skipped with a warning</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Preview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Example Excel Format (Showing Key Columns)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>asset_name</th>
                                    <th>asset_tag</th>
                                    <th>serial_number</th>
                                    <th>model</th>
                                    <th>brand</th>
                                    <th>category</th>
                                    <th>region</th>
                                    <th>court</th>
                                    <th>status</th>
                                    <th>condition</th>
                                    <th>purchase_date</th>
                                    <th>purchase_cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Dell Latitude Laptop</td>
                                    <td>LAP-ACC-001</td>
                                    <td>DL12345678</td>
                                    <td>Latitude 5420</td>
                                    <td>Dell</td>
                                    <td>Computers</td>
                                    <td>Greater Accra</td>
                                    <td>HC-ACC-001</td>
                                    <td>available</td>
                                    <td>excellent</td>
                                    <td>2024-01-15</td>
                                    <td>3500.00</td>
                                </tr>
                                <tr>
                                    <td>HP LaserJet Printer</td>
                                    <td>PRT-KSI-001</td>
                                    <td>HP987654321</td>
                                    <td>LaserJet Pro M404dn</td>
                                    <td>HP</td>
                                    <td>Office Equipment</td>
                                    <td>Ashanti</td>
                                    <td>Kumasi District Court</td>
                                    <td>assigned</td>
                                    <td>good</td>
                                    <td>2024-03-20</td>
                                    <td>1200.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        The template includes all available columns. Download the template to see the complete structure.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection