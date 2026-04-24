<form action="{{ isset($row) ? route('landlord.provinces.update', $row->id) : route('landlord.provinces.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="country_id" class="form-label">@translate('country') <span class="text-danger">*</span></label>
        <select class="select2 form-control" name="country_id" required>
            <option value="">@translate('select')</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ isset($row) && $row->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_capital" class="form-toggle" {{ isset($row) && $row->is_capital ? 'checked' : '' }} data-toggle="toggle"> @translate('is_capital')
        </label>
    </div>

    <div class="form-group">
        <label for="flag" class="form-label">@translate('flag')</label>
        <input type="file" name="flag" id="flag" class="border-0 form-control upload-image" accept="image/*">
        <div class="preview-image-container mt-2">
            <img src="{{ isset($row) && $row->flag ? $row->flag : asset('assets/shared/images/icons/defaults/image.png') }}"
                width="100px" height="100px" alt="Preview" class="preview-image" />
        </div>
    </div>

    <div class="form-group">
        <label for="phone_code" class="form-label">@translate('phone_code')</label>
        <input type="text" name="phone_code" id="phone_code" class="form-control"
            value="{{ isset($row) ? $row->phone_code : '' }}" required>
    </div>

    <div class="form-group">
        <label for="timezone" class="form-label">@translate('timezone')</label>
        <input type="text" name="timezone" id="timezone" class="form-control"
            value="{{ isset($row) ? $row->timezone : '' }}" required>
    </div>

    <div class="form-group">
        <label for="latitude" class="form-label">@translate('latitude')</label>
        <input type="number" step="any" name="latitude" id="latitude" class="form-control"
            value="{{ isset($row) ? $row->latitude : '' }}">
    </div>

    <div class="form-group">
        <label for="longitude" class="form-label">@translate('longitude')</label>
        <input type="number" step="any" name="longitude" id="longitude" class="form-control"
            value="{{ isset($row) ? $row->longitude : '' }}">
    </div>

    <div class="form-group">
        <label for="area_km2" class="form-label">@translate('area_km2')</label>
        <input type="number" step="0.01" name="area_km2" id="area_km2" class="form-control"
            value="{{ isset($row) ? $row->area_km2 : '' }}">
    </div>

    <div class="form-group">
        <label for="population" class="form-label">@translate('population')</label>
        <input type="number" name="population" id="population" class="form-control"
            value="{{ isset($row) ? $row->population : '' }}">
    </div>

    <div class="form-group">
        <label for="currency_code" class="form-label">@translate('currency_code')</label>
        <input type="text" name="currency_code" id="currency_code" class="form-control"
            value="{{ isset($row) ? $row->currency_code : '' }}" maxlength="3">
    </div>

    <div class="form-group">
        <label for="currency_symbol" class="form-label">@translate('currency_symbol')</label>
        <input type="text" name="currency_symbol" id="currency_symbol" class="form-control"
            value="{{ isset($row) ? $row->currency_symbol : '' }}" maxlength="10">
    </div>

    <div class="form-group">
        <label for="language_code" class="form-label">@translate('language_code')</label>
        <input type="text" name="language_code" id="language_code" class="form-control"
            value="{{ isset($row) ? $row->language_code : '' }}" maxlength="5">
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
