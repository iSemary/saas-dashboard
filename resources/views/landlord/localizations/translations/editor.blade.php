<form action="{{ isset($row) ? route('landlord.translations.update', $row->id) : route('landlord.translations.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="translationKey" class="form-label">Key</label>
        <input type="text" name="translation_key" id="translationKey" class="form-control"
            value="{{ isset($row) ? $row->translation_key : '' }}" required>
    </div>

    <div class="form-group">
        <label for="translationValue" class="form-label">Language</label>
        <select class="form-control select2" name="language_id" required>
            <option value="">@lang('select')</option>
            @foreach ($languages as $language)
                <option value="{{ $language->id }}" {{ isset($row) && $row->language_id == $language->id ? "selected": "" }}>{{ $language->name . " | " . $language->locale }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="translationValue" class="form-label">Value</label>
        <textarea name="translation_value" id="translationValue" class="form-control" required>{{ isset($row) ? $row->translation_value : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="translationContext" class="form-label">Context</label>
        <textarea name="translation_context" id="translationContext" class="form-control" required>{{ isset($row) ? $row->translation_context : '' }}</textarea>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? 'Update' : 'Create' }}</button>
    </div>
</form>
