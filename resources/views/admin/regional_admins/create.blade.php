@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('regional-admins.index') }}" class="text-decoration-none text-muted">Regional ICT Admins</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Add Regional ICT Administrator</h4>
            <p class="text-tiny text-muted mb-0">Create valid login credentials for a new Regional ICT Administrator.</p>
        </div>
        <div>
            <a href="{{ route('regional-admins.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="stunning-card mb-4">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Account Details</h6>
                </div>
                <div class="p-4 pt-1">
                    <form method="POST" action="{{ route('regional-admins.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Full Name *</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Email Address *</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Phone Number</label>
                                <input type="text" class="form-control form-control-sm" name="phone" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region Assignment *</label>
                                <select class="form-select form-select-sm @error('region_id') is-invalid @enderror" name="region_id" required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Password *</label>
                                <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" name="password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Confirm Password *</label>
                                <input type="password" class="form-control form-control-sm" name="password_confirmation" required>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <h6 class="card-title-small mb-3">Permissions</h6>
                        <div class="row g-2">
                             @foreach($permissions as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                        <label class="form-check-label text-tiny" for="perm_{{ $permission->id }}">
                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark rounded-pill shadow-sm px-4">
                                <i class="fas fa-save me-1"></i> Create ICT Administrator
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
