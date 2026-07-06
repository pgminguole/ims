@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('categories') }}" class="text-decoration-none text-muted">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ isset($category) ? 'Edit' : 'Create New' }}</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">{{ isset($category) ? 'Edit Category' : 'Create New Category' }}</h4>
            <p class="text-tiny text-muted mb-0">{{ isset($category) ? 'Update category details' : 'Add a new category to the system' }}</p>
        </div>
        <div>
            <a href="{{ route('categories') }}" class="btn btn-sm btn-light border rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="stunning-card">
                <div class="card-header-clean">
                    <h6 class="card-title-small">Category Details</h6>
                </div>
                <div class="p-4 pt-1">
                    <form method="POST" action="{{ isset($category) ? route('categories.edit', $category) : route('categories.create') }}">
                        @csrf
                        @if(isset($category))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Category Name *</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $category->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Parent Category</label>
                                <select class="form-select form-select-sm @error('parent_id') is-invalid @enderror" name="parent_id">
                                    <option value="">— No Parent (Main Category) —</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-tiny fw-bold text-uppercase text-muted">Description</label>
                                <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                          name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', isset($category) ? $category->is_active : true) ? 'checked' : '' }}>
                                    <label class="form-check-label text-small" for="is_active">Active Category</label>
                                </div>
                                <div class="form-text text-tiny">Inactive categories won't be available for new assets.</div>
                            </div>
                            
                            <div class="col-12 pt-2">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-dark rounded-pill shadow-sm">
                                        <i class="fas fa-save me-1"></i> {{ isset($category) ? 'Update Category' : 'Create Category' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection