<!-- Edit DTS Date Modal -->
<div class="modal fade" id="editDtsDateModal" tabindex="-1" aria-labelledby="editDtsDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDtsDateModalLabel">Change DTS Assigned Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dts-assignments.update-date') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="dts_id" id="edit_dts_date_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">DTS System:</label>
                        <p class="form-control-plaintext" id="dts_name_display"></p>
                    </div>
                    <div class="mb-3">
                        <label for="edit_dts_date_assigned" class="form-label">Date Assigned *</label>
                        <input type="date" class="form-control" id="edit_dts_date_assigned" name="date_assigned" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Date</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Date Modal -->
<!-- Edit Asset Date Modal -->
<div class="modal fade" id="editAssetDateModal" tabindex="-1" aria-labelledby="editAssetDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssetDateModalLabel">Change Asset Assigned Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('assets.change-court-assigned-date')}}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="asset_id" id="edit_asset_date_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Asset:</label>
                        <p class="form-control-plaintext" id="asset_name_display"></p>
                    </div>
                    <div class="mb-3">
                        <label for="edit_asset_date_assigned" class="form-label">Date Assigned *</label>
                        <input type="date" class="form-control" id="edit_asset_date_assigned" name="assigned_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Change (Optional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" placeholder="Enter reason for date change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Date</button>
                </div>
            </form>
        </div>
    </div>
</div>