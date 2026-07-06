@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">Import Assets from Excel</h1>
            <p class="text-muted">Upload an Excel file to import assets and assign them to users, courts, or offices.</p>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="row mb-4" id="uploadSection">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Excel File *</label>
                            <input type="file" class="form-control" id="file" name="file" 
                                   accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Supported formats: XLSX, XLS, CSV</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assignment_year" class="form-label">Assignment Year *</label>
                                <select class="form-select" id="assignment_year" name="assignment_year" required>
                                    @for($year = date('Y'); $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                <small class="text-muted">Assets will be assigned random dates between March-July</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="region_id" class="form-label">Filter by Region (Optional)</label>
                                <select class="form-select" id="region_id" name="region_id">
                                    <option value="">All Regions</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Only import entities from selected region</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Import Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>First column should contain entity names (Users, Courts, or Offices)</li>
                                <li>Column headers should match asset categories (COMPUTER, LAPTOP, UPS, etc.)</li>
                                <li>Cell values should be numbers indicating quantity to assign</li>
                                <li>Empty cells or zero values will be ignored</li>
                                <li>New entities will be created if they don't exist in the database</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Upload & Preview
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="row" id="previewSection" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Import Preview</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-light" id="editAllBtn">
                            <i class="fas fa-edit me-1"></i>Edit All
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="confirmImportBtn">
                            <i class="fas fa-check me-1"></i>Confirm Import
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="cancelPreviewBtn">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" id="previewWarning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Review carefully!</strong> New entities marked with "NEW" badge will be created.
                    </div>

                    <div class="mb-3">
                        <strong>Summary:</strong>
                        <span class="badge bg-info ms-2" id="totalEntities">0</span> Entities
                        <span class="badge bg-success ms-2" id="totalAssets">0</span> Assets
                        <span class="badge bg-warning ms-2" id="newEntities">0</span> New Entities
                    </div>

                    <!-- Debug Information -->
                    <div class="alert alert-info" id="debugInfo" style="display: none;">
                        <strong><i class="fas fa-info-circle"></i> Import Details:</strong>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <small>
                                    <strong>Headers Found:</strong> <span id="debugHeaderCount">0</span><br>
                                    <strong>Categories Mapped:</strong> <span id="debugCategoryCount">0</span>
                                </small>
                            </div>
                            <div class="col-md-8">
                                <small>
                                    <strong>Category Mappings:</strong>
                                    <div id="debugCategoryList" class="mt-1"></div>
                                </small>
                            </div>
                        </div>
                        <div id="debugUnmapped" class="mt-2" style="display: none;">
                            <small class="text-danger">
                                <strong><i class="fas fa-exclamation-triangle"></i> Unmapped Headers:</strong>
                                <span id="debugUnmappedList"></span>
                            </small>
                        </div>
                    </div>

                    <div id="previewContent" class="table-responsive">
                        <!-- Preview content will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <div class="spinner-border mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4 id="loadingText">Processing...</h4>
        </div>
    </div>
</div>

<style>
.entity-row {
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.entity-row.new-entity {
    border-left-color: #ffc107;
}

.entity-row.low-confidence-match {
    border-left-color: #ff9800;
    background: #fff8e1;
}

.asset-item {
    background: white;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 8px;
    border: 1px solid #dee2e6;
}

.asset-item:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.editable-field {
    cursor: pointer;
    border-bottom: 1px dashed #6c757d;
    padding: 2px 4px;
}

.editable-field:hover {
    background: #fff3cd;
}
</style>
@endsection

@section('scripts')
<!-- Ensure SweetAlert2 is loaded -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Ensure jQuery is loaded -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let previewData = null;

// Test if libraries are loaded
console.log('jQuery loaded:', typeof $ !== 'undefined');
console.log('SweetAlert2 loaded:', typeof Swal !== 'undefined');

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing event handlers');
    
    // Upload form submission
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Upload form submitted');
            
            const formData = new FormData(this);
            showLoading('Processing file...');

            $.ajax({
                url: '{{ route("assets.import.preview") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        console.log('Import Preview Response:', response);
                        console.log('Headers:', response.headers);
                        console.log('Category Mappings:', response.categories);
                        console.log('Preview Data:', response.preview);
                        
                        previewData = response;
                        displayPreview(response);
                        
                        const uploadSection = document.getElementById('uploadSection');
                        const previewSection = document.getElementById('previewSection');
                        
                        if (uploadSection) uploadSection.style.display = 'none';
                        if (previewSection) previewSection.style.display = 'block';
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const error = xhr.responseJSON?.message || 'Error processing file';
                    Swal.fire('Error', error, 'error');
                }
            });
        });
    }

    // Cancel preview button
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'cancelPreviewBtn') {
            console.log('Cancel button clicked');
            const uploadSection = document.getElementById('uploadSection');
            const previewSection = document.getElementById('previewSection');
            
            if (previewSection) previewSection.style.display = 'none';
            if (uploadSection) uploadSection.style.display = 'block';
            previewData = null;
        }
    });

    // Confirm import button - Multiple approaches to ensure it works
    document.addEventListener('click', function(e) {
        const target = e.target;
        const confirmBtn = target.id === 'confirmImportBtn' || target.closest('#confirmImportBtn');
        
        if (confirmBtn) {
            e.preventDefault();
            e.stopPropagation();
            console.log('=== CONFIRM BUTTON CLICKED ===');
            console.log('Event target:', target);
            console.log('PreviewData exists:', !!previewData);
            console.log('SweetAlert available:', typeof Swal !== 'undefined');
            
            if (typeof Swal === 'undefined') {
                alert('SweetAlert2 is not loaded! Please ensure it is included in your layout.');
                return;
            }
            
            if (!previewData || !previewData.preview || previewData.preview.length === 0) {
                Swal.fire('Error', 'No preview data available', 'error');
                return;
            }
            
            console.log('Showing SweetAlert confirmation...');
            
            Swal.fire({
                title: 'Confirm Import?',
                text: 'This will create assets and entities in the database.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, import!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                console.log('SweetAlert result:', result);
                if (result.isConfirmed) {
                    performImport();
                }
            });
        }
    });
    
    // Backup: Direct button click handler
    setTimeout(function() {
        const confirmBtn = document.getElementById('confirmImportBtn');
        if (confirmBtn) {
            console.log('Attaching direct click handler to confirm button');
            confirmBtn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('=== DIRECT CLICK HANDLER TRIGGERED ===');
                
                if (typeof Swal === 'undefined') {
                    alert('SweetAlert2 is not loaded! The import confirmation popup requires SweetAlert2.');
                    return false;
                }
                
                if (!previewData || !previewData.preview || previewData.preview.length === 0) {
                    Swal.fire('Error', 'No preview data available', 'error');
                    return false;
                }
                
                Swal.fire({
                    title: 'Confirm Import?',
                    text: 'This will create assets and entities in the database.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, import!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    console.log('SweetAlert result:', result);
                    if (result.isConfirmed) {
                        performImport();
                    }
                });
                
                return false;
            };
        } else {
            console.warn('Confirm button not found after timeout');
        }
    }, 500);

    // Editable fields
    document.addEventListener('click', function(e) {
        const editableField = e.target.closest('.editable-field');
        if (editableField) {
            const currentValue = editableField.textContent.trim();
            const field = editableField.getAttribute('data-field');
            const entityIndex = editableField.getAttribute('data-entity-index');
            const assetIndex = editableField.getAttribute('data-asset-index');

            const input = document.createElement('input');
            input.type = field === 'assigned_date' ? 'date' : 'text';
            input.className = 'form-control form-control-sm';
            input.value = currentValue;

            editableField.replaceWith(input);
            input.focus();

            const restoreSpan = function() {
                const newValue = input.value;
                const span = document.createElement('span');
                span.className = 'editable-field';
                span.setAttribute('data-field', field);
                span.setAttribute('data-entity-index', entityIndex);
                if (assetIndex !== null) {
                    span.setAttribute('data-asset-index', assetIndex);
                }
                span.textContent = newValue;

                input.replaceWith(span);

                // Update preview data
                if (assetIndex !== null && assetIndex !== 'null') {
                    previewData.preview[entityIndex].assets[assetIndex][field] = newValue;
                } else {
                    previewData.preview[entityIndex].entity[field] = newValue;
                }
            };

            input.addEventListener('blur', restoreSpan);
            input.addEventListener('keypress', function(e) {
                if (e.which === 13) {
                    input.blur();
                }
            });
        }
    });
});

function displayPreview(response) {
    const preview = response.preview;
    const categories = response.categories || {};
    const headers = response.headers || {};
    const debug = response.debug || {};
    
    let html = '';
    let totalAssets = 0;
    let newEntitiesCount = 0;

    // Display debug information
    if (debug && Object.keys(debug).length > 0) {
        const debugInfo = document.getElementById('debugInfo');
        if (debugInfo) debugInfo.style.display = 'block';
        
        const debugHeaderCount = document.getElementById('debugHeaderCount');
        const debugCategoryCount = document.getElementById('debugCategoryCount');
        if (debugHeaderCount) debugHeaderCount.textContent = debug.total_headers || 0;
        if (debugCategoryCount) debugCategoryCount.textContent = debug.mapped_categories || 0;
        
        // Show category mappings
        let categoryHtml = '';
        Object.keys(categories).forEach(header => {
            const cat = categories[header];
            categoryHtml += `<span class="badge bg-success me-1 mb-1">${header} → ${cat.name}</span>`;
        });
        const debugCategoryList = document.getElementById('debugCategoryList');
        if (debugCategoryList) {
            debugCategoryList.innerHTML = categoryHtml || '<em>No categories mapped</em>';
        }
        
        // Show unmapped headers
        const debugUnmapped = document.getElementById('debugUnmapped');
        if (debug.unmapped_headers && debug.unmapped_headers.length > 0) {
            if (debugUnmapped) debugUnmapped.style.display = 'block';
            const debugUnmappedList = document.getElementById('debugUnmappedList');
            if (debugUnmappedList) debugUnmappedList.textContent = debug.unmapped_headers.join(', ');
        } else {
            if (debugUnmapped) debugUnmapped.style.display = 'none';
        }
    }

    preview.forEach((row, index) => {
        const entity = row.entity;
        const assets = row.assets;
        const isNew = !entity.exists;
        const matchConfidence = entity.match_confidence || 100;
        const isLowConfidence = matchConfidence < 85 && !isNew;
        
        if (isNew) newEntitiesCount++;
        totalAssets += assets.length;

        // Group assets by category for better display
        const assetsByCategory = {};
        assets.forEach(asset => {
            if (!assetsByCategory[asset.category_name]) {
                assetsByCategory[asset.category_name] = [];
            }
            assetsByCategory[asset.category_name].push(asset);
        });

        html += `
            <div class="entity-row ${isNew ? 'new-entity' : ''} ${isLowConfidence ? 'low-confidence-match' : ''}" data-index="${index}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1">
                            <span class="badge bg-${getEntityTypeBadge(entity.type)}">${entity.type.toUpperCase()}</span>
                            ${isNew ? '<span class="badge bg-warning text-dark">NEW</span>' : ''}
                            ${isLowConfidence ? '<span class="badge bg-warning text-dark" title="Low confidence match - please verify"><i class="fas fa-exclamation-triangle"></i> Verify Match</span>' : ''}
                            <span class="editable-field" data-field="name" data-entity-index="${index}">${entity.name}</span>
                        </h5>
                        <small class="text-muted">
                            Region: ${entity.region_name || 'N/A'}
                            ${entity.location_name ? ` | Location: ${entity.location_name}` : ''}
                            ${entity.match_info ? ` | <span class="badge bg-info">${entity.match_info}</span>` : ''}
                            | Assets: <strong>${assets.length}</strong>
                        </small>
                        <br>
                        <small class="text-muted">
                            ${Object.keys(assetsByCategory).map(catName => 
                                `<span class="badge bg-secondary me-1">${catName}: ${assetsByCategory[catName].length}</span>`
                            ).join('')}
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEntity(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="row">
                    ${assets.map((asset, aIndex) => `
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="asset-item" data-asset-index="${aIndex}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong class="editable-field" data-field="asset_name" 
                                                data-entity-index="${index}" data-asset-index="${aIndex}">
                                            ${asset.asset_name}
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <span class="badge bg-primary">${asset.category_name}</span>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            ID: <span class="editable-field" data-field="asset_id" 
                                                      data-entity-index="${index}" data-asset-index="${aIndex}">
                                                ${asset.asset_id}
                                            </span>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            Date: <span class="editable-field" data-field="assigned_date" 
                                                        data-entity-index="${index}" data-asset-index="${aIndex}">
                                                ${asset.assigned_date}
                                            </span>
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="removeAsset(${index}, ${aIndex})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    });

    const previewContent = document.getElementById('previewContent');
    if (previewContent) previewContent.innerHTML = html;
    
    const totalEntitiesEl = document.getElementById('totalEntities');
    const totalAssetsEl = document.getElementById('totalAssets');
    const newEntitiesEl = document.getElementById('newEntities');
    
    if (totalEntitiesEl) totalEntitiesEl.textContent = preview.length;
    if (totalAssetsEl) totalAssetsEl.textContent = totalAssets;
    if (newEntitiesEl) newEntitiesEl.textContent = newEntitiesCount;
}

function getEntityTypeBadge(type) {
    const badges = {
        user: 'primary',
        court: 'success',
        office: 'info'
    };
    return badges[type] || 'secondary';
}

function removeEntity(index) {
    Swal.fire({
        title: 'Remove Entity?',
        text: 'This will remove all assets for this entity.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Remove'
    }).then((result) => {
        if (result.isConfirmed) {
            previewData.preview.splice(index, 1);
            displayPreview(previewData);
        }
    });
}

function removeAsset(entityIndex, assetIndex) {
    previewData.preview[entityIndex].assets.splice(assetIndex, 1);
    
    // Remove entity if no assets left
    if (previewData.preview[entityIndex].assets.length === 0) {
        previewData.preview.splice(entityIndex, 1);
    }
    
    displayPreview(previewData);
}

function performImport() {
    console.log('Starting import with data:', previewData);
    showLoading('Importing assets...');

    $.ajax({
        url: '{{ route("assets.import.store") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            preview_data: JSON.stringify(previewData.preview),
            assignment_year: previewData.assignment_year,
            region_id: previewData.region_id
        },
        success: function(response) {
            console.log('Import successful:', response);
            hideLoading();
            
            let message = `Successfully imported:
                <br>• ${response.imported.assets} Assets
                <br>• ${response.imported.users} New Users
                <br>• ${response.imported.courts} New Courts
                <br>• ${response.imported.offices} New Offices`;
            
            if (response.errors && response.errors.length > 0) {
                message += `<br><br><strong>Errors:</strong><br>${response.errors.join('<br>')}`;
            }

            Swal.fire({
                title: 'Import Complete!',
                html: message,
                icon: (response.errors && response.errors.length > 0) ? 'warning' : 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("assets.index") }}';
            });
        },
        error: function(xhr) {
            console.error('Import failed:', xhr);
            hideLoading();
            const error = xhr.responseJSON?.message || 'Import failed';
            Swal.fire('Error', error, 'error');
        }
    });
}

function showLoading(text) {
    const loadingText = document.getElementById('loadingText');
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingText) loadingText.textContent = text;
    if (loadingOverlay) loadingOverlay.style.display = 'block';
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) loadingOverlay.style.display = 'none';
}
</script>
@endsection