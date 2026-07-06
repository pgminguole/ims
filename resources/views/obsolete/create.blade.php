@extends('layouts.app')

@section('title', 'Record Obsolete Equipment')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center py-4">
                <div>
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1 bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('obsolete-assets.index') }}" class="text-decoration-none">Obsolete Equipment</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Record New</li>
                        </ol>
                    </nav>
                     <h4 class="mb-0 fw-bold">Record Obsolete Equipment</h4>
                </div>
            </div>

            <div class="card border-0 shadow-sm stunning-card">
                <div class="card-body p-4">
                    <form action="{{ route('obsolete-assets.store') }}" method="POST">
                        @csrf
                        
                        <h6 class="text-uppercase text-tiny fw-bold text-muted mb-3">Asset Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label text-small fw-bold">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="asset_name" class="form-control" placeholder="e.g., Old Dell Latitude Laptop" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Region</label>
                                <select name="region_id" id="region_id" class="form-select select2">
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Origin Type</label>
                                <select name="target_type" id="target_type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="court">Court</option>
                                    <option value="office">Department/Office</option>
                                    <option value="user">Individual User</option>
                                </select>
                            </div>
                            
                            <!-- Dynamic Target Selection -->
                            <div class="col-md-4 target-field d-none" id="court_field">
                                <label class="form-label text-small fw-bold">Court</label>
                                <select name="court_id" class="form-select select2">
                                    <option value="">Select Court</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}" data-region="{{ $court->region_id }}">{{ $court->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 target-field d-none" id="office_field">
                                <label class="form-label text-small fw-bold">Department/Office</label>
                                <select name="office_id" class="form-select select2">
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" data-region="{{ $office->region_id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 target-field d-none" id="user_field">
                                <label class="form-label text-small fw-bold">User</label>
                                <select name="owner_user_id" class="form-select select2">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-region="{{ $user->region_id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Serial Number</label>
                                <input type="text" name="serial_number" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Category</label>
                                <select name="category" class="form-select select2">
                                    <option value="">Select Category</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Desktop">Desktop</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="Printer">Printer</option>
                                    <option value="Scanner">Scanner</option>
                                    <option value="UPS">UPS</option>
                                    <option value="Router/Switch">Router/Switch</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                             <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Brand</label>
                                <input type="text" name="brand" class="form-control" placeholder="e.g., Dell, HP">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Model</label>
                                <input type="text" name="model" class="form-control" placeholder="e.g., Inspiron 15">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Date Acquired</label>
                                <input type="date" name="date_acquired" class="form-control">
                            </div>
                        </div>

                        <h6 class="text-uppercase text-tiny fw-bold text-muted mb-3">Obsolescence Details</h6>
                        <div class="row g-3 mb-4">
                             <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Date Obsolete <span class="text-danger">*</span></label>
                                <input type="date" name="date_obsolete" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                             <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Reported By</label>
                                <input type="text" name="reported_by_name" class="form-control" value="{{ Auth::user()->name }}">
                            </div>
                             <div class="col-md-12">
                                <label class="form-label text-small fw-bold">Reason for Obsolescence <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Describe why this asset is being written off..." required></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label text-small fw-bold">Disposal Method</label>
                                <select name="disposal_method" class="form-select select2">
                                    <option value="">Select Method (Optional)</option>
                                    <option value="Recycled">Recycled</option>
                                    <option value="Donated">Donated</option>
                                    <option value="Sold">Sold</option>
                                    <option value="Disposed">Disposed (Trash)</option>
                                    <option value="Stored">Stored (Archive)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('obsolete-assets.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Save Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Handle Origin Type change
    $('#target_type').on('change', function() {
        const type = $(this).val();
        $('.target-field').addClass('d-none');
        
        if (type === 'court') $('#court_field').removeClass('d-none');
        else if (type === 'office') $('#office_field').removeClass('d-none');
        else if (type === 'user') $('#user_field').removeClass('d-none');
    });

    // Handle Region filtering (optional but helpful)
    $('#region_id').on('change', function() {
        const regionId = $(this).val();
        if (!regionId) {
            $('.target-field select option').show();
            return;
        }

        $('.target-field select').each(function() {
            const $select = $(this);
            $select.find('option').each(function() {
                const optRegionId = $(this).data('region');
                if (!optRegionId || optRegionId == regionId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            // Reset selection if current choice is hidden
            if ($select.find('option:selected').css('display') === 'none') {
                $select.val('').trigger('change');
            }
        });
    });
});
</script>
@endpush
