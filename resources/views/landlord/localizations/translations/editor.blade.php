<form action="{{ isset($row) ? route('landlord.translations.update', $row->id) : route('landlord.translations.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="translationKey" class="form-label">@translate('key') <span class="text-danger">*</span></label>
        <input type="text" name="translation_key" id="translationKey" class="form-control"
            value="{{ isset($row) ? $row->translation_key : '' }}" required>
    </div>

    <div class="form-group">
        <label for="translationValue" class="form-label">@translate('language') <span class="text-danger">*</span></label>
        <select class="form-control select2" name="language_id" required>
            <option value="">@translate('select')</option>
            @foreach ($languages as $language)
                <option value="{{ $language->id }}" {{ isset($row) && $row->language_id == $language->id ? "selected": "" }}>{{ $language->name . " | " . $language->locale }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="translationValue" class="form-label">@translate('value') <span class="text-danger">*</span></label>
        <textarea name="translation_value" id="translationValue" class="form-control" required>{{ isset($row) ? $row->translation_value : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="translationContext" class="form-label">@translate('context')</label>
        <textarea name="translation_context" id="translationContext" class="form-control">{{ isset($row) ? $row->translation_context : '' }}</textarea>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_shareable" class="form-toggle" {{ isset($row) && $row->is_shareable ? 'checked' : '' }} data-toggle="toggle"> @translate('is_shareable') <small>@translate('shared_with_frontend_side_as_json')</small>
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
