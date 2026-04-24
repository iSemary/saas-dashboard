<form action="{{ isset($role) ? route('tenant.roles.update', $role->id) : route('tenant.roles.store') }}"
    class="{{ isset($role) ? 'edit-form' : 'create-form' }}" method="POST">
    @csrf
    @if (isset($role))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($role) ? $role->name : '' }}" required>
        <small class="form-text text-muted">@translate('use_lowercase_and_underscores')</small>
    </div>

    <div class="form-group">
        <label for="guard_name" class="form-label">@translate('guard_name')</label>
        <select name="guard_name" id="guard_name" class="form-control">
            <option value="web" {{ (isset($role) && $role->guard_name == 'web') ? 'selected' : '' }}>Web</option>
            <option value="api" {{ (isset($role) && $role->guard_name == 'api') ? 'selected' : '' }}>API</option>
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">@translate('permissions')</label>
        <div class="row">
            @foreach($permissionsGrouped as $resource => $permissions)
                <div class="col-md-6">
                    <div class="card mb-2">
                        <div class="card-header bg-light">
                            <strong>{{ ucfirst($resource) }}</strong>
                        </div>
                        <div class="card-body">
                            @foreach($permissions as $permission)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                        id="permission_{{ $permission->id }}" 
                                        name="permissions[]" 
                                        value="{{ $permission->id }}"
                                        {{ (isset($rolePermissions) && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('close')</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i>{{ isset($role) ? translate('update') : translate('create') }}
        </button>
    </div>
</form>

