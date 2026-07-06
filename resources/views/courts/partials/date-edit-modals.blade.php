<!-- Edit DTS Date Modal -->
<div class="modal fade" id="editDtsDateModal" tabindex="-1" aria-labelledby="editDtsDateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="editDtsDateModalLabel">
                    <i class="fas fa-calendar-alt"></i> Change DTS Assigned Date
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dts-assignments.update-date') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="dts_id" id="edit_dts_date_id">
                <div class="modal-body bg-light-subtle">
                    <div class="modal-section mb-0">
                        <div class="mb-3">
                            <label class="form-label text-muted">DTS System</label>
                            <div class="fw-bold text-dark fs-6" id="dts_name_display"></div>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="mb-0">
                            <label for="edit_dts_date_assigned" class="form-label">New Date Assigned <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg fs-6" id="edit_dts_date_assigned" name="date_assigned" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Date
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Date Modal -->
<div class="modal fade" id="editAssetDateModal" tabindex="-1" aria-labelledby="editAssetDateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssetDateModalLabel">
                    <i class="fas fa-calendar-check"></i> Change Asset Assigned Date
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('assets.change-court-assigned-date')}}" method="POST" id="editAssetDateForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="asset_id" id="edit_asset_date_id">
                <div class="modal-body bg-light-subtle">
                    <div class="modal-section mb-0">
                        <div class="mb-3">
                            <label class="form-label text-muted">Asset</label>
                            <div class="fw-bold text-dark fs-6" id="asset_name_display"></div>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="mb-3">
                            <label for="edit_asset_date_assigned" class="form-label">New Date Assigned <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg fs-6" id="edit_asset_date_assigned" name="assigned_date" required>
                        </div>
                        <div class="mb-0">
                            <label for="reason" class="form-label">Reason for Change (Optional)</label>
                            <textarea class="form-control" id="reason" name="reason" rows="2" placeholder="Why is this date being changed?"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Date
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>