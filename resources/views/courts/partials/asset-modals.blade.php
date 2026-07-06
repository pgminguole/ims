<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAssetModalLabel">Assign Asset to Court</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.assign-asset', $court) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Select Asset</label>
                        <select class="form-select" id="asset_id" name="asset_id" required>
                            <option value="">Choose Asset...</option>
                            @foreach($availableAssets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->asset_name }} ({{ $asset->asset_tag }}) - {{ $asset->category->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <h5 class="modal-title" id="viewAssetsModalLabel">All Assets - {{ $court->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-tiny">Asset Name</th>
                                <th class="text-tiny">Asset Tag</th>
                                <th class="text-tiny">Comment</th>
                                <th class="text-tiny">Category</th>
                                <th class="text-tiny">Status</th>
                                <th class="text-tiny">Condition</th>
                                <th class="text-tiny">Date Assigned</th>
                                <th class="text-tiny">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($court->assets as $asset)
                            <tr>
                                <td class="text-small">{{ $asset->asset_name }}</td>
                                <td class="text-small">{{ $asset->asset_tag ?? 'N/A' }}</td>
                                <td class="text-small">{{ Str::limit($asset->comments, 50) ?? 'N/A' }}</td>
                                <td class="text-small">{{ $asset->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $asset->status }} text-tiny">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td class="text-small">{{ ucfirst($asset->condition) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar me-2 text-muted" style="font-size: 0.75rem;"></i>
                                        <span class="text-small">{{ $asset->assigned_date ? $asset->assigned_date->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-info" title="View Asset">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary" title="Edit Asset">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('courts.remove-asset', $court) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, 'Remove this asset from court?', 'Yes, remove it!')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted text-small">No assets assigned to this court.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>