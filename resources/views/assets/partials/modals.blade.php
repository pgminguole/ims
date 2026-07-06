<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
             <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Assign Asset</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.assign', $asset->slug) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-small fw-bold">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="assign_type" name="assigned_type" required>
                            <option value="">Select Type...</option>
                            <option value="user">User</option>
                            <option value="office">Office</option>
                            <option value="court">Court</option>
                            <option value="region">Region</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="assign_user_section" style="display: none;">
                        <label class="form-label text-small fw-bold">Select User <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="assign_user_select" name="assigned_to" disabled>
                            <option value="">Search User...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="assign_office_section" style="display: none;">
                        <label class="form-label text-small fw-bold">Select Office <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="assign_office_select" name="assigned_to" disabled>
                            <option value="">Search Office...</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="assign_court_section" style="display: none;">
                        <label class="form-label text-small fw-bold">Select Court <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="assign_court_select" name="assigned_to" disabled>
                            <option value="">Search Court...</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}">{{ $court->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="assign_region_section" style="display: none;">
                        <label class="form-label text-small fw-bold">Select Region <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="assign_region_select" name="assigned_to" disabled>
                            <option value="">Search Region...</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-small fw-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-small fw-bold">Comments</label>
                        <textarea class="form-control form-control-sm" name="comments" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill btn-sm px-4">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reassign Asset Modal -->
<div class="modal fade" id="reassignAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
             <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Reassign Asset</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.assign', $asset->slug) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border text-small mb-3">
                        <span class="fw-bold">Current:</span> 
                         @if($asset->assignedUser)
                            User - {{ $asset->assignedUser->name }}
                        @elseif($asset->office)
                            Office - {{ $asset->office->name }}
                        @elseif($asset->court)
                            Court - {{ $asset->court->name }}
                        @elseif($asset->region)
                            Region - {{ $asset->region->name }}
                        @endif
                    </div>
                      <div class="mb-3">
                        <label class="form-label text-small fw-bold">Reassign To <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reassign_type" name="assigned_type" required>
                            <option value="">Select Type...</option>
                            <option value="user" {{ $asset->assigned_type == 'user' ? 'selected' : '' }}>User</option>
                            <option value="office" {{ $asset->assigned_type == 'office' ? 'selected' : '' }}>Office</option>
                            <option value="court" {{ $asset->assigned_type == 'court' ? 'selected' : '' }}>Court</option>
                            <option value="region" {{ $asset->assigned_type == 'region' ? 'selected' : '' }}>Region</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="reassign_user_section" style="display: {{ $asset->assigned_type == 'user' ? 'block' : 'none' }};">
                        <label class="form-label text-small fw-bold">Select User <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reassign_user_select" name="assigned_to" {{ $asset->assigned_type == 'user' ? '' : 'disabled' }}>
                             @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $asset->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="reassign_office_section" style="display: {{ $asset->assigned_type == 'office' ? 'block' : 'none' }};">
                         <label class="form-label text-small fw-bold">Select Office <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reassign_office_select" name="assigned_to" {{ $asset->assigned_type == 'office' ? '' : 'disabled' }}>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ $asset->assigned_to == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="reassign_court_section" style="display: {{ $asset->assigned_type == 'court' ? 'block' : 'none' }};">
                         <label class="form-label text-small fw-bold">Select Court <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reassign_court_select" name="assigned_to" {{ $asset->assigned_type == 'court' ? '' : 'disabled' }}>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ $asset->assigned_to == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="reassign_region_section" style="display: {{ $asset->assigned_type == 'region' ? 'block' : 'none' }};">
                         <label class="form-label text-small fw-bold">Select Region <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="reassign_region_select" name="assigned_to" {{ $asset->assigned_type == 'region' ? '' : 'disabled' }}>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ $asset->assigned_to == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                     <div class="mb-3">
                        <label class="form-label text-small fw-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                     <div class="mb-3">
                        <label class="form-label text-small fw-bold">Reason</label>
                        <textarea class="form-control form-control-sm" name="comments" rows="2" placeholder="Why is this being reassigned?"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill btn-sm px-4">Reassign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Asset Modal -->
<div class="modal fade" id="returnAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
             <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Return Asset</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             <form action="{{ route('assets.return', $asset) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-small fw-bold">Date Returned <span class="text-danger">*</span></label>
                         <input type="date" class="form-control form-control-sm" name="returned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-small fw-bold">Condition <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="condition" required>
                            <option value="excellent" {{ $asset->condition == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ $asset->condition == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ $asset->condition == 'fair' ? 'selected' : '' }}>Fair</option>
                             <option value="poor">Poor</option>
                            <option value="broken">Broken</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-small fw-bold">Returned By</label>
                        <input type="text" class="form-control form-control-sm" name="returnee" placeholder="Person returning item">
                    </div>
                     <div class="mb-3">
                        <label class="form-label text-small fw-bold">Reason/Notes</label>
                        <textarea class="form-control form-control-sm" name="returned_reason" rows="2" required></textarea>
                    </div>
                </div>
                 <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill btn-sm px-4">Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
         <div class="modal-content border-0 shadow-lg rounded-4">
             <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Asset History</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 @if($asset->histories->count() > 0)
                     <div class="table-responsive">
                        <table class="table table-sm align-middle text-small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asset->histories as $history)
                                <tr>
                                    <td class="text-muted">{{ $history->performed_at->format('M d, Y H:i') }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $history->action)) }}</span></td>
                                    <td>{{ $history->description }}</td>
                                    <td class="text-muted">{{ $history->performedBy->name ?? 'System' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No history records found.</p>
                @endif
            </div>
         </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared logic for both assign and reassign modals
    ['assign', 'reassign'].forEach(prefix => {
        const typeSelect = document.getElementById(prefix + '_type');
        if(typeSelect) {
            typeSelect.addEventListener('change', function() {
                const type = this.value;
                const userSection = document.getElementById(prefix + '_user_section');
                const officeSection = document.getElementById(prefix + '_office_section');
                const courtSection = document.getElementById(prefix + '_court_section');
                const regionSection = document.getElementById(prefix + '_region_section');
                
                const userSelect = document.getElementById(prefix + '_user_select');
                const officeSelect = document.getElementById(prefix + '_office_select');
                const courtSelect = document.getElementById(prefix + '_court_select');
                const regionSelect = document.getElementById(prefix + '_region_select');
                
                // Reset all
                [userSection, officeSection, courtSection, regionSection].forEach(s => s ? s.style.display = 'none' : null);
                [userSelect, officeSelect, courtSelect, regionSelect].forEach(s => s ? s.disabled = true : null);
                
                if (type === 'user') {
                    userSection.style.display = 'block';
                    userSelect.disabled = false;
                } else if (type === 'office') {
                    officeSection.style.display = 'block';
                    officeSelect.disabled = false;
                } else if (type === 'court') {
                    courtSection.style.display = 'block';
                    courtSelect.disabled = false;
                } else if (type === 'region') {
                    regionSection.style.display = 'block';
                    regionSelect.disabled = false;
                }
            });
        }
    });
});
</script>
