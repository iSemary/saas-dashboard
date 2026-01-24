<form action="{{ isset($row) ? route('landlord.permission-groups.update', $row->id) : route('landlord.permission-groups.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="guard_name" class="form-label">@translate('guard_name') <span class="text-danger">*</span></label>
        <input type="text" name="guard_name" id="guard_name" class="form-control"
            value="{{ isset($row) ? ($row->guard_name ?? 'api') : 'api' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control" rows="3">{{ isset($row) ? $row->description : '' }}</textarea>
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
                                    {{ isset($row) && $row->permissions->contains($permission) ? 'checked' : '' }}>
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
