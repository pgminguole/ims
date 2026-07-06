@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header and alerts remain the same -->
    <!-- ... -->

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
                            <li><i class="fas fa-check text-success me-2"></i><strong>name</strong> - User's full name</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">Optional Columns:</h6>
                        <ul class="list-unstyled ms-3">
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>email</strong> - Email address (auto-generated if not provided)</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>phone</strong> - Phone number</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>role</strong> - User role (default: staff)</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>password</strong> - Custom password (default: Password123!)</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>court</strong> - Court name or code</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>location</strong> - Location name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>roles</strong> - Comma-separated Spatie role names</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>status</strong> - active, inactive, or suspended</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>registry_officer</strong> - Registry officer email or name</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>is_approved</strong> - yes/no, 1/0</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>block</strong> - yes/no, 1/0</li>
                            <li><i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i><strong>require_password_reset</strong> - yes/no, 1/0</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-primary">Available Roles:</h6>
                        <ul class="list-unstyled ms-3">
                            <li><code>admin</code> - System Administrator</li>
                            <li><code>auditor</code> - Auditor</li>
                            <li><code>ict_staff</code> - ICT Staff</li>
                            <li><code>registry</code> - Registry Staff</li>
                            <li><code>judge</code> - Judge</li>
                            <li><code>staff</code> - General Staff (Default)</li>
                            <li><code>director</code> - Director</li>
                            <li><code>deputy_director</code> - Deputy Director</li>
                            <li><code>rao</code> - Regional Administrator</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>First row must contain column headers</li>
                            <li>Only <strong>name</strong> is required - all other fields are optional</li>
                            <li>If email is not provided, a unique identifier will be auto-generated</li>
                            <li>If role is not provided, default role "staff" will be assigned</li>
                            <li>Court and location must exist in the system</li>
                            <li>Duplicate emails will be skipped</li>
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
                                    <th>name</th>
                                    <th>email</th>
                                    <th>phone</th>
                                    <th>role</th>
                                    <th>court</th>
                                    <th>location</th>
                                    <th>roles</th>
                                    <th>status</th>
                                    <th>is_approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Mensah</td>
                                    <td>john.mensah@court.gov.gh</td>
                                    <td>0244123456</td>
                                    <td>judge</td>
                                    <td>HC-ACC-001</td>
                                    <td>Accra</td>
                                    <td>Judge,Case Manager</td>
                                    <td>active</td>
                                    <td>yes</td>
                                </tr>
                                <tr>
                                    <td>Sarah Osei</td>
                                    <td></td> <!-- No email provided -->
                                    <td>0201234567</td>
                                    <td></td> <!-- No role provided - will default to staff -->
                                    <td>Kumasi District Court</td>
                                    <td>Kumasi</td>
                                    <td>Registry Officer</td>
                                    <td>active</td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td>Kwame Asante</td>
                                    <td>kwame.asante@court.gov.gh</td>
                                    <td>0554567890</td>
                                    <td>director</td>
                                    <td>DC-KSI-001</td>
                                    <td>Kumasi</td>
                                    <td>Director,Document Manager</td>
                                    <td>active</td>
                                    <td>yes</td>
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