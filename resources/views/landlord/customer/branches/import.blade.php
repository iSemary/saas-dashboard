@extends('layouts.landlord.app')

@section('title', $title ?? translate('import_branches'))

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">@translate('import_branches')</h3>
        <div class="card-tools">
            <a href="{{ route('tenant.branches.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> @translate('back_to_branches')
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Instructions -->
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> @translate('import_instructions')</h5>
            <ul class="mb-0">
                <li>@translate('download_template_first')</li>
                <li>@translate('fill_template_with_branch_data')</li>
                <li>@translate('upload_filled_template')</li>
                <li>@translate('review_preview_before_import')</li>
            </ul>
        </div>

        <!-- Template Download -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@translate('download_template')</h5>
                    </div>
                    <div class="card-body">
                        <p>@translate('download_excel_template_with_sample_data')</p>
                        <a href="{{ route('tenant.branches.template') }}" class="btn btn-success">
                            <i class="fas fa-download"></i> @translate('download_template')
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@translate('required_fields')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>@translate('name')</strong> - @translate('branch_name_required')</li>
                            <li><strong>@translate('brand_id')</strong> - @translate('brand_id_required')</li>
                            <li><strong>@translate('status')</strong> - @translate('status_optional_default_active')</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload Form -->
        <form id="importForm" class="import-excel-form" action="{{ route('landlord.branches.process-import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="file">@translate('select_excel_file') <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" 
                                       accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="file">@translate('choose_file')</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            @translate('supported_formats') .xlsx, .xls, .csv | @translate('max_size') 10MB
                        </small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-upload"></i> @translate('upload_and_preview')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Import Status -->
        <div class="import-status mt-3"></div>

        <!-- Preview Table (will be populated after upload) -->
        <div id="previewSection" class="mt-4" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@translate('preview_data')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="previewTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@translate('row')</th>
                                    <th>@translate('name')</th>
                                    <th>@translate('code')</th>
                                    <th>@translate('brand')</th>
                                    <th>@translate('city')</th>
                                    <th>@translate('status')</th>
                                    <th>@translate('status')</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <strong>@translate('valid_rows')</strong>: <span id="validCount">0</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-danger">
                                <strong>@translate('invalid_rows')</strong>: <span id="invalidCount">0</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="button" class="btn btn-secondary" onclick="resetImport()">
                            @translate('reset')
                        </button>
                        <button type="button" class="btn btn-success" id="confirmImportBtn" onclick="confirmImport()">
                            @translate('confirm_import')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let importData = [];
let validRows = [];
let invalidRows = [];

$(document).ready(function() {
    // File input change handler
    $('#file').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName);
    });

    // Form submission handler
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.import-status').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> @translate("processing_file")...</div>');
                $('#previewSection').hide();
            },
            success: function(response) {
                if (response.success) {
                    displayPreview(response.data);
                } else {
                    $('.import-status').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = '@translate("upload_failed")';
                
                if (response && response.message) {
                    errorMessage = response.message;
                }
                
                $('.import-status').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMessage + '</div>');
            }
        });
    });
});

function displayPreview(data) {
    importData = data;
    validRows = data.valid_rows || [];
    invalidRows = data.invalid_rows || [];
    
    // Update counts
    $('#validCount').text(validRows.length);
    $('#invalidCount').text(invalidRows.length);
    
    // Populate preview table
    const tbody = $('#previewTableBody');
    tbody.empty();
    
    // Add valid rows
    validRows.forEach((row, index) => {
        tbody.append(`
            <tr class="table-success">
                <td>${index + 1}</td>
                <td>${row.name || ''}</td>
                <td>${row.code || ''}</td>
                <td>${row.brand_name || ''}</td>
                <td>${row.city || ''}</td>
                <td>${row.status || 'active'}</td>
                <td><span class="badge badge-success">@translate("valid")</span></td>
            </tr>
        `);
    });
    
    // Add invalid rows
    invalidRows.forEach((row, index) => {
        tbody.append(`
            <tr class="table-danger">
                <td>${validRows.length + index + 1}</td>
                <td>${row.name || ''}</td>
                <td>${row.code || ''}</td>
                <td>${row.brand_name || ''}</td>
                <td>${row.city || ''}</td>
                <td>${row.status || ''}</td>
                <td><span class="badge badge-danger">@translate("invalid")</span></td>
            </tr>
        `);
    });
    
    // Show preview section
    $('#previewSection').show();
    
    // Update status
    $('.import-status').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> @translate("file_processed_successfully")</div>');
    
    // Enable/disable confirm button
    if (validRows.length > 0) {
        $('#confirmImportBtn').prop('disabled', false);
    } else {
        $('#confirmImportBtn').prop('disabled', true);
    }
}

function confirmImport() {
    if (validRows.length === 0) {
        Swal.fire({
            title: '@translate("no_valid_data")',
            text: '@translate("no_valid_rows_to_import")',
            icon: 'warning'
        });
        return;
    }
    
    Swal.fire({
        title: '@translate("confirm_import")',
        text: '@translate("are_you_sure_you_want_to_import") ' + validRows.length + ' @translate("branches")?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '@translate("yes_import")',
        cancelButtonText: '@translate("cancel")'
    }).then((result) => {
        if (result.isConfirmed) {
            performImport();
        }
    });
}

function performImport() {
    $.ajax({
        url: '{{ route("tenant.branches.process-import") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            import_data: validRows
        },
        beforeSend: function() {
            $('.import-status').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> @translate("importing_data")...</div>');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: '@translate("import_successful")',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    window.location.href = '{{ route("tenant.branches.index") }}';
                });
            } else {
                Swal.fire({
                    title: '@translate("import_failed")',
                    text: response.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            let errorMessage = '@translate("import_failed")';
            
            if (response && response.message) {
                errorMessage = response.message;
            }
            
            Swal.fire({
                title: '@translate("import_failed")',
                text: errorMessage,
                icon: 'error'
            });
        }
    });
}

function resetImport() {
    $('#file').val('');
    $('#file').next('.custom-file-label').text('@translate("choose_file")');
    $('#previewSection').hide();
    $('.import-status').empty();
    importData = [];
    validRows = [];
    invalidRows = [];
}
</script>
@endsection
