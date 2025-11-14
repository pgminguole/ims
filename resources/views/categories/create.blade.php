@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-{{ isset($category) ? 'edit' : 'plus' }} text-primary me-2"></i>
                        <h5 class="mb-0 fw-semibold">
                            {{ isset($category) ? 'Edit Category' : 'Create New Category' }}
                        </h5>
                        <a href="{{ route('categories') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="{{ isset($category) ? route('categories.edit', $category) : route('categories.create') }}">
                        @csrf
                        @if(isset($category))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $category->name ?? '') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="parent_id" class="form-label">Parent Category</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" 
                                        id="parent_id" 
                                        name="parent_id">
                                    <option value="">— No Parent (Main Category) —</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" 
                                                {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', isset($category) ? $category->is_active : true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Category
                                    </label>
                                </div>
                                <small class="text-muted">Inactive categories won't be available for new assets</small>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>
                                        {{ isset($category) ? 'Update Category' : 'Create Category' }}
                                    </button>
                                    <a href="{{ route('categories') }}" class="btn btn-outline-secondary px-4">
                                        Cancel
                                    </a>
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