<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                     <h5 class="modal-title fw-bold text-dark">Assign Asset</h5>
                    <p class="text-muted text-small mb-0">Select an asset to assign to {{ $user->name }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($availableAssets->count() > 0)
                    <div class="alert alert-light border text-small mb-3 rounded-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Only assets without office/court assignment are listed here.
                    </div>
                    <div class="table-responsive rounded-3 border" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="text-tiny text-uppercase text-muted fw-bold ps-3 py-2">Asset Name</th>
                                    <th class="text-tiny text-uppercase text-muted fw-bold py-2">Tag</th>
                                    <th class="text-tiny text-uppercase text-muted fw-bold py-2">Category</th>
                                    <th class="text-tiny text-uppercase text-muted fw-bold pe-3 py-2 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableAssets as $asset)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="text-small fw-bold text-dark">{{ $asset->asset_name }}</div>
                                    </td>
                                    <td class="py-3"><div class="badge bg-light text-dark border font-monospace">{{ $asset->asset_tag }}</div></td>
                                    <td class="py-3"><div class="text-small text-muted">{{ $asset->category->name ?? '-' }}</div></td>
                                    <td class="pe-3 py-3 text-end">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm assign-asset-btn" 
                                                data-asset-id="{{ $asset->id }}"
                                                data-user-id="{{ $user->id }}">
                                            Assign
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-box-open fa-3x text-muted opacity-25"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No Assets Available</h6>
                        <p class="text-muted text-small mb-0">There are no assets currently available for assignment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Asset Modal -->
<div class="modal fade" id="createAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark">Create & Assign Asset</h5>
                    <p class="text-muted text-small mb-0">Add a new asset and immediately assign to this user.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             <form action="{{ route('users.create-asset', $user) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasAssignedRole('super_admin'))
                         <div class="col-md-12">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Record Type <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="record_type" required>
                                <option value="assignment">Official Assignment</option>
                                <option value="inventory">Inventory Collection</option>
                            </select>
                        </div>
                        @endif
                         <div class="col-md-6">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="category_id" required>
                                <option value="">Select Category...</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Brand / Model</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" name="brand" placeholder="Brand (e.g. HP)">
                                <input type="text" class="form-control form-control-lg" name="model" placeholder="Model (e.g. EliteBook)">
                            </div>
                        </div>
                         <div class="col-md-4">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Quantity</label>
                            <input type="number" class="form-control form-control-lg" name="quantity" value="1" min="1" max="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Condition</label>
                             <select class="form-select form-select-lg" name="condition">
                                <option value="good">Good</option>
                                <option value="new">New</option>
                                <option value="fair">Fair</option>
                            </select>
                        </div>
                         <div class="col-md-4">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Assigned Date</label>
                            <input type="date" class="form-control form-control-lg" name="assigned_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-small fw-bold text-uppercase text-muted">Notes</label>
                            <textarea class="form-control" name="comments" rows="2" placeholder="Start typing..."></textarea>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 shadow-sm">Create & Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Asset Confirmation Modal -->
<div class="modal fade" id="removeAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-4">
                <i class="fas fa-exclamation-circle text-warning fa-3x mb-3"></i>
                <h6 class="fw-bold mb-2">Unassign Asset?</h6>
                <p class="text-muted text-small mb-4">Are you sure you want to remove <span id="remove-asset-name" class="fw-bold text-dark"></span> from this user?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill btn-sm px-3" id="confirm-remove-asset">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
