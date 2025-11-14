@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <!-- Header -->
   <!-- Replace the header section in your index view with this -->
<div class="row mb-4">
    <div class="col">
        <h1 class="text-heading mb-0">Courts Management</h1>
        <p class="text-muted">Manage court information and assignments</p>
    </div>
    <div class="col-auto">
        <div class="btn-group-compact">
            <a href="{{ route('courts.import.form') }}" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-upload"></i> Import
            </a>
            <a href="{{ route('courts.create') }}" class="btn btn-primary btn-compact">
                <i class="fas fa-plus"></i> Add Court
            </a>
        </div>
    </div>
</div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-compact">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-circle primary me-3">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $totalCourts }}</div>
                        <div class="stat-label">Total Courts</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-compact">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-circle success me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $activeCourts }}</div>
                        <div class="stat-label">Active Courts</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-compact">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-circle warning me-3">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $highCourts }}</div>
                        <div class="stat-label">High Courts</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-compact">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-circle info me-3">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $districtCourts }}</div>
                        <div class="stat-label">District Courts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('courts') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                           placeholder="Search courts...">
                </div>
                <div class="form-group">
                    <label class="form-label">Region</label>
                    <select class="form-select" name="region_id">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Court Type</label>
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="high_court" {{ request('type') == 'high_court' ? 'selected' : '' }}>High Court</option>
                        <option value="district_court" {{ request('type') == 'district_court' ? 'selected' : '' }}>District Court</option>
                        <option value="magistrate_court" {{ request('type') == 'magistrate_court' ? 'selected' : '' }}>Magistrate Court</option>
                        <option value="special_court" {{ request('type') == 'special_court' ? 'selected' : '' }}>Special Court</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-compact w-100">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Courts Table -->
    <div class="table-responsive-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Court Information</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Total Assets</th>
                        <th>Computers</th>
                        <th>Laptops</th>
                        <th>DTS</th>
                        <th>UPS</th>
                        <th>Stabilizers</th>
                        <th>Photocopiers</th>
                        <th>Printers</th>
                        <th>Scanners</th>
                        <th>Personnel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courts as $court)
                    <tr>
                        <td>
                            <div class="asset-name-text">{{ $court->name }}</div>
                            <div class="asset-brand-text">Code: {{ $court->code }}</div>
                            <div class="asset-brand-text">{{ $court->region->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->location->name ?? 'N/A' }}</div>
                            <div class="asset-brand-text text-truncate" style="max-width: 200px;" title="{{ $court->address }}">
                                {{ Str::limit($court->address, 50) }}
                            </div>
                        </td>
                        <td>
                            <span class="court-type-badge {{ $court->type }}">
                                {{ str_replace('_', ' ', ucfirst($court->type)) }}
                            </span>
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->totalAssets ?? '0' }}</div>
                       
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->computers ?? '0' }}</div>

                        </td>
                              <td>
                            <div class="detail-value">{{ $court->laptops ?? '0' }}</div>
                        </td>


                        <td>
                            <div class="detail-value">{{ $court->dts ?? '0' }}</div>
                   
                        </td>
                           <td>
                            <div class="detail-value">{{ $court->ups ?? '0' }}</div>
                   
                        </td>
                           <td>
                            <div class="detail-value">{{ $court->stabilizers ?? '0' }}</div>
                   
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->photocopiers ?? '0' }}</div>
                
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->printers ?? '0' }}</div>
                      
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->scanners ?? '0' }}</div>
                   
                        </td>
                        <td>
                            <div class="detail-value">{{ $court->presidingJudge->full_name ?? 'N/A' }}</div>
                            <div class="asset-brand-text">Presiding Judge</div>
                        </td>
                        
                     
                        <td>
                            <div class="action-btn-group">
                                <a href="{{ route('courts.show', $court) }}" class="action-btn-compact view-btn" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('courts.edit', $court) }}" class="action-btn-compact edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('courts.destroy', $court) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-compact delete-btn" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this court?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state-compact">
                                <i class="fas fa-gavel"></i>
                                <h5>No Courts Found</h5>
                                <p>No courts match your search criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($courts->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Showing {{ $courts->firstItem() }} to {{ $courts->lastItem() }} of {{ $courts->total() }} results
        </div>
        {{ $courts->links() }}
    </div>
    @endif
</div>
@endsection