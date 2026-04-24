<form action="{{ isset($user) ? route('tenant.users.update', $user->id) : route('tenant.users.store') }}"
    class="{{ isset($user) ? 'edit-form' : 'create-form' }}" method="POST">
    @csrf
    @if (isset($user))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@translate('name') <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ isset($user) ? $user->name : '' }}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="email">@translate('email') <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control"
                    value="{{ isset($user) ? $user->email : '' }}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="username">@translate('username')</label>
                <input type="text" name="username" id="username" class="form-control"
                    value="{{ isset($user) ? $user->username : '' }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">@translate('phone')</label>
                <input type="text" name="phone" id="phone" class="form-control"
                    value="{{ isset($user) ? $user->phone : '' }}">
            </div>
        </div>
    </div>

    @if (!isset($user))
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">@translate('password') <span class="text-danger">*</span></label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="password_confirmation">@translate('confirm_password') <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
        </div>
    </div>
    @endif

    <div class="form-group">
        <label>@translate('roles')</label>
        <div class="row">
            @foreach($roles as $role)
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" 
                            id="role_{{ $role->id }}" 
                            name="roles[]" 
                            value="{{ $role->id }}"
                            {{ (isset($userRoles) && in_array($role->id, $userRoles)) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="role_{{ $role->id }}">
                            {{ ucfirst($role->name) }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('close')</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i>{{ isset($user) ? translate('update') : translate('create') }}
        </button>
    </div>
</form>

