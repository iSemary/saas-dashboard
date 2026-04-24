<form action="{{ isset($permission) ? route('tenant.permissions.update', $permission->id) : route('tenant.permissions.store') }}"
    class="{{ isset($permission) ? 'edit-form' : 'create-form' }}" method="POST">
    @csrf
    @if (isset($permission))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($permission) ? $permission->name : '' }}" required placeholder="view.users">
        <small class="form-text text-muted">@translate('format_action_resource') (e.g., view.users, create.roles)</small>
    </div>

    <div class="form-group">
        <label for="guard_name" class="form-label">@translate('guard_name')</label>
        <select name="guard_name" id="guard_name" class="form-control">
            <option value="web" {{ (isset($permission) && $permission->guard_name == 'web') ? 'selected' : '' }}>Web</option>
            <option value="api" {{ (isset($permission) && $permission->guard_name == 'api') ? 'selected' : '' }}>API</option>
        </select>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('close')</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i>{{ isset($permission) ? translate('update') : translate('create') }}
        </button>
    </div>
</form>

