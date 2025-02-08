<form action="{{ isset($row) ? route('landlord.units.update', $row->id) : route('landlord.units.store') }}"
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
        <label for="code" class="form-label">@translate('code') <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control"
            value="{{ isset($row) ? $row->code : '' }}" required>
    </div>

    <div class="form-group">
        <label for="base_conversion" class="form-label">@translate('base_conversion') <span
                class="text-danger">*</span></label>
        <input type="text" name="base_conversion" id="base_conversion" class="form-control"
            value="{{ isset($row) ? $row->base_conversion : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description') <span
                class="text-danger">*</span></label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="type_id" class="form-label">@translate('type') <span class="text-danger">*</span></label>
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
            <input type="checkbox" name="is_base_unit" class="form-toggle" {{ isset($row) && $row->is_base_unit ? 'checked' : '' }} data-toggle="toggle"> @translate('is_base_unit')
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
