<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAssetModalLabel">Assign Asset to Office</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('offices.assign-asset', $office) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Select Asset</label>
                        <select class="form-select" name="asset_id" required>
                            <option value="">Choose an asset...</option>
                            @foreach($availableAssets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->asset_name }} 
                                   
                                    @if($asset->office_id == $office->id)
                                        - Currently Assigned
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View All Assets Modal -->
<div class="modal fade" id="viewAssetsModal" tabindex="-1" aria-labelledby="viewAssetsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAssetsModalLabel">All Assets - {{ $office->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($office->assets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th class="text-tiny">Asset Name</th>
                                <th class="text-tiny">Asset Tag</th>
                                <th class="text-tiny">Comment</th>
                                <th class="text-tiny">Category</th>
                                <th class="text-tiny">Status</th>
                                <th class="text-tiny">Condition</th>
                                <th class="text-tiny">Assigned Date</th>
                                <th class="text-tiny">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($office->assets as $asset)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2 bg-primary">
                                            <i class="fas fa-laptop"></i>
                                        </div>
                                        <div>
                                            <div class="asset-name-text text-small">{{ $asset->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-small">{{ $asset->asset_tag ?? 'N/A' }}</td>
                                <td class="text-small">{{ Str::limit($asset->comments, 50) ?? 'N/A' }}</td>
                                <td class="text-small">{{ $asset->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $asset->status }} text-tiny">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td class="text-small">{{ ucfirst($asset->condition) }}</td>
                                <td class="text-small">{{ $asset->assigned_date ? $asset->assigned_date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-info" title="View Asset">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary" title="Edit Asset">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('offices.remove-asset', $office) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(event, 'Remove this asset from office?', 'Yes, remove it!')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4 text-small">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>No assets assigned to this office yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>