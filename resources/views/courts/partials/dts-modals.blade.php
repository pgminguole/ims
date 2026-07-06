<!-- Add DTS Modal -->
<div class="modal fade" id="addDtsModal" tabindex="-1" aria-labelledby="addDtsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="addDtsModalLabel">
                    <i class="fas fa-video text-primary"></i> Add New DTS System
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.store-dts', $court) }}" method="POST">
                @csrf
                <div class="modal-body bg-light-subtle">
                    
                    <!-- Section: General Information -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <i class="fas fa-info-circle text-primary"></i> General Info
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">DTS Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $court->name }} DTS System" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date_assigned" class="form-label">Date Assigned <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_assigned" name="date_assigned" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="assign_as_primary" name="assign_as_primary" checked>
                                    <label class="form-check-label fw-bold" for="assign_as_primary">
                                        Set as primary DTS for this court
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Hardware Components -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <i class="fas fa-desktop text-primary"></i> Hardware Components
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="monitors_count" class="form-label">Monitors</label>
                                <input type="number" class="form-control" id="monitors_count" name="monitors_count" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="splitters_count" class="form-label">Splitters</label>
                                <input type="number" class="form-control" id="splitters_count" name="splitters_count" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="sony_recorders_count" class="form-label">Sony Recorders</label>
                                <input type="number" class="form-control" id="sony_recorders_count" name="sony_recorders_count" value="0" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Cabling & Accessories -->
                    <div class="modal-section mb-0">
                        <div class="modal-section-title">
                            <i class="fas fa-plug text-primary"></i> Cabling & Accessories
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="hdmi_short_cables_count" class="form-label">HDMI Cables (5M)</label>
                                <input type="number" class="form-control" id="hdmi_short_cables_count" name="hdmi_short_cables_count" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="hdmi_long_cables_count" class="form-label">HDMI Cables (20M)</label>
                                <input type="number" class="form-control" id="hdmi_long_cables_count" name="hdmi_long_cables_count" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="extension_boards_count" class="form-label">Extension Boards</label>
                                <input type="number" class="form-control" id="extension_boards_count" name="extension_boards_count" value="0" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="trucking_count" class="form-label">Trucking (Trunking)</label>
                                <input type="number" class="form-control" id="trucking_count" name="trucking_count" value="0" min="0">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Add DTS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit DTS Modal -->
<div class="modal fade" id="editDtsModal" tabindex="-1" aria-labelledby="editDtsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="editDtsModalLabel">
                    <i class="fas fa-edit text-primary"></i> Edit DTS System
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.update-dts', $court) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="dts_id" id="edit_dts_id">
                <div class="modal-body bg-light-subtle">
                    
                    <!-- Section: General Information -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <i class="fas fa-info-circle text-primary"></i> General Info
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_dts_name" class="form-label">DTS Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_dts_name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_date_assigned" class="form-label">Date Assigned <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_date_assigned" name="date_assigned" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Hardware Components -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <i class="fas fa-desktop text-primary"></i> Hardware Components
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="edit_monitors_count" class="form-label">Monitors</label>
                                <input type="number" class="form-control" id="edit_monitors_count" name="monitors_count" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_splitters_count" class="form-label">Splitters</label>
                                <input type="number" class="form-control" id="edit_splitters_count" name="splitters_count" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_sony_recorders_count" class="form-label">Sony Recorders</label>
                                <input type="number" class="form-control" id="edit_sony_recorders_count" name="sony_recorders_count" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Cabling & Accessories -->
                    <div class="modal-section mb-0">
                        <div class="modal-section-title">
                            <i class="fas fa-plug text-primary"></i> Cabling & Accessories
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_hdmi_short_cables_count" class="form-label">HDMI Cables (5M)</label>
                                <input type="number" class="form-control" id="edit_hdmi_short_cables_count" name="hdmi_short_cables_count" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_hdmi_long_cables_count" class="form-label">HDMI Cables (20M)</label>
                                <input type="number" class="form-control" id="edit_hdmi_long_cables_count" name="hdmi_long_cables_count" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_extension_boards_count" class="form-label">Extension Boards</label>
                                <input type="number" class="form-control" id="edit_extension_boards_count" name="extension_boards_count" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_trucking_count" class="form-label">Trucking (Trunking)</label>
                                <input type="number" class="form-control" id="edit_trucking_count" name="trucking_count" min="0">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Update DTS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Existing DTS Modal -->
<div class="modal fade" id="assignDtsModal" tabindex="-1" aria-labelledby="assignDtsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDtsModalLabel">
                    <i class="fas fa-link text-primary"></i> Assign Existing DTS
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courts.assign-dts', $court) }}" method="POST">
                @csrf
                <div class="modal-body bg-light-subtle">
                    <div class="modal-section mb-0">
                        <div class="mb-2">
                            <label for="dts_id" class="form-label">Select DTS <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="dts_id" name="dts_id" required>
                                <option value="">Choose DTS...</option>
                                @foreach($availableDtsAssets as $dtsAsset)
                                    <option value="{{ $dtsAsset->id }}">{{ $dtsAsset->asset_name }} ({{ $dtsAsset->asset_tag }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> Assign DTS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>