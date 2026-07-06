<!-- Create Asset Modal -->
<div class="modal fade" id="createAssetModal" tabindex="-1" aria-labelledby="createAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssetModalLabel">
                    <i class="fas fa-plus-circle"></i> Create New Asset for {{ $court->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.create-asset', $court) }}" method="POST">
                @csrf
                <div class="modal-body bg-light-subtle">
                    <div class="row g-3">
                        
                        <!-- Section: Classification & Basics -->
                        <div class="col-12">
                            <div class="modal-section">
                                <div class="modal-section-title">
                                    <i class="fas fa-tags text-primary"></i> Classification & Basics
                                </div>
                                <div class="row g-3">
                                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasAssignedRole('super_admin'))
                                    <div class="col-md-12">
                                        <label class="form-label">Record Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="record_type" required>
                                            <option value="assignment">Official Assignment</option>
                                            <option value="inventory">Inventory Collection</option>
                                        </select>
                                    </div>
                                    @endif

                                    <!-- Category -->
                                    <div class="col-md-6">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="category_id" id="create_asset_category" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories ?? [] as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-md-6">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="quantity" min="1" max="50" value="1" required>
                                            <span class="input-group-text bg-light text-muted">units</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Hardware Details -->
                        <div class="col-12">
                            <div class="modal-section">
                                <div class="modal-section-title">
                                    <i class="fas fa-laptop text-primary"></i> Hardware Details
                                </div>
                                <div class="row g-3">
                                    <!-- Brand -->
                                    <div class="col-md-6">
                                        <label class="form-label">Brand</label>
                                        <input type="text" class="form-control" name="brand" placeholder="e.g., Dell, HP">
                                    </div>

                                    <!-- Model -->
                                    <div class="col-md-6">
                                        <label class="form-label">Model</label>
                                        <input type="text" class="form-control" name="model" placeholder="e.g., Latitude 5420">
                                    </div>
                                    
                                    <!-- Condition -->
                                    <div class="col-md-6">
                                        <label class="form-label">Condition <span class="text-danger">*</span></label>
                                        <select class="form-select" name="condition" required>
                                            <option value="excellent">Excellent</option>
                                            <option value="good" selected>Good</option>
                                            <option value="fair">Fair</option>
                                            <option value="poor">Poor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Assignment & Logistics -->
                        <div class="col-12">
                            <div class="modal-section mb-0">
                                <div class="modal-section-title">
                                    <i class="fas fa-truck-loading text-primary"></i> Assignment & Financials
                                </div>
                                <div class="row g-3">
                                    <!-- Assigned Date -->
                                    <div class="col-md-6">
                                        <label class="form-label">Assigned Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="assigned_date" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>

                                    <!-- Assigned To User -->
                                    <div class="col-md-6">
                                        <label class="form-label">Direct Assignment (Optional)</label>
                                        <select class="form-select select2" name="assigned_to">
                                            <option value="">Leave assigned to Court only</option>
                                            @foreach($users ?? [] as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Warranty Months -->
                                    <div class="col-md-4">
                                        <label class="form-label">Warranty</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="warranty_months" placeholder="24" min="0">
                                            <span class="input-group-text bg-light text-muted">mos</span>
                                        </div>
                                    </div>

                                    <!-- Purchase Date -->
                                    <div class="col-md-4">
                                        <label class="form-label">Purchase Date</label>
                                        <input type="date" class="form-control" name="purchase_date">
                                    </div>

                                    <!-- Purchase Price -->
                                    <div class="col-md-4">
                                        <label class="form-label">Price (Per Asset)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">GH₵</span>
                                            <input type="number" step="0.01" class="form-control" name="purchase_price" placeholder="0.00" min="0">
                                        </div>
                                    </div>

                                    <!-- Comments -->
                                    <div class="col-md-12">
                                        <label class="form-label">Comments</label>
                                        <textarea class="form-control" name="comments" rows="2" placeholder="Any additional notes about this asset..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus me-2"></i>Create Asset(s)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>