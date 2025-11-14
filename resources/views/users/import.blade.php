@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Import Users</h1>
            <p class="text-muted">Upload an Excel file to import multiple users at once</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('users') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-arrow-left"></i> Back to Users
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
                    
                    <form action="{{ route('users.import.process') }}" method="POST" enctype="multipart/form-data">
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
                            <strong>Note:</strong> Default password for imported users is <code>Password123!</code>. Users will be required to change their password on first login.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Import Users
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
                            <li><i class="fas fa-check text-success me-2"></i><strong>first_name</strong> - User's first name</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>last_name</strong> - User's last name</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>email</strong> - Unique email address</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>username</strong> - Unique username</li>
                            <li><i class="fas fa-check text-success me-2"></i><strong>access_type</strong> - judge, staff, registry, or admin</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">Optional Columns:</h6>
                        <ul class="list-unstyled ms-3">
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>phone</strong> - Phone number</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>password</strong> - Custom password (default: Password123!)</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>court</strong> - Court name or code</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>location</strong> - Location name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>roles</strong> - Comma-separated role names</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>status</strong> - active, inactive, or suspended</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>registry_officer</strong> - Registry officer email or name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>is_approved</strong> - yes/no, 1/0</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>block</strong> - yes/no, 1/0</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>require_password_reset</strong> - yes/no, 1/0</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>First row must contain column headers</li>
                            <li>Email and username must be unique</li>
                            <li>Court and location must exist in the system</li>
                            <li>Duplicate emails/usernames will be skipped</li>
                            <li>Use comma to separate multiple roles</li>
                        </ul>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('users.import.template') }}" class="btn btn-outline-primary">
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
                                    <th>first_name</th>
                                    <th>last_name</th>
                                    <th>email</th>
                                    <th>username</th>
                                    <th>phone</th>
                                    <th>access_type</th>
                                    <th>court</th>
                                    <th>location</th>
                                    <th>roles</th>
                                    <th>status</th>
                                    <th>is_approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John</td>
                                    <td>Mensah</td>
                                    <td>john.mensah@court.gov.gh</td>
                                    <td>jmensah</td>
                                    <td>0244123456</td>
                                    <td>judge</td>
                                    <td>HC-ACC-001</td>
                                    <td>Accra</td>
                                    <td>Judge,Case Manager</td>
                                    <td>active</td>
                                    <td>yes</td>
                                </tr>
                                <tr>
                                    <td>Sarah</td>
                                    <td>Osei</td>
                                    <td>sarah.osei@court.gov.gh</td>
                                    <td>sosei</td>
                                    <td>0201234567</td>
                                    <td>registry</td>
                                    <td>Kumasi District Court</td>
                                    <td>Kumasi</td>
                                    <td>Registry Officer</td>
                                    <td>active</td>
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