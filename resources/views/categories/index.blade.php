@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0 fw-bold">Categories Management</h4>
                <p class="text-muted text-small mb-0">Organize assets into standardized categories.</p>
            </div>
            <div>
                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-dark rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add Category
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Categories</div>
                <div class="metric-v2-value">{{ $categories->count() }}</div>
                <div class="text-tiny text-muted mt-2">All Registered</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-folder"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Main Categories</div>
                <div class="metric-v2-value">{{ $mainCategories->count() }}</div>
                <div class="text-tiny text-success mt-2">Top Level</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-sitemap"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Subcategories</div>
                <div class="metric-v2-value">{{ $categories->whereNotNull('parent_id')->count() }}</div>
                <div class="text-tiny text-muted mt-2">Nested Items</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-folder-tree"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stunning-card metric-v2">
            <div>
                <div class="metric-v2-label">Total Assets</div>
                <div class="metric-v2-value">{{ $categories->sum(fn($cat) => $cat->assets->count()) }}</div>
                <div class="text-tiny text-muted mt-2">Categorized</div>
            </div>
            <div class="metric-v2-icon">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="col-12">
        <div class="stunning-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-tiny fw-bold text-muted">Category Info</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Parent</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Description</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Assets</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Subcategories</th>
                            <th class="text-center text-uppercase text-tiny fw-bold text-muted">Status</th>
                            <th class="text-uppercase text-tiny fw-bold text-muted">Added By</th>
                            <th class="text-end pe-4 text-uppercase text-tiny fw-bold text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white rounded me-3" style="width: 36px; height: 36px;">
                                        <i class="fas fa-folder fa-sm"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-small text-dark">{{ $category->name }}</div>
                                        <div class="text-tiny text-muted">{{ $category->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-light text-dark border fw-normal text-tiny">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-tiny text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-tiny text-muted text-truncate" style="max-width: 200px;">
                                    {{ Str::limit($category->description, 50) ?: 'No description' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border-0 fw-bold rounded-pill text-tiny">{{ $category->assets->count() }}</span>
                            </td>
                            <td class="text-center">
                                @if($category->children->count() > 0)
                                    <span class="badge bg-info-subtle text-info border-0 fw-bold rounded-pill text-tiny">{{ $category->children->count() }}</span>
                                @else
                                    <span class="text-tiny text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                    <span class="badge bg-success-subtle text-success border-0 fw-bold rounded-pill text-tiny">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border-0 fw-bold rounded-pill text-tiny">Inactive</span>
                                @endif
                            </td>
                            <td class="text-small text-muted">
                                <div class="fw-bold">{{ $category->creator->name ?? 'Superadmin' }}</div>
                                <div class="text-tiny">{{ $category->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-icon btn-sm btn-light border rounded-circle text-muted" title="Edit"><i class="fas fa-edit fa-xs"></i></a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm btn-light border rounded-circle text-danger" title="Delete" onclick="return confirm('Delete this category?')">
                                            <i class="fas fa-trash-alt fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="fas fa-folder-open fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">No Categories Found</h6>
                                    <p class="text-muted text-small mb-0">Create a category to get started.</p>
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
@endsection