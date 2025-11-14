@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Categories Management</h2>
            <p class="text-muted mb-0">Organize assets into categories and subcategories</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('categories.create') }}" class="btn btn-primary px-4">
                <i class="fas fa-plus me-2"></i>Add New Category
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper primary-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Categories</h6>
                            <h3 class="mb-0">{{ $categories->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper success-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Main Categories</h6>
                            <h3 class="mb-0">{{ $mainCategories->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper info-icon">
                                <i class="fas fa-folder-tree"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Subcategories</h6>
                            <h3 class="mb-0">{{ $categories->whereNotNull('parent_id')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper warning-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 stat-details">
                            <h6 class="mb-1">Total Assets</h6>
                            <h3 class="mb-0">{{ $categories->sum(function($cat) { return $cat->assets->count(); }) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-list text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">All Categories</h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">CATEGORY NAME</th>
                            <th class="py-3">PARENT CATEGORY</th>
                            <th class="py-3">DESCRIPTION</th>
                            <th class="py-3">ASSETS COUNT</th>
                            <th class="py-3">SUBCATEGORIES</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="category-icon-wrapper bg-primary text-white">
                                            <i class="fas fa-folder"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $category->name }}</div>
                                        <small class="text-muted">{{ $category->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-light text-dark">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ Str::limit($category->description, 50) ?: 'No description' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $category->assets->count() }}</span>
                            </td>
                            <td>
                                @if($category->children->count() > 0)
                                    <span class="badge bg-info rounded-pill">{{ $category->children->count() }} subcategories</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $category->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-btn-group">
                                    <a href="{{ route('categories.edit', $category) }}" 
                                       class="action-btn edit-btn" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Category">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" 
                                                data-bs-toggle="tooltip" 
                                                title="Delete Category"
                                                onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrapper">
                                    <i class="fas fa-folder-open"></i>
                                    <h5>No categories found</h5>
                                    <p>Get started by creating your first category</p>
                                    <a href="{{ route('categories.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Create Category
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.category-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush
@endsection