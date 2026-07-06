@extends('layouts.app')

@section('title', 'Edit Obsolete Equipment')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center py-4">
                <div>
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('obsolete-assets.index') }}" class="text-decoration-none">Obsolete Equipment</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('obsolete-assets.show', $obsoleteAsset) }}" class="text-decoration-none">{{ Str::limit($obsoleteAsset->asset_name, 20) }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                     <h4 class="mb-0 fw-bold">Edit Record</h4>
                </div>
            </div>

            <div class="card border-0 shadow-sm stunning-card">
                <div class="card-body p-4">
                    <form action="{{ route('obsolete-assets.update', $obsoleteAsset) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="text-uppercase text-tiny fw-bold text-muted mb-3">Asset Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label text-small fw-bold">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" name="asset_name" class="form-control" value="{{ old('asset_name', $obsoleteAsset->asset_name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Region</label>
                                <select name="region_id" id="region_id" class="form-select select2">
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $obsoleteAsset->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Origin Type</label>
                                <select name="target_type" id="target_type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="court" {{ old('target_type', $obsoleteAsset->target_type) == 'court' ? 'selected' : '' }}>Court</option>
                                    <option value="office" {{ old('target_type', $obsoleteAsset->target_type) == 'office' ? 'selected' : '' }}>Department/Office</option>
                                    <option value="user" {{ old('target_type', $obsoleteAsset->target_type) == 'user' ? 'selected' : '' }}>Individual User</option>
                                </select>
                            </div>
                            
                            <!-- Dynamic Target Selection -->
                            <div class="col-md-4 target-field {{ $obsoleteAsset->target_type === 'court' ? '' : 'd-none' }}" id="court_field">
                                <label class="form-label text-small fw-bold">Court</label>
                                <select name="court_id" class="form-select select2">
                                    <option value="">Select Court</option>
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}" data-region="{{ $court->region_id }}" {{ old('court_id', $obsoleteAsset->court_id) == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 target-field {{ $obsoleteAsset->target_type === 'office' ? '' : 'd-none' }}" id="office_field">
                                <label class="form-label text-small fw-bold">Department/Office</label>
                                <select name="office_id" class="form-select select2">
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" data-region="{{ $office->region_id }}" {{ old('office_id', $obsoleteAsset->office_id) == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 target-field {{ $obsoleteAsset->target_type === 'user' ? '' : 'd-none' }}" id="user_field">
                                <label class="form-label text-small fw-bold">User</label>
                                <select name="owner_user_id" class="form-select select2">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-region="{{ $user->region_id }}" {{ old('owner_user_id', $obsoleteAsset->owner_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Serial Number</label>
                                <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $obsoleteAsset->serial_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Category</label>
                                <select name="category" class="form-select select2">
                                    <option value="">Select Category</option>
                                    @foreach(['Laptop', 'Desktop', 'Monitor', 'Printer', 'Scanner', 'UPS', 'Router/Switch', 'Furniture', 'Other'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $obsoleteAsset->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Brand</label>
                                <input type="text" name="brand" class="form-control" value="{{ old('brand', $obsoleteAsset->brand) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Model</label>
                                <input type="text" name="model" class="form-control" value="{{ old('model', $obsoleteAsset->model) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-small fw-bold">Date Acquired</label>
                                <input type="date" name="date_acquired" class="form-control" value="{{ old('date_acquired', $obsoleteAsset->date_acquired?->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <h6 class="text-uppercase text-tiny fw-bold text-muted mb-3">Obsolescence Details</h6>
                        <div class="row g-3 mb-4">
                             <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Date Obsolete <span class="text-danger">*</span></label>
                                <input type="date" name="date_obsolete" class="form-control" value="{{ old('date_obsolete', $obsoleteAsset->date_obsolete->format('Y-m-d')) }}" required>
                            </div>
                             <div class="col-md-6">
                                <label class="form-label text-small fw-bold">Reported By</label>
                                <input type="text" name="reported_by_name" class="form-control" value="{{ old('reported_by_name', $obsoleteAsset->reported_by_name) }}">
                            </div>
                             <div class="col-md-12">
                                <label class="form-label text-small fw-bold">Reason for Obsolescence <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required>{{ old('reason', $obsoleteAsset->reason) }}</textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label text-small fw-bold">Disposal Method</label>
                                <select name="disposal_method" class="form-select select2">
                                    <option value="">Select Method (Optional)</option>
                                    @foreach(['Recycled', 'Donated', 'Sold', 'Disposed', 'Stored'] as $method)
                                        <option value="{{ $method }}" {{ old('disposal_method', $obsoleteAsset->disposal_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('obsolete-assets.show', $obsoleteAsset) }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Update Record</button>
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

    // Handle Region filtering
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
