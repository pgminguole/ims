@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Import Courts</h1>
            <p class="text-muted">Upload an Excel file to import multiple courts at once</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courts') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-arrow-left"></i> Back to Courts
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
        <ul class="mb-0">
            @foreach(session('errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Import Form -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Upload Excel File</h5>
                    
                    <form action="{{ route('courts.import.process') }}" method="POST" enctype="multipart/form-data">
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Import Courts
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Import Instructions</h5>
                    
                    <div class="mb-4">
                        <h6 class="text-primary">Required Columns:</h6>
                        <ul class="list-unstyled ms-3">
                            <li><i class="fas fa-check text-success me-2"></i><strong>name</strong> - Court name</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>code</strong> - Unique court code</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>type</strong> - Court type (high_court, district_court, magistrate_court, special_court)</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>region</strong> - Region name or code</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">Optional Columns:</h6>
                        <ul class="list-unstyled ms-3">
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>location</strong> - Location name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>address</strong> - Court address</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>presiding_judge</strong> - Judge email or full name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>registry_officer</strong> - Officer email or full name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>is_active</strong> - Status (yes/no, 1/0, active/inactive)</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>First row must contain column headers</li>
                            <li>Court codes must be unique</li>
                            <li>Region must exist in the system</li>
                            <li>Duplicate codes will be skipped</li>
                        </ul>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('courts.import.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Download Sample Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Preview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Example Excel Format</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>name</th>
                                    <th>code</th>
                                    <th>type</th>
                                    <th>region</th>
                                    <th>location</th>
                                    <th>address</th>
                                    <th>presiding_judge</th>
                                    <th>registry_officer</th>
                                    <th>is_active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Accra High Court</td>
                                    <td>HC-ACC-001</td>
                                    <td>high_court</td>
                                    <td>Greater Accra</td>
                                    <td>Accra</td>
                                    <td>High Street, Accra</td>
                                    <td>judge@example.com</td>
                                    <td>registry@example.com</td>
                                    <td>yes</td>
                                </tr>
                                <tr>
                                    <td>Kumasi District Court</td>
                                    <td>DC-KSI-001</td>
                                    <td>district_court</td>
                                    <td>Ashanti</td>
                                    <td>Kumasi</td>
                                    <td>Prempeh II Street, Kumasi</td>
                                    <td>John Doe</td>
                                    <td>Jane Smith</td>
                                    <td>1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection