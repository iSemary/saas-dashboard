<div class="modal-header">
    <h4 class="modal-title">@translate('assign_user_to_tenant')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="tenant-owner-form" action="{{ route('landlord.tenant-owners.store') }}" method="POST">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tenant_id">@translate('tenant') <span class="text-danger">*</span></label>
                    <select class="form-control @error('tenant_id') is-invalid @enderror" 
                            id="tenant_id" name="tenant_id" required>
                        <option value="">@translate('select_tenant')</option>
                        @foreach($tenants ?? [] as $tenant)
                            <option value="{{ $tenant->id }}" 
                                    {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->name }} ({{ $tenant->domain }})
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="user_id">@translate('user') <span class="text-danger">*</span></label>
                    <select class="form-control @error('user_id') is-invalid @enderror" 
                            id="user_id" name="user_id" required>
                        <option value="">@translate('select_user')</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" 
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="role">@translate('role') <span class="text-danger">*</span></label>
                    <select class="form-control @error('role') is-invalid @enderror" 
                            id="role" name="role" required>
                        <option value="">@translate('select_role')</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>@translate('owner')</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>@translate('admin')</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>@translate('manager')</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>@translate('user')</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status">@translate('status') <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>@translate('active')</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>@translate('inactive')</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>@translate('suspended')</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_super_admin" 
                               name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_super_admin">
                            @translate('is_super_admin')
                        </label>
                    </div>
                    <small class="form-text text-muted">
                        @translate('super_admin_description')
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="permissions">@translate('permissions')</label>
                    <div class="permissions-container">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>@translate('tenant_owner_permissions')</h6>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_read_tenant_owners" name="permissions[]" 
                                           value="read.tenant_owners" {{ in_array('read.tenant_owners', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_read_tenant_owners">
                                        @translate('read_tenant_owners')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_create_tenant_owners" name="permissions[]" 
                                           value="create.tenant_owners" {{ in_array('create.tenant_owners', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_create_tenant_owners">
                                        @translate('create_tenant_owners')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_update_tenant_owners" name="permissions[]" 
                                           value="update.tenant_owners" {{ in_array('update.tenant_owners', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_update_tenant_owners">
                                        @translate('update_tenant_owners')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_delete_tenant_owners" name="permissions[]" 
                                           value="delete.tenant_owners" {{ in_array('delete.tenant_owners', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_delete_tenant_owners">
                                        @translate('delete_tenant_owners')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>@translate('general_permissions')</h6>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_read_users" name="permissions[]" 
                                           value="read.users" {{ in_array('read.users', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_read_users">
                                        @translate('read_users')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_read_tenants" name="permissions[]" 
                                           value="read.tenants" {{ in_array('read.tenants', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_read_tenants">
                                        @translate('read_tenants')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                           id="permission_read_brands" name="permissions[]" 
                                           value="read.brands" {{ in_array('read.brands', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_read_brands">
                                        @translate('read_brands')
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('permissions')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('cancel')</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> @translate('assign_user')
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Form submission
    $('#tenant-owner-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> @translate("assigning")...').prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#tenant-owner-form')[0].reset();
                    $('.modal').modal('hide');
                    // Reload the main table
                    if (typeof window.reloadTenantOwnersTable === 'function') {
                        window.reloadTenantOwnersTable();
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    displayValidationErrors(errors);
                } else {
                    toastr.error('@translate("error_occurred")');
                }
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Super admin checkbox change
    $('#is_super_admin').on('change', function() {
        if ($(this).is(':checked')) {
            // Check all permission checkboxes when super admin is selected
            $('.permission-checkbox').prop('checked', true);
        }
    });

    // Permission checkbox change
    $('.permission-checkbox').on('change', function() {
        if (!$(this).is(':checked')) {
            // Uncheck super admin if any permission is unchecked
            $('#is_super_admin').prop('checked', false);
        }
    });

    function displayValidationErrors(errors) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Display new errors
        $.each(errors, function(field, messages) {
            const input = $(`[name="${field}"]`);
            input.addClass('is-invalid');
            
            if (input.length) {
                input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
            }
        });
    }
});
</script>
