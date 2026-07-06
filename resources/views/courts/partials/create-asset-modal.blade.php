<!-- Create Asset Modal -->
<div class="modal fade" id="createAssetModal" tabindex="-1" aria-labelledby="createAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssetModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Create New Asset for {{ $court->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.create-asset', $court) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasAssignedRole('super_admin'))
                         <div class="col-md-12">
                            <label class="form-label fw-semibold">Record Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="record_type" required>
                                <option value="assignment">Official Assignment</option>
                                <option value="inventory">Inventory Collection</option>
                            </select>
                        </div>
                        @endif
                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="create_asset_category" required>
                                <option value="">Select Category</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" 
                                   min="1" max="50" value="1" required>
                            <small class="text-muted">Number of assets to create</small>
                        </div>

                        <!-- Brand -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand</label>
                            <input type="text" class="form-control" name="brand" 
                                   placeholder="e.g., Dell, HP">
                        </div>

                        <!-- Model -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Model</label>
                            <input type="text" class="form-control" name="model" 
                                   placeholder="e.g., Latitude 5420">
                        </div>

                        <!-- Assigned Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="assigned_date" 
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <!-- Assigned To User -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Directly Assign to User (Optional)</label>
                            <select class="form-select select2" name="assigned_to">
                                <option value="">No, leave assigned to Court</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted text-tiny">If selected, the asset will be assigned to this user instead of just the court.</small>
                        </div>

                        <!-- Condition -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                            <select class="form-select" name="condition" required>
                                <option value="excellent">Excellent</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>

                        <!-- Warranty Months -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warranty (Months)</label>
                            <input type="number" class="form-control" name="warranty_months" 
                                   placeholder="e.g., 24" min="0">
                        </div>

                        <!-- Purchase Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Purchase Date</label>
                            <input type="date" class="form-control" name="purchase_date">
                        </div>

                        <!-- Purchase Price -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Purchase Price (Per Asset)</label>
                            <input type="number" step="0.01" class="form-control" name="purchase_price" 
                                   placeholder="0.00" min="0">
                        </div>

                        <!-- Comments -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Comments</label>
                            <textarea class="form-control" name="comments" rows="3" 
                                      placeholder="Any additional notes about this asset..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Asset(s)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>