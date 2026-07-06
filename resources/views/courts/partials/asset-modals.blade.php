<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAssetModalLabel">
                    <i class="fas fa-link text-primary"></i> Assign Asset to Court
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.assign-asset', $court) }}" method="POST">
                @csrf
                <div class="modal-body bg-light-subtle">
                    <div class="modal-section mb-0">
                        <div class="mb-2">
                            <label for="asset_id" class="form-label">Select Asset <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="asset_id" name="asset_id" required>
                                <option value="">Choose Asset...</option>
                                @foreach($availableAssets as $asset)
                                    <option value="{{ $asset->id }}">
                                        {{ $asset->asset_name }} ({{ $asset->asset_tag }}) - {{ $asset->category->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted mt-2">
                                Only unassigned assets are shown here.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> Assign Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View All Assets Modal -->
<div class="modal fade" id="viewAssetsModal" tabindex="-1" aria-labelledby="viewAssetsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAssetsModalLabel">
                    <i class="fas fa-boxes text-primary"></i> All Assets - {{ $court->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light-subtle">
                <div class="p-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-uppercase text-muted text-tiny fw-bold ps-4">Asset Name</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold">Asset Tag</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold">Comment</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold">Category</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold">Status</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold">Condition</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold">Date Assigned</th>
                                        <th class="text-uppercase text-muted text-tiny fw-bold pe-4 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($court->assets as $asset)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark text-small">{{ $asset->asset_name }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted text-small">{{ $asset->asset_tag ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted text-tiny" title="{{ $asset->comments }}">{{ Str::limit($asset->comments, 30) ?: 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-small">{{ $asset->category->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status == 'active' ? 'success' : ($asset->status == 'maintenance' ? 'warning' : 'danger') }}-subtle text-{{ $asset->status == 'active' ? 'success' : ($asset->status == 'maintenance' ? 'warning' : 'danger') }} border-0 px-2 py-1">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-small">{{ ucfirst($asset->condition) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="far fa-calendar-alt me-2"></i>
                                                <span class="text-small">{{ $asset->assigned_date ? $asset->assigned_date->format('M d, Y') : 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-light border rounded-circle text-info" title="View Asset" data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-light border rounded-circle text-primary" title="Edit Asset" data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('courts.remove-asset', $court) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                                    <button type="submit" class="btn btn-sm btn-light border rounded-circle text-danger" title="Remove from Court" data-bs-toggle="tooltip" onclick="confirmDelete(event, 'Remove this asset from court?', 'Yes, remove it!')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fs-2 mb-3 opacity-50"></i>
                                                <p class="mb-0 text-small">No assets currently assigned to this court.</p>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>