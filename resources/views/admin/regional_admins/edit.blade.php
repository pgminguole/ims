@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
     <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('regional-admins.index') }}" class="text-decoration-none text-muted">Regional ICT Admins</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('regional-admins.show', $regionalAdmin) }}" class="text-decoration-none text-muted">{{ $regionalAdmin->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Edit ICT Administrator</h4>
        </div>
        <div>
            <a href="{{ route('regional-admins.show', $regionalAdmin) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="stunning-card">
                 <div class="card-header-clean">
                    <h6 class="card-title-small">Edit Details & Permissions</h6>
                </div>
                <div class="p-4 pt-1">
                     <form method="POST" action="{{ route('regional-admins.update', $regionalAdmin) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Full Name *</label>
                                <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name', $regionalAdmin->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Email *</label>
                                <input type="email" class="form-control form-control-sm" name="email" value="{{ old('email', $regionalAdmin->email) }}" required>
                            </div>
                             <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Phone</label>
                                <input type="text" class="form-control form-control-sm" name="phone" value="{{ old('phone', $regionalAdmin->phone) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Region *</label>
                                <select class="form-select form-select-sm" name="region_id" required>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $regionalAdmin->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">New Password (leave blank to keep current)</label>
                                <input type="password" class="form-control form-control-sm" name="password">
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <h6 class="card-title-small mb-3">Permissions</h6>
                        <div class="row g-2">
                             @foreach($permissions as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                            id="perm_{{ $permission->id }}"
                                            {{ $regionalAdmin->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                        <label class="form-check-label text-tiny" for="perm_{{ $permission->id }}">
                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark rounded-pill shadow-sm px-4">
                                <i class="fas fa-save me-1"></i> Update Administrator
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
