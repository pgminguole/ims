<!-- Add DTS Modal -->
<div class="modal fade" id="addDtsModal" tabindex="-1" aria-labelledby="addDtsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDtsModalLabel">Add New DTS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.store-dts', $court) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">DTS Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $court->name }} DTS System" required>
                            </div>
                        </div>
                               <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_date_assigned" class="form-label">Date Assigned *</label>
                                <input type="date" class="form-control" id="edit_date_assigned" name="date_assigned" required>
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="monitors_count" class="form-label">Monitors</label>
                                <input type="number" class="form-control" id="monitors_count" name="monitors_count" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="splitters_count" class="form-label">Splitters</label>
                                <input type="number" class="form-control" id="splitters_count" name="splitters_count" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hdmi_short_cables_count" class="form-label">HDMI 5M Cables</label>
                                <input type="number" class="form-control" id="hdmi_short_cables_count" name="hdmi_short_cables_count" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hdmi_long_cables_count" class="form-label">HDMI 20M Cables</label>
                                <input type="number" class="form-control" id="hdmi_long_cables_count" name="hdmi_long_cables_count" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="extension_boards_count" class="form-label">Extension Boards</label>
                                <input type="number" class="form-control" id="extension_boards_count" name="extension_boards_count" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="trucking_count" class="form-label">Trucking</label>
                                <input type="number" class="form-control" id="trucking_count" name="trucking_count" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sony_recorders_count" class="form-label">Sony Recorders</label>
                                <input type="number" class="form-control" id="sony_recorders_count" name="sony_recorders_count" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="assign_as_primary" name="assign_as_primary" checked>
                            <label class="form-check-label" for="assign_as_primary">
                                Set as primary DTS for this court
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add DTS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit DTS Modal -->
<div class="modal fade" id="editDtsModal" tabindex="-1" aria-labelledby="editDtsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDtsModalLabel">Edit DTS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.update-dts', $court) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="dts_id" id="edit_dts_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_dts_name" class="form-label">DTS Name</label>
                                <input type="text" class="form-control" id="edit_dts_name" name="name" required>
                            </div>
                        </div>
                               <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_date_assigned" class="form-label">Date Assigned *</label>
                                <input type="date" class="form-control" id="edit_date_assigned" name="date_assigned" required>
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_monitors_count" class="form-label">Monitors</label>
                                <input type="number" class="form-control" id="edit_monitors_count" name="monitors_count" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_splitters_count" class="form-label">Splitters</label>
                                <input type="number" class="form-control" id="edit_splitters_count" name="splitters_count" min="0">
                            </div>
                        </div>
                        
                        
                      
                        
                        
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_hdmi_short_cables_count" class="form-label">HDMI 5M Cables</label>
                                <input type="number" class="form-control" id="edit_hdmi_short_cables_count" name="hdmi_short_cables_count" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_hdmi_long_cables_count" class="form-label">HDMI 20M Cables</label>
                                <input type="number" class="form-control" id="edit_hdmi_long_cables_count" name="hdmi_long_cables_count" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_extension_boards_count" class="form-label">Extension Boards</label>
                                <input type="number" class="form-control" id="edit_extension_boards_count" name="extension_boards_count" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_trucking_count" class="form-label">Trucking</label>
                                <input type="number" class="form-control" id="edit_trucking_count" name="trucking_count" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sony_recorders_count" class="form-label">Sony Recorders</label>
                                <input type="number" class="form-control" id="edit_sony_recorders_count" name="sony_recorders_count" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update DTS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Existing DTS Modal (Keep this if you still need to assign existing DTS assets) -->
<div class="modal fade" id="assignDtsModal" tabindex="-1" aria-labelledby="assignDtsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDtsModalLabel">Assign Existing DTS to Court</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.assign-dts', $court) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dts_id" class="form-label">Select DTS</label>
                        <select class="form-select" id="dts_id" name="dts_id" required>
                            <option value="">Choose DTS...</option>
                            @foreach($availableDtsAssets as $dtsAsset)
                                <option value="{{ $dtsAsset->id }}">{{ $dtsAsset->asset_name }} ({{ $dtsAsset->asset_tag }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign DTS</button>
                </div>
            </form>
        </div>
    </div>
</div>