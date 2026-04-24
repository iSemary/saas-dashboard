@extends('layouts.tenant.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">@translate('bulk_create') - @translate('permissions')</h3>
    </div>
    <div class="card-body">
        <form id="bulkCreateForm" method="POST" action="{{ route('tenant.permissions.bulk-store') }}">
            @csrf

            <div class="form-group">
                <label for="resource">@translate('resource') <span class="text-danger">*</span></label>
                <input type="text" name="resource" id="resource" class="form-control" 
                    placeholder="users" required>
                <small class="form-text text-muted">@translate('enter_resource_name_lowercase')</small>
            </div>

            <div class="form-group">
                <label>@translate('actions') <span class="text-danger">*</span></label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="action_view" name="actions[]" value="view" checked>
                            <label class="custom-control-label" for="action_view">@translate('view')</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="action_create" name="actions[]" value="create" checked>
                            <label class="custom-control-label" for="action_create">@translate('create')</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="action_update" name="actions[]" value="update" checked>
                            <label class="custom-control-label" for="action_update">@translate('update')</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="action_delete" name="actions[]" value="delete" checked>
                            <label class="custom-control-label" for="action_delete">@translate('delete')</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>@translate('example'):</strong> 
                @translate('resource') = "users", @translate('actions') = view, create, update, delete<br>
                <strong>@translate('will_create'):</strong> view.users, create.users, update.users, delete.users
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-2"></i>@translate('create_permissions')
                </button>
                <a href="{{ route('tenant.permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>@translate('back')
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$('#bulkCreateForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const formData = new FormData(this);
    
    Swal.fire({
        title: '@translate("creating_permissions")',
        text: '@translate("please_wait")',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.close();
            if (response.success)
            {
                Swal.fire({
                    icon: 'success',
                    title: '@translate("success")',
                    text: response.message,
                    timer: 2000
                }).then(() => {
                    window.location.href = '{{ route("tenant.permissions.index") }}';
                });
            }
            else
            {
                Swal.fire({
                    icon: 'error',
                    title: '@translate("error")',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: '@translate("error")',
                text: xhr.responseJSON?.message || '@translate("an_error_occurred")'
            });
        }
    });
});
</script>
@endsection

