<form action="{{ isset($row) ? route('landlord.cities.update', $row->id) : route('landlord.cities.store') }}"
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
        <label for="postal_code" class="form-label">@translate('postal_code')</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control"
            value="{{ isset($row) ? $row->postal_code : '' }}">
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_capital" class="form-toggle" {{ isset($row) && $row->is_capital ? 'checked' : '' }} data-toggle="toggle"> @translate('is_capital')
        </label>
    </div>

    <div class="form-group">
        <label for="phone_code" class="form-label">@translate('phone_code')</label>
        <input type="text" name="phone_code" id="phone_code" class="form-control"
            value="{{ isset($row) ? $row->phone_code : '' }}">
    </div>

    <div class="form-group">
        <label for="timezone" class="form-label">@translate('timezone')</label>
        <input type="text" name="timezone" id="timezone" class="form-control"
            value="{{ isset($row) ? $row->timezone : '' }}">
    </div>

    <div class="form-group">
        <label for="province" class="form-label">@translate('province') <span class="text-danger">*</span></label>
        <select class="select2 form-control" name="province_id" required>
            <option value="">@translate('select')</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->id }}"
                    {{ isset($row) && $row->province_id == $province->id ? 'selected' : '' }}>{{ $province->name }}
                </option>
            @endforeach
        </select>
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
        <label for="elevation_m" class="form-label">@translate('elevation_m')</label>
        <input type="number" step="0.01" name="elevation_m" id="elevation_m" class="form-control"
            value="{{ isset($row) ? $row->elevation_m : '' }}">
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
