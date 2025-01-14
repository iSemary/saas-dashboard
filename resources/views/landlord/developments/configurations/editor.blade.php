<form action="{{ isset($row) ? route('landlord.development.configurations.update', $row->id) : route('landlord.development.configurations.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="configuration_key" class="form-label">@translate('configuration_key')</label>
        <input type="text" name="configuration_key" id="configuration_key" class="form-control snake-input"
            value="{{ isset($row) ? $row->configuration_key : '' }}" required>
    </div>

    <div class="form-group">
        <label for="configuration_value" class="form-label">@translate('configuration_value')</label>
        <textarea name="configuration_value" id="configuration_value" class="form-control" required>{{ isset($row) ? $row->configuration_value : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="type_id" class="form-label">@translate('type')</label>
        <select class="select2 form-control" name="type_id" required>
            <option value="">@translate('select')</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}"
                    {{ isset($row) && $row->type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_encrypted" class="form-toggle" {{ isset($row) && $row->is_encrypted ? 'checked' : '' }} data-toggle="toggle"> @translate('is_encrypted')
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_system" class="form-toggle" {{ isset($row) && $row->is_system ? 'checked' : '' }} data-toggle="toggle"> @translate('is_system')
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_visible" class="form-toggle" {{ isset($row) ? ($row->is_visible ? 'checked' : '') : 'checked' }} data-toggle="toggle"> @translate('is_visible')
        </label>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
