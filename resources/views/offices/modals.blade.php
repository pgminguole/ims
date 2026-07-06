<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
             <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Assign Asset to {{ $office->name }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($availableAssets->count() > 0)
                     <div class="alert alert-light border text-small mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Available assets for office assignment.
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-tiny text-uppercase text-muted">Asset Name</th>
                                    <th class="text-tiny text-uppercase text-muted">Tag</th>
                                    <th class="text-tiny text-uppercase text-muted">Category</th>
                                    <th class="text-tiny text-uppercase text-muted">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableAssets as $asset)
                                <tr>
                                    <td class="text-small fw-medium">{{ $asset->asset_name }}</td>
                                    <td class="text-small text-muted">{{ $asset->asset_tag }}</td>
                                    <td class="text-small text-muted">{{ $asset->category->name ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('offices.assign-asset', $office) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                            <button type="submit" class="btn btn-xs btn-primary rounded-pill px-3">
                                                Assign
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                   <div class="text-center py-4">
                        <p class="text-muted text-small mb-0">No available assets to assign.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Asset Modal -->
<div class="modal fade" id="createAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
         <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Create New Asset</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('offices.create-asset', $office) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasAssignedRole('super_admin'))
                         <div class="col-md-12">
                            <label class="form-label text-small fw-bold">Record Type <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="record_type" required>
                                <option value="assignment">Official Assignment</option>
                                <option value="inventory">Inventory Collection</option>
                            </select>
                        </div>
                        @endif
                         <div class="col-md-6">
                            <label class="form-label text-small fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="category_id" required>
                                <option value="">Select...</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-small fw-bold">Brand/Model</label>
                             <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="brand" placeholder="Brand">
                                <input type="text" class="form-control" name="model" placeholder="Model">
                            </div>
                        </div>
                         <div class="col-md-4">
                            <label class="form-label text-small fw-bold">Quantity</label>
                            <input type="number" class="form-control form-control-sm" name="quantity" value="1" min="1" max="10">
                        </div>
                         <div class="col-md-4">
                            <label class="form-label text-small fw-bold">Condition</label>
                             <select class="form-select form-select-sm" name="condition">
                                <option value="good">Good</option>
                                <option value="new">New</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-small fw-bold">Assigned Date</label>
                            <input type="date" class="form-control form-control-sm" name="assigned_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-small fw-bold">Directly Assign to User (Optional)</label>
                            <select class="form-select form-select-sm select2" name="assigned_to">
                                <option value="">No, leave assigned to Office</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted text-tiny">If selected, the asset will be assigned to this user instead of just the office.</small>
                        </div>
                         <div class="col-12">
                            <label class="form-label text-small fw-bold">Notes</label>
                            <textarea class="form-control form-control-sm" name="comments" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill btn-sm px-4">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
