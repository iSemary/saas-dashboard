<form
    action="{{ isset($row) ? route('landlord.development.configurations.update', $row->id) : route('landlord.development.configurations.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
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
        <label for="inputType" class="form-label">@translate('input_type')</label>
        <select class="select2 form-control" name="input_type" id="inputType" required>
            <option value="">@translate('select')</option>
            @foreach ($inputTypes as $inputType)
                <option value="{{ $inputType }}"
                    {{ isset($row) && $row->input_type == $inputType ? 'selected' : '' }}>{{ translate($inputType) }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="form-group">
        <label for="configuration_value" class="form-label">@translate('configuration_value')</label>
         <div class="form-group" id="inputTypeFields">
               <input name="configuration_value" type="text" id="configuration_value" value="{{ isset($row) ? $row->configuration_value : '' }}" class="form-control" required /></input>
         </div>
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
                <option value="{{ $type->id }}" {{ isset($row) && $row->type_id == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_encrypted" class="form-toggle"
                {{ isset($row) && $row->is_encrypted ? 'checked' : '' }} data-toggle="toggle"> @translate('is_encrypted')
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_system" class="form-toggle"
                {{ isset($row) && $row->is_system ? 'checked' : '' }} data-toggle="toggle"> @translate('is_system')
        </label>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_visible" class="form-toggle"
                {{ isset($row) ? ($row->is_visible ? 'checked' : '') : 'checked' }} data-toggle="toggle">
            @translate('is_visible')
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
<script src="{{ asset("assets/landlord/js/developments/configurations/editor.js") }}"></script>
@if(isset($row))
<script>
    var inputType = `{{$row->input_type}}`;
    var configurationValue =`{{$row->configuration_value}}`;
    handleInputType(inputType, configurationValue);
</script>
@endif
