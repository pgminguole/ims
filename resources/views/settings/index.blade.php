@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 fw-bold">Database Management</h4>
                <p class="text-muted text-small mb-0">Securely backup, restore, or reset your system data.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 text-start">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Backup -->
    <div class="col-md-4 mb-3">
        <div class="stunning-card d-flex flex-column align-items-center text-center p-4">
            <div class="mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width: 70px; height: 70px;">
                    <i class="fas fa-download fa-2x text-success"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2">Backup Database</h5>
            <p class="text-muted text-small mb-4">Create a secure backup of all current database records and download it.</p>
            <button type="button" class="btn btn-success rounded-pill px-4 mt-auto" onclick="confirmBackup()">
                <i class="fas fa-play me-1"></i> Run Backup
            </button>
        </div>
    </div>

    <!-- Restore -->
    <div class="col-md-4 mb-3">
        <div class="stunning-card d-flex flex-column align-items-center text-center p-4">
            <div class="mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10" style="width: 70px; height: 70px;">
                    <i class="fas fa-upload fa-2x text-warning"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2">Restore Database</h5>
            <p class="text-muted text-small mb-4">Restore the database from an SQL file. <br><span class="text-danger fw-bold">Overwrites current data.</span></p>
            <form id="restore-form" action="{{ route('settings.restore') }}" method="POST" enctype="multipart/form-data" class="w-100 mt-auto">
                @csrf
                <input type="file" name="backup_file" id="backup_file" class="d-none" accept=".sql" onchange="confirmRestore(this)">
                <button type="button" class="btn btn-warning rounded-pill px-4 w-100" onclick="document.getElementById('backup_file').click()">
                    <i class="fas fa-sync-alt me-1"></i> Upload & Restore
                </button>
            </form>
        </div>
    </div>

    <!-- Wipe -->
    <div class="col-md-4 mb-3">
        <div class="stunning-card d-flex flex-column align-items-center p-4" style="border: 1px solid rgba(220, 53, 69, 0.2) !important;">
            <div class="mb-3 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="width: 70px; height: 70px;">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2 text-danger text-center">Selective Wipe</h5>
            <p class="text-muted text-small mb-3 text-center">Select data to permanently delete.</p>
            
            <form id="wipe-form" action="{{ route('settings.wipe') }}" method="POST" class="w-100 flex-grow-1 d-flex flex-column">
                @csrf
                <input type="hidden" name="confirmation" id="wipe_confirmation" value="">
                
                <div class="mb-3 text-start w-100 bg-light p-2 rounded" style="font-size: 0.8rem; max-height: 140px; overflow-y: auto;">
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="wipe_options[]" value="assets" id="wipe_assets" checked>
                        <label class="form-check-label fw-bold" for="wipe_assets">Assets & Activities</label>
                    </div>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="wipe_options[]" value="users" id="wipe_users">
                        <label class="form-check-label fw-bold" for="wipe_users">Standard Users</label>
                    </div>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="wipe_options[]" value="locations" id="wipe_locations">
                        <label class="form-check-label fw-bold" for="wipe_locations">Locations & Offices</label>
                    </div>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="wipe_options[]" value="categories" id="wipe_categories">
                        <label class="form-check-label fw-bold" for="wipe_categories">Categories</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="wipe_options[]" value="regions" id="wipe_regions">
                        <label class="form-check-label fw-bold" for="wipe_regions">Regions</label>
                    </div>
                </div>

                <button type="button" class="btn btn-danger rounded-pill px-4 w-100 mt-auto" onclick="confirmWipe()">
                    <i class="fas fa-trash-alt me-1"></i> Wipe Selected
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmBackup() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to create a database backup?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, back it up!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Backing up...',
                    text: 'Please wait while we secure your data.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('settings.backup') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Download Backup',
                            confirmButtonColor: '#28a745',
                        }).then(() => {
                            window.location.href = data.download_url;
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                });
            }
        });
    }

    function confirmRestore(input) {
        if (input.files && input.files[0]) {
            let fileName = input.files[0].name;
            Swal.fire({
                title: 'Restore Database?',
                html: `You are about to restore the database using file:<br><strong>${fileName}</strong><br><br><span class="text-danger">This will overwrite all current data!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Restoring...',
                        text: 'Please wait, this might take a moment.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('restore-form').submit();
                } else {
                    input.value = '';
                }
            });
        }
    }

    function confirmWipe() {
        const selectedOptions = Array.from(document.querySelectorAll('input[name="wipe_options[]"]:checked')).map(cb => cb.nextElementSibling.innerText.trim());
        
        if (selectedOptions.length === 0) {
            Swal.fire('No selection', 'Please select at least one data type to wipe.', 'info');
            return;
        }

        let optionsList = '<ul class="text-start mt-3" style="max-height: 120px; overflow-y: auto;">' + selectedOptions.map(opt => `<li>${opt}</li>`).join('') + '</ul>';

        Swal.fire({
            title: 'WARNING: EXTREME DANGER',
            html: `<p class="text-danger">You are about to permanently delete the following data:</p>
                   ${optionsList}
                   <p class="mt-3">To proceed, please type <strong>confirm wipe</strong> below:</p>`,
            icon: 'error',
            input: 'text',
            inputPlaceholder: 'Type "confirm wipe" here...',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'WIPE SELECTED DATA',
            cancelButtonText: 'Cancel',
            preConfirm: (inputValue) => {
                if (inputValue !== 'confirm wipe') {
                    Swal.showValidationMessage('You must type exactly "confirm wipe" to proceed.');
                    return false;
                }
                return inputValue;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('wipe_confirmation').value = 'confirm wipe';
                Swal.fire({
                    title: 'Wiping Data...',
                    text: 'Please wait while we securely delete the selected information.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('wipe-form').submit();
            }
        });
    }
</script>
@endsection
