<form action="{{ isset($row) ? route('landlord.system-users.update', $row->id) : route('landlord.system-users.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="row">
        <div class="form-group col-6">
            <label for="name" class="form-label">@translate('name')</label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ isset($row) ? $row->name : '' }}" required>
        </div>
        <div class="form-group col-6">
            <label for="username" class="form-label">@translate('username')</label>
            <input type="text" name="username" id="username" class="form-control"
                value="{{ isset($row) ? $row->username : '' }}" required>
        </div>
        <div class="form-group col-6">
            <label for="email" class="form-label">@translate('email')</label>
            <input type="email" name="email" id="email" class="form-control"
                value="{{ isset($row) ? $row->email : '' }}" required>
        </div>
        <div class="form-group col-6">
            <label for="password" class="form-label">@translate('password')</label>
            <input type="password" name="password" id="password" class="form-control"
                value="" required>
        </div>
        <div class="form-group col-6">
            <label for="role_id" class="form-label">@translate('role')</label>
            <select name="role_id" id="role_id" class="form-control select2" disabled>
                <option value="">
                    @translate('landlord')
                </option>
            </select>
        </div>
        <div class="form-group col-6">
            <label for="country_id" class="form-label">@translate('country')</label>
            <select class="select2 form-control" name="country_id" required>
                <option value="">@translate('select')</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}"
                        {{ isset($row) && $row->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>    
        <div class="form-group col-6">
            <label for="language_id" class="form-label">@translate('language')</label>
            <select class="select2 form-control" name="language_id" required>
                <option value="">@translate('select')</option>
                @foreach ($languages as $language)
                    <option value="{{ $language->id }}"
                        {{ isset($row) && $row->language_id == $language->id ? 'selected' : '' }}>{{ $language->name }}
                    </option>
                @endforeach
            </select>
        </div>    
    </div>



    <div class="form-group">
        <label for="permissions" class="form-label">@translate('permissions')</label>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach ($permissions as $permission)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}"
                                    class="form-check-input" value="{{ $permission->id }}"
                                    {{ isset($row) ? ($row->permissions->contains($permission) ? 'checked' : '') : 'checked' }}>
                                <label for="permission_{{ $permission->id }}" class="form-check-label">
                                    {{ translate($permission->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
